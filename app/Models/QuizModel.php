<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class QuizModel extends Model {

    protected $table = 'psy_countries'; // Define table name

    public function getCountryList() {
        return $this->db->table('psy_countries')
                        ->select('id, name') // Adjust column names based on your table structure
                        ->orderBy('name', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    public function getUserData($user_id) {
        return $this->db->table('psy_users u')
                        ->select('u.*, c.company_name') // Add company name from join
                        ->join('psy_companies c', 'u.company_id = c.id', 'left') // Left join in case company_id is null
                        ->where('u.user_id', $user_id)
                        ->get()
                        ->getRowArray(); // Return single user record with company name
    }

    public function getUserPrimaryId($user_id) {
        return $this->db->table('psy_users')
                        ->select('id')
                        ->where('user_id', $user_id)
                        ->get()
                        ->getRowArray(); // returns ['id' => value]
    }

    public function isUserAlreadyRegistered($userId) {
        return $this->db->table('psy_user_registration')
                        ->where('user_id', $userId)
                        ->countAllResults() > 0;
    }

    public function saveRegistration($data) {
        return $this->db->table('psy_user_registration')->insert($data);
    }

    public function getTotalQuestion($quiz_test_id = null, $userId) {
        $factorIds = $this->getTestFactorIds($userId);

        if (empty($factorIds)) {
            return 0; // Or handle accordingly
        }
        $questionCount = $this->db->table('psy_questions')
                ->whereIn('test_factor_id', $factorIds)
                ->countAllResults();

        return $questionCount;
    }

    public function getTestName($userId) {
        $builder = $this->db->table('psy_users u');
        $builder->select("
        u.id AS user_id,
        CASE 
            WHEN u.is_master_test = 1 THEN mt.test_name
            ELSE t.test_name
        END AS test_name,
        t.test_description
    ", false);

        $builder->join('psy_master_tests mt', 'u.test_id = mt.id AND u.is_master_test = 1', 'left');
        // Remove is_master_test condition here so description always available
        $builder->join('psy_tests t', 'u.test_id = t.id', 'left');

        $builder->where('u.id', $userId);

        $query = $builder->get();
        // Debug
        // echo $this->db->getLastQuery(); exit;

        return $query->getRow();
    }

    public function getTestIds($user_id) {
        // Fetch user data
        $user = $this->db->table('psy_users')
                ->select('test_id, is_master_test')
                ->where('id', $user_id)
                ->get()
                ->getRowArray();

        if (!$user) {
            return [];
        }

        // Initialize array to hold final test info
        $tests = [];

        // If is_master_test is set, load multiple test_ids from master table
        if (!empty($user['is_master_test'])) {
            $masterTest = $this->db->table('psy_master_tests')
                    ->select('test_ids')
                    ->where('id', $user['test_id']) // user.test_id = master_test id
                    ->get()
                    ->getRowArray();

            if (!$masterTest || empty($masterTest['test_ids'])) {
                return [];
            }

            $testIds = json_decode($masterTest['test_ids'], true);

            if (!is_array($testIds)) {
                return [];
            }

            // Fetch test_id and test_description for each test_id
            $tests = $this->db->table('psy_tests')
                    ->select('id as test_id, test_description')
                    ->whereIn('id', $testIds)
                    ->get()
                    ->getResultArray();

            return $tests;
        }

        // If not master, return single test_id and test_description
        $singleTest = $this->db->table('psy_tests')
                ->select('id as test_id, test_description')
                ->where('id', $user['test_id'])
                ->get()
                ->getRowArray();

        return $singleTest ? [$singleTest] : [];
    }

    public function getTestFactorIds($user_id) {
        // Fetch user data
        $user = $this->db->table('psy_users')
                ->select('test_id, is_master_test')
                ->where('id', $user_id)
                ->get()
                ->getRowArray();

        if (!$user) {
            return [];
        }

        // If is_master_test is not null, fetch from master test
        if ($user['is_master_test'] > 0) {
            $masterTest = $this->db->table('psy_master_tests')
                    ->select('test_ids')
                    ->where('id', $user['test_id']) // user.test_id is the master test ID
                    ->get()
                    ->getRowArray();

            if (!$masterTest || empty($masterTest['test_ids'])) {
                return [];
            }

            $testIds = json_decode($masterTest['test_ids'], true);

            if (!is_array($testIds)) {
                return [];
            }

            // Fetch all factor_ids from these test IDs
            $tests = $this->db->table('psy_tests')
                    ->select('factor_ids')
                    ->whereIn('id', $testIds)
                    ->get()
                    ->getResultArray();

            $allFactorIds = [];

            foreach ($tests as $test) {
                $factors = json_decode($test['factor_ids'], true);
                if (is_array($factors)) {
                    $allFactorIds = array_merge($allFactorIds, $factors);
                }
            }

            // Return unique and trimmed factor IDs
            return array_values(array_unique(array_map('trim', $allFactorIds)));
        }
        // Else, fetch factor_ids from the single test
        $test = $this->db->table('psy_tests')
                ->select('factor_ids')
                ->where('id', $user['test_id']) // user.test_id is the regular test ID
                ->get()
                ->getRowArray();
        if (!$test || empty($test['factor_ids'])) {
            return [];
        }

        $factorIds = json_decode($test['factor_ids'], true);

        if (!is_array($factorIds)) {
            return [];
        }

        return array_map('trim', $factorIds);
    }

    public function getAllQuestionDetails($userId = null) {
        $factorIds = $this->getTestFactorIds($userId);

        // Step 1: Fetch all normal and memory questions with factor info
        $questionsQuery = $this->db->table('psy_questions')
                ->select('psy_questions.*, psy_test_factor.factor_description, psy_test_factor.factor_name, psy_test_factor.timer, psy_test_factor.id AS factorId, psy_test_factor.is_mandatory')
                ->join('psy_test_factor', 'psy_test_factor.id = psy_questions.test_factor_id', 'left')
                ->whereIn('psy_questions.test_factor_id', $factorIds)
                ->where('psy_questions.status', 1)
                ->get()
                ->getResultArray();

        // Step 2: Fetch paragraph groups
        $paragraphQuery = $this->db->table('psy_paragraph_questions')
                ->select('psy_paragraph_questions.*, psy_test_factor.factor_description, psy_test_factor.factor_name, psy_test_factor.timer, psy_test_factor.is_mandatory')
                ->join('psy_test_factor', 'psy_test_factor.id = psy_paragraph_questions.test_factor_id', 'left')
                ->whereIn('psy_paragraph_questions.test_factor_id', $factorIds)
                ->where('psy_paragraph_questions.status', 1)
                ->get()
                ->getResultArray();

        // Step 3: Fetch sub-questions of paragraphs
        $paragraphIds = !empty($paragraphQuery) ? array_column($paragraphQuery, 'id') : [];
        $subQuestionsQuery = [];
        if (!empty($paragraphIds)) {
            $subQuestionsQuery = $this->db->table('psy_questions')
                    ->whereIn('paragraph_question_id', $paragraphIds)
                    ->where('status', 1)
                    ->get()
                    ->getResultArray();
        }

        // Step 4: Combine all question IDs to fetch options
        $allQuestions = array_merge($questionsQuery, $subQuestionsQuery);
        $questionIds = array_column($allQuestions, 'id');

        // Step 5: Fetch options including is_correct
        $optionsRaw = $this->db->table('psy_question_options as o')
                ->select('o.id, o.question_id, o.option_text, o.option_image, o.is_correct')
                ->whereIn('o.question_id', $questionIds)
                ->get()
                ->getResultArray();

        // Step 6: Group options by question
        $optionsGrouped = [];
        foreach ($optionsRaw as $option) {
            $optionsGrouped[$option['question_id']][] = $option;
        }

        // Step 7: Fetch user previous answers
        $userAnswers = [];
        if ($userId) {
            $userAnswersRaw = $this->db->table('psy_user_answers')
                    ->select('question_id, selected_option_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getResultArray();

            foreach ($userAnswersRaw as $ua) {
                $userAnswers[$ua['question_id']] = $ua['selected_option_id'];
            }
        }

        // Step 8: Build grouped factor data
        $groupedData = [];
        $section = 0;

        // Process normal and memory questions
        foreach ($questionsQuery as $q) {
            $factorId = $q['test_factor_id'];
            $groupKey = $factorId;

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'factorId' => $factorId,
                    'factorName' => $q['factor_name'],
                    'sectionNo' => ++$section,
                    'factor_description' => $q['factor_description'],
                    'isMandatory' => $q['is_mandatory'],
                    'memoryQuestion' => $q['memory_main_id'] ? 'TRUE' : 'FALSE',
                    'memoryImagePath' => '',
                    'disapearTime' => '',
                    'factor_timer' => $q['timer'],
                    'paragraphQuestion' => 'FALSE',
                    'paragraphText' => '',
                    'questions' => []
                ];
            }

            // Attach memory image if present
            if ($q['memory_main_id']) {
                $masterImage = $this->db->table('psy_memory_main_image')
                        ->select('memory_main_image, disapearing_time')
                        ->where('id', $q['memory_main_id'])
                        ->get()
                        ->getRow();

                if ($masterImage) {
                    $groupedData[$groupKey]['memoryImagePath'] = $masterImage->memory_main_image;
                    $groupedData[$groupKey]['disapearTime'] = $masterImage->disapearing_time;
                }
            }

            // Attach options & selected answer
            $qOptions = $optionsGrouped[$q['id']] ?? [];

            // For demo=0 use is_correct from DB, otherwise remove
            if ($q['is_demo'] == 0) {
                foreach ($qOptions as &$opt) {
                    $opt['is_correct'] = $opt['is_correct'] ?? 0;
                }
                unset($opt);
            } else {
                foreach ($qOptions as &$opt) {
                    unset($opt['is_correct']);
                }
                unset($opt);
            }

            $q['options'] = $qOptions;
            $q['selected_option_id'] = $userAnswers[$q['id']] ?? null;
            $q['paragraphQuestion'] = 'FALSE';
            $q['paragraphText'] = '';

            unset($q['factor_description']);
            $groupedData[$groupKey]['questions'][] = $q;
        }

        // Step 9: Merge paragraph sub-questions under their factor
        // Step 9: Merge paragraph sub-questions under their factor
        foreach ($paragraphQuery as $paragraph) {
            $factorId = $paragraph['test_factor_id'];
            $groupKey = $factorId;

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'factorId' => $factorId,
                    'factorName' => $paragraph['factor_name'],
                    'sectionNo' => ++$section,
                    'factor_description' => $paragraph['factor_description'],
                    'isMandatory' => $paragraph['is_mandatory'],
                    'memoryQuestion' => 'FALSE',
                    'memoryImagePath' => '',
                    'disapearTime' => '',
                    'factor_timer' => $paragraph['timer'],
                    'paragraphQuestion' => 'FALSE',
                    'paragraphText' => '',
                    'questions' => []
                ];
            }

            foreach ($subQuestionsQuery as $sq) {
                if ($sq['paragraph_question_id'] == $paragraph['id']) {
                    $sqOptions = $optionsGrouped[$sq['id']] ?? [];

                    // ✅ Keep is_correct only for demo=0
                    if ($sq['is_demo'] == 0) {
                        foreach ($sqOptions as &$opt) {
                            $opt['is_correct'] = $opt['is_correct'] ?? 0;
                        }
                        unset($opt);
                    } else {
                        foreach ($sqOptions as &$opt) {
                            unset($opt['is_correct']);
                        }
                        unset($opt);
                    }

                    // Attach options and paragraph text
                    $sq['options'] = $sqOptions;
                    $sq['selected_option_id'] = $userAnswers[$sq['id']] ?? null;
                    $sq['paragraphQuestion'] = 'TRUE';
                    $sq['paragraphText'] = $paragraph['paragraph_text'];

                    unset($sq['factor_description']);

                    // 🚨 Avoid duplicate questions: 
                    // Remove same question id from normal questions if exists
                    $groupedData[$groupKey]['questions'] = array_filter(
                            $groupedData[$groupKey]['questions'],
                            fn($q) => $q['id'] !== $sq['id']
                    );

                    // Add only the version with description
                    $groupedData[$groupKey]['questions'][] = $sq;
                }
            }
        }

        if ($userId) {
            $testId = $this->db->table('psy_users')
                    ->select('test_id')
                    ->where('id', $userId)
                    ->get()
                    ->getRow('test_id');
        }


        // Step 10: Sort grouped data by factorId
        usort($groupedData, function ($a, $b) {
            return $a['factorId'] <=> $b['factorId'];
        });

        // Step 11: Randomization logic
        if ($testId == 20) {

            // 🔥 GLOBAL RANDOMIZATION (Ignore factor-wise)
            $allQuestions = [];

            foreach ($groupedData as &$group) {
                foreach ($group['questions'] as $q) {
                    $q['sectionNo'] = $group['sectionNo']; // preserve section reference
                    $allQuestions[] = $q;
                }
                $group['questions'] = []; // clear existing
            }
            unset($group);

            // Keep demo=0 first, then demo=1 (optional)
            $demoZero = array_filter($allQuestions, fn($q) => $q['is_demo'] == 0);
            $demoOne = array_filter($allQuestions, fn($q) => $q['is_demo'] == 1);

            shuffle($demoZero);
            shuffle($demoOne);

            $shuffled = array_merge($demoZero, $demoOne);

            // Put everything into the FIRST factor only
            $groupedData[0]['questions'] = $shuffled;
        } else {

            // Step 11: Shuffle questions inside each factor (demo=0 first)
            foreach ($groupedData as &$group) {
                if (!empty($group['questions'])) {
                    $demoZero = array_filter($group['questions'], fn($q) => $q['is_demo'] == 0);
                    $demoOne = array_filter($group['questions'], fn($q) => $q['is_demo'] == 1);

                    shuffle($demoZero);
                    shuffle($demoOne);

                    $group['questions'] = array_merge($demoZero, $demoOne);
                }
            }
            unset($group);
        }

        return array_values($groupedData);
    }

    public function updateLogoutTime($user_id) {
        // Get the latest attempt for the user (you can refine this if needed)

        $attempt = $this->db->table('psy_quiz_attempts')
                ->select('started_at')
                ->where('user_id', $user_id)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();

        if ($attempt->started_at) {
            $started_at = new DateTime($attempt->started_at);
            $ended_at = new DateTime(); // now
            $total_time_taken = $started_at->diff($ended_at)->format('%H:%I:%S');

            $data = [
                'ended_at' => $ended_at->format('Y-m-d H:i:s'),
                'total_time_taken' => $total_time_taken
            ];

            // Update the latest attempt
            return $this->db->table('psy_quiz_attempts')
                            ->where('user_id', $user_id)
                            ->orderBy('id', 'DESC') // optional: update latest attempt
                            ->limit(1)
                            ->update($data);
        }

        return false; // No attempt found
    }

    public function saveAnswers($data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_user_answers');

        $builder->insert($data);
        return $this->db->insertID();
    }

    public function saveQuizAttempts($data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_quiz_attempts');

        $builder->insert($data);
        return $this->db->insertID();
    }

    public function deletePreviousAnswer($userId, $questionId) {
        $query = $this->db->table('psy_user_answers')
                ->where('user_id', $userId)
                ->where('question_id', $questionId)
                ->delete();

        if (!$query) {
            // Query failed, get the last error
            $error = $this->db->error();
            print_r($error, true);
            exit;
            log_message('error', 'Delete failed: ' . print_r($error, true));
        }
    }

    public function saveElapsedTime($userId, $testId, $elapsedTime) {
        $builder = \Config\Database::connect()->table('psy_quiz_user_timer');

        $existing = $builder->where(['user_id' => $userId, 'test_id' => $testId])->get()->getRow();

        if ($existing) {
            return $builder->where(['user_id' => $userId, 'test_id' => $testId])
                            ->update(['elapsed_time' => $elapsedTime]);
        } else {
            return $builder->insert([
                        'user_id' => $userId,
                        'test_id' => $testId,
                        'elapsed_time' => $elapsedTime
            ]);
        }
    }

    public function getElapsedTime($userId, $testId) {
        $builder = \Config\Database::connect()->table('psy_quiz_user_timer');

        $record = $builder->where(['user_id' => $userId, 'test_id' => $testId])->get()->getRow();

        return $record ? $record->elapsed_time : 0;
    }
}
