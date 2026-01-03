<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Signin extends Controller {

    public function index() {
        return view('login_page/signin');
    }

    public function loginProcess() {
        $session = session();
        $model = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        log_message('debug', "Attempting login: Username - $username");

        $user = $model->authenticate($username, $password);
        if ($user) {
            log_message('debug', 'Login successful for user: ' . $username);

            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role_level' => $user['role_level'],
                'logged_in' => true,
            ]);

            return redirect()->to('admin');
        } else {
            log_message('error', 'Login failed: Invalid credentials for user ' . $username);
            $session->setFlashdata('error', 'Invalid username or password.');
            return redirect()->to('/signin')->withInput();
        }
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/signin');
    }
}
