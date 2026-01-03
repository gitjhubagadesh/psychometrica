<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\QuizModel;

class QuizRegisterController extends Controller {

    protected $session;
    protected $adminModel;

    public function __construct() {
        $this->session = session(); // ✅ Correctly assign to the class property
        // Check if 'username' exists in the session
        if (!$this->session->has('quiz_username')) {
            header('Location: /test-signin');
            exit; // Stop further execution
        }

        // Load model
        $this->userModel = new UserModel(); // ✅ Initialize userModel
        $this->quizModel = new QuizModel();
    }

    public function index() {
        return view('quiz_pages/login');
    }

    public function quizRregistration() {
        if (!$this->session->has('quiz_username')) {
            return redirect()->to('/test-signin')->send();
        }
        $session = session();
        $data = []; // Initialize $data array
        // Load countries list
        $data['countries'] = $this->quizModel->getCountryList();

        // Load user data based on session user_id
        $user_id = $session->get('quiz_user_id');
        if ($this->quizModel->isUserAlreadyRegistered($session->get('quiz_id'))) {
            if ($session->get('quiz_id') === 24 || $session->get('quiz_id') === 25) {
                return redirect()->to(base_url('test#!/cognitive'));
            }
            return redirect()->to('/test');
        }
        if ($user_id) {
            $data['user_data'] = $this->quizModel->getUserData($user_id);
        } else {
            $data['user_data'] = []; // Ensure it's always set to avoid errors
        }

        return view('quiz_pages/registration', $data);
    }

    public function saveRregistration() {
        $validation = \Config\Services::validation();

        $rules = [
            'user_id' => 'required|alpha_dash',
            'first_name' => 'required',
            'middle_name' => 'permit_empty',
            'last_name' => 'required',
            'email' => 'required|valid_email',
            'designation' => 'required',
            'gender' => 'required|in_list[Male,Female,Transgender]',
            'dob' => 'required|valid_date',
            'city' => 'required',
            'company' => 'permit_empty',
            'experience' => 'required|numeric',
            'country' => 'required|numeric',
            'id_type' => 'permit_empty',
            'id_number' => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $validation->listErrors());
        }

        // Get internal ID from user_id
        $userRef = $this->quizModel->getUserPrimaryId($this->request->getPost('user_id'));

        if (!$userRef) {
            return redirect()->back()->with('error', 'User ID not found.');
        }

        $data = [
            'user_id' => $userRef['id'],
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'designation' => $this->request->getPost('designation'),
            'gender' => $this->request->getPost('gender'),
            'dob' => $this->request->getPost('dob'),
            'city' => $this->request->getPost('city'),
            'company_name' => $this->request->getPost('company'),
            'experience' => $this->request->getPost('experience'),
            'country_id' => $this->request->getPost('country'),
            'identification_type' => $this->request->getPost('id_type'),
            'identification_no' => $this->request->getPost('id_number'),
            'status' => 1
        ];
        $this->quizModel->saveRegistration($data);
        
        // 🔄 Update session 'quiz_name'
        session()->set('quiz_name', $data['first_name'] . ' ' . $data['last_name']);

        return redirect()->to('test');
    }

    public function questionnaire() {
        return view('quiz_pages/questionnaire');
    }

    public function logout() {
        $this->session->destroy();
        return redirect()->to('/signin');
    }
}
