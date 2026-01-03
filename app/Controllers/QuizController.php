<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\QuizModel;

class QuizController extends Controller {

    protected $session;
    protected $adminModel;

    public function __construct() {
        $this->session = session(); // ✅ Correctly assign to the class property

        if (!$this->session->has('quiz_username')) {
            echo '<meta http-equiv="refresh" content="0; url=/quiz-signin">';
        }

        // Load model
        $this->userModel = new UserModel(); // ✅ Initialize userModel
        $this->quizModel = new QuizModel();
    }

    public function index() {
        return view('quiz_pages/main');
    }
    
    public function cognitive() {
        return view('quiz_pages/cognitive');
    }
    

    public function questionnaire() {
        return view('quiz_pages/questionnaire');
    }

    public function quizFinish() {
        return view('quiz_pages/quiz_finish');
    }
    public function cogQuizFinish() {
        return view('quiz_pages/cog_quiz_finish');
    }
    
    

    public function contactError() {
        return view('quiz_pages/contact_error');
    }

    public function questionDetails() {
        $testId = session()->get('quiz_test_id');
        $userId = session()->get('quiz_id');

        if (!$testId) {
            return $this->response->setJSON([
                        'error' => 'Test ID not found in session.'
            ]);
        }


        $questionDetails = $this->quizModel->getAllQuestionDetails($userId);
        $noOfTotalQuestionCount = $this->quizModel->getTotalQuestion($testId, $userId);
        $testName = $this->quizModel->getTestName($userId);

        return $this->response->setJSON([
                    'noOfTotalQuestion' => $noOfTotalQuestionCount,
                    'questionDetails' => $questionDetails,
                    'testName' => $testName->test_name,
                    'testInstruction' => $testName->test_description,
                    'testIds' => $this->quizModel->getTestIds($userId)
        ]);
    }

    public function logoutTime() {
        $this->quizModel->updateLogoutTime(session()->get('quiz_id'));
    }

    public function saveAnswers() {
        // Check if the session contains the quiz_id
        if (!session()->has('quiz_id')) {
            return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Session expired. Please login again.',
                        'redirect' => '/test-signin'
            ]);
        }

        // Get the JSON data from the POST request
        $requestData = $this->request->getJSON(true);
        $userId = session()->get('quiz_id');
        $attemptId = session()->get('userAttemptId');

        // Validate input
        if (!$requestData || !isset($requestData['answers']) || !is_array($requestData['answers'])) {
            return $this->response->setStatusCode(400)->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid or missing data'
            ]);
        }

        $successCount = 0;
        foreach ($requestData['answers'] as $answer) {
            if (!isset($answer['question_id'], $answer['option_id'])) {
                continue; // skip invalid entry
            }

            // Optional: validate question_id and option_id types (e.g., integers)
            $questionId = (int) $answer['question_id'];
            $optionId = (int) $answer['option_id'];

            // Delete existing answer if it exists
            $this->quizModel->deletePreviousAnswer($userId, $questionId);

            // Save the answer
            $this->quizModel->saveAnswers([
                'user_id' => $userId,
                'question_id' => $questionId,
                'selected_option_id' => $optionId,
                'attempt_id' => $attemptId,
                'answered_at' => date('Y-m-d H:i:s'),
            ]);

            $successCount++;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'message' => "$successCount answers saved successfully"
        ]);
    }

    public function saveElapsedTime() {
        $json = $this->request->getJSON(true);

        $userId = session()->get('quiz_id');
        $testId = session()->get('quiz_test_id');
        $elapsedTime = $json['elapsed_time'];

        $this->quizModel->saveElapsedTime($userId, $testId, $elapsedTime);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Elapsed time saved.']);
    }

    public function getQuizData() {
        // Existing quiz loading logic here
        // Fetch the test, questions etc.

        $this->quizModel->getElapsedTime(session()->get('quiz_user_id'), session()->get('quiz_test_id'));

        return $this->respond([
                    'questionDetails' => $questions,
                    'testName' => $testName,
                    'testInstruction' => $testInstruction,
                    'elapsed_time' => $elapsedTime
        ]);
    }

    public function logout() {
        $this->session->destroy();
        return redirect()->to('/test-signin');
    }
}
