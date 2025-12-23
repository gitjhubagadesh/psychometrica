<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\QuizModel;

class QuizLoginController extends Controller {

    protected $session;
    protected $userModel; // ✅ Declare userModel as a class property
    protected $quizModel;

    public function __construct() {
        $this->session = session(); // ✅ Initialize session
        $this->userModel = new UserModel(); // ✅ Initialize userModel
        $this->quizModel = new QuizModel();
    }

    public function index() {
        return view('quiz_pages/login');
    }

    public function login() {
        // Ensure userModel is available
        if (!$this->userModel) {
            session()->setFlashdata('error', 'Internal error: UserModel not initialized.');
            return redirect()->to('/test-signin');
        }

        // Get POST values
        $user_id = $this->request->getPost('user_id');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($user_id) || empty($password)) {
            session()->setFlashdata('error', 'User ID and Password are required.');
            return redirect()->to('/test-signin');
        }

        // Fetch user from the database
        $user = $this->userModel->checkUsersLogin($user_id, $password);
        if ($user) {
            // Check if the user account is valid or expired based on the current date
            $currentDate = date('Y-m-d');

            $firstName = isset($user['first_name']) ? $user['first_name'] : '';
            $lastName = isset($user['last_name']) ? $user['last_name'] : '';

            if ($user['validity_from'] <= $currentDate && $user['validity_to'] >= $currentDate) {
                // Store user details in session
                session()->set([
                    'quiz_id' => $user['id'],
                    'quiz_user_id' => $user['user_id'],
                    'quiz_username' => $user['username'],
                    'quiz_name' => trim($firstName . ' ' . $lastName),
                    'quiz_user_type' => $user['user_type'],
                    'quiz_group_id' => $user['group_id'],
                    'quiz_test_id' => $user['test_id'],
                    'quiz_company_name' => $user['company_name'],
                    'quiz_website' => $user['website'],
                    'quiz_company_logo' => $user['logo_image_path'],
                    'quiz_logged_in' => true
                ]);

                // Check if register_id is not empty or null
                if (empty($user['register_id'])) {
                    // If register_id is empty, redirect to the registration page
                    return redirect()->to('/test-registration'); // Redirect to the registration page
                }

                $userAttemptId = $this->quizModel->saveQuizAttempts([
                    'user_id' => session()->get('quiz_id'),
                    'started_at' => date('Y-m-d H:i:s'),
                ]);
                session()->set('userAttemptId', $userAttemptId);
                if (session()->get('quiz_test_id') == 24 || session()->get('quiz_test_id') == 25) {
                    return redirect()->to(base_url('test#!/cognitive'));
                }

                return redirect()->to('test');
            } else {
                // Account is expired
                session()->setFlashdata('error', 'Your account has expired. Please contact support.');
                return redirect()->to('/test-signin');
            }
        }

        // Invalid credentials
        session()->setFlashdata('error', 'Invalid credentials.');
        return redirect()->to('/test-signin');
    }
}
