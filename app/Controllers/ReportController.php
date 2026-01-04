<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AdminModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Mpdf\Mpdf;

class ReportController extends BaseController {

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

    /**
     * Generate and download MSP PDF Report for a given user
     *
     * @return ResponseInterface
     */
    public function downloadMspPdfReport(): ResponseInterface {
        // Retrieve the user ID from the GET parameters
        $userId = $this->request->getGet('userId');

        // Validate the user ID
        if (empty($userId)) {
            return $this->response
                            ->setJSON(['error' => 'Missing userId'])
                            ->setStatusCode(400);
        }

        // Fetch user and test-related data
        $userData = $this->adminModel->getUsersData($userId);
        $questions = $this->adminModel->getUsersTestQuestion($userId);
        $testName = $this->adminModel->getUserTestName($userId);

        // Check for missing data
        if (empty($userData) || empty($questions) || empty($testName)) {
            return $this->response
                            ->setJSON(['error' => 'Invalid user data'])
                            ->setStatusCode(404);
        }

        // Retrieve and process test factors
        $rawFactorList = $this->adminModel->getFactorAndCount(
                $userData->is_master_test,
                $userData->test_id,
                $userData->id
        );

        $scoredFactors = $this->assignScoreLevels($rawFactorList);

        // Get skill-related data
        $rawSkills = $this->adminModel->getFactorSkills(1);
        $formattedSkills = $this->adminModel->getFormattedSkills('MSP');
        $topSkills = $this->adminModel->getReportTopSkills();
        $managementSkills = $this->adminModel->getManagementSkills($userData->uName);

        // Split authenticity-related and regular skills
        $authenticitySection = [];
        $factorSkills = [];

        foreach ($rawSkills as $section => $skills) {
            $section = trim($section);

            if ($section === 'Authenticity Meter') {
                $authenticitySection[$section] = $skills;
            } else {
                $factorSkills[$section] = $skills;
            }
        }

        // Separate authenticity factor from the rest
        $authenticityFactor = null;
        $factorList = [];

        foreach ($scoredFactors as $factor) {
            $name = trim($factor['factor_name']);

            if ($name === 'Authenticity Meter MSP') {
                $authenticityFactor = $factor;
            } else {
                $factorList[] = $factor;
            }
        }

        // Provide default if authenticity factor not found
        if ($authenticityFactor === null) {
            $authenticityFactor = [
                'factor_id' => 0,
                'factor_name' => 'Authenticity Meter MSP',
                'question_count' => 0,
                'total_score' => 3,
                'pdf_score' => '',
                'score_level' => 'Moderate'
            ];
        }

        // Render the HTML content from the view
        $html = view('admin_pages/pdf_reports/mspPdfReport', [
            'user' => $userData,
            'questions' => $questions,
            'testName' => $testName,
            'factorList' => $factorList,
            'authenticityFactorList' => $authenticityFactor,
            'factorSkills' => $factorSkills,
            'authenticitySection' => $authenticitySection,
            'getFormattedSkills' => $formattedSkills,
            'getReportTopSkills' => $topSkills,
            'managementSkills' => $managementSkills
        ]);

        try {
            // Initialize Mpdf with appropriate configuration
            $mpdf = new Mpdf([
                'tempDir' => WRITEPATH . 'mpdf-temp',
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'default_font' => 'dejavusans',
                'dpi' => 96,
                'img_dpi' => 96,
                'shrink_tables_to_fit' => 1,
                'margin_bottom' => 10,
            ]);

            // Configure Mpdf for better image handling and layout
            $mpdf->showImageErrors = true;
            $mpdf->curlAllowUnsafeSslRequests = true;
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetBasePath(FCPATH);

            // Load HTML content into PDF
            $mpdf->WriteHTML($html);

            // Return the PDF file as a downloadable response
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader(
                                    'Content-Disposition',
                                    'attachment; filename="' . $userData->uName . '-' . $testName->test_name . ' report.pdf"'
                            )
                            ->setBody($mpdf->Output('', 'S'));
        } catch (MpdfException $e) {
            // Handle PDF generation error
            return $this->response
                            ->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()])
                            ->setStatusCode(500);
        }
    }

    public function assignScoreLevels(array $factors) {
        foreach ($factors as &$factor) {
            $score = (float) $factor['total_score'];

            if ($score <= 45) {
                $factor['score'] = 'Low';
            } elseif ($score <= 75) {
                $factor['score'] = 'Moderate';
            } else {
                $factor['score'] = 'High';
            }
        }
        unset($factor);
        return $factors;
    }

    public function processExcelReport() {
        $reportId = $this->request->getGet('testReportId');
        $userId = $this->request->getGet('userId');
        $userData = $this->adminModel->getUsersData($userId);
        if (!$reportId && !$userData) {
            return redirect()->back()->with('error', 'Report ID is missing.');
        }

        $reportId = $this->adminModel->getReportId($userData->is_master_test ?? null, $userData->test_id);

        if (!$reportId) {
            return redirect()->back()->with('error', 'Report ID not found.');
        }

        // Route based on report type
        switch (strtoupper($reportId)) {
            case 1:
                return $this->downloadMSPExcelReport($userId);
            case 2:
                return $this->downloadMSPExcelReport($userId);
            case 7:
                return $this->downloadMFPAExcelReport($userId);
            case 8:
                return $this->downloadMFWPExcelReport($userId);
            case 22:
                return $this->downloadCOGTestExcelReport($userId);
            case 23:
                return $this->downloadCOGTestExcelReport($userId);
            default:
                return redirect()->back()->with('error', 'Unknown report type.');
        }
    }

    public function downloadCOGTestExcelReport($userId) {

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Missing userId'])->setStatusCode(400);
        }

        $reportData = $this->prepareReportData($userId);
        if (!$reportData) {
            return $this->response->setJSON(['error' => 'Invalid user data'])->setStatusCode(404);
        }

        //$answersMap = array_column($reportData['answers'], 'option_mark', 'question_id');
        $answersMap = [];
        foreach ($reportData['answers'] as $answer) {
            $answersMap[$answer['question_id']] = [
                'option_mark' => $answer['option_mark'],
                'optionLabel' => $answer['optionLabel'],
                'option_text' => $answer['option_text']
            ];
        }

        $questionsByFactor = $this->groupQuestionsByFactor($reportData['questions']);

        $spreadsheet = new Spreadsheet();

        $this->generateRawScoreSheet(
                $spreadsheet->getActiveSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                'Cognitive-Raw Score'
        );

        $this->generateReliabilityScoreSheet(
                $spreadsheet->createSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
        );

        $this->outputExcelFile($spreadsheet, $reportData['testName']->test_name . " Report");
    }

    public function downloadMSPExcelReport($userId) {

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Missing userId'])->setStatusCode(400);
        }

        $reportData = $this->prepareReportData($userId);
        if (!$reportData) {
            return $this->response->setJSON(['error' => 'Invalid user data'])->setStatusCode(404);
        }

        //$answersMap = array_column($reportData['answers'], 'option_mark', 'question_id');
        $answersMap = [];
        foreach ($reportData['answers'] as $answer) {
            $answersMap[$answer['question_id']] = [
                'option_mark' => $answer['option_mark'],
                'optionLabel' => $answer['optionLabel'],
                'option_text' => $answer['option_text']
            ];
        }

        $questionsByFactor = $this->groupQuestionsByFactor($reportData['questions']);

        $spreadsheet = new Spreadsheet();

        $this->generateRawScoreSheet(
                $spreadsheet->getActiveSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                'MSP-Raw Score'
        );

        $this->generateMSPReliabilityScoreSheet(
                $spreadsheet->createSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                5
        );

        $this->generatePercentileSheet(
                $spreadsheet->createSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                'MSP-Percentile',
                false
        );

        $this->outputExcelFile($spreadsheet, $reportData['testName']->test_name . " Report");
    }

    private function prepareReportData($userId) {
        $userData = $this->adminModel->getUsersData($userId);

        $reportData = [
            'user' => $userData,
            'questions' => $this->adminModel->getUsersTestQuestion($userId),
            'testName' => $this->adminModel->getUserTestName($userId),
            'factors' => $this->adminModel->getTestFactorList(
                    $userData->is_master_test,
                    $userData->test_id
            ),
            'factor_percentile' => $this->adminModel->getFactorAndCount(
                    $userData->is_master_test,
                    $userData->test_id,
                    $userId
            ),
            'answers' => $this->adminModel->getUserQuestionAnswers($userId)
        ];

        if (empty($reportData['user']) ||
                empty($reportData['questions']) ||
                empty($reportData['testName'])) {
            return null;
        }

        return $reportData;
    }

    private function groupQuestionsByFactor(array $questions): array {
        $grouped = [];
        foreach ($questions as $question) {
            $grouped[$question->test_factor_id][] = $question;
        }
        return $grouped;
    }

    private function generateRawScoreSheet($sheet, $reportData, $answersMap, $questionsByFactor, $title = 'MSP-Raw Score') {
        $sheet->setTitle($title);
        $this->setupBasicSheetStructure($sheet, $reportData['testName']->test_name . ' - Summary');
        $sheet->getRowDimension(4)->setRowHeight(200);
        $this->applyColumnWidths($sheet);
        $this->addUserData($sheet, $reportData['user']);

        $factorTotals = $this->calculateFactorTotals(
                $reportData['factors'],
                $questionsByFactor,
                $answersMap
        );

        $this->generateFactorColumns(
                $sheet,
                $reportData['factors'],
                $questionsByFactor,
                $factorTotals,
                fn($value) => number_format($value, 2)
        );
    }

    private function generateReliabilityScoreSheet($sheet, $reportData, $answersMap, $questionsByFactor) {
        $sheet->setTitle('Test Reliability Score');
        $this->setupBasicSheetStructure($sheet, $reportData['testName']->test_name);
        $this->applyColumnWidths($sheet);
        $this->addUserData($sheet, $reportData['user']);

        $this->addFactorHeaders($sheet, $reportData['factors'], $questionsByFactor);
        $this->addQuestionAnswers(
                $sheet,
                $reportData['factors'],
                $questionsByFactor,
                $answersMap,
                $reportData['user'],
                $userRow ?? 5
        );

        // ---------------------------
        // Add Correct/Wrong/Blank/Total summary
        // ---------------------------
        $row = $sheet->getHighestRow() + 2; // Leave a gap after question data
        $sheet->setCellValue("A{$row}", "Factor Summary");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        // Table headers
        $sheet->setCellValue("A{$row}", "Factor Name");
        $sheet->setCellValue("B{$row}", "Correct");
        $sheet->setCellValue("C{$row}", "Wrong");
        $sheet->setCellValue("D{$row}", "Blank");
        $sheet->setCellValue("E{$row}", "Total");

        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'] // Dark Blue background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:E{$row}")->getAlignment()->setHorizontal('center');
        $row++;

        // Loop through factors
        foreach ($reportData['factors'] as $factor) {
            $factorId = $factor['id'];
            $questions = $questionsByFactor[$factorId] ?? [];

            $correct = $wrong = $blank = $total = 0;

            foreach ($questions as $question) {

                // ✅ Skip demo questions
                if ($question->is_demo == 0) {
                    continue;
                }

                $total++;
                $qId = $question->id;

                if (isset($answersMap[$qId])) {
                    $userAnswer = $answersMap[$qId]['option_mark'];

                    if ($userAnswer == $question->question_mark) {
                        $correct++;
                    } else {
                        $wrong++;
                    }
                } else {
                    $blank++;
                }
            }

            // Add factor data to sheet
            $sheet->setCellValue("A{$row}", $factor['factor_name']);
            $sheet->setCellValue("B{$row}", $correct);
            $sheet->setCellValue("C{$row}", $wrong);
            $sheet->setCellValue("D{$row}", $blank);
            $sheet->setCellValue("E{$row}", $total);

            $dataRowStyle = $this->getDataRowStyle();
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($dataRowStyle);
            $row++;
        }


        // Add borders for clarity
        $sheet->getStyle("A" . ($row - count($reportData['factors']) - 1) . ":E" . ($row - 1))
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
    
    private function generateMSPReliabilityScoreSheet($sheet, $reportData, $answersMap, $questionsByFactor) {
        $sheet->setTitle('Test Reliability Score');
        $this->setupBasicSheetStructure($sheet, $reportData['testName']->test_name);
        $this->applyColumnWidths($sheet);
        $this->addUserData($sheet, $reportData['user']);

        $this->addFactorHeaders($sheet, $reportData['factors'], $questionsByFactor);
        $this->addQuestionAnswers(
                $sheet,
                $reportData['factors'],
                $questionsByFactor,
                $answersMap,
                $reportData['user'],
                $userRow ?? 5
        );
    }

    private function generatePercentileSheet($sheet, $reportData, $answersMap, $questionsByFactor, $setTitle = 'MSP-Percentile', $range_details = null) {
        $sheet->setTitle($setTitle);
        $this->setupBasicSheetStructure($sheet, $reportData['testName']->test_name . ' - Percentage');
        $this->applyColumnWidths($sheet);
        $this->addUserData($sheet, $reportData['user']);

        // Create a map of factor_id to percentile data for easy lookup
        $percentileMap = [];
        foreach ($reportData['factor_percentile'] as $item) {
            $percentileMap[$item['factor_id']] = $item;
        }

        $currentColIndex = 8; // Start from column H
        $factorRow = 4;
        $totalRow = 5;
        $percentileRow = 5; // New row for percentile data

        foreach ($reportData['factors'] as $index => $factor) {
            $factorId = $factor['id'];
            $columnLetter = Coordinate::stringFromColumnIndex($currentColIndex);

            // Skip if there are no questions for this factor
            if (empty($questionsByFactor[$factorId])) {
                continue;
            }

            // Factor name
            $sheet->setCellValue("{$columnLetter}{$factorRow}", $factor['factor_name']);
            $this->applyFactorHeaderStyles($sheet, $columnLetter, $columnLetter, $factorRow, $index);

            // Total score
            $totalValue = $this->calculateFactorTotal($factorId, $questionsByFactor, $answersMap, true);
            $sheet->setCellValue("{$columnLetter}{$totalRow}", round($totalValue, 2));
            $this->applyTotalStyles($sheet, $columnLetter, $columnLetter, $totalRow);

            // Percentile data
            if (isset($percentileMap[$factorId])) {
                $percentileData = $percentileMap[$factorId];
                preg_match('/Band:\s*(.*?),\s*Percentile:\s*(.*)/', $percentileData['pdf_score'], $matches);
                $sheet->setCellValue("{$columnLetter}{$percentileRow}", $matches[2] ?? round($percentileData['total_score'], 2));
                //$this->applyPercentileStyles($sheet, $columnLetter, $columnLetter, $percentileRow);
            }

            $currentColIndex++;
        }
        if ($range_details) {
            $this->rangeDetails($sheet);
        }
    }

    private function rangeDetails($sheet) {
        // Static Percentile Range legend (starting from A8 to E9)
        $sheet->setCellValue('A8', 'Percentile Range');
        $sheet->setCellValue('B8', 'Superior');
        $sheet->setCellValue('C8', 'Above Average');
        $sheet->setCellValue('D8', 'Average');
        $sheet->setCellValue('E8', 'Below Average');

        $sheet->setCellValue('A9', 'All');
        $sheet->setCellValue('B9', '90 and 95');
        $sheet->setCellValue('C9', '75');
        $sheet->setCellValue('D9', '50');
        $sheet->setCellValue('E9', 'Between 5 and 25');

// Apply styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'] // dark blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ],
            ],
        ];

        $labelStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050'] // green for "Percentile Range"
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ],
            ],
        ];

// Apply styles
        $sheet->getStyle('A8')->applyFromArray($labelStyle);
        $sheet->getStyle('B8:E8')->applyFromArray($headerStyle);
        $sheet->getStyle('A9:E9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A9:E9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function applyPercentileStyles($sheet, $start, $end, $row) {
        $sheet->getStyle("{$start}{$row}:{$end}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6EFE9'] // Light green background
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function generateFactorColumns($sheet, $factors, $questionsByFactor, $totals, $formatCallback) {
        $currentColIndex = 8; // Start from column H
        $factorRow = 4;
        $totalRow = 5;

        foreach ($factors as $index => $factor) {
            $factorId = $factor['id'];
            $questions = $questionsByFactor[$factorId] ?? [];

            // Skip if there are no questions
            if (empty($questions)) {
                continue;
            }

            // Render one column per factor
            $this->addFactorSection(
                    $sheet,
                    $currentColIndex,
                    $factor,
                    $index,
                    $questions,
                    $factorRow,
                    $totalRow,
                    $totals[$factorId] ?? 0,
                    $formatCallback
            );

            // Increment by 1 column only after each factor
            $currentColIndex++;
        }
    }

    private function addFactorSection($sheet, $colIndex, $factor, $factorIndex, $questions, $factorRow, $totalRow, $totalValue, $formatCallback) {
        $columnLetter = Coordinate::stringFromColumnIndex($colIndex);

        // Factor name
        $sheet->setCellValue("{$columnLetter}{$factorRow}", $factor['factor_name']);
        $this->applyFactorHeaderStyles($sheet, $columnLetter, $columnLetter, $factorRow, $factorIndex);

        // Total score (or whatever value) under it
        $sheet->setCellValue("{$columnLetter}{$totalRow}", $formatCallback($totalValue));
        $this->applyTotalStyles($sheet, $columnLetter, $columnLetter, $totalRow);
    }

    private function applyFactorHeaderStyles($sheet, $start, $end, $row, $factorIndex) {
        $style = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->generateColorByIndex($factorIndex)]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'textRotation' => 90
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle("{$start}{$row}:{$end}{$row}")->applyFromArray($style);
    }

    private function applyTotalStyles($sheet, $start, $end, $row) {
        $sheet->getStyle("{$start}{$row}:{$end}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAF7']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function calculateFactorTotal($factorId, $questionsByFactor, $answersMap, $calculatePercentage = false) {
        $questions = $questionsByFactor[$factorId] ?? [];
        $total = 0;
        $maxTotal = 0;

        foreach ($questions as $question) {
            if (isset($answersMap[$question->id])) {
                $total += $answersMap[$question->id]['option_mark'];
            }
            $maxTotal += 5; // Assume each question max 5
        }

        if ($calculatePercentage && $maxTotal > 0) {
            return ($total / $maxTotal) * 100;
        }

        return $total;
    }

    private function calculateFactorTotals($factors, $questionsByFactor, $answersMap, $calculatePercentage = false) {
        $totals = [];

        foreach ($factors as $factor) {
            $factorId = $factor['id'];
            $questions = $questionsByFactor[$factorId] ?? [];

            $total = 0;
            $maxTotal = 0;

            foreach ($questions as $question) {
                if (isset($answersMap[$question->id])) {
                    // 👇 Only add the `option_mark`
                    $total += $answersMap[$question->id]['option_mark'];
                }
                $maxTotal += 5; // Assume each question max 5
            }

            if ($calculatePercentage && $maxTotal > 0) {
                $totals[$factorId] = round(($total / $maxTotal) * 100); // Round to nearest integer
            } else {
                $totals[$factorId] = $total;
            }
        }

        return $totals;
    }

    private function setupBasicSheetStructure(&$sheet, $testName) {
        // Title Row
        $sheet->setCellValue('A1', $testName);
        $sheet->mergeCells('A1:AI1');

        // Date Row
        $sheet->setCellValue('A2', 'Date: ' . date('d-m-Y'));
        $sheet->mergeCells('A2:AI2');

        // Title Styling
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle('A1:A2')->applyFromArray($titleStyle);

        // Headers (User Information)
        $headers = ['User ID', 'User Name', 'Age', 'Gender', 'Company Name', 'Work Experience', 'Test Date'];
        $sheet->fromArray($headers, null, 'A4');

        // Header Styling (Dark Blue with White text)
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'] // Dark Blue background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Example User Row Styling (Light Blue Background)
        // Assuming user data will come in row 5
        $sheet->getStyle('A5:G5')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAF7'] // Light Blue background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Optional: Set column widths nicely
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function applyColumnWidths(&$sheet) {
        $sheet->getDefaultColumnDimension()->setWidth(5);
        $columnWidths = ['A' => 15, 'B' => 20, 'C' => 12, 'D' => 10, 'E' => 20, 'F' => 15, 'G' => 15];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    private function addUserData(&$sheet, $userData) {
        // Fill user data
        $userInfo = [
            $userData->user_id,
            $userData->uName,
            $userData->Age . ' Years',
            $userData->gender,
            $userData->company_name,
            $userData->experience,
            $userData->created_on
        ];

        $sheet->fromArray($userInfo, null, 'A5');

        // Apply style to header row (Row 4)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17375D']], // Dark Blue
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

        // Apply style to user info row (Row 5)
        $userRowStyle = [
            'font' => ['bold' => false],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAF7']], // Light Blue
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($userRowStyle);
    }

    private function addFactorHeaders(&$sheet, $factors, $questionsByFactor) {
        $startColIndex = 8; // Starting from column H

        foreach ($factors as $index => $factor) {
            if (empty($questionsByFactor[$factor['id']]))
                continue;

            $questionCount = count($questionsByFactor[$factor['id']]);
            $startColLetter = Coordinate::stringFromColumnIndex($startColIndex);
            $endColLetter = Coordinate::stringFromColumnIndex($startColIndex + $questionCount - 1);

            // Merge cells for factor name
            $sheet->setCellValue("{$startColLetter}3", $factor['factor_name']);
            $sheet->mergeCells("{$startColLetter}3:{$endColLetter}3");

            // Generate color based on index
            $colorHex = $this->generateColorByIndex($index); // returns like "548235"
            // Apply color to merged header
            $this->applyCategoryStyle($sheet, "{$startColLetter}3:{$endColLetter}3", $colorHex);

            $startColIndex += $questionCount;
        }
    }

    private function addQuestionAnswers(&$sheet, $factors, $questionsByFactor, $answersMap, $userData, $userRow = 5) {
        $currentColIndex = 8;

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

        foreach ($factors as $index => $factor) {
            $factorId = $factor['id'];
            if (empty($questionsByFactor[$factorId]))
                continue;

            $color = $this->generateColorByIndex($index);
            $questionStyle = $this->getQuestionCellStyle($color);
            $answerStyle = $this->getAnswerCellStyle($color);

            foreach ($questionsByFactor[$factorId] as $question) {
                $colLetter = Coordinate::stringFromColumnIndex($currentColIndex);
                $sheet->getColumnDimension($colLetter)->setWidth(8);

                // Question header
                $sheet->setCellValue($colLetter . '4', 'Q' . $question->id);
                $sheet->getStyle($colLetter . '4')->applyFromArray($questionStyle);

                // Fetch answer details safely
                $answerData = $answersMap[$question->id] ?? null;

                // Row 5 - option_mark
                $mark = $answerData['option_mark'] ?? '-';
                $sheet->setCellValue($colLetter . $userRow, $mark);
                $sheet->getStyle($colLetter . $userRow)->applyFromArray($answerStyle);

                // Row 6 - optionLabel
                $label = $answerData['optionLabel'] ?? '-';
                $sheet->setCellValue($colLetter . ($userRow + 1), $label);
                $sheet->getStyle($colLetter . ($userRow + 1))->applyFromArray($answerStyle);

                // Row 7 - option_text
                $optionText = $answerData['option_text'] ?? '-';
                $sheet->setCellValue($colLetter . ($userRow + 2), $optionText);
                $sheet->getStyle($colLetter . ($userRow + 2))->applyFromArray($answerStyle);

                $currentColIndex++;
            }
        }

        // Apply consistent styling to data rows
        $dataRowStyle = $this->getDataRowStyle();
        $sheet->getStyle('A5:G7')->applyFromArray($dataRowStyle); // Rows 5 to 7
        $sheet->getStyle('H5:' . Coordinate::stringFromColumnIndex($currentColIndex - 1) . '7')->applyFromArray($dataRowStyle);
    }

    private function getQuestionCellStyle($color) {
        return [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];
    }

    private function getAnswerCellStyle($color) {
        return [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];
    }

    private function getDataRowStyle() {
        return [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ADD8E6']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
    }

    private function outputExcelFile(&$spreadsheet, $fileName) {
        // Ensure the filename ends with .xlsx
        if (strtolower(substr($fileName, -5)) !== '.xlsx') {
            $fileName .= '.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"" . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function applyCategoryStyle($sheet, $range, $fillColor) {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // White font
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $fillColor],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    private function generateColorByIndex($index) {
        // Use HSL to create visually distinct colors
        $hue = ($index * 137) % 360; // Golden angle approximation for color spacing
        $saturation = 70;
        $lightness = 50;

        return $this->hslToHex($hue, $saturation, $lightness);
    }

    private function hslToHex($h, $s, $l) {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } elseif ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } elseif ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } elseif ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } elseif ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }

        $r = dechex(round(($r + $m) * 255));
        $g = dechex(round(($g + $m) * 255));
        $b = dechex(round(($b + $m) * 255));

        return strtoupper(str_pad($r, 2, '0', STR_PAD_LEFT)
                . str_pad($g, 2, '0', STR_PAD_LEFT)
                . str_pad($b, 2, '0', STR_PAD_LEFT));
    }

    public function downloadCOGPDFReport(): ResponseInterface {
        // Retrieve the user ID from the GET parameters
        $userId = $this->request->getGet('userId');

        // Validate the user ID
        if (empty($userId)) {
            return $this->response
                            ->setJSON(['error' => 'Missing userId'])
                            ->setStatusCode(400);
        }

        // Fetch user and test-related data
        $userData = $this->adminModel->getUsersData($userId);
        $questions = $this->adminModel->getUsersTestQuestion($userId);
        $testName = $this->adminModel->getUserTestName($userId);

        // Check for missing data
        if (empty($userData) || empty($questions) || empty($testName)) {
            return $this->response
                            ->setJSON(['error' => 'Invalid user data'])
                            ->setStatusCode(404);
        }

        // Retrieve and process test factors
        $rawFactorList = $this->adminModel->getFactorAndCountForCOG(
                $userData->is_master_test,
                $userData->test_id,
                $userData->id
        );

        // Get skill-related data
        $rawSkills = $this->adminModel->getFactorSkills(22);
        $topSkills = $this->adminModel->getReportTopSkills();
        $managementSkills = $this->adminModel->getManagementSkills($userData->uName);
        // Extract only factor_ids from $rawFactorList
        $factorIds = array_column($rawFactorList, 'factor_id');
        $getTestPerformanceSummary = $this->adminModel->getTestPerformanceSummary($userId, $factorIds);

        // Split authenticity-related and regular skills
        $authenticitySection = [];
        $factorSkills = [];

        foreach ($rawSkills as $section => $skills) {
            $section = trim($section);
            $factorSkills[$section] = $skills;
        }

        // Separate authenticity factor from the rest
        $authenticityFactor = null;
        $factorList = [];

        foreach ($rawFactorList as $factor) {
            $name = trim($factor['factor_name']);
            $factorList[] = $factor;
        }

        // Render the HTML content from the view
        $html = view('admin_pages/pdf_reports/cog_test', [
            'user' => $userData,
            'questions' => $questions,
            'testName' => $testName,
            'factorList' => $factorList,
            'authenticityFactorList' => $authenticityFactor,
            'factorSkills' => $factorSkills,
            'authenticitySection' => $authenticitySection,
            'getReportTopSkills' => $topSkills,
            'managementSkills' => $managementSkills,
            'testSummary' => $getTestPerformanceSummary
        ]);

        try {
            // Initialize Mpdf with appropriate configuration
            $mpdf = new Mpdf([
                'tempDir' => WRITEPATH . 'mpdf-temp',
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'default_font' => 'dejavusans',
                'dpi' => 96,
                'img_dpi' => 96,
                'shrink_tables_to_fit' => 1,
                'margin_bottom' => 10,
            ]);

            // Configure Mpdf for better image handling and layout
            $mpdf->showImageErrors = true;
            $mpdf->curlAllowUnsafeSslRequests = true;
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetBasePath(FCPATH);

            // Load HTML content into PDF
            $mpdf->WriteHTML($html);

            // Return the PDF file as a downloadable response
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader(
                                    'Content-Disposition',
                                    'attachment; filename="' . $userData->uName . '-' . $testName->test_name . ' report.pdf"'
                            )
                            ->setBody($mpdf->Output('', 'S'));
        } catch (MpdfException $e) {
            // Handle PDF generation error
            return $this->response
                            ->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()])
                            ->setStatusCode(500);
        }
    }

    public function downloadCOGCRPDFReport(): ResponseInterface {
        // Retrieve the user ID from the GET parameters
        $userId = $this->request->getGet('userId');

        // Validate the user ID
        if (empty($userId)) {
            return $this->response
                            ->setJSON(['error' => 'Missing userId'])
                            ->setStatusCode(400);
        }

        // Fetch user and test-related data
        $userData = $this->adminModel->getUsersData($userId);
        $questions = $this->adminModel->getUsersTestQuestion($userId);
        $testName = $this->adminModel->getUserTestName($userId);

        // Check for missing data
        if (empty($userData) || empty($questions) || empty($testName)) {
            return $this->response
                            ->setJSON(['error' => 'Invalid user data'])
                            ->setStatusCode(404);
        }

        // Retrieve and process test factors
        $rawFactorList = $this->adminModel->getFactorAndCountForCOG(
                $userData->is_master_test,
                $userData->test_id,
                $userData->id
        );

        // Get skill-related data
        $rawSkills = $this->adminModel->getFactorSkills(23);
        $formattedSkills = $this->adminModel->getFormattedSkills('MSP');
        $topSkills = $this->adminModel->getReportTopSkills();
        $managementSkills = $this->adminModel->getManagementSkills($userData->uName);

        // Split authenticity-related and regular skills
        $authenticitySection = [];
        $factorSkills = [];

        foreach ($rawSkills as $section => $skills) {
            $section = trim($section);
            $factorSkills[$section] = $skills;
        }

        // Separate authenticity factor from the rest
        $authenticityFactor = null;
        $factorList = [];

        foreach ($rawFactorList as $factor) {
            $name = trim($factor['factor_name']);
            $factorList[] = $factor;
        }

        // Render the HTML content from the view
        $html = view('admin_pages/pdf_reports/cog_cr_test', [
            'user' => $userData,
            'questions' => $questions,
            'testName' => $testName,
            'factorList' => $factorList,
            'authenticityFactorList' => $authenticityFactor,
            'factorSkills' => $factorSkills,
            'authenticitySection' => $authenticitySection,
            'getFormattedSkills' => $formattedSkills,
            'getReportTopSkills' => $topSkills,
            'managementSkills' => $managementSkills
        ]);

        try {
            // Initialize Mpdf with appropriate configuration
            $mpdf = new Mpdf([
                'tempDir' => WRITEPATH . 'mpdf-temp',
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'default_font' => 'dejavusans',
                'dpi' => 96,
                'img_dpi' => 96,
                'shrink_tables_to_fit' => 1,
                'margin_bottom' => 10,
            ]);

            // Configure Mpdf for better image handling and layout
            $mpdf->showImageErrors = true;
            $mpdf->curlAllowUnsafeSslRequests = true;
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetBasePath(FCPATH);

            // Load HTML content into PDF
            $mpdf->WriteHTML($html);

            // Return the PDF file as a downloadable response
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader(
                                    'Content-Disposition',
                                    'attachment; filename="' . $userData->uName . '-' . $testName->test_name . ' report.pdf"'
                            )
                            ->setBody($mpdf->Output('', 'S'));
        } catch (MpdfException $e) {
            // Handle PDF generation error
            return $this->response
                            ->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()])
                            ->setStatusCode(500);
        }
    }

    public function downloadMFPDFReport(): ResponseInterface {
        // Retrieve the user ID from the GET parameters
        $userId = $this->request->getGet('userId');

        // Validate the user ID
        if (empty($userId)) {
            return $this->response
                            ->setJSON(['error' => 'Missing userId'])
                            ->setStatusCode(400);
        }

        // Fetch user and test-related data
        $userData = $this->adminModel->getUsersData($userId);
        $questions = $this->adminModel->getUsersTestQuestion($userId);
        $testName = $this->adminModel->getUserTestName($userId);

        // Check for missing data
        if (empty($userData) || empty($questions) || empty($testName)) {
            return $this->response
                            ->setJSON(['error' => 'Invalid user data'])
                            ->setStatusCode(404);
        }

        // Retrieve and process test factors
        $rawFactorList = $this->adminModel->getFactorAndCountForMF(
                $userData->is_master_test,
                $userData->test_id,
                $userData->id
        );

        $scoredFactors = $this->assignScoreLevels($rawFactorList);

        // Get skill-related data
        $rawSkills = $this->adminModel->getFactorSkills(7);
        $formattedSkills = $this->adminModel->getFormattedSkills('MSP');
        $topSkills = $this->adminModel->getReportTopSkills();
        $managementSkills = $this->adminModel->getManagementSkills($userData->uName);

        // Split authenticity-related and regular skills
        $authenticitySection = [];
        $factorSkills = [];

        foreach ($rawSkills as $section => $skills) {
            $section = trim($section);
            $factorSkills[$section] = $skills;
        }

        $factorList = [];

        foreach ($scoredFactors as $factor) {
            $name = trim($factor['factor_name']);
            $factorList[] = $factor;
        }
        print_r($factorList);exit;
        $headerImagePath = FCPATH . 'img/pdf_images/header.png';
        $footerImagePath = FCPATH . 'img/pdf_images/footer.png';

        $header = '
    <htmlpageheader name="mainHeader">
        <div style="text-align: center; width: 100%;">
            <img src="file://' . str_replace('\\', '/', $headerImagePath) . '" style="width: 100%; max-height: 30mm;" />
        </div>
    </htmlpageheader>';

        $footer = '
    <htmlpagefooter name="mainFooter">
        <div style="text-align: center; width: 100%;">
            <img src="file://' . str_replace('\\', '/', $footerImagePath) . '" style="width: 100%; max-height: 20mm;" />
        </div>
    </htmlpagefooter>';
        try {
            // 2. Initialize mPDF with correct configuration
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 35, // Must be larger than header height
                'margin_bottom' => 25, // Must be larger than footer height
                'margin_header' => 5, // Space between header and content
                'margin_footer' => 5, // Space between footer and content
                'tempDir' => WRITEPATH . 'mpdf-temp',
                'default_font' => 'dejavusans',
                'autoMarginPadding' => true  // Replaces the deprecated SetAutoTopMargin
            ]);

            // 3. Set header and footer
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);

            // 4. Write content
            // Render the HTML content from the view
            $html = view('admin_pages/pdf_reports/MF-PA-Report', [
                'user' => $userData,
                'questions' => $questions,
                'testName' => $testName,
                'factorList' => $factorList,
            ]);
            $mpdf->WriteHTML($html);

            // Return the PDF file as a downloadable response
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader(
                                    'Content-Disposition',
                                    'attachment; filename="' . $userData->uName . '-' . $testName->test_name . ' report.pdf"'
                            )
                            ->setBody($mpdf->Output('', 'S'));
        } catch (MpdfException $e) {
            // Handle PDF generation error
            return $this->response
                            ->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()])
                            ->setStatusCode(500);
        }
    }

    public function downloadMFPAExcelReport($userId) {

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Missing userId'])->setStatusCode(400);
        }

        $reportData = $this->prepareReportData($userId);
        if (!$reportData) {
            return $this->response->setJSON(['error' => 'Invalid user data'])->setStatusCode(404);
        }

        //$answersMap = array_column($reportData['answers'], 'option_mark', 'question_id');
        $answersMap = [];
        foreach ($reportData['answers'] as $answer) {
            $answersMap[$answer['question_id']] = [
                'option_mark' => $answer['option_mark'],
                'optionLabel' => $answer['optionLabel'],
                'option_text' => $answer['option_text']
            ];
        }

        $questionsByFactor = $this->groupQuestionsByFactor($reportData['questions']);

        $spreadsheet = new Spreadsheet();

        $this->generateRawScoreSheet(
                $spreadsheet->getActiveSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                'MF-Raw Score'
        );

        $this->generateReliabilityScoreSheet(
                $spreadsheet->createSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                5
        );

        $this->generatePercentileSheet(
                $spreadsheet->createSheet(),
                $reportData,
                $answersMap,
                $questionsByFactor,
                'Mind-Frame-Percentile',
                false
        );

        $this->outputExcelFile($spreadsheet, $reportData['testName']->test_name . " Report");
    }
}
