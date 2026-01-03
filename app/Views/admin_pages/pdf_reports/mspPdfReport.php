<?php

function convertTextByGender($text, $gender) {
    $url = "http://139.59.89.40:8000/convert";

    $payload = json_encode([
        "text" => $text,
        "gender" => $gender
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Content-Length: " . strlen($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $result = "Error: " . curl_error($ch);
    } else {
        $json = json_decode($response, true);
        $result = $json['converted'] ?? "Conversion failed";
    }

    curl_close($ch);
    return $result;
}

function normalizeExperienceLevel($experienceText) {
    return strtolower(trim($experienceText)) === 'more than 10 years' ? 1 : 0;
}

function formatTimeTaken($timeStr) {
    if (empty($timeStr))
        return 'N/A';

    list($hours, $minutes, $seconds) = explode(':', $timeStr);
    return sprintf(
            "%d Hour%s %d Minute%s %d Second%s",
            (int) $hours, $hours == 1 ? '' : 's',
            (int) $minutes, $minutes == 1 ? '' : 's',
            (int) $seconds, $seconds == 1 ? '' : 's'
    );
}

function generateVerticalLabelImage($text, $filename) {
    $fontSize = 12;
    $width = 120;
    $height = 100;
    $fontFile = FCPATH . 'fonts/DejaVuSans.ttf'; // Ensure this font file exists

    $image = imagecreatetruecolor($width, $height);
    imagesavealpha($image, true);
    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $transparent);

    $black = imagecolorallocate($image, 0, 0, 0);

// Use the wrapping function
    $wrappedLines = wrapText($text, $fontSize, $fontFile, $width - 10);

    $lineHeight = 18;
    $y = 20;

    foreach ($wrappedLines as $line) {
        imagettftext($image, $fontSize, 0, 5, $y, $black, $fontFile, $line);
        $y += $lineHeight;
    }

    $rotated = imagerotate($image, 90, $transparent);
    imagesavealpha($rotated, true);

    $outputPath = WRITEPATH . $filename;
    imagepng($rotated, $outputPath);

    imagedestroy($image);
    imagedestroy($rotated);

    return $outputPath;
}

function wrapText($text, $fontSize, $fontFile, $maxWidth) {
    $words = explode(' ', $text);
    $lines = [];
    $currentLine = '';

    foreach ($words as $word) {
        $testLine = $currentLine ? $currentLine . ' ' . $word : $word;
        $bbox = imagettfbbox($fontSize, 0, $fontFile, $testLine);
        $lineWidth = abs($bbox[2] - $bbox[0]);

        if ($lineWidth <= $maxWidth) {
            $currentLine = $testLine;
        } else {
            if ($currentLine !== '') {
                $lines[] = $currentLine;
            }
            $currentLine = $word;
        }
    }

    if ($currentLine !== '') {
        $lines[] = $currentLine;
    }

    return $lines;
}

function normalizeSkillKey($key) {
    $key = str_ireplace("MSP", "", $key);
    $key = str_ireplace("skills", "Skills", $key);
    $key = str_ireplace("skill-", "Skill", $key);
    $key = str_ireplace("managment", "Management", $key);
    $key = str_ireplace("decision making", "Decision Management", $key);
    return trim($key);
}

// Initialize variables
$genderKey = strtolower($user->gender);
$userExpLevel = $user->experience > 10 ? 1 : 0;
$timeFormatted = formatTimeTaken($user->total_time_taken ?? '');
$factorScore = [];
$mappedResults = [];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Managerial Skills Assessment Report</title>
        <style>
            @page {
                margin: 0;
                padding: 0;
                header: myheader;
                footer: myfooter;
            }

            @page :first {
                header: none;
                footer: none;
                margin-top: 0;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: 'Times New Roman', serif, sans-serif;
                font-size: 0.9rem;
            }

            /* Header styling */
            htmlpageheader[name=myheader] {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                margin: 0;
                padding: 0;
            }

            htmlpageheader[name=myheader] div {
                background-image: url('<?= base_url('img/pdf_images/header.png') ?>');
                background-repeat: no-repeat;
                background-size: 100% 100%; /* Stretch to full width */
                background-position: top left;
                height: 50px;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            /* Footer styling */
            htmlpagefooter[name=myfooter] {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                margin: 0;
                padding: 0;
            }

            htmlpagefooter[name=myfooter] div {
                background-image: url('<?= base_url('img/pdf_images/footer.png') ?>');
                background-repeat: no-repeat;
                background-size: 100% 100%; /* Stretch to full width */
                background-position: bottom left;
                height: 35px;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            /* Main content area */
            .page {
                /*                width: 210mm;
                                height: 267mm;
                page-break-after: always;*/
                position: relative;
                margin: 0;
                padding: 25px;
            }

            .info-table {
                width: 100%;
                font-family: Times New Roman;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .info-cell {
                font-size: 16px;
                background: linear-gradient(to right, #d7edf8, #d9d9d9);
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                padding: 10px;
            }
            .section-divider {
                height: 2px;
                background-color: #69BFC4;
                width: 100%;
                margin: 10px 0;
            }
            .skill-table {
                width: 100%;
                font-family: Times New Roman;
                border-collapse: collapse;
                text-align: center;
            }
            .skill-table th {
                background-color: #ccc;
                padding: 10px;
                font-weight: bold;
                border-right: 1px solid #fff;
            }
            .skill-name {
                color: #003399;
                font-weight: bold;
                font-size: 16px;
                padding: 5px;
                text-align: left;
            }
            .percentile-table {
                border-collapse: collapse;
                width: 400px;
                font-family: Arial, sans-serif;
                font-size: 0.9rem;
            }
            .percentile-table td {
                background-color: #2B6CB0;
                color: white;
                font-weight: bold;
                border: 2px solid #fff;
                padding: 10px;
            }

            /* First page special case */
            @page :first {
                margin-top: 0;
            }
            htmlpagefooter[name=myfooter] div {
                height: 35px; /* Should match your footer height */
            }
        </style>
    </head>
    <body>

        <!-- HEADER shown on all pages except first -->
    <htmlpageheader name="myheader">
        <div style="
             background-image: url('<?= base_url('img/pdf_images/header.png') ?>');
             background-repeat: no-repeat;
             background-size: cover;
             /*             height: 50px;*/
             width: 100%;
             margin-top: 0;
             "></div>
    </htmlpageheader>

    <!-- FOOTER on all pages -->
    <htmlpagefooter name="myfooter">
        <div style="
             background-image: url('<?= base_url('img/pdf_images/footer.png') ?>');
             background-repeat: no-repeat;
             background-size: cover;
             /*             height: 35px;*/
             width: 100%;
             margin-bottom: 0;
             "></div>
    </htmlpagefooter>
    <!-- Cover Page -->
    <div  class="page">
        <div>
            <div style="text-align: right;">
                <img src="<?= base_url('img/pdf_images/Logo.png') ?>" alt="Company Logo">
            </div>
            <div style="padding-top: 60px;">
                <p style="text-align: center;">
                    <img src="<?= base_url('img/pdf_images/title.png') ?>" alt="Report Title" width="60%">
                </p>
                <p>
                    <img src="<?= base_url('img/pdf_images/banner.jpg') ?>" alt="Banner" style="width: 100%;">
                </p>

                <!-- User Information Section -->
                <div style="padding:0px 5%">
                    <?php
                    $infoFields = [
                        ['label' => 'Name', 'value' => $user->uName],
                        ['label' => 'Company', 'value' => $user->company_name ?: 'N/A'],
                        [
                            'label' => '',
                            'value' => '<table style="width: 100%; font-size: 16px;">
                            <tr>
                                <td style="width: 50%;">Age: <span>' . htmlspecialchars($user->Age . ' Years') . '</span></td>
                                <td>Country: <span>' . htmlspecialchars($user->country_name) . '</span></td>
                            </tr>
                        </table>'
                        ],
                        [
                            'label' => '',
                            'value' => '<table style="width: 100%; font-size: 16px;">
                            <tr>
                                <td style="width: 50%;">Work Experience: <span>' . htmlspecialchars($user->experience > 0 ? $user->experience . ' Years' : 'Fresher') . '</span></td>
                                <td>Gender: <span>' . htmlspecialchars(ucfirst(strtolower($user->gender))) . '</span></td>
                            </tr>
                        </table>'
                        ],
                        ['label' => 'Designation', 'value' => $user->designation ?? 'N/A'],
                        [
                            'label' => '',
                            'value' => '<table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; font-size: 16px;">Date of Assessment: <span>' . date('d M Y', strtotime($user->created_on)) . '</span></td>
                                <td style="font-size: 16px;">User Id: <span>' . htmlspecialchars($user->user_id) . '</span></td>
                            </tr>
                        </table>'
                        ],
                        ['label' => 'Time Taken for completing test', 'value' => $timeFormatted]
                    ];

                    foreach ($infoFields as $field):
                        ?>
                        <table class="info-table">
                            <tr>
                                <td class="info-cell">
                                    <?php if (!empty($field['label'])): ?>
                                        <?= $field['label'] ?>: <span><?= $field['value'] ?></span>
                                    <?php else: ?>
                                        <?= $field['value'] ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Introduction Page -->
        <div class="page">
            <div style="margin-top: 20px;">
                <table style="padding-top: 10px !important; text-align: justify;">
                    <tr>
                        <td style="width:25%; font-weight:bold;">
                            <h3 style="font-size: 14px;">About the test</h3>
                        </td>
                        <td style="line-height: 18px;">
                            <?php $reportIntro = '
                            <p>The Managerial Skills Profiler is a psychometric instrument that helps profile an employee\'s skills pertaining to her ability to perform a managerial role. The test focuses on those interpersonal, intra personal and professional skills that are critical for success as a manager across industries, sectors, organisations and job profiles. This report describes the candidate\'s skills profile.</p>
                            <br>
                            <p>It provides insights into her key skills that, when harnessed appropriately, will give her an edge as a manager. Additionally, it helps identify areas where she needs to sharpen her skills, and also provides inputs as to how she can do the same.</p>
                            <br>
                            <p>The report begins with a graphical representation of ' . htmlspecialchars($user->uName) . '\'s skill areas. This is followed by a description of ' . htmlspecialchars($user->uName) . '\'s top core skill areas, and how these are beneficial to her in her role as a manager.</p>
                            '; ?>
                            <?= convertTextByGender($reportIntro, $genderKey) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top:20px; padding-bottom:20px; font-weight:bold;">
                            <h1 style="font-size: 14px;">Further these managerial skills are categorised into three major baskets.</h1>
                        </td>
                    </tr>
                </table>

                <!-- Skill Categories -->
                <?php
                foreach ($managementSkills as $category => $data):
                    ?>
                    <table class="table table-sm table-borderless">
                        <tr style="border-bottom: 1px solid #2494B1; padding-top: 20px; padding-bottom:20px">
                            <td style="width:25%; font-weight:bold;">
                                <h5 style="font-size: 14px;"><?= $category ?></h5>
                            </td>
                            <td style="padding-bottom:20px; text-align: justify; line-height: 18px;">
                                <p><?= convertTextByGender($data['top_content'], $genderKey) ?></p>
                            </td>
                        </tr>
                    </table>
                    <div class="section-divider"></div>
                <?php endforeach; ?>

                <!-- Score Interpretation -->
                <table style="padding-top: 20px;">
                    <tr>
                        <td style="width: 60%">&nbsp;</td>
                        <td style="text-align: center;">
                            <h5 style="font-size: 14px;">How to interpret your scores</h5>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:60%; line-height: 18px; font-size: 13px;">
                            <p>Finally, the report concludes by providing developmental inputs for <?= htmlspecialchars($user->uName) ?>.<br>The scores are presented in the form of percentile ranks, ranging from 0-100. The higher your percentile rank, higher is your skill.</p>
                        </td>
                        <td class="text-right" style="padding-left: 5%; padding-right: 0%;">
                            <table class="percentile-table">
                                <tr>
                                    <td rowspan="3" style="background-color: #4CAF50; text-align: center; vertical-align: middle; border: 2px solid #fff; padding: 0px;">
                                        <img src="<?= base_url('img/pdf_images/percentile.svg') ?>" alt="PERCENTILE" style="height: 100px; padding: 10px;">
                                    </td>
                                    <td>0 - 45</td>
                                    <td>Low</td>
                                </tr>
                                <tr>
                                    <td>46 - 75</td>
                                    <td>Moderate</td>
                                </tr>
                                <tr>
                                    <td>76 - 100</td>
                                    <td>High</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Skills Performance Page -->
        <div class="page">
            <div style="margin-top: 8%;">
                <h3 style="color:#2b6cb0"><?= htmlspecialchars($user->uName) ?>'s Performance on the Managerial Skills Test</h3>

                <!-- Skills Table -->
                <table border="1" cellspacing="0" cellpadding="10" width="100%" style="font-family: Times New Roman, sans-serif; text-align: center;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Skills</th>
                            <th>Score</th>
                            <th>Low</th>
                            <th>Moderate</th>
                            <th>High</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sectionColors = ['#7DCEA0', '#F7DC6F', '#85C1E9', '#F5B7B1', '#D2B4DE', '#F8C471'];
                        $colorIndex = 0;

                        foreach ($factorSkills as $section => $skills):
                            $rowspan = count($skills);
                            $first = true;
                            $bgColor = $sectionColors[$colorIndex % count($sectionColors)];
                            $colorIndex++;

                            foreach ($skills as $skill):
                                $factor = null;
                                foreach ($factorList as $f) {
                                    if (str_starts_with(strtolower(trim($f['factor_name'])), strtolower(trim($skill)))) {
                                        $factor = $f;
                                        break;
                                    }
                                }

                                if ($factor):
                                    $score = number_format($factor['total_score'], 0);
                                    $factorScore[$skill] = $factor['pdf_score'];
                                    $pdf_score = $factor['pdf_score'];
                                    preg_match('/Band:\s*(.*?),\s*Percentile:\s*(.*)/', $pdf_score, $matches);
                                    ?>
                                    <tr>
                                        <?php if ($first): ?>
                                            <td rowspan="<?= $rowspan ?>" style="background-color: <?= $bgColor ?>;">
                                                <?php
                                                $imageFile = 'label_' . preg_replace('/\s+/', '_', strtolower($section)) . '.png';
                                                $imagePath = generateVerticalLabelImage($section, $imageFile);
                                                ?>
                                                <img src="<?= $imagePath ?>" alt="" style="height:100px;">
                                            </td>
                                            <?php $first = false; ?>
                                        <?php endif; ?>
                                        <td style="text-align: left;"><?= preg_replace('/[-\s]*MSP/i', '', htmlspecialchars($skill)) ?></td>
                                        <td><?= $matches[2] ?? '' ?></td>

                                        <?php $band = trim($matches[1] ?? ''); ?>

                                        <?php if ($band == 'Low'): ?>
                                            <td><img src="<?= base_url('img/pdf_images/arrow.png') ?>" alt=""></td>
                                            <td></td>
                                            <td></td>

                                        <?php elseif ($band == 'Mod'): ?>
                                            <td></td>
                                            <td><img src="<?= base_url('img/pdf_images/arrow.png') ?>" alt=""></td>
                                            <td></td>

                                        <?php else: ?>
                                            <td></td>
                                            <td></td>
                                            <td><img src="<?= base_url('img/pdf_images/arrow.png') ?>" alt=""></td>
                                        <?php endif; ?>

                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Authenticity Meter -->
                <h3 style="color:#2b6cb0; padding-top: 50px;">Authenticity Meter</h3>
                <?php
                $scorePosition = min(6, max(0, round($authenticityFactorList['total_score'])));
                $colors = ['#F5F8FD', '#F5F8FD', '#D2E0F2', '#D2E0F2', '#B5D0F3', '#B5D0F3', '#B5D0F3'];
                $authenticityMeterTexts = [
                    5 => "$user->uName has a high score on the Authenticity Meter. This indicates that " . ($genderKey === 'male' ? 'he' : 'she') . " has responded to the test items spontaneously and honestly. Thus, the test scores can be taken as a valid indicator of " . ($genderKey === 'male' ? 'his' : 'her') . " managerial skills.",
                    6 => "$user->uName has a high score on the Authenticity Meter. This indicates that " . ($genderKey === 'male' ? 'he' : 'she') . " has responded to the test items spontaneously and honestly. Thus, the test scores can be taken as a valid indicator of " . ($genderKey === 'male' ? 'his' : 'her') . " managerial skills.",
                    3 => "$user->uName has a moderate score on the Authenticity Meter. This indicates that while " . ($genderKey === 'male' ? 'he' : 'she') . " has responded to the test items fairly honestly, there is a strong desire in " . ($genderKey === 'male' ? 'him' : 'her') . " to behave in socially desirable ways. This may at times result in " . ($genderKey === 'male' ? 'his' : 'her') . " portraying a different picture of " . ($genderKey === 'male' ? 'himself' : 'herself') . ". The test results thus are moderately reliable.",
                    4 => "$user->uName has a moderate score on the Authenticity Meter. This indicates that while " . ($genderKey === 'male' ? 'he' : 'she') . " has responded to the test items fairly honestly, there is a strong desire in " . ($genderKey === 'male' ? 'him' : 'her') . " to behave in socially desirable ways. This may at times result in " . ($genderKey === 'male' ? 'his' : 'her') . " portraying a different picture of " . ($genderKey === 'male' ? 'himself' : 'herself') . ". The test results thus are moderately reliable.",
                    0 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, " . ($genderKey === 'male' ? 'he' : 'she') . " may often curb " . ($genderKey === 'male' ? 'his' : 'her') . " own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency.",
                    1 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, " . ($genderKey === 'male' ? 'he' : 'she') . " may often curb " . ($genderKey === 'male' ? 'his' : 'her') . " own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency.",
                    2 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, " . ($genderKey === 'male' ? 'he' : 'she') . " may often curb " . ($genderKey === 'male' ? 'his' : 'her') . " own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency."
                ];
                ?>
                <table border="1" style="border-collapse: collapse; font-family: Times New Roman, sans-serif; text-align: center; margin-top: 10px;" width="100%">
                    <tr>
                        <th style="padding: 10px;">Skill</th>
                        <th style="padding: 10px;">Score</th>
                        <?php
                        for ($i = 0;
                                $i <= 6;
                                $i++):
                            ?>
                            <th style="padding: 10px;"><?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #21778c; color: white; font-weight: bold; text-align: left;">
                            Authenticity Meter
                        </td>
                        <td style="padding: 10px; background-color: #F5F8FD;">
                            <?= number_format($authenticityFactorList['total_score'], 0) ?>
                        </td>
                        <?php
                        for ($i = 0;
                                $i <= 6;
                                $i++):
                            ?>
                            <td style="padding: 10px; background-color: <?= $colors[$i] ?>;">
                                <?php if ($i == $scorePosition): ?>
                                    <img src="<?= base_url('img/pdf_images/arrow.png') ?>" alt="">
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                </table>
                <p><?= $authenticityMeterTexts[$scorePosition] ?></p>
            </div>
        </div>

        <!-- Top Core Skills Page -->
        <div class="page">
            <div style="margin-top: 8%;">
                <h3 style="color:#2b6cb0"><?= htmlspecialchars($user->uName) ?>'s Top Core Managerial Skills</h3>

                <?php
                foreach ($factorScore as $rawKey => $score) {
                    $pdf_score = $score;
                    preg_match('/Band:\s*(.*?),\s*Percentile:\s*(.*)/', $pdf_score, $matches);
                    $normalizedKey = str_replace("-", "", normalizeSkillKey($rawKey));

                    $description = $getReportTopSkills[$normalizedKey] ?? "Description not available";
                    $mappedResults[$normalizedKey] = [
                        'score' => $score,
                        'level' => trim($matches[1] ?? '') ?? '',
                        'description' => $description
                    ];
                }

                $counter = 0; // Initialize counter
                foreach ($mappedResults as $skill => $data):
                    if ($data['level'] === 'High'):
                        $counter++; // Increment counter for each high-level skill
                        // Determine layout based on counter
                        $imagePosition = ($counter <= 2) ? 'left' : 'right';
                        ?>
                        <table style="margin-bottom: 10px;">
                            <tr>
                                <?php if ($imagePosition === 'left'): ?>
                                    <td style="width: 40%;">
                                        <img src="<?= base_url('img/pdf_images/' . str_replace('-', '', $skill) . '.png') ?>" width="264" height="157" alt="">
                                    </td>
                                <?php endif; ?>

                                <td style="width:60%; line-height: 20px;">
                                    <h3><?= str_replace("-", "", $skill) ?></h3>
                                    <span><br>
                                        <?= convertTextByGender($data['description'], $genderKey); ?>
                                    </span>
                                </td>

                                <?php if ($imagePosition === 'right'): ?>
                                    <td style="width: 40%;">
                                        <img src="<?= base_url('img/pdf_images/' . str_replace('-', '', $skill) . '.png') ?>" width="264" height="157" alt="">
                                    </td>
                                <?php endif; ?>
                            </tr>
                        </table>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="section-divider"></div>
        <div>
            <div class="page">
                <?php $skillCategories = ["Managing Others", "Self Management", "Situation Management"]; ?>
                <?php foreach ($skillCategories as $category) { ?>
                    <div style="margin-top: 20px;">
                        <?php
                        $factorScores = [/* your array data here */];

                        // Pre-process data for efficiency
                        $managingOthers = $factorSkills[$category] ?? [];
                        $userName = htmlspecialchars($user->uName);
                        $genderKey = $genderKey ?? 'male'; // Default gender if not set
                        $experienceLevel = (int) ($user->experience ?? 0);
                        $experienceText = $experienceLevel > 10 ? 'More than 10 years' : 'Less than 10 years';

                        // Score level mapping
                        $scoreLevelMap = [
                            'low' => 'low',
                            'mod' => 'medium',
                            'medium' => 'medium',
                            'high' => 'high'
                        ];

                        // Image paths
                        $imagePaths = [
                            'low' => base_url('img/pdf_images/low.png'),
                            'mod' => base_url('img/pdf_images/moderate.png'),
                            'medium' => base_url('img/pdf_images/moderate.png'),
                            'high' => base_url('img/pdf_images/high.png')
                        ];

                        $levelCount = [
                            'Low' => 0,
                            'Mod' => 0,
                            'High' => 0
                        ];
                        $band = [];
                        foreach ($managingOthers as $skillName) {
                            $matchedFactor = null;

                            // Find matching factor
                            foreach ($factorList as $factor) {
                                if (strtolower(trim($factor['factor_name'])) == strtolower(trim($skillName))) {
                                    $matchedFactor = $factor;
                                    break;
                                }
                            }

                            if ($matchedFactor) {
                                // Extract score details
                                preg_match('/Band:\s*(\w+),\s*Percentile:\s*(.*)/', $matchedFactor['pdf_score'] ?? '', $matches);
                                $band[] = $matches[1] ?? '';
                            }
                        }

                        $counts = array_count_values($band);

                        $majorityLevel = array_key_first($counts);
                        ?>

                        <table style="margin-top: -30px;">
                            <tr>
                                <td style="width:25%">
                                    <h3><?= $category ?></h3>
                                </td>
                                <td style="line-height: 20px; text-align: justify;">
                                    <p><?= $managementSkills[$category][$genderKey]['description1'] ?? '' ?></p>
                                    <p><?= $managementSkills[$category][$genderKey]['description2'] ?? '' ?></p>
                                </td>
                            </tr>
                        </table>

                        <?php
                        // Map short forms to full text
                        $levelNames = [
                            'Low' => 'Low',
                            'Mod' => 'Moderate',
                            'High' => 'High'
                        ];
                        // Convert majorityLevel to full name
                        $displayLevel = isset($levelNames[$majorityLevel]) ? $levelNames[$majorityLevel] : $majorityLevel;
                        ?>

                        <div style="color:#003399; padding:10px 0 20px; font-size: 16px;">
                            <?= $userName ?> has 
                            <strong><?= $displayLevel ?> Skills</strong> 
                            in <?= $category ?>. Following is a description of <?= $userName ?>'s <?= $category ?> profile:
                        </div>

                        <div style="page-break-inside: avoid;" keep-together="true">
                            <table border="0" cellspacing="1" cellpadding="10" keep-together="true" style="page-break-inside: avoid;">
                                <thead>
                                    <tr>
                                        <th style="background-color: #ccc; padding: 10px; text-align: left; font-weight: bold; border-right:1px solid #fff; width:250px; font-size: 16px;"><?= $category ?></th>
                                        <th style="background-color: #ccc; padding: 10px; font-weight: bold; width: 150px; border-right:1px solid #fff; font-size: 16px;">&nbsp;</th>
                                        <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Low</th>
                                        <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Moderate</th>
                                        <th style="background-color: #ccc; padding: 10px; font-weight: bold; font-size: 16px;">High</th>
                                    </tr>
                                </thead>
                                <tbody style="margin-top:20px;">
                                    <?php foreach ($managingOthers as $skillName): ?>
                                        <?php
                                        // Find matching factor
                                        $matchedFactor = null;
                                        foreach ($factorList as $factor) {
                                            if (strtolower(trim($factor['factor_name'])) === strtolower(trim($skillName))) {
                                                $matchedFactor = $factor;
                                                break;
                                            }
                                        }

                                        if ($matchedFactor):
                                            // Extract score details
                                            preg_match('/Band:\s*(\w+),\s*Percentile:\s*(.*)/', $matchedFactor['pdf_score'] ?? '', $matches);
                                            $band = $matches[1] ?? '';
                                            $percentile = $matches[2] ?? '';
                                            $image = $imagePaths[strtolower($band)] ?? '';

                                            // Clean skill name for display
                                            $displaySkillName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $skillName);
                                            ?>
                                            <tr>
                                                <td class="skill-name"><?= htmlspecialchars(str_replace('MSP', '', $displaySkillName)) ?></td>
                                                <td style="color: #003399; text-align: center; font-weight: bold; font-size: 16px;"><?= $percentile ?></td>
                                                <td colspan="3">
                                                    <?php if ($image): ?>
                                                        <img src="<?= $image ?>" alt="<?= strtolower($band) ?>">
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php foreach ($managingOthers as $skillName): ?>
                            <?php
                            $cleanSkillName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $skillName);
                            $matchedFactor = null;

                            // Find matching factor
                            foreach ($factorList as $factor) {
                                if (strtolower(trim($factor['factor_name'])) == strtolower(trim($skillName))) {
                                    $matchedFactor = $factor;
                                    break;
                                }
                            }

                            if ($matchedFactor && isset($getFormattedSkills[$cleanSkillName])):
                                // Extract score level
                                preg_match('/Band:\s*(\w+)/i', $matchedFactor['pdf_score'] ?? '', $scoreMatch);
                                $rawScoreLevel = strtolower($scoreMatch[1] ?? '');
                                $scoreLevel = $scoreLevelMap[$rawScoreLevel] ?? '';

                                // Find matching statement
                                $matchedStatement = null;
                                foreach ($getFormattedSkills[$cleanSkillName] as $entry) {
                                    if (strtolower($entry['level'] ?? '') === $scoreLevel) {
                                        $matchedStatement = $entry['statement'];
                                        break;
                                    }
                                }

                                if ($matchedStatement):
                                    ?>
                                    <div>
                                        <div style="font-size: 16px; font-weight: bold; padding-top: 10px;">
                                            <img src="<?= base_url('img/pdf_images/blarrow.png') ?>" alt="" width="32px">
                                            <span style="font-weight: bold;"><?= htmlspecialchars($cleanSkillName) ?></span>
                                        </div>
                                        <div style="line-height: 20px; padding-top: 5px; text-align: justify;">
                                            <?= convertTextByGender($matchedStatement, $genderKey); ?>
                                        </div>
                                    </div>
                                    <br>
                                <?php else: ?>
                                    <div>No matching data for: <?= htmlspecialchars($cleanSkillName) ?></div>
                                    <hr>
                                <?php endif; ?>
                            <?php else: ?>
                                <div>No data for skill: <?= htmlspecialchars($cleanSkillName) ?></div>
                                <hr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <div class="section-divider"></div><br>

                <?php } ?>
                
            </div>
        </div>
        <div style="page-break-before: avoid; page-break-inside: avoid;" keep-together="true">
            <div style="padding: 20px;">
                <!-- Signature section above the disclaimer -->
                <div style="position: absolute; bottom: 100px; width: 100%; color: #238DA9;">
                    Best wishes to <?= htmlspecialchars($user->uName) ?> for a successful managerial career<br><br><br><br><br>
                    Ms. Samindara Sawant<br>
                    Sr. Clinical Psychologist || 16PF Certified 
                </div>
            </div>
            <!-- Last Page - Add this before the closing </div> of your last page -->
            <htmlpagefooter name="disclaimer-footer">
                <div style="text-align: left; font-size: 8px; padding-left: 25px; padding-right: 25px;">
                    <p style="font-size: 10px; font-weight: bold;">Disclaimer:</p>
                    <p style="font-size: 7px; margin-bottom: 5px;">
                        Our psychometric tests are designed to assess personal qualities, such as personality, beliefs, values, and interests, as well as motivation or 'drive related factors'. They are usually administered without a time limit and the questions have no 'right' and 'wrong' answers. The answers reflect how the person taking the test would usually or typically feel, what they believe, or what they think about things. Psychometric tests should be used as an adjunct tool for selection or appraisal purposes and not as standalone tools for decision making.
                    </p>
                    <p style="font-size: 9px; font-weight: bold;">*Results of this assessment are valid for 6 months from the date of assessment.</p>
                </div>
            </htmlpagefooter>
        </div>
    </div>
    <sethtmlpagefooter name="disclaimer-footer" value="on" />
</body>
</html>