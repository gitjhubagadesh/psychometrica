<?php

namespace App\Controllers;

use App\Models\AdminModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PsyMainController extends BaseController {

    protected $session;
    protected $adminModel;

    public function __construct() {
        $session = session();

        // Check if 'username' exists in the session
        if (!$session->has('username')) {
            header('Location: /signin'); // Redirect to login page
            exit(); // Ensure script stops executing
        }
        // ✅ Load model correctly
        $this->adminModel = new AdminModel();
    }

    public function index() {
        $data['reportMenu'] = $this->adminModel->getGroupedReportMenu();
        return view('admin_pages/main', $data);
    }

    public function dashboard() {
        return view('admin_pages/dashboard'); // Load dashboard view
    }

    public function dashboardData() {
        $countData = $this->adminModel->getCombinedCounts();
        $response['data'] = [
            'total_tests' => 0,
            'total_questions' => 0,
            'total_users' => 0,
            'registered_users' => 0,
            'companies_count' => 0,
            'attempts_data' => 0,
            'total_test_factors' => 0,
            'active_today' => 0,
            'completion_rate' => 0,
        ];

        // Map the results to keys
        foreach ($countData as $row) {
            switch ($row->source) {
                case 'test_count':
                    $response['data']['total_tests'] = $row->total;
                    break;
                case 'total_questions':
                    $response['data']['total_questions'] = $row->total;
                    break;
                case 'registered_users':
                    $response['data']['registered_users'] = $row->total;
                    break;
                case 'companies_count':
                    $response['data']['companies_count'] = $row->total;
                    break;
                case 'users_attempt_count':
                    $response['data']['attempts_data'] = $row->total;
                    break;
                case 'total_test_factors':
                    $response['data']['total_test_factors'] = $row->total;
                    break;
                case 'active_today':
                    $response['data']['active_today'] = $row->total;
                    break;
                // Add more cases if needed
            }
        }

        // Calculate completion rate
        $completionData = $this->adminModel->getCompletionRate();
        if ($completionData && isset($completionData['completed']) && isset($completionData['total']) && $completionData['total'] > 0) {
            $response['data']['completion_rate'] = round(($completionData['completed'] / $completionData['total']) * 100, 1);
        }

        return $this->response->setJSON($response);
    }

    public function getRecentCompletions() {
        $recentCompletions = $this->adminModel->getRecentCompletions(10);

        return $this->response->setJSON([
            'data' => $recentCompletions
        ]);
    }

    public function getTestCompletionBreakdown() {
        $breakdown = $this->adminModel->getTestCompletionBreakdown();

        return $this->response->setJSON([
            'data' => $breakdown
        ]);
    }

    public function getAttemptStats() {
        // Get stats from your model
        $stats = $this->adminModel->getAttemptStats();

        // Initialize response with default values
        $response = [
            'data' => [
                'active_users' => 0,
                'completed_users' => 0,
                'currently_logged_in' => 0,
                'completed_today' => 0
            ]
        ];

        // Map the database result to the response
        foreach ($stats as $row) {
            $response['data'][$row->label] = $row->total;
        }

        // Return the response as JSON
        return $this->response->setJSON($response);
    }

    public function adminUser() {
        return view('admin_pages/admin_users');
    }

    // Update user details
    public function manageAdminUser($id = null) {
        return view('admin_pages/manage_admin_users');
    }

    // Fetch user by ID
    public function getAdminUser($id) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_admin_users', $id);
        if (!$record) {
            return $this->response->setJSON(['error' => 'User not found'])->setStatusCode(404);
        }
        return $this->response->setJSON($record);
    }

    public function createAdminUser() {
        $userModel = new UserModel();
        $data = $this->request->getJSON();

        $newUser = [
            'name' => $data->name,
            'username' => $data->username,
            'email' => $data->email,
            'password' => password_hash($data->password, PASSWORD_BCRYPT) // Encrypt password
        ];

        $this->adminModel->insert($newUser);
        return $this->response->setJSON(['status' => 'success', 'message' => 'User created successfully']);
    }

    public function updateAdminUser() {
        $requestData = $this->request->getJSON(true); // Ensure data is received as an array

        if (!isset($requestData['id'])) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }

        // Prepare update data
        $updateData = [
            'name' => $requestData['name'] ?? null,
            'username' => $requestData['username'] ?? null,
            'email' => $requestData['email'] ?? null,
        ];

        // Check if password is provided and hash it before updating
        if (!empty($requestData['password'])) {
            $updateData['password'] = password_hash($requestData['password'], PASSWORD_BCRYPT);
        }

        // Fix: Ensure update() method is used correctly
        $updated = $this->adminModel->updateAdminUser($requestData['id'], $updateData);

        if ($updated) {
            return $this->response->setJSON(['message' => 'User updated successfully']);
        } else {
            return $this->response->setJSON(['error' => 'Update failed'])->setStatusCode(500);
        }
    }

    public function deleteAdminUser($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_admin_users', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function getAdminList() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $whereCondition = ''; // Modify based on your search
        $columns = '*'; // Fetch all columns
        $search = $this->request->getGet('search') ?? ''; // Get search query from request
        $dataQuery = ''; // Not needed
        $indexColumn = 'id';
        $table = 'psy_admin_users'; // Change this to your actual table
        $joins = [];
        $searchColumn = '';
        $orderBy = 'psy_admin_users.id ASC';
        // Get the total records count dynamically
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search,
                $dataQuery,
                $indexColumn,
                $table,
                $joins,
                $searchColumn,
                $orderBy,
                $whereCondition,
                $limit,
                $offset
        );
        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function companyList() {
        return view('admin_pages/company_list');
    }

    public function manageCompany($companyId = null) {
        return view('admin_pages/manage_company');
    }

    public function saveCompany($id = null) {
        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'website' => $this->request->getPost('website'),
            'contact_name' => $this->request->getPost('contact_name'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
            'branding' => $this->request->getPost('branding'),
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id')
        ];

        // Handle file upload
        $logo = $this->request->getFile('logo_image_path');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move('uploads/', $newName);
            $data['logo_image_path'] = 'uploads/' . $newName; // Ensure this column exists
        }

        // Save or Update Company
        $result = $this->adminModel->saveCompany($id, $data);

        if ($result) {
            return $this->response->setJSON(["message" => $id ? "Company updated successfully!" : "Company added successfully!"]);
        } else {
            return $this->response->setJSON(["message" => "Failed to save company"], 400);
        }
    }

    public function getCompanyData($id) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_companies', $id);
        if (!$record) {
            return $this->response->setJSON(['error' => 'User not found'])->setStatusCode(404);
        }
        return $this->response->setJSON($record);
    }

    public function deleteCompany($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_companies', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function getCompanyList() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $whereCondition = ''; // Modify based on your search
        $columns = '*'; // Fetch all columns
        $search = $this->request->getGet('search') ?? ''; // Get search query from request
        $dataQuery = ''; // Not needed
        $indexColumn = 'id';
        $table = 'psy_companies'; // Change this to your actual table
        $joins = [];
        $searchColumn = '';
        $orderBy = 'psy_companies.id ASC';
        // Get the total records count dynamically
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search,
                $dataQuery,
                $indexColumn,
                $table,
                $joins,
                $searchColumn,
                $orderBy,
                $whereCondition,
                $limit,
                $offset
        );
        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function addTestName($id = null) {
        $requestData = $this->request->getJSON(true);
        $data = [
            'test_name' => $requestData['test_name'],
            'parent_id' => $requestData['parent_id'],
            'status' => $requestData['status'] ?? 1,
            'created_by' => session()->get('user_id'),
        ];
        $result = $this->adminModel->saveTestName($id, $data);

        if ($result) {
            return $this->response->setJSON(["message" => $id ? "Company updated successfully!" : "Company added successfully!"]);
        } else {
            return $this->response->setJSON(["message" => "Failed to save company"], 400);
        }
    }

    public function manageTests() {
        return view('admin_pages/manage_test');
    }

    public function getTestList() {
        $data = [
            'master_test_data' => $this->adminModel->getTestHierarchy(),
            'test_reports' => $this->adminModel->getTestReportList()
        ];

        return $this->response->setJSON($data);
    }

    public function deleteTestName($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_test_name', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function getTestData($id = null) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_test_name', $id);
        return $this->response->setJSON($record);
    }

    public function manageTestFactor() {
        return view('admin_pages/manage_test_factor');
    }

    public function getTestFactorList() {
        // Only apply pagination if limit is explicitly provided
        $limit = $this->request->getGet('limit') !== null ? (int)$this->request->getGet('limit') : null;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $data = [
            'test_factors' => $this->adminModel->getAllPsyTestFactor($limit, $offset, $search),
            'total' => $this->adminModel->getTestFactorCount($search),
            'test_reports' => $this->adminModel->getTestReportList()
        ];

        return $this->response->setJSON($data);
    }

    public function getTestTestList() {
        return $this->response->setJSON($this->adminModel->getAllTest());
    }

    public function addTestFactor($id = null) {
        $requestData = $this->request->getJSON(true);
        $data = [];

        // Always update fields if they exist in request
        if (isset($requestData['factor_name'])) {
            $data['factor_name'] = $requestData['factor_name'];
        }

        if (isset($requestData['prefix'])) {
            $data['prefix'] = $requestData['prefix'];
        }

        if (isset($requestData['factor_description'])) {
            $data['factor_description'] = $requestData['factor_description'];
        }

        if (isset($requestData['timer'])) {
            $minutes = floatval($requestData['timer']); // keep decimal part
            $seconds = (int) round($minutes * 60); // convert to total seconds
            $data['timer'] = gmdate("H:i:s", $seconds);
        }

        if (isset($requestData['is_mandatory'])) {
            $data['is_mandatory'] = $requestData['is_mandatory'];
        }

        if (isset($requestData['status'])) {
            $data['status'] = $requestData['status'];
        }

        $data['created_by'] = session()->get('user_id');

        $result = $this->adminModel->saveTestFactor($id, $data);

        if ($result) {
            return $this->response->setJSON(["message" => 'success']);
        } else {
            return $this->response->setJSON(["message" => "fails"], 400);
        }
    }

    public function getFactorData($id = null) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_test_factor', $id);
        return $this->response->setJSON($record);
    }

    public function deleteTestFacor($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_test_factor', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function generateUser() {
        return view('admin_pages/generate_user');
    }

    public function createTest() {
        return view('admin_pages/create_test');
    }

    public function createMasterTest() {
        return view('admin_pages/create_master_test');
    }

    public function saveTest($id = null) {
        $requestData = $this->request->getJSON(true);
        $data = [
            'test_name' => $requestData['test_name'],
            'test_description' => $requestData['test_description'],
            'creator_name' => $requestData['creator_name'],
            'user_prefix' => $requestData['user_prefix'],
            'test_report_id' => $requestData['test_report_id'],
            'factor_ids' => json_encode($requestData['factor_ids']),
            'status' => $requestData['status'] ?? 1,
            'created_by' => session()->get('user_id'),
        ];
        $result = $this->adminModel->saveTest($id, $data);

        if ($result) {
            return $this->response->setJSON(["message" => 'success']);
        } else {
            return $this->response->setJSON(["message" => "fails"], 400);
        }
    }

    public function saveMasterTest($id = null) {
        $requestData = $this->request->getJSON(true);
        $data = [
            'test_name' => $requestData['test_name'],
            'creator_name' => $requestData['creator_name'],
            'user_prefix' => $requestData['user_prefix'],
            'test_report_id' => $requestData['test_report_id'],
            'test_ids' => json_encode($requestData['test_ids']),
            'status' => $requestData['status'] ?? 1,
            'created_by' => session()->get('user_id'),
        ];
        $result = $this->adminModel->saveMasterTest($id, $data);

        if ($result) {
            return $this->response->setJSON(["message" => 'success']);
        } else {
            return $this->response->setJSON(["message" => "fails"], 400);
        }
    }

    public function testList() {
        return view('admin_pages/test_list');
    }

    public function getAllTest() {
        return $this->response->setJSON($this->adminModel->getAllTest());
    }

    public function getUserSections() {
        return $this->response->setJSON($this->adminModel->getUserSections());
    }

    public function getAllCompany() {
        return $this->response->setJSON($this->adminModel->getAllCompany());
    }

    public function getTestListData() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $table = 'psy_tests';
        $indexColumn = 'psy_tests.id';
        $orderBy = 'psy_tests.id DESC';

        // Select columns, including factor_names retrieved via JSON join
        $columns = "psy_tests.*, 
                (SELECT GROUP_CONCAT(ptf.factor_name SEPARATOR ', ') 
                 FROM psy_test_factor ptf
                 WHERE JSON_CONTAINS(psy_tests.factor_ids, JSON_QUOTE(CAST(ptf.id AS CHAR)))
                ) AS factor_names";

        // Joins array (not needed since we're using a subquery)
        $joins = [];

        // Search condition
        $whereCondition = "";
        if (!empty($search)) {
            $whereCondition = "psy_tests.test_name LIKE '%$search%' 
                           OR EXISTS (
                               SELECT 1 FROM psy_test_factor ptf 
                               WHERE JSON_CONTAINS(psy_tests.factor_ids, JSON_QUOTE(CAST(ptf.id AS CHAR))) 
                               AND ptf.factor_name LIKE '%$search%'
                           )";
        }

        // Get total records count
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        // Fetch filtered data
        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search,
                "",
                $indexColumn,
                $table,
                $joins,
                "",
                $orderBy,
                $whereCondition,
                $limit,
                $offset
        );

        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function getMasterTestListData() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $table = 'psy_master_tests';
        $indexColumn = 'psy_master_tests.id';
        $orderBy = 'psy_master_tests.id ASC';

        // Select columns, including test_names retrieved via JSON join
        $columns = "psy_master_tests.*, 
                (SELECT GROUP_CONCAT(ptf.test_name SEPARATOR ', ') 
                 FROM psy_tests ptf
                 WHERE JSON_CONTAINS(psy_master_tests.test_ids, JSON_QUOTE(CAST(ptf.id AS CHAR)))
                ) AS test_names";

        // Joins array (not needed since we're using a subquery)
        $joins = [];

        // Search condition
        $whereCondition = "";
        if (!empty($search)) {
            $whereCondition = "psy_master_tests.test_name LIKE '%$search%'";
        }

        // Get total records count
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        // Fetch filtered data
        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search,
                "",
                $indexColumn,
                $table,
                $joins,
                "",
                $orderBy,
                $whereCondition,
                $limit,
                $offset
        );

        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function getUsersList() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $table = 'psy_users';
        $indexColumn = 'psy_users.id';
        $orderBy = 'psy_users.id DESC';

        // Select required columns
        $columns = "
            psy_users.*, psy_companies.company_name, psy_user_type.section_name, psy_tests.test_name, psy_user_groups.group_code, 
            (SELECT CONCAT('#', LPAD(HEX(0xAAAAAA | (CONV(LEFT(MD5(P1.group_id), 6), 16, 10) & 0x555555)), 6, '0')) 
            FROM psy_users AS P1 WHERE P1.id = psy_users.id) AS color_code";

        $joins = [
            ['psy_companies', 'psy_companies.id = psy_users.company_id', 'left'],
            ['psy_user_type', 'psy_user_type.id = psy_users.user_type', 'left'],
            ['psy_tests', 'psy_tests.id = psy_users.test_id', 'left'],
            ['psy_user_groups', 'psy_user_groups.id = psy_users.group_id', 'left']
        ];

        $whereCondition = "";
        if (!empty($search)) {
            $escapedSearch = $search;
            $whereCondition = "(
                psy_users.user_id LIKE '%$escapedSearch%' 
                OR psy_users.username LIKE '%$escapedSearch%' 
                OR psy_companies.company_name LIKE '%$escapedSearch%' 
                OR psy_user_type.section_name LIKE '%$escapedSearch%' 
                OR psy_tests.test_name LIKE '%$escapedSearch%' 
                OR psy_user_groups.group_code LIKE '%$escapedSearch%'
            )";
        }

        // Get total records count
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        // Fetch filtered data
        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search, // ✅ If this is not needed, remove it
                "",
                $indexColumn,
                $table,
                $joins,
                "",
                $orderBy,
                $whereCondition, // ✅ Ensure this is a string, not an array
                $limit,
                $offset
        );

        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function generateFileName($testName, $testId) {
        // Static part of the filename
        $staticName = "Kalyani";

        // Extract first 5 characters from test_name (uppercase)
        $testCode = strtoupper(substr($testName, 0, 5));

        // Get current date in M_D_Y format
        $datePart = date("n_j_Y");

        // Get current time in H_i_s A format
        $timePart = date("g_i_s A");

        // Combine all parts into a filename
        return "{$staticName}-{$testCode}-{$testId}_{$datePart} {$timePart}.xlsx";
    }

    public function updateUserValidity() {
        $requestData = $this->request->getJSON(true);

        // Extract values from request
        $user_ids = $requestData['users'] ?? [];
        $start_date = $requestData['start_date'] ?? null;
        $end_date = $requestData['end_date'] ?? null;

        // Validate input
        if (empty($user_ids) || !$start_date || !$end_date) {
            return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid data provided'
                    ])->setStatusCode(400);
        }

        // Call the model function to update records
        $updated = $this->adminModel->updateValidityDates($user_ids, $start_date, $end_date);

        // Return success or warning response
        return $this->response->setJSON([
                    'status' => $updated ? 'success' : 'warning',
                    'message' => $updated ? 'User validity dates updated successfully' : 'No changes made. The dates might be the same.'
        ]);
    }

    public function downloadUserGroup() {
        // Get group_id from the request
        $groupId = $this->request->getGet('group_id');

        if (!$groupId) {
            return $this->response->setJSON(['error' => 'Missing group_id'])->setStatusCode(400);
        }

        $userGroupData = $this->adminModel->getUsersGroupFileName($groupId);
        $getGeneratedUsersData = $this->adminModel->getGeneratedUsersData($groupId);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'User ID');
        $sheet->setCellValue('B1', 'Password');

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'A9A9A9']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        // Apply header style
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Populate user data
        $row = 2;
        foreach ($getGeneratedUsersData as $userData) {
            $sheet->setCellValue('A' . $row, $userData['user_id']);
            $sheet->setCellValue('B' . $row, $userData['password']);
            $row++;
        }

        // Determine the last row AFTER inserting data
        $lastRow = $row - 1;

        // Apply border styling to the entire table (header + data)
        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray($styleArray);

        // Auto-size columns
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = $this->generateFileName($userGroupData->test_name, $userGroupData->test_id);

        // Clean any previous output buffering
        ob_end_clean();

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Output Excel file directly to browser
        $writer->save('php://output');
        exit;
    }

    public function deleteUser($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_users', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function getTestRowData($id = null) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_tests', $id);
        return $this->response->setJSON($record);
    }

    public function getMasterTestRowData($id = null) {
        if ($id === null) {
            return $this->response->setJSON(['error' => 'User ID is required'])->setStatusCode(400);
        }
        $record = $this->adminModel->getById('psy_master_tests', $id);
        return $this->response->setJSON($record);
    }

    public function deleteTest($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_tests', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function deleteMasterTest($id = null) {
        $requestData = $this->request->getJSON(true);

        if (!isset($requestData['id'])) {
            return false;
        }

        $this->adminModel->deleteRecord('psy_master_tests', $requestData['id']);

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    private function generateRandomString($length = 6) {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public function saveGenerateUser($id = null) {
        $requestData = $this->request->getJSON(true);
        $isMaster = isset($requestData['is_master']) ? (int) $requestData['is_master'] : 0;
        $noOfUsers = isset($requestData['no_of_users']) ? (int) $requestData['no_of_users'] : 0;

        if ($noOfUsers <= 0) {
            return $this->response->setJSON(["message" => "Invalid number of users"], 400);
        }

        // Set validity dates
        $validityFrom = date('Y-m-d'); // Current date
        $validityTo = date('Y-m-d', strtotime('+3 months')); // 3 months from today
        // Generate a unique group code
        $groupCode = strtoupper(substr($requestData['user_type'], 0, 3)) . '-' . $requestData['test_id'] . '-' . time();

        // Insert group details into psy_user_groups
        $groupData = [
            'group_code' => $groupCode,
            'description' => 'User group for ' . $requestData['user_type'] . ' - Test ' . $requestData['test_id'] . ' - ' . date('Y-m-d H:i:s'),
            'created_by' => session()->get('user_id'),
        ];

        $groupId = $this->adminModel->saveUserGroup(null, $groupData); // Get the inserted group ID

        if (!$groupId) {
            return $this->response->setJSON(["message" => "Failed to create user group"], 400);
        }

        // Prepare user data
        $dataTemplate = [
            'user_type' => $requestData['user_type'],
            'test_id' => $requestData['test_id'],
            'is_master_test' => $isMaster,
            'company_id' => $requestData['company_id'] ?? null,
            'validity_from' => $validityFrom,
            'validity_to' => $validityTo,
            'group_id' => $groupId, // Link users to the group
            'created_by' => session()->get('user_id'),
        ];

        // Test Code (short form of test name, e.g., "PLD" for "Pilot Leadership Development")
        $prefixCode = $this->adminModel->getUserPrefix($requestData['test_id']);

        $insertedUsers = [];
        for ($i = 0; $i < $noOfUsers; $i++) {
            // Generate User ID Format: {user_id}{test_code}_{randomString(6)}
            $dataTemplate['user_id'] = $prefixCode . "_" . $this->generateRandomString(6);

            // Generate Password: 6-character random string
            $dataTemplate['password'] = $this->generateRandomString(6);

            $result = $this->adminModel->saveGenerateUser(null, $dataTemplate);
            if ($result) {
                $insertedUsers[] = $result;
            }
        }

        if (count($insertedUsers) === $noOfUsers) {
            return $this->response->setJSON(["message" => "All users created successfully"]);
        } else {
            return $this->response->setJSON(["message" => "Some users failed to create"], 400);
        }
    }

    public function questionarreList() {
        return view('admin_pages/questionarre_list');
    }

    public function getQuestionnaireList() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $table = 'psy_questions';
        $indexColumn = 'psy_questions.id';
        $orderBy = 'psy_questions.id DESC';

        // Select required columns
        $columns = "
        psy_questions.id, IFNULL(psy_questions.question_text, psy_questions.question_image) AS question_display,
        psy_test_factor.factor_name, psy_questions.status, psy_questions.is_demo AS isDemo, psy_questions.memory_main_id";

        $joins = [
            ['psy_test_factor', 'psy_test_factor.id = psy_questions.test_factor_id', 'left'],
        ];

        // Where condition for search
        $whereCondition = "";
        if (!empty($search)) {
            $escapedSearch = $search;
            $whereCondition = "(
        LOWER(psy_questions.question_text) LIKE LOWER('%$escapedSearch%') 
        OR LOWER(psy_test_factor.factor_name) LIKE LOWER('%$escapedSearch%') 
    )";
            // Do NOT reset offset here
        }


        // Get total records count
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        // Fetch filtered data
        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search, // If this is not needed, remove it
                "",
                $indexColumn,
                $table,
                $joins,
                "",
                $orderBy,
                $whereCondition, // Ensure this is a string, not an array
                $limit,
                $offset
        );

        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function manageQuestionnaire($questionnaireId = null) {
        return view('admin_pages/manage_questionnaire');
    }

    public function getAllTestFactor() {
        return $this->response->setJSON($this->adminModel->getAllTestFactor());
    }

    public function getLanguage() {
        return $this->response->setJSON($this->adminModel->getLanguage());
    }

    public function loadQuestionnaire($questionId) {
        // Load models
        $question = $this->adminModel->getQuestionById($questionId);
        $options = $this->adminModel->getQuestionOptions($questionId);

        if (!$question) {
            return $this->failNotFound('Question not found');
        }

        // Combine question and options
        $response = [
            'question' => $question,
            'options' => $options
        ];

        return $this->response->setJSON($response);
    }

    public function saveQuestionnaire() {
        $request = service('request');

        $data = [
            'test_factor_id' => $request->getPost('testFactorId'),
            'language_id' => $request->getPost('languageId'),
            'question_type' => $request->getPost('questionType'),
            'question_mark' => $request->getPost('questionMark'),
            'is_demo' => $request->getPost('is_demo') ?? 1,
            'status' => $request->getPost('status') ?? 1,
        ];

        $correctAnswerIndex = $request->getPost('correctAnswer');
        // Handle text-based question
        if (in_array($data['question_type'], ['1', '2'])) {
            $data['question_text'] = $request->getPost('textQuestion');
        }

        // Handle image-based question
        if (in_array($data['question_type'], ['3', '4'])) {
            $imageFile = $this->request->getFile('imageQuestion');

            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $newName = $imageFile->getRandomName();
                $imageFile->move(FCPATH . 'uploads/', $newName);
                $data['question_image'] = 'uploads/' . $newName;
            } else {
                print_r($imageFile->getErrorString());
                exit;
                log_message('error', 'Image upload failed: ' . $imageFile->getErrorString());
            }
        }

        $questionId = $this->adminModel->insertQuestion(null, $data);

        $optionData = [];
        $options = $this->request->getPost('options'); // Get text options
        $files = $this->request->getFiles(); // Get uploaded images

        if ($options) {
            foreach ($options as $index => $option) {
                $optionText = $option['text'] ?? null;
                $optionImagePath = null;
                $optionMark = $option['option_mark'] ?? 0; // Get the mark value
                // Check if an image exists for this option
                if (isset($files['options'][$index]['image']) && $files['options'][$index]['image']->isValid() && !$files['options'][$index]['image']->hasMoved()) {
                    $optionFile = $files['options'][$index]['image'];
                    $optionImageName = $optionFile->getRandomName();
                    $optionFile->move(FCPATH . 'uploads/', $optionImageName);
                    $optionImagePath = 'uploads/' . $optionImageName;
                }

                // Determine if this is the correct answer
                $isCorrect = ($index == $correctAnswerIndex) ? 1 : 0;

                // Store both text & image together
                $optionData[] = [
                    'question_id' => $questionId,
                    'option_text' => $optionText, // Store text if available
                    'option_image' => $optionImagePath, // Store image if available
                    'is_correct' => $isCorrect,
                    'option_mark' => $optionMark
                ];
            }
        }

// Insert options into the database
        if (!empty($optionData)) {
            foreach ($optionData as $option) {
                $this->adminModel->insertQuestionOptions(null, $option);
            }
        }

        return $this->response->setJSON([
                    'mesaage' => $questionId
        ]);
    }

    public function updateQuestionnareStatus() {
        $request = $this->request->getJSON();
        $question_id = $request->question_id;
        $status = $request->status;

        if (!$question_id || !isset($status)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request data']);
        }

        $updateData = ['status' => $status];

        $update = $this->adminModel->updateQuestionStatus($question_id, $updateData);

        if ($update) {
            return $this->response->setJSON(['status' => true, 'message' => 'Question status updated successfully']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Failed to update question status']);
        }
    }

    public function deleteQuestionnaire() {
        // Decode JSON input safely
        $request = json_decode($this->request->getBody(), true);

        // Check if $request is an array and contains 'question_id'
        if (!is_array($request) || !isset($request['question_id'])) {
            return $this->response->setJSON(['error' => 'Invalid request data'])->setStatusCode(400);
        }

        $question_id = (int) $request['question_id']; // Ensure it's an integer
        // Debugging - Ensure we have a valid question_id
        $question = $this->adminModel->getQuestionById($question_id);
        $options = $this->adminModel->getQuestionOptions($question_id);

        // Delete the main question image if exists
        if (!empty($question['question_image'])) {
            $questionImagePath = FCPATH . $question['question_image'];
            if (file_exists($questionImagePath)) {
                unlink($questionImagePath); // Delete the image file
            }
        }

        // Delete option images if they exist
        foreach ($options as $option) {
            if (!empty($option['option_image'])) {
                $optionImagePath = FCPATH . $option['option_image'];
                if (file_exists($optionImagePath)) {
                    unlink($optionImagePath); // Delete the image file
                }
            }
        }

        // Delete from 'psy_questions' where 'id' = $question_id
        $this->adminModel->deleteRecord('psy_questions', $question_id, 'id');
        $this->adminModel->deleteRecord('psy_question_options', $question_id, 'question_id');

        return $this->response->setJSON(['message' => 'Record deleted successfully.']);
    }

    public function getMemoryImageDetails() {
        $memory_main_id = $this->request->getGet('memory_main_id');

        if (!$memory_main_id) {
            return $this->response->setJSON(['status' => false, 'message' => 'Question ID required']);
        }

        $memoryQuestionData = $this->adminModel->getMemoryImageDetails($memory_main_id);
        if ($memoryQuestionData) {

            return $this->response->setJSON([
                        'status' => true,
                        'data' => [
                            'question_image' => $memoryQuestionData['memory_main_image'],
                        ]
            ]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'No data found']);
        }
    }

    public function getQuestionDetails() {
        $question_id = $this->request->getGet('question_id');

        if (!$question_id) {
            return $this->response->setJSON(['status' => false, 'message' => 'Question ID required']);
        }

        $questionData = $this->adminModel->getQuestionById($question_id);

        if ($questionData) {
            // Fetch options related to the question
            $options = $this->adminModel->getQuestionOptions($question_id);

            return $this->response->setJSON([
                        'status' => true,
                        'data' => [
                            'id' => $questionData['id'],
                            'question_type' => $questionData['question_type'],
                            'question_text' => $questionData['question_text'],
                            'question_image' => $questionData['question_image'],
                            'test_factor_id' => $questionData['test_factor_id'],
                            'language_id' => $questionData['language_id'],
                            'question_mark' => $questionData['question_mark'],
                            'status' => $questionData['status'],
                            'options' => $options // Include options
                        ]
            ]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'No data found']);
        }
    }

    public function memoryQuestionnaire() {
        return view('admin_pages/memory_questionnaire');
    }

    public function paragraphQuestionnaire() {
        return view('admin_pages/paragraph_questionnaire');
    }

    public function saveParagraphQuestions() {
        $data = $this->request->getJSON();

        // Prepare and save main paragraph details
        $main_data = [
            'test_factor_id' => $data->testFactorId,
            'language_id' => $data->languageId,
            'paragraph_text' => $data->paragraph,
            'question_mark' => $data->questionMark,
            'is_demo' => $data->isDemo ?? 1,
            'status' => $data->status ?? 1,
            'disapearing_time' => gmdate("H:i:s", intval($data->disapearingTime) * 60),
        ];

        // Calculate per-question mark
        $perQuestionMark = $data->questionMark / count($data->questions);
        $paragraphMainId = $this->adminModel->insertParagraphMainDetails(null, $main_data); // save paragraph info
        // Insert each question
        foreach ($data->questions as $qIndex => $question) {
            $questionData = [
                'question_type' => 1, // 1 = text question
                'question_text' => $question->text,
                'test_factor_id' => $data->testFactorId,
                'language_id' => $data->languageId,
                'question_mark' => $data->questionMark,
                'is_demo' => $data->isDemo ?? 1,
                'status' => $data->status ?? 1,
                'paragraph_question_id' => $paragraphMainId,
            ];

            // Insert question and get its ID
            $questionId = $this->adminModel->insertQuestion(null, $questionData);

            // Insert options
            foreach ($question->options as $optIndex => $option) {
                $isCorrect = ($optIndex == $question->correctOption) ? 1 : 0;
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => $option->text,
                    'is_correct' => $isCorrect,
                    'option_mark' => $isCorrect ? $perQuestionMark : 0
                ];

                $this->adminModel->insertQuestionOptions(null, $optionData);
            }
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Paragraph questions saved successfully'
        ]);
    }

    public function saveMemoryQuestions() {
        $data = $this->request->getPost();
        $files = $this->request->getFiles();
        $request = service('request');

        $memory_main_image = $this->request->getFile('memory_main_image');

        $questions = $data['questions'] ?? [];

        if (empty($questions)) {
            return $this->response->setJSON(['error' => 'No questions provided']);
        }

        $uploadDir = FCPATH . 'uploads/'; // Define upload directory
        // ✅ Ensure the folder exists and is writable
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            log_message('error', 'Failed to create upload directory: ' . $uploadDir);
            return $this->response->setJSON(['error' => 'Upload directory not writable.']);
        }
        $memory_data = [];
        $memory_data = [
            'test_factor_id' => $request->getPost('testFactorId'),
            'language_id' => $request->getPost('languageId'),
            'question_type' => $request->getPost('questionType'),
            'question_mark' => $request->getPost('questionMark'),
            'is_demo' => $request->getPost('is_demo') ?? 1,
            'status' => $request->getPost('status') ?? 1,
            'disapearing_time' => gmdate("H:i:s", intval($request->getPost('disapearingTime')) * 60),
        ];

        if ($memory_main_image->isValid() && !$memory_main_image->hasMoved()) {
            $newFileName = $memory_main_image->getRandomName();
            $memory_main_image->move($uploadDir, $newFileName);
            $memory_data['memory_main_image'] = 'uploads/' . $newFileName;
        } else {
            echo "File Upload Error: " . ($memory_main_image ? $memory_main_image->getErrorString() : "File is NULL");
        }


        $memoryMainQuestionId = $this->adminModel->insertMemoryMainQuestion(null, $memory_data);
        // ✅ Unset the image field after inserting
        unset($memory_data['memory_main_image']);
        unset($memory_data['disapearing_time']);

        $processedQuestions = [];

        foreach ($questions as $qIndex => $questionData) {
            $questionId = $questionData['question_id'] ?? null;
            $correctAnswerIndex = $questionData['correctOption'] ?? null;
            $options = $questionData['options'] ?? [];

            if (!$questionId) {
                continue; // Skip if no question ID
            }

            $questionImagePath = null; // Reset for each question
            // ✅ Fetch image for the question itself
            if (isset($files['questions'][$qIndex]['image']) &&
                    $files['questions'][$qIndex]['image']->isValid() &&
                    !$files['questions'][$qIndex]['image']->hasMoved()
            ) {

                $uploadedFile = $files['questions'][$qIndex]['image'];
                $questionImageName = $uploadedFile->getRandomName();

                if ($uploadedFile->move($uploadDir, $questionImageName)) {
                    log_message('error', "File moved successfully: {$uploadDir}{$questionImageName}");
                    $questionImagePath = 'uploads/' . $questionImageName;
                } else {
                    log_message('error', "File move failed for question {$qIndex}");
                }
            }

            $optionData = [];
            foreach ($options as $optIndex => $optionText) {
                $isCorrect = ($optIndex == $correctAnswerIndex) ? 1 : 0;

                // ✅ Store option without image
                $optionData[] = [
                    'option_text' => $optionText,
                    'is_correct' => $isCorrect
                ];
            }

            $memory_data['question_image'] = $questionImagePath;
            $memory_data['memory_main_id'] = $memoryMainQuestionId;
            $question_id = $this->adminModel->insertQuestion(null, $memory_data);

            if (!empty($optionData)) {
                foreach ($optionData as $option) {
                    $option['question_id'] = $question_id;
                    $this->adminModel->insertQuestionOptions(null, $option);
                }
            }

            // ✅ Add processed question with separate image and options
            $processedQuestions[] = [
                'question_id' => $questionId,
                'question_image' => $questionImagePath,
                'options' => $optionData
            ];
        }

        // ✅ Return all processed questions in required format
        return $this->response->setJSON($processedQuestions);
    }

    public function getReportUsersList() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';

        $table = 'psy_users';
        $indexColumn = 'psy_users.id';
        $orderBy = 'psy_users.id DESC';

        // Select required columns
        $columns = "
    psy_users.*, 
    psy_user_registration.email, 
    psy_user_registration.company_name AS cName, 
    (CONCAT_WS('', psy_user_registration.first_name, ' ', psy_user_registration.last_name)) AS uName, 
    psy_companies.company_name, 
    psy_user_type.section_name, 
    psy_tests.test_name, 
    psy_user_groups.group_code, 
    (SELECT COUNT(DISTINCT q.id) FROM psy_user_answers ua JOIN psy_questions q ON q.id = ua.question_id WHERE ua.user_id = psy_users.id AND q.is_demo = 1) AS questionTaken,

    (SELECT CONCAT('#', LPAD(HEX(0xAAAAAA | (CONV(LEFT(MD5(P1.group_id), 6), 16, 10) & 0x555555)), 6, '0')) 
     FROM psy_users AS P1 WHERE P1.id = psy_users.id) AS color_code,

    (GetTotalQuestionsForUser(psy_users.id)) AS progress,
    getTestReportId(psy_users.id) AS testReportId,
    psy_user_registration.created_on AS reportingDate
";

        $joins = [
            ['psy_user_registration', 'psy_users.id = psy_user_registration.user_id', 'inner'],
            ['psy_companies', 'psy_companies.id = psy_users.company_id', 'left'],
            ['psy_user_type', 'psy_user_type.id = psy_users.user_type', 'left'],
            ['psy_tests', 'psy_tests.id = psy_users.test_id', 'left'],
            ['psy_user_groups', 'psy_user_groups.id = psy_users.group_id', 'left']
        ];

        $whereCondition = "";
        if (!empty($search)) {
            $escapedSearch = $search;
            $whereCondition = "(
                psy_users.user_id LIKE '%$escapedSearch%' 
                OR psy_users.username LIKE '%$escapedSearch%'
                OR psy_user_registration.company_name LIKE '%$escapedSearch%'     
                OR psy_user_registration.first_name LIKE '%$escapedSearch%' 
                OR psy_user_registration.first_name LIKE '%$escapedSearch%'     
                OR psy_user_registration.email LIKE '%$escapedSearch%' 
                OR psy_companies.company_name LIKE '%$escapedSearch%' 
                OR psy_user_type.section_name LIKE '%$escapedSearch%' 
                OR psy_tests.test_name LIKE '%$escapedSearch%' 
                OR psy_user_groups.group_code LIKE '%$escapedSearch%'
            )";
        }

        // Get total records count
        $totalRecords = $this->adminModel->getTotalRecords($table, $joins, $whereCondition);

        // Fetch filtered data
        $results = $this->adminModel->ajax_datatable_basic(
                $columns,
                $search, // ✅ If this is not needed, remove it
                "",
                $indexColumn,
                $table,
                $joins,
                "",
                $orderBy,
                $whereCondition, // ✅ Ensure this is a string, not an array
                $limit,
                $offset
        );

        // Debugging logs
        log_message('debug', "Pagination Request: Limit=$limit, Offset=$offset, Total=$totalRecords");

        return $this->response->setJSON([
                    'data' => $results,
                    'total' => $totalRecords
        ]);
    }

    public function mainReports() {
        return view('admin_pages/main_report');
    }

    public function logout() {
        $session = session();
        $session->destroy(); // Destroy session data

        return redirect()->to('/signin'); // Redirect to login page
    }
}
