<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class ProgressController extends BaseController
{
    use ResponseTrait;
    protected $session;

    public function saveProgress()
    {
        $this->session = session(); 
        $data = $this->request->getJSON();
        $testId = session()->get('quiz_test_id');

        $db = \Config\Database::connect();
        $builder = $db->table('psy_user_test_progress');

        // Check if record exists
        $exists = $builder->where([
            'user_id' => session()->get('quiz_id'),
            'test_id' => $testId,
            'question_id' => $data->question_id
        ])->get()->getRow();

        $saveData = [
            'user_id' => session()->get('quiz_id'),
            'test_id' => $testId,
            'question_id' => $data->question_id,
            'factor_id' => $data->factor_id ?? null,
            'elapsed_time' => $data->elapsed_time,
            'total_duration' => $data->total_duration
        ];

        if ($exists) {
            $builder->where('id', $exists->id)->update($saveData);
        } else {
            $builder->insert($saveData);
        }

        return $this->respond(['status' => 'success']);
    }

    public function getProgress()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('psy_user_test_progress');
        $builder->where('user_id', session()->get('quiz_id'));
        $builder->where('test_id', session()->get('quiz_test_id'));

        $result = $builder->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
