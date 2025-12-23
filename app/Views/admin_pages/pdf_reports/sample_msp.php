<?php $genderKey = strtolower($user->gender); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>PDF Page with Shared Background Image</title>
    </head>
    <body style="margin:0;font-family: 'Times New Roman',serif, sans-serif;font-size: 0.9rem; ">
        <div style=" width: 210mm;
             height: 267mm;
             page-break-after: always;
             position: relative;">
            <div style="text-align: right;">
                <img src="<?= base_url('img/pdf_images/Logo.png'); ?>" alt="">
            </div>
            <div style="padding-top: 60px;">
                <p style="text-align: center;">
                    <img src="<?= base_url('img/pdf_images/title.png'); ?>" alt="" width="60%">
                </p>
                <p>
                    <img src="<?= base_url('img/pdf_images/banner.jpg'); ?>" alt="" style="width: 100%;">
                </p>
                <div style="padding:0px 5%">
                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style=" font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"> 
                                Name: <span><?= htmlspecialchars($user->uName) ?></span>
                            </td>
                        </tr>
                    </table>

                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"> 
                                Company: <span><?= htmlspecialchars($user->company_name ?: 'N/A') ?></span>
                            </td>
                        </tr>
                    </table>

                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <table style="width: 100%; font-size: 16px;">
                                    <tr>
                                        <td style="width: 50%;">Age: <span><?= htmlspecialchars($user->Age . ' Years') ?></span></td>
                                        <td style="font-size: 16px;">Country: <span><?= htmlspecialchars($user->country_name) ?></span></td> <!-- If country is not in $user, hardcode or add -->
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <table style="width: 100%; font-size: 16px;">
                                    <tr>
                                        <td style="width: 50%;">Work Experience: <span><?= htmlspecialchars($user->experience > 0 ? $user->experience . ' Years' : 'Fresher') ?></span></td>
                                        <td>Gender: <span><?= htmlspecialchars(ucfirst(strtolower($user->gender))) ?></span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table cellpadding="10" cellspacing="0" style="font-size: 16px; width: 100%;font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                Designation: <span><?= htmlspecialchars($user->designation ?? 'N/A') ?></span> <!-- Or pull real designation -->
                            </td>
                        </tr>
                    </table>

                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="font-size: 16px; width: 50%;">Date of Assessment: <span><?= date('d M Y', strtotime($user->created_on)) ?></span></td>
                                        <td style="font-size: 16px;">User Id: <span><?= htmlspecialchars($user->user_id) ?></span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <?php
                    if (!empty($user->total_time_taken)) {
                        list($hours, $minutes, $seconds) = explode(':', $user->total_time_taken);
                        $timeFormatted = sprintf(
                                "%d Hour%s %d Minute%s %d Second%s",
                                (int) $hours, $hours == 1 ? '' : 's',
                                (int) $minutes, $minutes == 1 ? '' : 's',
                                (int) $seconds, $seconds == 1 ? '' : 's'
                        );
                    } else {
                        $timeFormatted = 'N/A';
                    }
                    ?>

                    <table cellpadding="10" cellspacing="0" style="width: 100%; font-family:Times New Roman; border-collapse: collapse; margin-top: 10px;">
                        <tr>
                            <td style="font-size: 16px; background: linear-gradient(to right, #d7edf8, #d9d9d9); border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                Time Taken for completing test: <span><?= $timeFormatted ?></span> <!-- You can pass this too -->
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
        <div>
            <header style="top: 0; background-position: top; position: fixed;
                    left: 0;
                    right: 0;
                    height: 50px;
                    background-image: url(<?= base_url('img/pdf_images/header.png'); ?>); /* Replace with your image path */
                    background-repeat: no-repeat;
                    background-size: cover;
                    z-index: 1000;
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-shadow: 1px 1px 2px black;"></header>
            <div style="margin-top: 20px;">
                <table style="padding-top: 10px !important; text-align: justify;">
                    <tr>
                        <td style="width:25%; font-weight:bold;">
                            <h3 style="font-size: 14px; ">About the test </h1>
                        </td>
                        <td style="line-height: 18px; ">
                            <p style="text-align: justify;">The Managerial Skills Profiler is a psychometric instrument that helps profile an employee’s skills pertaining to her ability to perform a managerial role. The test focuses on those interpersonal, intra personal and professional skills that are critical for success as a manager across industries, sectors, organisations and job profiles. This report describes the candidate’s skills profile. </p>
                            <br>
                            <p> It provides insights into her key skills that, when harnessed appropriately, will give her an edge as a manager. Additionally, it helps identify areas where she needs to sharpen her skills, and also provides inputs as to how she can do the same.</p>
                            <br>
                            <p>The report begins with a graphical representation of <?= htmlspecialchars($user->uName) ?>'s skill areas. This is followed by a description of <?= htmlspecialchars($user->uName) ?>'s top core skill areas, and how these are beneficial to her in her role as a manager.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="" colspan="2" style="padding-top:20px; font-weight:bold;">
                            <h1 style="font-size: 14px; ">Further these managerial skills are categorised into three major baskets. </h1>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="table table-sm table-borderless">
                <tr style=" border-bottom: 1px solid #2494B1; padding-top: 20px; bottom:20px">
                    <Td style="width:25% ;padding-top:20px; font-weight:bold; ">
                        <h5 style="font-size: 14px; ">Managing-Others</h5>
                    </Td>
                    <td style="padding-top:20px; padding-bottom: 20px;text-align: justify; line-height: 18px;">
                        <?php if ($user->gender == 'Male') { ?>
                            <p>This section assesses how adept <?= htmlspecialchars($user->uName) ?> is at managing the team around him, how efficient he is at communicating and interacting with his team members, delegating tasks to them, and managing interpersonal conflict between team members. It also provides insights into how skilfully he can negotiate with both clients as well as team members and convey his viewpoint across.</p>
                        <?php } else { ?>
                            <p>This section assesses how adept <?= htmlspecialchars($user->uName) ?> is at managing the team around her, how efficient she is at communicating and interacting with her team members, delegating tasks to them, and managing interpersonal conflict between team members. It also provides insights into how skilfully she can negotiate with both clients as well as team members and convey her viewpoint across.</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <div style="height: 2px; background-color: #69BFC4; width: 100%; margin: 10px 0;"></div>
            <table class="table table-sm table-borderless">
                <tr style=" border-bottom: 1px solid #2494B1; padding-top: 20px; padding-bottom:20px">
                    <Td style="width:25% ; font-weight:bold;">
                        <h5 style="font-size: 14px; ">Self-Management</h5>
                    </Td>
                    <td style="padding-bottom:20px; text-align: justify; line-height: 18px;">
                        <?php if ($user->gender == 'Male') { ?>
                            <p>A successful manager not only has to manage the people around him, but also needs to be skilled at managing his own emotions, responses and behaviours. The Self-Management category assesses <?= htmlspecialchars($user->uName) ?>'s ability to take sound decisions, manage his time optimally, solve problems, manage information overload, as well as his skill and expertise in solving problems.</p>
                        <?php } else { ?>
                            <p> A successful manager not only has to manage the people around her, but also needs to be skilled at managing her own emotions, responses and behaviours. The Self-Management category assesses <?= htmlspecialchars($user->uName) ?>'s ability to take sound decisions, manage her time optimally, solve problems, manage information overload, as well as her skill and expertise in solving problems.</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <div style="height: 2px; background-color: #69BFC4; width: 100%; margin: 10px 0;"></div>
            <table class="table table-sm table-borderless">
                <tr style=" border-bottom: 1px solid #2494B1; padding-top: 20px; bottom:20px">
                    <Td style="width:25% ; font-weight:bold;">
                        <h5 style="font-size: 14px; ">Situation Management</h5>
                    </Td>
                    <td style="padding-bottom: 20px; line-height: 18px; text-align: justify;">
                        <?php if ($user->gender == 'Male') { ?>
                            <p>A successful manager not only has to manage the people around him, but also needs to be skilled at managing his own emotions, responses and behaviours. The Self-Management category assesses <?= htmlspecialchars($user->uName) ?>'s ability to take sound decisions, manage his time optimally, solve problems, manage information overload, as well as his skill and expertise in solving problems.</p>
                        <?php } else { ?>
                            <p> A successful manager not only has to manage the people around her, but also needs to be skilled at managing her own emotions, responses and behaviours. The Self-Management category assesses <?= htmlspecialchars($user->uName) ?>'s ability to take sound decisions, manage her time optimally, solve problems, manage information overload, as well as her skill and expertise in solving problems.</p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <div style="height: 2px; background-color: #69BFC4; width: 100%; margin: 10px 0;"></div>
            <table style="padding-top: 20px;">
                <tr>
                    <td style="width: 60%">&nbsp;</td>
                    <td style="text-align: center;">
                        <h5 style="font-size: 14px;  ">How to interpret your scores</h5>
                    </td>
                </tr>
                <tr>
                    <td style="width:60%; line-height: 18px; font-size: 13px; ">
                        <p> Finally, the report concludes by providing developmental inputs for <?= htmlspecialchars($user->uName) ?>. <br> The scores are presented in the form of percentile ranks, ranging from 0-100. The higher your percentile rank, higher is your skill. </p>
                    </td>
                    <td class="text-right" style="padding-left: 5%; padding-right: 0%;">
                        <table style="border-collapse: collapse; width: 400px; font-family: Arial, sans-serif;font-size: 0.9rem; " class="table-sm">
                            <tr>
                                <td rowspan="3" style="background-color: #4CAF50; text-align: center; vertical-align: middle; border: 2px solid #fff; padding: 0px;">
                                    <img src="<?= base_url('img/pdf_images/percentile.svg'); ?>" alt="PERCENTILE" style="height: 100px; padding: 10px;">
                                </td>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> 0 - 45 </td>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> Low </td>
                            </tr>
                            <tr>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> 46 - 75 </td>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> Moderate </td>
                            </tr>
                            <tr>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> 76 - 100 </td>
                                <td style="background-color: #2B6CB0; color: white; font-weight: bold; border: 2px solid #fff; padding: 10px;"> High </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <footer style="background-image: url(<?= base_url('img/pdf_images/footer.jpg'); ?>);
                    background-repeat: no-repeat;
                    height: 35px; width: 100%;
                    background-size: cover; margin-top: 5%;"></footer>
            <div style="  page-break-before: always;"></div>
            <div>
                <header style="top: 0; background-position: top; position: fixed;
                        left: 0;
                        right: 0;
                        height: 50px;
                        background-image: url(<?= base_url('img/pdf_images/header.png'); ?>); /* Replace with your image path */
                        background-repeat: no-repeat;
                        background-size: cover;
                        z-index: 1000;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-shadow: 1px 1px 2px black;"></header>
                <div style="margin-top: 8%;">
                    <h3 style="color:#2b6cb0"><?= htmlspecialchars($user->uName) ?>’s Performance on the Managerial Skills Test </h3>
                    <?php
                    $factorLookup = [];
                    $factorScore = [];
                    foreach ($factorList as $factor) {
                        $key = strtolower(trim($factor['factor_name']));
                        $factorLookup[$key] = $factor;
                    }
                    ?>

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
                            // Array of background colors to use per section
                            $sectionColors = [
                                '#7DCEA0', // green
                                '#F7DC6F', // yellow
                                '#85C1E9', // blue
                                '#F5B7B1', // red/pink
                                '#D2B4DE', // purple
                                '#F8C471', // orange
                            ];
                            $colorIndex = 0;

                            foreach ($factorSkills as $section => $skills) {
                                $rowspan = count($skills);
                                $first = true;
                                $bgColor = $sectionColors[$colorIndex % count($sectionColors)];
                                $colorIndex++;

                                foreach ($skills as $skill) {
                                    $key = strtolower(trim($skill));
                                    $factor = null;

                                    foreach ($factorLookup as $name => $fact) {
                                        if (str_starts_with($name, strtolower(trim($skill)))) {
                                            $factor = $fact;
                                            break;
                                        }
                                    }

                                    echo '<tr>';

                                    if ($first) {
                                        $imageFile = 'label_' . preg_replace('/\s+/', '_', strtolower($section)) . '.png';
                                        $imagePath = generateVerticalLabelImage($section, $imageFile);

                                        echo '<td rowspan="' . $rowspan . '" class="vertical-header1" style="background-color: ' . $bgColor . ';">';
                                        echo '<img src="' . $imagePath . '" alt="" style="height:100px;" />';
                                        echo '</td>';
                                        $first = false;
                                    }

                                    echo '<td style="text-align: left;">' . htmlspecialchars($skill) . '</td>';

                                    if ($factor) {
                                        $score = number_format($factor['total_score'], 0);
                                        $factorScore[$skill] = $score;
                                        echo '<td>P' . $score . '</td>';

                                        if ($score <= 45) {
                                            echo '<td><img src="' . base_url('img/pdf_images/arrow.png') . '" alt=""></td><td></td><td></td>';
                                        } elseif ($score <= 75) {
                                            echo '<td></td><td><img src="' . base_url('img/pdf_images/arrow.png') . '" alt=""></td><td></td>';
                                        } else {
                                            echo '<td></td><td></td><td><img src="' . base_url('img/pdf_images/arrow.png') . '" alt=""></td>';
                                        }
                                    } else {
                                        echo '<td>-</td><td></td><td></td><td></td>';
                                    }

                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>

                    </table>


                    <?php

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
                    ?>
                    <h3 style="color:#2b6cb0; padding-top: 50px;">Authenticity Meter</h3>
                    <?php
// Round the score to nearest integer between 0 and 6
                    $scorePosition = min(6, max(0, round($authenticityFactorList['total_score'])));

// Define color bands (from score 0 to 6)
                    $colors = [
                        0 => '#F5F8FD',
                        1 => '#F5F8FD',
                        2 => '#D2E0F2',
                        3 => '#D2E0F2',
                        4 => '#B5D0F3',
                        5 => '#B5D0F3',
                        6 => '#B5D0F3'
                    ];
                    ?>

                    <table border="1" style="border-collapse: collapse;  font-family: Times New Roman, sans-serif; text-align: center; margin-top: 10px;" width="100%">
                        <tr>
                            <th style="padding: 10px;">Skill</th>
                            <th style="padding: 10px;">Score</th>
                            <?php for ($i = 0; $i <= 6; $i++): ?>
                                <th style="padding: 10px;"><?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <td style="padding: 10px; background-color: #21778c; color: white; font-weight: bold; text-align: left;">
                                Authenticity Meter
                            </td>
                            <td style="padding: 10px; background-color: #F5F8FD;">
                                P<?= number_format($authenticityFactorList['total_score'], 0) ?>
                            </td>
                            <?php for ($i = 0; $i <= 6; $i++): ?>
                                <td style="padding: 10px; background-color: <?= $colors[$i] ?>;">
                                    <?php if ($i == $scorePosition): ?>
                                        <img src="<?= base_url('img/pdf_images/arrow.png'); ?>" alt="">
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    </table>
                    <?php
                    $authenticityMeterTexts = [
                        0 => "$user->uName has a high score on the Authenticity Meter. This indicates that he has responded to the test items spontaneously and honestly. Thus, the test scores can be taken as a valid indicator of his managerial skills.",
                        1 => "$user->uName has a high score on the Authenticity Meter. This indicates that she has responded to the test items spontaneously and honestly. Thus, the test scores can be taken as a valid indicator of her managerial skills.",
                        2 => "$user->uName has a moderate score on the Authenticity Meter. This indicates that while he has responded to the test items fairly honestly, there is a strong desire in him to behave in socially desirable ways. This may at times result in his portraying a different picture of himself. The test results thus are moderately reliable.",
                        3 => "$user->uName has a moderate score on the Authenticity Meter. This indicates that while she has responded to the test items fairly honestly, there is a strong desire in her to behave in socially desirable ways. This may at times result in her portraying a different picture of herself. The test results thus are moderately reliable.",
                        4 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, he may often curb his own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency.",
                        5 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, she may often curb her own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency.",
                        6 => "$user->uName's score on the Authenticity Meter is low, indicating a strong need to behave in socially desirable ways. Thus, she may often curb her own personality in an attempt to seek approval of others, and the test results are likely to be impacted by this tendency."
                    ];
                    ?>
                    <p> <?php echo $authenticityMeterTexts[$scorePosition]; ?></p>
                </div>
                <footer style="background-image: url(<?= base_url('img/pdf_images/footer.jpg'); ?>);
                        background-repeat: no-repeat;
                        height: 35px; width: 100%;
                        background-size: cover; margin-top: 5%;"></footer>
            </div>
            <div style="  page-break-before: always;"></div>
            <div>
                <header style="top: 0; background-position: top; position: fixed;
                        left: 0;
                        right: 0;
                        height: 50px;
                        background-image: url(<?= base_url('img/pdf_images/header.png'); ?>; /* Replace with your image path */
                        background-repeat: no-repeat;
                        background-size: cover;
                        z-index: 1000;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-shadow: 1px 1px 2px black;"></header>

                <?php
                $managementSkills = [
                    'Communication Skills' => "A high score on this skill indicates that he has the ability to express his views, ideas and opinions to others in language that is clear and to the point.",
                    'Time Management Skills' => "A high score on this skill indicates that he uses his time efficiently, planning in advance, and prioritising tasks based on their importance of criticality.",
                    'Decision Management Skills' => "A high score on this skill indicates that his decision making skills are strong. He has the ability to arrive at a well thought through decision in a timely manner.",
                    'Delegation Skills' => "A high score on this skill indicates that he has the ability to allot tasks and responsibilities to team members in an optimal and thought through manner.",
                    'Negotiation Skills' => "He is skilled in the art of communicating with others during a difference of opinion, in such a manner that a mutually satisfactory and a win-win solution can be evolved.",
                    'Crisis Management' => "He has the ability to remain objective and calm in times of crisis, taking stock of the situation, and evolving the best possible solution.",
                    'Conflict Management' => "A high score indicates that he has the ability to handle disputes, disagreements or arguments between team members in an objective, non defensive manner, and to resolve the conflict optimally.",
                    'Change Management' => "A high score in this area indicates that he is capable of leading his team through a process of organisational change, addressing people's fears and concerns, dispelling doubts and providing clarity and structure to the ambiguity brought about by the change.",
                    'People Management' => "A high score on this skill indicates that he has ability to deal with people of differing personalities, temperaments and capabilities with ease and comfort.",
                    'Stress Management' => "He can manage his stress levels very well. During stressful situations, he stays calm and focuses on solutions rather than worrying about the outcome.",
                    'Information Management' => "He is very good at handling and managing huge amounts of data and facts. He can sort and categorise information very well, and use this optimally.",
                    'Problem Solving Skills' => "He has the ability to approach problems from various perspectives, have clarity about the nature of the problem, and work out the best possible solution for the problems."
                ];

                // Normalize keys for matching and mapping
                function normalizeSkillKey($key) {
                    $key = str_ireplace("MSP", "", $key);
                    $key = str_ireplace("skills", "Skills", $key);
                    $key = str_ireplace("skill-", "Skill", $key);
                    $key = str_ireplace("managment", "Management", $key);
                    $key = str_ireplace("decision making", "Decision Management", $key);
                    return trim($key);
                }

// Map and display results
                $mappedResults = [];

                foreach ($factorScore as $rawKey => $score) {
                    $normalizedKey = normalizeSkillKey($rawKey);

                    $level = '';
                    if ($score <= 45) {
                        $level = 'Low';
                    } elseif ($score <= 75) {
                        $level = 'Moderate';
                    } else {
                        $level = 'High';
                    }

                    $description = $managementSkills[$normalizedKey] ?? "Description not available";

                    $mappedResults[$normalizedKey] = [
                        'score' => $score,
                        'level' => $level,
                        'description' => $description
                    ];
                }
                ?>
                <div class="content">
                    <h3 style="color:#2b6cb0; padding-top: 50px;"><?= htmlspecialchars($user->uName) ?>'s Top Core Managerial Skills</h3>
                    <?php foreach ($mappedResults as $skill => $data) { ?>
                        <?php if ($data['level'] === 'High') { ?>
                            <table style="margin-bottom: 100px;">
                                <tr>
                                    <td style="width:60%; line-height: 20px; ">
                                        <h3 ><?php echo $skill; ?></h3>

                                        <span><br><?php echo replacePronouns($data['description'], $genderKey); ?></span>
                                    </td>
                                    <td style="width: 40%;">
                                        <img src="<?= base_url('img/pdf_images/one.png'); ?>" alt="">
                                    </td>
                                </tr>
                            </table>
                        <?php } ?>
                    <?php } ?>
                </div>
                <footer style="background-image: url(<?= base_url('img/pdf_images/footer.jpg'); ?>);
                        background-repeat: no-repeat;
                        height: 35px; width: 100%;
                        background-size: cover; margin-top: 5%;"></footer>
            </div>
            <div style="  page-break-before: always;"></div>
            <div class="page">
                <header style="top: 0; background-position: top; position: fixed;
                        left: 0;
                        right: 0;
                        height: 50px;
                        background-image: url(<?= base_url('img/pdf_images/header.png'); ?>); /* Replace with your image path */
                        background-repeat: no-repeat;
                        background-size: cover;
                        z-index: 1000;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-shadow: 1px 1px 2px black;"></header>
                <div class="content">

                    <?php
                    $managementSkills = [
                        'Managing Others' => [
                            'male' => [
                                'description1' => "As a manager, one has to deal with a number of people, most significantly, the team that reports to one. A manager's success depends less on his own technical and functional expertise, and more on how well he can work with his team, and get the best from the team that is provided to him.",
                                'description2' => "Some key skills needed for a manager in order to manage others include: Communication Skills, Conflict Management Skills, Negotiation Skills, People Management Skills, and Delegation Skills. Following is " . htmlspecialchars($user->uName) . "'s profile on managing others."
                            ],
                            'female' => [
                                'description1' => "As a manager, one has to deal with a number of people, most significantly, the team that reports to one. A manager's success depends less on her own technical and functional expertise, and more on how well she can work with her team, and get the best from the team that is provided to her.",
                                'description2' => "Some key skills needed for a manager in order to manage others include: Communication Skills, Conflict Management Skills, Negotiation Skills, People Management Skills, and Delegation Skills. Following is " . htmlspecialchars($user->uName) . "'s profile on managing others."
                            ]
                        ],
                        'Self-Management' => [
                            'male' => [
                                'description1' => "As important as it is to manage people, a successful manager is also one who can manage himself excellently. Managing one's self involves being able to respond to various challenging situations in a timely, thought through, and effective manner. A successful manager is one who can inspire others by example, and he does so by effectively and efficiently managing himself.",
                                'description2' => "Some self-management skills include: Time Management Skills, Decision Making Skills, Stress Management Skills, Information Management Skills and Problem Solving Skills."
                            ],
                            'female' => [
                                'description1' => "As important as it is to manage people, a successful manager is also one who can manage herself excellently. Managing one's self involves being able to respond to various challenging situations in a timely, thought through, and effective manner. A successful manager is one who can inspire others by example, and she does so by effectively and efficiently managing herself.",
                                'description2' => "Some self-management skills include: Time Management Skills, Decision Making Skills, Stress Management Skills, Information Management Skills and Problem Solving Skills."
                            ]
                        ],
                        'Situation-Management' => [
                            'male' => [
                                'description1' => "While a manager does many tasks that are routine and predictable, he also needs to deal with a huge amount of unpredictability and uncertainty. How well a manager manages unexpected situations and makes the most of them is crucial for his success. Often, it is the efficient management of situations as and when they arise that can determine the success of the outcomes or decisions that the manager takes.",
                                'description2' => "Two key situations that a manager needs to be able to deal with are managing change and managing crises."
                            ],
                            'female' => [
                                'description1' => "While a manager does many tasks that are routine and predictable, she also needs to deal with a huge amount of unpredictability and uncertainty. How well a manager manages unexpected situations and makes the most of them is crucial for her success. Often, it is the efficient management of situations as and when they arise that can determine the success of the outcomes or decisions that the manager takes",
                                'description2' => "Two key situations that a manager needs to be able to deal with are managing change and managing crises."
                            ]
                        ]
                    ];
                    $managingOthersDesc1 = $managementSkills['Managing Others'][$genderKey]['description1'];
                    $managingOthersDesc2 = $managementSkills['Managing Others'][$genderKey]['description2'];

                    $Self_ManagementDesc1 = $managementSkills['Self-Management'][$genderKey]['description1'];
                    $Self_ManagementDesc2 = $managementSkills['Self-Management'][$genderKey]['description2'];

                    $Situation_ManagementDesc1 = $managementSkills['Situation-Management'][$genderKey]['description1'];
                    $Situation_ManagementDesc2 = $managementSkills['Situation-Management'][$genderKey]['description2'];

                    $skills = [
                        'Communication Skills' => [
                            'description' => 'A high score on this skill indicates that he has the ability to express his views, ideas and opinions to others in language that is clear and to the point.'
                        ],
                        'Time Management Skills' => [
                            'description' => 'A high score on this skill indicates that he uses his time efficiently, planning in advance, and prioritising tasks based on their importance of criticality.'
                        ],
                        'Decision Management Skills' => [
                            'description' => 'A high score on this skill indicates that his decision making skills are strong. He has the ability to arrive at a well thought through decision in a timely manner.'
                        ],
                        'Delegation Skills' => [
                            'description' => 'A high score on this skill indicates that he has the ability to allot tasks and responsibilities to team members in an optimal and thought through manner.'
                        ],
                        'Negotiation Skills' => [
                            'description' => 'He is skilled in the art of communicating with others during a difference of opinion, in such a manner that a mutually satisfactory and a win-win solution can be evolved.'
                        ],
                        'Crisis Management' => [
                            'description' => 'He has the ability to remain objective and calm in times of crisis, taking stock of the situation, and evolving the best possible solution.'
                        ],
                        'Conflict Management' => [
                            'description' => 'A high score indicates that he has the ability to handle disputes, disagreements or arguments between team members in an objective, non defensive manner, and to resolve the conflict optimally.'
                        ],
                        'Change Management' => [
                            'description' => 'A high score in this area indicates that he is capable of leading his team through a process of organisational change, addressing people\'s fears and concerns, dispelling doubts and providing clarity and structure to the ambiguity brought about by the change.'
                        ],
                        'People Management' => [
                            'description' => 'A high score on this skill indicates that he has ability to deal with people of differing personalities, temperaments and capabilities with ease and comfort.'
                        ],
                        'Stress Management' => [
                            'description' => 'He can manage his stress levels very well. During stressful situations, he stays calm and focuses on solutions rather than worrying about the outcome.'
                        ],
                        'Information Management' => [
                            'description' => 'He is very good at handling and managing huge amounts of data and facts. He can sort and categorise information very well, and use this optimally.'
                        ],
                        'Problem Solving Skills' => [
                            'description' => 'He has the ability to approach problems from various perspectives, have clarity about the nature of the problem, and work out the best possible solution for the problems.'
                        ]
                    ];

                    function replacePronouns($text, $gender) {
                        if ($gender === strtolower('Female')) {
                            $replacements = [
                                ' he ' => ' she ',
                                'He ' => 'She ',
                                ' his ' => ' her ',
                                'His ' => 'Her ',
                                ' him' => ' her',
                                ' himself' => ' herself'
                            ];
                            return str_replace(array_keys($replacements), array_values($replacements), $text);
                        }
                        return $text;
                    }
                    ?>
                    <table style="margin-top: 50px; ">
                        <tr>
                            <Td style="width:25%">
                                <h3> Managing Others</h3>
                            </Td>
                            <Td style="line-height: 20px; text-align: justify;">
                                <?php
                                echo "<p>" . $managingOthersDesc1 . "</p>";
                                echo "<p>" . $managingOthersDesc2 . "</p>";
                                ?>

                            </Td>
                        </tr>

                    </table>
                    <?php $managingOthers = $factorSkills["Managing Others"]; ?>

                    <div>
                        <div style="color:#003399; padding-top:10px;  padding-bottom:10px; font-weight: bold; font-size: 16px; padding-bottom: 20px;"><?= htmlspecialchars($user->uName) ?> has Moderate Skills in Managing Others. Following is a description of <?= htmlspecialchars($user->uName) ?>'s Managing Others profile:</div>

                        <table border="0" cellspacing="1" cellpadding="10">
                            <thead>
                                <tr>
                                    <th style="background-color: #ccc; padding: 10px; text-align: left; font-weight: bold; border-right:1px solid #fff; width:250px; font-size: 16px;">MANAGING-OTHERS</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; width: 150px; border-right:1px solid #fff; font-size: 16px;">&nbsp;</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Low</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Moderate</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; font-size: 16px;">High</th>
                                </tr>
                            </thead>
                            <tbody style="margin-top:20px;">
                                <?php foreach ($managingOthers as $skillName): ?>
                                    <?php
                                    $matchedFactor = null;
                                    foreach ($factorList as $factor) {
                                        if (trim(strtolower($factor['factor_name'])) === trim(strtolower($skillName))) {
                                            $matchedFactor = $factor;
                                            break;
                                        }
                                    }

                                    if ($matchedFactor):
                                        $score = strtolower($matchedFactor['score']); // low, moderate, or high
                                        $image = '';
                                        if ($score === 'low') {
                                            $image = base_url('img/pdf_images/low.png');
                                        } elseif ($score === 'moderate') {
                                            $image = base_url('img/pdf_images/moderate.png');
                                        } elseif ($score === 'high') {
                                            $image = base_url('img/pdf_images/high.png');
                                        }
                                        ?>
                                        <tr>
                                            <td style="color: #003399; padding: 5px; font-weight: bold; font-size: 16px;"><?= htmlspecialchars(str_replace('MSP', '', $matchedFactor['factor_name'])) ?></td>
                                            <td style="color: #003399; text-align: center; font-weight: bold; font-size: 16px;">P<?= $matchedFactor['factor_id'] ?></td>
                                            <td colspan="3">
                                                <img src="<?= $image ?>" alt="<?= $score ?>">
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php

                        function formatStatementAsList($statement) {
                            // Split by new lines or numbered points
                            $lines = preg_split('/\n|\r\n|\d+\)\s*/', trim($statement));
                            $formattedList = "<ol>";
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (!empty($line)) {
                                    $formattedList .= "<li>" . htmlspecialchars($line) . "</li>";
                                }
                            }
                            $formattedList .= "</ol>";
                            return $formattedList;
                        }
                        ?>
                        <?php

// Convert experience level string to a numeric flag (0 or 1)
                        function normalizeExperienceLevel($experienceText) {
                            $experienceText = strtolower(trim($experienceText));
                            return $experienceText === 'more than 10 years' ? 1 : 0;
                        }

// Determine if user has more than 10 years (1) or not (0)
                        $userExpLevel = $user->experience > 10 ? 1 : 0;

                        foreach ($managingOthers as $skillName) {
                            $cleanSkillName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $skillName);

                            // Find matching factor
                            $matchedFactor = null;
                            foreach ($factorList as $factor) {
                                $factorCleanName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $factor['factor_name']);
                                if (strcasecmp($factorCleanName, $cleanSkillName) === 0) {
                                    $matchedFactor = $factor;
                                    break;
                                }
                            }

                            if (isset($getFormattedSkills[$cleanSkillName]) && $matchedFactor) {
                                $matched = false;

                                foreach ($getFormattedSkills[$cleanSkillName] as $entry) {
                                    $entryExpLevel = normalizeExperienceLevel($entry['experience_level']);

                                    if (
                                            strtolower($entry['level']) === strtolower($matchedFactor['score']) &&
                                            $entryExpLevel === $userExpLevel
                                    ) {
                                        ?>

                                        <div>
                                            <div style="font-size: 16px; font-weight: bold; padding-top: 10px;"> <img src="<?= base_url('img/pdf_images/blarrow.png'); ?>" alt="" width="32px"> <span style="font-weight: bold;"><?= htmlspecialchars($cleanSkillName) ?></span></div>

                                            <div style="line-height: 20px; padding-top: 5px; text-align: justify;"> <?php echo replacePronouns(htmlspecialchars($entry['statement']), $genderKey); ?></div>
                                        </div><br>
                                        <?php
                                        $matched = true;
                                        break;
                                    }
                                }

                                if (!$matched) {
                                    echo "<div>No matching data for: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                                }
                            } else {
                                echo "<div>No data for skill: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                            }
                        }
                        ?>






                    </div>
                </div>
            </div>
            <div style="  page-break-before: always;"></div>
            <div >
                <header style="top: 0; background-position: top; position: fixed;
                        left: 0;
                        right: 0;
                        height: 50px;
                        background-image: url(<?= base_url('img/pdf_images/header.png'); ?>); /* Replace with your image path */
                        background-repeat: no-repeat;
                        background-size: cover;
                        z-index: 1000;
                        color: white;
                        display: flex;"></header>





                <div >
                    <table style="margin-top: 50px; ">
                        <tr>
                            <Td style="width:25%">
                                <h3> Self-Management</h3>
                            </Td>
                            <Td style="line-height: 20px; text-align: justify;">
                                <?php
                                echo "<p>" . $Self_ManagementDesc1 . "</p>";
                                echo "<p>" . $Self_ManagementDesc2 . "</p>";
                                ?>

                            </Td>
                        </tr>

                    </table>

                    <div>
                        <div style="color:#003399; padding-top:10px;  padding-bottom:10px; font-weight: bold; font-size: 16px; padding-bottom: 20px;"><?= htmlspecialchars($user->uName) ?> has Moderate skills in Self-Management. Following is a description of <?= htmlspecialchars($user->uName) ?>’s
                            Self-Management profile:</div>
                        <?php $managingOthers = $factorSkills["Self Management"]; ?>

                        <table border="0" cellspacing="1" cellpadding="10">
                            <thead>
                                <tr>
                                    <th style="background-color: #ccc; padding: 10px; text-align: left; font-weight: bold; border-right:1px solid #fff; width:250px; font-size: 16px;">MANAGING-OTHERS</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; width: 150px; border-right:1px solid #fff; font-size: 16px;">&nbsp;</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Low</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Moderate</th>
                                    <th style="background-color: #ccc; padding: 10px; font-weight: bold; font-size: 16px;">High</th>
                                </tr>
                            </thead>
                            <tbody style="margin-top:20px;">
                                <?php foreach ($managingOthers as $skillName): ?>
                                    <?php
                                    $matchedFactor = null;
                                    foreach ($factorList as $factor) {
                                        if (trim(strtolower($factor['factor_name'])) === trim(strtolower($skillName))) {
                                            $matchedFactor = $factor;
                                            break;
                                        }
                                    }

                                    if ($matchedFactor):
                                        $score = strtolower($matchedFactor['score']); // low, moderate, or high
                                        $image = '';
                                        if ($score === 'low') {
                                            $image = base_url('img/pdf_images/low.png');
                                        } elseif ($score === 'moderate') {
                                            $image = base_url('img/pdf_images/moderate.png');
                                        } elseif ($score === 'high') {
                                            $image = base_url('img/pdf_images/high.png');
                                        }
                                        ?>
                                        <tr>
                                            <td style="color: #003399; padding: 5px; font-weight: bold; font-size: 16px;"><?= htmlspecialchars(str_replace('MSP', '', $matchedFactor['factor_name'])) ?></td>
                                            <td style="color: #003399; text-align: center; font-weight: bold; font-size: 16px;">P<?= $matchedFactor['factor_id'] ?></td>
                                            <td colspan="3">
                                                <img src="<?= $image ?>" alt="<?= $score ?>">
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div style="padding-top: 50px;">

                            <?php
// Determine if user has more than 10 years (1) or not (0)
                            $userExpLevel = $user->experience > 10 ? 1 : 0;

                            foreach ($managingOthers as $skillName) {
                                $cleanSkillName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $skillName);

                                // Find matching factor
                                $matchedFactor = null;
                                foreach ($factorList as $factor) {
                                    $factorCleanName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $factor['factor_name']);
                                    if (strcasecmp($factorCleanName, $cleanSkillName) === 0) {
                                        $matchedFactor = $factor;
                                        break;
                                    }
                                }

                                if (isset($getFormattedSkills[$cleanSkillName]) && $matchedFactor) {
                                    $matched = false;

                                    foreach ($getFormattedSkills[$cleanSkillName] as $entry) {
                                        $entryExpLevel = normalizeExperienceLevel($entry['experience_level']);

                                        if (
                                                strtolower($entry['level']) === strtolower($matchedFactor['score']) &&
                                                $entryExpLevel === $userExpLevel
                                        ) {
                                            ?>

                                            <div>
                                                <div style="font-size: 16px; font-weight: bold; padding-top: 10px;"> <img src="<?= base_url('img/pdf_images/blarrow.png'); ?>" alt="" width="32px"> <span style="font-weight: bold;"><?= htmlspecialchars($cleanSkillName) ?></span></div>

                                                <div style="line-height: 20px; padding-top: 5px; text-align: justify;"> <?php echo replacePronouns(htmlspecialchars($entry['statement']), $genderKey); ?></div>
                                            </div><br>
                                            <?php
                                            $matched = true;
                                            break;
                                        }
                                    }

                                    if (!$matched) {
                                        echo "<div>No matching data for: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                                    }
                                } else {
                                    echo "<div>No data for skill: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div style="  page-break-before: always;"></div>
                <div class="content">

                    <div style="margin-top: 800px;">
                        <header style="top: 0; background-position: top; position: fixed;
                                left: 0;
                                right: 0;
                                height: 50px;
                                background-image: url(<?= base_url('img/pdf_images/header.png'); ?>); /* Replace with your image path */
                                background-repeat: no-repeat;
                                background-size: cover;
                                z-index: 1000;
                                color: white;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                text-shadow: 1px 1px 2px black;"></header>
                        <div class="content">




                            <table style="margin-top: 50px; ">
                                <tr>
                                    <Td style="width:30%">
                                        <h3> Situation-Management</h3>
                                    </Td>
                                    <Td style="line-height: 20px; font-size: 14px; text-align: justify;">
                                        <?php
                                        echo "<p>" . $Situation_ManagementDesc1 . "</p>";
                                        echo "<p>" . $managingOthersDesc2 . "</p>";
                                        ?>
                                    </Td>
                                </tr>

                            </table>


                            <div>

                                <div style="color:#003399; padding-top:10px;  padding-bottom:10px; font-weight: bold; font-size: 16px; padding-bottom: 20px;"><?= htmlspecialchars($user->uName) ?> has Moderate skills in Situation Management.Following is a description of
                                    <?= htmlspecialchars($user->uName) ?>’s Situation Management profile:</div>

                                <?php $managingOthers = $factorSkills["Situation Management"]; ?>

                                <table border="0" cellspacing="1" cellpadding="10">
                                    <thead>
                                        <tr>
                                            <th style="background-color: #ccc; padding: 10px; text-align: left; font-weight: bold; border-right:1px solid #fff; width:250px; font-size: 16px;">MANAGING-OTHERS</th>
                                            <th style="background-color: #ccc; padding: 10px; font-weight: bold; width: 150px; border-right:1px solid #fff; font-size: 16px;">&nbsp;</th>
                                            <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Low</th>
                                            <th style="background-color: #ccc; padding: 10px; font-weight: bold; border-right:1px solid #fff; font-size: 16px;">Moderate</th>
                                            <th style="background-color: #ccc; padding: 10px; font-weight: bold; font-size: 16px;">High</th>
                                        </tr>
                                    </thead>
                                    <tbody style="margin-top:20px;">
                                        <?php foreach ($managingOthers as $skillName): ?>
                                            <?php
                                            $matchedFactor = null;
                                            foreach ($factorList as $factor) {
                                                if (trim(strtolower($factor['factor_name'])) === trim(strtolower($skillName))) {
                                                    $matchedFactor = $factor;
                                                    break;
                                                }
                                            }

                                            if ($matchedFactor):
                                                $score = strtolower($matchedFactor['score']); // low, moderate, or high
                                                $image = '';
                                                if ($score === 'low') {
                                                    $image = base_url('img/pdf_images/low.png');
                                                } elseif ($score === 'moderate') {
                                                    $image = base_url('img/pdf_images/moderate.png');
                                                } elseif ($score === 'high') {
                                                    $image = base_url('img/pdf_images/high.png');
                                                }
                                                ?>
                                                <tr>
                                                    <td style="color: #003399; padding: 5px; font-weight: bold; font-size: 16px;"><?= htmlspecialchars(str_replace('MSP', '', $matchedFactor['factor_name'])) ?></td>
                                                    <td style="color: #003399; text-align: center; font-weight: bold; font-size: 16px;">P<?= $matchedFactor['factor_id'] ?></td>
                                                    <td colspan="3">
                                                        <img src="<?= $image ?>" alt="<?= $score ?>">
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php

                                foreach ($managingOthers as $skillName) {
                                    $cleanSkillName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $skillName);

                                    // Find matching factor
                                    $matchedFactor = null;
                                    foreach ($factorList as $factor) {
                                        $factorCleanName = preg_replace('/\s*-.*$|\s+[A-Z]{2,}$/', '', $factor['factor_name']);
                                        if (strcasecmp($factorCleanName, $cleanSkillName) === 0) {
                                            $matchedFactor = $factor;
                                            break;
                                        }
                                    }

                                    if (isset($getFormattedSkills[$cleanSkillName]) && $matchedFactor) {
                                        $matched = false;

                                        foreach ($getFormattedSkills[$cleanSkillName] as $entry) {
                                            $entryExpLevel = normalizeExperienceLevel($entry['experience_level']);

                                            if (
                                                    strtolower($entry['level']) === strtolower($matchedFactor['score']) &&
                                                    $entryExpLevel === $userExpLevel
                                            ) {
                                                ?>

                                                <div>
                                                    <div style="font-size: 16px; font-weight: bold; padding-top: 10px;"> <img src="<?= base_url('img/pdf_images/blarrow.png'); ?>" alt="" width="32px"> <span style="font-weight: bold;"><?= htmlspecialchars($cleanSkillName) ?></span></div>

                                                    <div style="line-height: 20px; padding-top: 5px; text-align: justify;"> <?php echo replacePronouns(htmlspecialchars($entry['statement']), $genderKey); ?></div>
                                                </div><br>
                                                <?php
                                                $matched = true;
                                                break;
                                            }
                                        }

                                        if (!$matched) {
                                            echo "<div>No matching data for: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                                        }
                                    } else {
                                        echo "<div>No data for skill: " . htmlspecialchars($cleanSkillName) . "</div><hr>";
                                    }
                                }
                                ?>

                                <div style="color:#238DA9; padding-top: 70px;">Best wishes to <?= htmlspecialchars($user->uName) ?> for a successful managerial career <br> Ms. Samindara Sawant <br> Sr. Clinical Psychologist || 16PF Certified </div>
                                <p style="font-size: 10px; font-weight: bold;">Disclaimer:</p>
                                <p style="font-size: 7px;">Our psychometric tests are designed to assess personal qualities, such as personality, beliefs, values, and interests, as well as motivation or ‘drive related factors’. They are usually administered without a time limit and the questions have no ‘right’ and ‘wrong’ answers. The answers reflect how the person taking the test would usually or typically feel, what they believe, or what they think about things. Psychometric tests should be used as an adjunct tool for selection or appraisal purposes and not as standalone tools for decision making</p>
                                <p style="font-size: 9px;">*Results of this assessment are valid for 6 months from the date of assessment.</p>
                            </div>
                        </div>
                        <footer style="background-image: url(<?= base_url('img/pdf_images/footer.jpg'); ?>; 
                                background-repeat: no-repeat;
                                height: 35px; width: 100%;
                                background-size: cover; margin-top: 5%;"></footer>
                    </div>
                    </body>
                    </html>
