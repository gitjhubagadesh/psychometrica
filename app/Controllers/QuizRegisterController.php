<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\QuizModel;
use CodeIgniter\Controller;

class QuizRegisterController extends Controller
{
    protected $session;
    protected $userModel;
    protected $quizModel;

    public function __construct()
    {
        $this->session   = session();
        $this->userModel = new UserModel();
        $this->quizModel = new QuizModel();
    }

    /**
     * Guard: Ensure quiz user is logged in
     */
    private function ensureQuizLogin()
    {
        if (!$this->session->has('quiz_username')) {
            return redirect()->to('/test-signin');
        }
        return null;
    }

    /**
     * Quiz Registration Page
     */
    public function quizRregistration()
    {
        if ($redirect = $this->ensureQuizLogin()) {
            return $redirect;
        }

        // Already registered → redirect
        if ($this->quizModel->isUserAlreadyRegistered($this->session->get('quiz_id'))) {

            if (in_array($this->session->get('quiz_test_id'), [24, 25], true)) {
                // SPA route
                return redirect()->to('test#!/cognitive');
            }

            return redirect()->to('/test');
        }

        $userId = $this->session->get('quiz_user_id');

        return view('quiz_pages/registration', [
            'countries' => $this->quizModel->getCountryList(),
            'user_data' => $userId
                ? $this->quizModel->getUserData($userId)
                : []
        ]);
    }

    /**
     * Save Registration
     */
    public function saveRregistration()
    {
        if ($redirect = $this->ensureQuizLogin()) {
            return $redirect;
        }

        $rules = [
            'user_id'     => 'required|alpha_dash',
            'first_name'  => 'required',
            'middle_name' => 'permit_empty',
            'last_name'   => 'required',
            'email'       => 'required|valid_email',
            'designation' => 'required',
            'gender'      => 'required|in_list[Male,Female,Transgender]',
            'dob'         => 'required|valid_date',
            'city'        => 'required',
            'company'     => 'permit_empty',
            'experience'  => 'required|numeric',
            'country'     => 'required|numeric',
            'id_type'     => 'permit_empty',
            'id_number'   => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        // Resolve internal user ID
        $userRef = $this->quizModel->getUserPrimaryId(
            $this->request->getPost('user_id')
        );

        if (!$userRef) {
            return redirect()->back()->with('error', 'User ID not found.');
        }

        $data = [
            'user_id'             => $userRef['id'],
            'first_name'          => $this->request->getPost('first_name'),
            'middle_name'         => $this->request->getPost('middle_name'),
            'last_name'           => $this->request->getPost('last_name'),
            'email'               => $this->request->getPost('email'),
            'designation'         => $this->request->getPost('designation'),
            'gender'              => $this->request->getPost('gender'),
            'dob'                 => $this->request->getPost('dob'),
            'city'                => $this->request->getPost('city'),
            'company_name'        => $this->request->getPost('company'),
            'experience'          => $this->request->getPost('experience'),
            'country_id'          => $this->request->getPost('country'),
            'identification_type' => $this->request->getPost('id_type'),
            'identification_no'   => $this->request->getPost('id_number'),
            'status'              => 1,
        ];

        $this->quizModel->saveRegistration($data);

        // Update session display name
        $this->session->set(
            'quiz_name',
            $data['first_name'] . ' ' . $data['last_name']
        );

        // Redirect based on test type
        if (session()->get('quiz_test_id') == 24 || session()->get('quiz_test_id') == 25) {
            return redirect()->to('test#!/cognitive');
        }

        return redirect()->to('/test');
    }

    /**
     * Questionnaire page
     */
    public function questionnaire()
    {
        if ($redirect = $this->ensureQuizLogin()) {
            return $redirect;
        }

        return view('quiz_pages/questionnaire');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/test-signin');
    }
}
