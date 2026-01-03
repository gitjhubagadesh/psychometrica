<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model {

    public function ajax_datatable_basic(
            $columns,
            $query,
            $dataQuery,
            $indexColumn,
            $table = 'ci_session',
            $joins,
            $searchColumn,
            $orderBy,
            $whereCondition,
            $limit = 2,
            $offset = 0
    ) {
        $builder = $this->db->table($table);  // Use dynamic table name
        $builder->select($columns);

        // Apply multiple JOINs properly
        if (!empty($joins)) {
            foreach ($joins as $join) {
                if (is_array($join) && count($join) === 3) {
                    $builder->join($join[0], $join[1], $join[2]);
                }
            }
        }

        // Apply WHERE conditions
        if (!empty($whereCondition)) {
            $builder->where($whereCondition);
        }

        // Apply search filter
        if (!empty($searchColumn) && !empty($query)) {
            $builder->like($searchColumn, $query);
        }

        // Apply ordering
        if (!empty($orderBy)) {
            $builder->orderBy($orderBy);
        }

        // Apply limit and offset
        $builder->limit($limit, $offset);

        // Execute the query
        $result = $builder->get()->getResultArray();

        // Print last executed query for debugging
        //echo $this->db->getLastQuery();exit;

        return $result;
    }

    public function getTotalRecords($table, $joins = [], $whereCondition = "") {
        $builder = $this->db->table($table);

        // ✅ Apply joins safely
        if (!empty($joins)) {
            foreach ($joins as $join) {
                if (is_array($join) && count($join) === 3) {
                    $builder->join($join[0], $join[1], $join[2]);
                } else {
                    log_message('error', 'Invalid join format: ' . print_r($join, true));
                }
            }
        }

        // ✅ Apply where conditions properly
        if (!empty($whereCondition)) {
            $builder->where($whereCondition, null, false);
        }

        return $builder->countAllResults();
    }

    public function getTotalRecords1($table, $joins = [], $whereCondition = []) {
        $builder = $this->db->table($table);

        // Apply JOINs
        if (!empty($joins)) {
            foreach ($joins as $joinTable => $joinCondition) {
                $builder->join($joinTable, $joinCondition, 'left'); // Use 'left', 'inner', or 'right' as needed
            }
        }

        // Apply WHERE conditions
        if (!empty($whereCondition)) {
            $builder->where($whereCondition);
        }

        return $builder->countAllResults(); // Get total count
    }

    public function getById(string $table, int $id) {
        $builder = $this->db->table($table);
        return $builder->where($this->primaryKey, $id)->get()->getRow();
    }

    public function getCombinedCounts() {
        $sql = "
        SELECT COUNT(*) AS total, 'test_count' AS source FROM psy_tests WHERE status = 1
        UNION
        SELECT COUNT(*) AS total, 'total_questions' AS source FROM psy_questions WHERE status = 1
        UNION
        SELECT COUNT(*) AS total, 'registered_users' AS source FROM psy_users
        UNION
        SELECT COUNT(*) AS total, 'companies_count' AS source FROM psy_companies WHERE status = 1
        UNION
        SELECT COUNT(*) AS total, 'users_attempt_count' AS source FROM psy_quiz_attempts WHERE started_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        UNION
        SELECT COUNT(*) AS total, 'total_test_factors' AS source FROM psy_test_factor WHERE status = 1
        UNION
        SELECT COUNT(DISTINCT user_id) AS total, 'active_today' AS source FROM psy_quiz_attempts WHERE DATE(started_at) = CURDATE();
    ";

        $query = $this->db->query($sql);
        return $query->getResult();
    }

    public function getCompletionRate() {
        $sql = "
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN ended_at IS NOT NULL THEN 1 ELSE 0 END) AS completed
        FROM psy_quiz_attempts
        WHERE started_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    ";

        $result = $this->db->query($sql)->getRow();
        return [
            'total' => $result->total ?? 0,
            'completed' => $result->completed ?? 0
        ];
    }

    public function getRecentCompletions($limit = 10) {
        $sql = "
        SELECT
            u.name,
            u.email,
            t.test_name,
            c.company_name,
            qa.started_at,
            qa.ended_at,
            TIMESTAMPDIFF(MINUTE, qa.started_at, qa.ended_at) AS duration_minutes,
            qa.ended_at AS test_finish_time
        FROM psy_quiz_attempts qa
        INNER JOIN psy_users u ON qa.user_id = u.id
        INNER JOIN psy_tests t ON qa.test_id = t.id
        LEFT JOIN psy_companies c ON u.company_id = c.id
        WHERE qa.ended_at IS NOT NULL
        ORDER BY qa.ended_at DESC
        LIMIT ?
    ";

        return $this->db->query($sql, [$limit])->getResult();
    }

    public function getTestCompletionBreakdown() {
        $sql = "
        SELECT
            t.id AS test_id,
            t.test_name,
            tn.test_name AS test_category,
            COUNT(*) AS total_attempts,
            SUM(CASE WHEN qa.ended_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_attempts,
            SUM(CASE WHEN qa.ended_at IS NULL THEN 1 ELSE 0 END) AS incomplete_attempts,
            ROUND((SUM(CASE WHEN qa.ended_at IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) AS completion_rate,
            AVG(CASE
                WHEN qa.ended_at IS NOT NULL
                THEN TIMESTAMPDIFF(MINUTE, qa.started_at, qa.ended_at)
                ELSE NULL
            END) AS avg_duration_minutes,
            MAX(qa.ended_at) AS last_completed_at
        FROM psy_quiz_attempts qa
        INNER JOIN psy_tests t ON qa.test_id = t.id
        LEFT JOIN psy_test_name tn ON t.test_name_id = tn.id
        WHERE qa.started_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        GROUP BY t.id, t.test_name, tn.test_name
        ORDER BY completion_rate ASC, total_attempts DESC
    ";

        return $this->db->query($sql)->getResult();
    }

    public function getAttemptStats() {
        $sql = "
        SELECT 'active_users' AS label, COUNT(*) AS total
        FROM psy_quiz_attempts
        WHERE started_at IS NOT NULL AND ended_at IS NULL

        UNION

        SELECT 'completed_users' AS label, COUNT(*) AS total
        FROM psy_quiz_attempts
        WHERE started_at IS NOT NULL AND ended_at IS NOT NULL

        UNION

        SELECT 'currently_logged_in' AS label, COUNT(*) AS total
        FROM psy_quiz_attempts
        WHERE started_at IS NOT NULL AND ended_at IS NULL
          AND DATE(started_at) = CURDATE()

        UNION

        SELECT 'completed_today' AS label, COUNT(*) AS total
        FROM psy_quiz_attempts
        WHERE ended_at IS NOT NULL
          AND DATE(ended_at) = CURDATE()
    ";

        return $this->db->query($sql)->getResult();
    }

    /**
     * Update admin user details
     * @param int $id - User ID
     * @param array $data - Updated user data
     * @return bool
     */
    public function updateAdminUser($id, $data) {
        if (!isset($id) || empty($data)) {
            return false;
        }

        // Ensure the update function is executed correctly
        $result = $this->db->table('psy_admin_users')->where($this->primaryKey, $id)->update($data);

        return $result !== false;
    }

    public function deleteRecord($table, $id, $column = 'id') {
        if (!$table || !$id) {
            throw new \Exception("Table name and ID are required for deletion.");
        }

        // Debugging: Check table, column, and ID before deletion
        log_message('debug', "Attempting to delete from table: $table, where $column = $id");

        $result = $this->db->table($table)->where($column, $id)->delete();

        if ($this->db->affectedRows() > 0) {
            log_message('info', "Successfully deleted from $table where $column = $id");
            return true;
        } else {
            log_message('error', "Failed to delete from $table where $column = $id. It may not exist.");
            return false;
        }
    }

    public function saveCompany($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_companies'); // Explicit table name

        if ($id) {
            // Update existing record
            return $builder->where('id', $id)->update($data);
        } else {
            // Insert new record
            return $builder->insert($data);
        }
    }

    public function getTestHierarchy() {

        $query = $this->db->query("SELECT id, test_name FROM psy_tests ORDER BY id ASC");

        return $query->getResultArray();
    }

    private function buildTree($elements, $parentId = null) {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function getAllPsyTestFactor($limit = null, $offset = 0, $search = '') {
        $builder = $this->db->table('psy_test_factor');

        // Apply search filter if provided
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('factor_name', $search)
                    ->orLike('prefix', $search)
                    ->orLike('factor_description', $search)
                    ->groupEnd();
        }

        // Apply pagination if limit is provided
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        return $query->getResult();
    }

    public function getTestFactorCount($search = '') {
        $builder = $this->db->table('psy_test_factor');

        // Apply search filter if provided
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('factor_name', $search)
                    ->orLike('prefix', $search)
                    ->orLike('factor_description', $search)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function getTestReportList() {
        $builder = $this->db->table('psy_test_reports');
        $query = $builder->get();

        return $query->getResult();
    }

    public function saveTestName($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_test_name');

        if ($id) {
            return $builder->where('id', $id)->update($data);
        } else {
            // Insert new record
            return $builder->insert($data);
        }
    }

    public function saveTestFactor($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_test_factor');

        if ($id) {
            return $builder->where('id', $id)->update($data);
        } else {
            // Insert new record
            return $builder->insert($data);
        }
    }

    public function getAllCompany() {
        $builder = $this->db->table('psy_companies');
        $builder->where('status', 1); // Add condition where status = 1
        $query = $builder->get();

        return $query->getResult();
    }

    public function getAllTest() {
        $sql = "SELECT id, test_name, 0 as is_master  FROM psy_tests WHERE status = 1 UNION SELECT id, CONCAT(test_name, ' (𝗠)') as test_name, 1 as is_master FROM psy_master_tests WHERE status = 1 
            ORDER BY test_name ASC";

        $query = $this->db->query($sql);
        return $query->getResult();
    }

    public function getUserSections() {
        $builder = $this->db->table('psy_user_type');
        $query = $builder->get();

        return $query->getResult();
    }

    public function saveTest($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_tests');

        // Convert factor_ids to JSON if it's an array
        if (isset($data['factor_ids']) && is_array($data['factor_ids'])) {
            $data['factor_ids'] = json_encode($data['factor_ids']);
        }

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function saveMasterTest($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_master_tests');

        // Convert factor_ids to JSON if it's an array
        if (isset($data['test_ids']) && is_array($data['test_ids'])) {
            $data['test_ids'] = json_encode($data['test_ids']);
        }

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function saveGenerateUser($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_users');

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function saveUserGroup($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_user_groups');
        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function updateValidityDates($user_ids, $start_date, $end_date) {
        $builder = $this->db->table('psy_users'); // Change 'users' to your actual table name

        if (!empty($user_ids)) {
            $data = [
                'validity_from' => $start_date,
                'validity_to' => $end_date
            ];

            // Update users where id is in the provided array
            $builder->whereIn('id', $user_ids)->update($data);

            return $this->db->affectedRows() > 0; // Return true if rows were affected
        }

        return false; // No updates performed
    }

    public function getGeneratedUsersData($groupId) {
        $builder = $this->db->table('psy_users');
        $builder->select('user_id, password');
        $builder->where('group_id', $groupId);
        $query = $builder->get();

        return $query->getResultArray(); // Returns a single record as an object
    }

    public function getUserPrefix($test_id) {
        $builder = $this->db->table('psy_tests');
        $builder->select('user_prefix');
        $builder->where('id', $test_id);
        $query = $builder->get();

        $result = $query->getRowArray();

        return $result ? $result['user_prefix'] : null;
    }

    public function getUsersGroupFileName($groupId) {
        $builder = $this->db->table('psy_users');
        $builder->select('psy_users.*, psy_tests.test_name'); // Select all user fields + test_name
        $builder->join('psy_tests', 'psy_tests.id = psy_users.test_id', 'left'); // Join on test_id
        $builder->where('psy_users.group_id', $groupId);
        $builder->limit(1); // Fetch only one record
        $query = $builder->get();

        return $query->getRow(); // Returns a single record as an object
    }

    public function getAllTestFactor() {
        $builder = $this->db->table('psy_test_factor');
        $builder->where('status', 1);
        $query = $builder->get();

        return $query->getResult();
    }

    public function getLanguage() {
        $builder = $this->db->table('psy_languages');
        $query = $builder->get();

        return $query->getResult();
    }

    public function insertQuestion($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_questions');

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function insertMemoryMainQuestion($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_memory_main_image');

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function insertParagraphMainDetails($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_paragraph_questions');

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function getGroupedReportMenu() {
        return $this->db->table('psy_test_reports')
                        ->select('id, test_report_name, report_type')
                        ->where('status', 1)
                        ->orderBy('id', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    public function getQuestionById($questionId) {
        return $this->db->table('psy_questions')
                        ->where('id', $questionId)
                        ->get()
                        ->getRowArray();
    }

    public function getMemoryImageDetails($memory_main_id) {
        return $this->db->table('psy_memory_main_image')
                        ->where('id', $memory_main_id)
                        ->get()
                        ->getRowArray();
    }

    public function getQuestionOptions($questionId) {
        return $this->db->table('psy_question_options')
                        ->where('question_id', $questionId)
                        ->get()
                        ->getResultArray();
    }

    public function insertQuestionOptions($id = null, $data) {
        if (empty($data)) {
            return false;
        }

        $builder = $this->db->table('psy_question_options');

        if ($id) {
            // Update existing record
            $builder->where('id', $id)->update($data);
            return $this->db->affectedRows() > 0; // Return true if rows were affected
        } else {
            // Insert new record
            $builder->insert($data);
            return $this->db->insertID(); // Return inserted ID
        }
    }

    public function updateQuestionStatus($question_id, $data) {
        if (empty($data)) {
            return false;
        }
        return $this->db->table('psy_questions') // Use Query Builder
                        ->where('id', $question_id)
                        ->update($data);
    }

    public function getTestFactorList($is_master_test, $test_id) {
        $db = \Config\Database::connect();

        $test_id = (int) $test_id;
        $is_master_test = (int) $is_master_test;

        if ($test_id <= 0) {
            return [];
        }

        // Common number sequence generator
        $numberGenerator = "
        SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
        UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
        UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
    ";

        if ($is_master_test) {
            // MASTER TEST QUERY
            $sql = "
        SELECT f.id, f.factor_name, COUNT(q.id) AS question_count
        FROM (
            SELECT DISTINCT 
                JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', n.n, ']'))) AS factor_id
            FROM psy_master_tests pmt
            JOIN ($numberGenerator) AS idx ON idx.n < JSON_LENGTH(pmt.test_ids)
            JOIN psy_tests pt ON pt.id = JSON_UNQUOTE(JSON_EXTRACT(pmt.test_ids, CONCAT('$[', idx.n, ']')))
            JOIN ($numberGenerator) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
            WHERE pmt.id = ?
        ) AS tf
        JOIN psy_test_factor f ON f.id = tf.factor_id
        LEFT JOIN psy_questions q ON q.test_factor_id = f.id
        GROUP BY f.id, f.factor_name
        ORDER BY f.factor_name ASC";

            $params = [$test_id];
        } else {
            // NORMAL TEST QUERY
            $sql = "
        SELECT f.id, f.factor_name, COUNT(q.id) AS question_count
        FROM (
            SELECT DISTINCT 
                JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', n.n, ']'))) AS factor_id
            FROM psy_tests pt
            JOIN ($numberGenerator) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
            WHERE pt.id = ?
        ) AS tf
        JOIN psy_test_factor f ON f.id = tf.factor_id
        LEFT JOIN psy_questions q ON q.test_factor_id = f.id
        GROUP BY f.id, f.factor_name
        ORDER BY f.factor_name ASC";

            $params = [$test_id];
        }

        try {
            $builder = $db->query($sql, $params);
            //echo $db->getLastQuery();exit;
            return $builder->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Failed to get test factor list: ' . $e->getMessage());
            return [];
        }
    }

    public function getFactorAndCountForCOG($is_master_test, $test_id, $user_id) {
        $db = \Config\Database::connect();

        $sql = "
        SELECT 
            f.id AS factor_id,
            f.factor_name,
            COUNT(DISTINCT q.id) AS question_count,
            COALESCE(SUM(qo.option_mark), 0) AS total_score,
            CASE 
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 0 AND 9 THEN 'Low'
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 10 AND 14 THEN 'Medium'
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 15 AND 17 THEN 'High'
                WHEN COALESCE(SUM(qo.option_mark), 0) >= 18 THEN 'Superior'
                ELSE 'Unknown'
            END AS score
        FROM (
            SELECT DISTINCT 
                JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('\$[', n.n, ']'))) AS factor_id
            FROM psy_tests pt
            JOIN (
                SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
                UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
            ) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
            WHERE pt.id = ?
        ) AS tf
        JOIN psy_test_factor f ON f.id = tf.factor_id
        LEFT JOIN psy_questions q ON q.test_factor_id = f.id
        LEFT JOIN psy_user_answers ua ON ua.question_id = q.id AND ua.user_id = ?
        LEFT JOIN psy_question_options qo ON qo.id = ua.selected_option_id
        GROUP BY f.id, f.factor_name
        ORDER BY f.factor_name ASC
    ";

        try {
            $builder = $db->query($sql, [$test_id, $user_id]);
            //echo $db->getLastQuery();exit;
            return $builder->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Failed to get test factor list: ' . $e->getMessage());
            return [];
        }
    }

    public function getFactorAndCountForMF($is_master_test, $test_id, $user_id) {
        $db = \Config\Database::connect();

        $sql = "
        SELECT 
            f.id AS factor_id,
            f.factor_name,
            COUNT(DISTINCT q.id) AS question_count,
            COALESCE(SUM(qo.option_mark), 0) AS total_score,
            CASE 
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 1 AND 3 THEN 'Low'
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 4 AND 7 THEN 'Moderate'
                WHEN COALESCE(SUM(qo.option_mark), 0) BETWEEN 8 AND 10 THEN 'High'
                ELSE 'Unknown'
            END AS score
        FROM (
            SELECT DISTINCT 
                JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('\$[', n.n, ']'))) AS factor_id
            FROM psy_tests pt
            JOIN (
                SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
                UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
            ) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
            WHERE pt.id = ?
        ) AS tf
        JOIN psy_test_factor f ON f.id = tf.factor_id
        LEFT JOIN psy_questions q ON q.test_factor_id = f.id
        LEFT JOIN psy_user_answers ua ON ua.question_id = q.id AND ua.user_id = ?
        LEFT JOIN psy_question_options qo ON qo.id = ua.selected_option_id
        GROUP BY f.id, f.factor_name
        ORDER BY f.factor_name ASC
    ";

        try {
            $builder = $db->query($sql, [$test_id, $user_id]);
            return $builder->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Failed to get test factor list: ' . $e->getMessage());
            return [];
        }
    }

    public function getFactorAndCount($is_master_test, $test_id, $user_id) {
        $db = \Config\Database::connect();

        $test_id = (int) $test_id;
        $is_master_test = (int) $is_master_test;
        $user_id = (int) $user_id;

        if ($test_id <= 0) {
            return [];
        }

        // Common number sequence generator
        $numberGenerator = "
        SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
        UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
        UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
    ";

        if ($is_master_test) {
            // MASTER TEST QUERY
            $sql = "
            SELECT 
                f.id AS factor_id,
                f.factor_name,
                COUNT(DISTINCT q.id) AS question_count,
                COALESCE(SUM(qo.option_mark), 0) AS total_score,
                getBandAndPercentileByUser(COALESCE(SUM(qo.option_mark), 0), f.id, " . $user_id . ") AS pdf_score
            FROM (
                SELECT DISTINCT 
                    JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', n.n, ']'))) AS factor_id
                FROM psy_master_tests pmt
                JOIN ($numberGenerator) AS idx ON idx.n < JSON_LENGTH(pmt.test_ids)
                JOIN psy_tests pt ON pt.id = JSON_UNQUOTE(JSON_EXTRACT(pmt.test_ids, CONCAT('$[', idx.n, ']')))
                JOIN ($numberGenerator) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
                WHERE pmt.id = ?
            ) AS tf
            JOIN psy_test_factor f ON f.id = tf.factor_id
            LEFT JOIN psy_questions q ON q.test_factor_id = f.id
            LEFT JOIN psy_user_answers ua ON ua.question_id = q.id AND ua.user_id = ?
            LEFT JOIN psy_question_options qo ON qo.id = ua.selected_option_id
            GROUP BY f.id, f.factor_name
            ORDER BY f.factor_name ASC
        ";
            $params = [$test_id, $user_id];
        } else {
            // NORMAL TEST QUERY
            $sql = "
            SELECT 
                f.id AS factor_id,
                f.factor_name,
                COUNT(DISTINCT q.id) AS question_count,
                COALESCE(SUM(qo.option_mark), 0) AS total_score,
                getBandAndPercentileByUser(COALESCE(SUM(qo.option_mark), 0), f.id, " . $user_id . ") AS pdf_score
            FROM (
                SELECT DISTINCT 
                    JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', n.n, ']'))) AS factor_id
                FROM psy_tests pt
                JOIN ($numberGenerator) AS n ON n.n < JSON_LENGTH(pt.factor_ids)
                WHERE pt.id = ?
            ) AS tf
            JOIN psy_test_factor f ON f.id = tf.factor_id
            LEFT JOIN psy_questions q ON q.test_factor_id = f.id
            LEFT JOIN psy_user_answers ua ON ua.question_id = q.id AND ua.user_id = ?
            LEFT JOIN psy_question_options qo ON qo.id = ua.selected_option_id
            GROUP BY f.id, f.factor_name
            ORDER BY f.factor_name ASC
        ";
            $params = [$test_id, $user_id];
        }

        try {
            $builder = $db->query($sql, $params);
            //echo $db->getLastQuery();exit;
            return $builder->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Failed to get test factor list: ' . $e->getMessage());
            return [];
        }
    }

    public function getReportId($isMaster = null, $testId) {
        if (!$testId) {
            return null;
        }

        $table = $isMaster ? 'psy_master_tests' : 'psy_tests';

        $reportRow = $this->db->table($table)
                ->select('test_report_id')
                ->where('id', $testId)
                ->get()
                ->getRowArray();

        return $reportRow['test_report_id'] ?? null;
    }

    public function getUsersData($userId) {
        $builder = $this->db->table('psy_users');
        $builder->select("
        psy_users.*, 
        reg.designation,    
        reg.gender, 
        reg.company_name, 
        reg.experience, 
        CONCAT_WS(' ', reg.first_name, reg.last_name) AS uName, 
        TIMESTAMPDIFF(YEAR, reg.dob, CURDATE()) AS Age,
        quiz.total_time_taken,
        DATE_FORMAT(quiz.ended_at, '%d-%m-%Y') as created_date,
        country.name AS country_name
    ");
        $builder->join('psy_user_registration reg', 'reg.user_id = psy_users.id', 'left');
        $builder->join('psy_quiz_attempts quiz', 'quiz.user_id = psy_users.id', 'left');
        $builder->join('psy_countries country', 'country.id = reg.country_id', 'left');
        $builder->where('psy_users.id', $userId);
        $builder->limit(1);
        $query = $builder->get();

        $user = $query->getRow();

        if ($user) {
            // Standard Prefix Mapping
            switch (strtolower($user->gender)) {
                case 'male':
                    $prefix = 'Mr.';
                    break;
                case 'female':
                    $prefix = 'Ms.';
                    break;
                default:
                    $prefix = ''; // Or 'Mx.' if you prefer
                    break;
            }

            // Create combined display name
            $user->display_name = trim($prefix . ' ' . $user->uName);
        }

        return $user;
    }

    public function getUsersTestQuestion($userId) {
        $db = \Config\Database::connect();

        $user = $db->table('psy_users')->where('id', $userId)->get()->getRow();

        if (!$user) {
            return [];
        }

        $isMasterTest = (int) $user->is_master_test;
        $testId = (int) $user->test_id;

        if ($testId <= 0) {
            return [];
        }

        $numbersTable = "
        SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
        SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION
        SELECT 10 UNION SELECT 11 UNION SELECT 12
    ";

        if ($isMasterTest) {
            $sql = "
            SELECT q.*
            FROM psy_questions q
            WHERE q.test_factor_id IN (
                SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', nf.n, ']')))
                FROM psy_master_tests pmt
                JOIN ($numbersTable) idx ON idx.n < JSON_LENGTH(pmt.test_ids)
                JOIN psy_tests pt ON pt.id = JSON_UNQUOTE(JSON_EXTRACT(pmt.test_ids, CONCAT('$[', idx.n, ']')))
                JOIN ($numbersTable) nf ON nf.n < JSON_LENGTH(pt.factor_ids)
                WHERE pmt.id = ?
            )
        ";
            $params = [$testId];
        } else {
            $sql = "
            SELECT q.*
            FROM psy_questions q
            WHERE q.test_factor_id IN (
                SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(pt.factor_ids, CONCAT('$[', nf.n, ']')))
                FROM psy_tests pt
                JOIN ($numbersTable) nf ON nf.n < JSON_LENGTH(pt.factor_ids)
                WHERE pt.id = ?
            )
        ";
            $params = [$testId];
        }

        try {
            $query = $db->query($sql, $params);
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch user test questions: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserQuestionAnswers($userId) {

        $builder = $this->db->table('psy_user_answers a');
        $builder->select([
            'a.question_id',
            'c.test_factor_id',
            'b.option_text',
            'getOptionLabel(b.id) AS optionLabel',
            '(CASE WHEN b.is_correct = 1 THEN 1 ELSE b.option_mark END) AS option_mark'
        ]);
        $builder->join('psy_question_options b', 'a.selected_option_id = b.id', 'left');
        $builder->join('psy_questions c', 'a.question_id = c.id', 'left');
        $builder->where('a.user_id', $userId);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getUserTestName($userId) {
        $builder = $this->db->table('psy_users u');
        $builder->select("
        u.id AS user_id,
        CASE 
            WHEN u.is_master_test = 1 THEN mt.test_name
            ELSE t.test_name
        END AS test_name
    ", false);
        $builder->join('psy_master_tests mt', 'u.test_id = mt.id AND u.is_master_test = 1', 'left');
        $builder->join('psy_tests t', 'u.test_id = t.id AND u.is_master_test = 0', 'left');
        $builder->where('u.id', $userId);

        $query = $builder->get();
        return $query->getRow(); // Assuming you're expecting a single row
    }

    public function getFactorSkills($reportId) {
        $builder = $this->db->table('psy_factor_mapping m');
        $builder->select('m.main_factor_name, f.factor_name');
        $builder->join('psy_test_factor f', 'm.factor_id = f.id');
        $builder->where('m.status', 1);
        //$builder->where('f.status', 1);
        $builder->where('m.report_id', $reportId);
        $builder->orderBy('m.main_factor_name');
        $builder->orderBy('f.factor_name');

        $query = $builder->get();
        //echo $this->db->getLastQuery();exit;
        $results = $query->getResult();

        $categories = [];
        foreach ($results as $row) {
            $categories[$row->main_factor_name][] = $row->factor_name;
        }

        return $categories;
    }

    public function getFormattedSkills($report_for = null) {
        $builder = $this->db->table('psy_skill_statements');
        $builder->where('report_for', $report_for);
        $query = $builder->get();
        $skills = $query->getResultArray();

        $formatted = [];

        foreach ($skills as $skill) {
            $formatted[$skill['factor_name']][] = [
                'level' => $skill['skill_level'],
                'experience_level' => $skill['experience_level'],
                'statement' => $skill['statement'],
            ];
        }

        return $formatted;
    }

    public function getReportTopSkills() {
        $builder = $this->db->table('psy_report_top_skills');
        $query = $builder->get();
        $skills = $query->getResultArray();

        $data = [];

        foreach ($skills as $skill) {
            $factor = $skill['title'];

            // If not already set, assign the first matching statement
            if (!isset($data[$factor])) {
                $data[$factor] = $skill['description'];
            }
        }

        return $data;
    }

    public function getManagementSkills($uName) {
        $managementSkills = [];

        $builder = $this->db->table('psy_report_metadata_definitions');
        $query = $builder->get();
        $results = $query->getResultArray();

        foreach ($results as $row) {
            // Normalize key
            $key = str_replace('-', ' ', $row['desc_name']);

            $managementSkills[$key] = [
                'top_content' => str_replace("ABC", htmlspecialchars($uName), $row['top_content']),
                'male' => [
                    'description1' => $row['description_male_1'],
                    'description2' => str_contains($row['description_male_2'], "ABC") ? str_replace("ABC", htmlspecialchars($uName), $row['description_male_2']) : $row['description_male_2'],
                ],
                'female' => [
                    'description1' => $row['description_female_1'],
                    'description2' => str_contains($row['description_female_2'], "ABC") ? str_replace("ABC", htmlspecialchars($uName), $row['description_female_2']) : $row['description_female_2'],
                ],
            ];
        }

        return $managementSkills;
    }

    public function getTestPerformanceSummary($userId, $factorIds) {

        $builder = $this->db->table('psy_questions q');
        $builder->select("
        q.test_factor_id AS factor_id,
        f.factor_name,
        COUNT(DISTINCT q.id) AS total_questions,
        COUNT(DISTINCT ua.question_id) AS attempted_questions,
        COALESCE(SUM(qo.option_mark), 0) AS total_score,
        COUNT(DISTINCT CASE WHEN qo.is_correct = 1 THEN ua.question_id END) AS correct_answers,
        (COUNT(DISTINCT ua.question_id) - COUNT(DISTINCT CASE WHEN qo.is_correct = 1 THEN ua.question_id END)) AS wrong_answers,
        (COUNT(DISTINCT q.id) - COUNT(DISTINCT ua.question_id)) AS blank_answers
    ");
        $builder->join('psy_test_factor f', 'f.id = q.test_factor_id', 'left');
        $builder->join('psy_user_answers ua', 'ua.question_id = q.id AND ua.user_id = ' . (int) $userId, 'left');
        $builder->join('psy_question_options qo', 'qo.id = ua.selected_option_id', 'left');
        $builder->where('q.is_demo', 1);
        $builder->whereIn('q.test_factor_id', $factorIds);
        $builder->groupBy('q.test_factor_id, f.factor_name');
        $builder->orderBy('q.test_factor_id', 'ASC');

        $query = $builder->get();
        $results = $query->getResultArray();

        $data = [];

        foreach ($results as $row) {
            $factorId = $row['factor_id'];
            $data[$factorId] = [
                'factor_id' => $factorId,
                'factor_name' => $row['factor_name'],
                'total_questions' => $row['total_questions'],
                'attempted_questions' => $row['attempted_questions'],
                'correct_answers' => $row['correct_answers'],
                'wrong_answers' => $row['wrong_answers'],
                'blank_answers' => $row['blank_answers'],
                'total_score' => $row['total_score']
            ];
        }

        return $data;
    }
}
