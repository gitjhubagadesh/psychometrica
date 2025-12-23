<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('signin', 'Signin::index');
$routes->get('/', 'QuizLoginController::index');

$routes->post('signin/loginProcess', 'Signin::loginProcess'); // Login processing
$routes->get('dashboard', 'PsyMainController::dashboard'); // Redirect after login
$routes->get('logout', 'Signin::logout'); // Logout route

// Admin Module
$routes->get('admin', 'PsyMainController::index'); 
$routes->get('dashboardData', 'PsyMainController::dashboardData'); 
$routes->get('getAttemptStats', 'PsyMainController::getAttemptStats'); 
$routes->get('admin/adminUser', 'PsyMainController::adminUser'); 
$routes->get('admin/getAdminList', 'PsyMainController::getAdminList');
$routes->get('admin/manageAdminUser/(:num)', 'PsyMainController::manageAdminUser/$1');
$routes->get('admin/manageAdminUser', 'PsyMainController::manageAdminUser'); // Route without ID
$routes->get('admin/getAdminUser/(:num)', 'PsyMainController::getAdminUser/$1');
$routes->post('admin/updateAdminUser', 'PsyMainController::updateAdminUser');
$routes->post('admin/deleteAdminUser', 'PsyMainController::deleteAdminUser');



// Company Module
$routes->get('admin/manageCompanies', 'PsyMainController::companyList');
$routes->get('admin/getCompanyList', 'PsyMainController::getCompanyList');
$routes->get('admin/manageCompany', 'PsyMainController::manageCompany');  // Without companyId
$routes->get('admin/manageCompany/(:num)', 'PsyMainController::manageCompany/$1');  // With companyId
$routes->get('admin/getCompanyData/(:num)', 'PsyMainController::getCompanyData/$1');
$routes->post('admin/saveCompany/(:num)', 'PsyMainController::saveCompany/$1'); // Update company
$routes->post('admin/saveCompany', 'PsyMainController::saveCompany'); // Add new company
$routes->post('admin/deleteCompany', 'PsyMainController::deleteCompany');

// Test Module
$routes->get('admin/manageTests', 'PsyMainController::manageTests');
$routes->get('admin/getTestList', 'PsyMainController::getTestList');
$routes->post('admin/addTestName/(:num)', 'PsyMainController::addTestName/$1'); // Update company
$routes->post('admin/addTestName', 'PsyMainController::addTestName'); 
$routes->get('admin/getTestData/(:num)', 'PsyMainController::getTestData/$1');
$routes->post('admin/deleteTestName', 'PsyMainController::deleteTestName');

//manageTestFactor
$routes->get('admin/manageTestFactor', 'PsyMainController::manageTestFactor');
$routes->get('admin/getTestFactorList', 'PsyMainController::getTestFactorList');
$routes->post('admin/addTestFactor/(:num)', 'PsyMainController::addTestFactor/$1'); // Update company
$routes->post('admin/addTestFactor', 'PsyMainController::addTestFactor');
$routes->get('admin/getFactorData/(:num)', 'PsyMainController::getFactorData/$1');
$routes->post('admin/deleteTestFacor', 'PsyMainController::deleteTestFacor');

//generateUser
$routes->get('admin/generateUser', 'PsyMainController::generateUser');
$routes->get('admin/getAllTest', 'PsyMainController::getAllTest');
$routes->get('admin/getAllCompany', 'PsyMainController::getAllCompany');
$routes->get('admin/getUserSections', 'PsyMainController::getUserSections');
$routes->post('admin/saveGenerateUser/(:num)', 'PsyMainController::saveGenerateUser/$1'); // Update company
$routes->post('admin/saveGenerateUser', 'PsyMainController::saveGenerateUser');
$routes->get('admin/getUsersList', 'PsyMainController::getUsersList');
$routes->post('admin/deleteUser', 'PsyMainController::deleteUser');
$routes->get('admin/downloadUserGroup', 'PsyMainController::downloadUserGroup');
$routes->post('admin/updateUserValidity', 'PsyMainController::updateUserValidity');




//createTest
$routes->get('admin/createTest', 'PsyMainController::createTest');
$routes->post('admin/saveTest/(:num)', 'PsyMainController::saveTest/$1'); // Update company
$routes->post('admin/saveTest', 'PsyMainController::saveTest');
$routes->get('admin/testList', 'PsyMainController::testList');
$routes->get('admin/getTestListData', 'PsyMainController::getTestListData');
$routes->get('admin/getTestRowData/(:num)', 'PsyMainController::getTestRowData/$1');
$routes->post('admin/deleteTest', 'PsyMainController::deleteTest');

// QuestionarreController
$routes->get('admin/questionarreList', 'PsyMainController::questionarreList');
$routes->get('admin/getQuestionnaireList', 'PsyMainController::getQuestionnaireList');
$routes->get('admin/manageQuestionnaire/(:num)', 'PsyMainController::manageQuestionnaire/$1');
$routes->get('admin/manageQuestionnaire', 'PsyMainController::manageQuestionnaire');
$routes->get('admin/getAllTestFactor', 'PsyMainController::getAllTestFactor');
$routes->get('admin/getLanguage', 'PsyMainController::getLanguage');
$routes->post('admin/saveQuestionnaire', 'PsyMainController::saveQuestionnaire');
$routes->get('admin/loadQuestionnaire/(:num)', 'PsyMainController::loadQuestionnaire/$1');
$routes->post('admin/updateQuestionnareStatus', 'PsyMainController::updateQuestionnareStatus');
$routes->get('admin/getQuestionDetails', 'PsyMainController::getQuestionDetails');
$routes->post('admin/deleteQuestionnaire', 'PsyMainController::deleteQuestionnaire');


//Memory
$routes->get('admin/memoryQuestionnaire', 'PsyMainController::memoryQuestionnaire');
$routes->post('admin/saveMemoryQuestions', 'PsyMainController::saveMemoryQuestions');
$routes->get('admin/getMemoryImageDetails', 'PsyMainController::getMemoryImageDetails');

// Paragraph
$routes->get('admin/paragraphQuestionnaire', 'PsyMainController::paragraphQuestionnaire');
$routes->post('admin/saveParagraphQuestions', 'PsyMainController::saveParagraphQuestions');

//Master Test
$routes->get('admin/createMasterTest', 'PsyMainController::createMasterTest');
$routes->get('admin/getMasterTestListData', 'PsyMainController::getMasterTestListData');
$routes->get('admin/getMasterTestRowData/(:num)', 'PsyMainController::getMasterTestRowData/$1');
$routes->post('admin/saveMasterTest/(:num)', 'PsyMainController::saveMasterTest/$1'); // Update company
$routes->post('admin/saveMasterTest', 'PsyMainController::saveMasterTest');
$routes->post('admin/deleteMasterTest', 'PsyMainController::deleteMasterTest');

//Report
$routes->get('report', 'ReportController::index'); 
$routes->get('admin/mainReports', 'PsyMainController::mainReports');
$routes->get('admin/getReportUsersList', 'PsyMainController::getReportUsersList');
$routes->get('admin/downloadExcelReport', 'PsyMainController::downloadExcelReport');
$routes->get('report/downloadMSPExcelReport', 'ReportController::downloadMSPExcelReport');
$routes->get('report/downloadMSPPDFReport', 'ReportController::downloadMSPPDFReport');
$routes->get('report/processExcelReport', 'ReportController::processExcelReport');
$routes->get('report/downloadMFPDFReport', 'ReportController::downloadMFPDFReport');
$routes->get('report/downloadCOGPDFReport', 'ReportController::downloadCOGPDFReport');
$routes->get('report/downloadCOGCRPDFReport', 'ReportController::downloadCOGCRPDFReport');


//Quiz Controller
$routes->get('test-signin', 'QuizLoginController::index');
$routes->post('test-login', 'QuizLoginController::login');
$routes->get('test-registration', 'QuizRegisterController::quizRregistration');
$routes->post('save-registration', 'QuizRegisterController::saveRregistration');
$routes->get('test', 'QuizController::index'); 
$routes->get('cognitive', 'QuizController::cognitive');
$routes->get('questionnaire', 'QuizController::questionnaire');

$routes->get('quiz/questionDetails', 'QuizController::questionDetails'); 
$routes->post('quiz/saveAnswers', 'QuizController::saveAnswers');
$routes->get('contactError', 'QuizController::contactError'); 
$routes->get('quiz/logout', 'QuizController::logout');
$routes->get('quizFinish', 'QuizController::quizFinish');
$routes->get('cogQuizFinish', 'QuizController::cogQuizFinish');
$routes->get('logoutTime', 'QuizController::logoutTime');

$routes->post('quiz/saveElapsedTime', 'QuizController::saveElapsedTime');
$routes->get('quiz/getElapsedTime', 'QuizController::getElapsedTime');



$routes->post('api/saveProgress', 'ProgressController::saveProgress');
$routes->get('api/getProgress', 'ProgressController::getProgress');







