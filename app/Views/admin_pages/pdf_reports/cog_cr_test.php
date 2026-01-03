<?php

// Helper functions
function replacePronouns1($text, $gender) {
    $femaleToMale = [
        ' she ' => ' he ',
        'She ' => 'He ',
        ' her ' => ' his ',
        'Her ' => 'His ',
        ' her.' => ' him.',
        ' herself' => ' himself'
    ];

    $maleToFemale = [
        ' he ' => ' she ',
        'He ' => 'She ',
        ' his ' => ' her ',
        'His ' => 'Her ',
        ' him' => ' her',
        ' himself' => ' herself'
    ];

    if (strtolower($gender) === 'male') {
        return str_replace(array_keys($femaleToMale), array_values($femaleToMale), $text);
    } elseif (strtolower($gender) === 'female') {
        return str_replace(array_keys($maleToFemale), array_values($maleToFemale), $text);
    }

    return $text; // No change for other/unknown
}

function replacePronouns($text, $gender) {
    // Define more comprehensive pronoun mappings
    $pronounMaps = [
        'male' => [
            'she' => 'he',
            'She' => 'He',
            'her' => 'his',
            'Her' => 'His',
            'hers' => 'his',
            'Hers' => 'His',
            'herself' => 'himself',
            'Herself' => 'Himself',
            // Handle possessive forms
            "her " => "his ",
            "Her " => "His ",
            // Handle object forms
            " her" => " him",
            " Her" => " Him",
        ],
        'female' => [
            'he' => 'she',
            'He' => 'She',
            'his' => 'her',
            'His' => 'Her',
            'him' => 'her',
            'Him' => 'Her',
            'himself' => 'herself',
            'Himself' => 'Herself',
            // Handle possessive forms
            "his " => "her ",
            "His " => "Her ",
            // Handle object forms
            " him" => " her",
            " Him" => " Her",
        ]
    ];

    // Add neutral/other gender options if needed
    $gender = strtolower($gender);

    if (!array_key_exists($gender, $pronounMaps)) {
        return $text;
    }

    // Create regex patterns that match whole words only
    $patterns = [];
    $replacements = [];

    foreach ($pronounMaps[$gender] as $from => $to) {
        // Use word boundaries to avoid partial matches
        $patterns[] = '/\b' . preg_quote($from, '/') . '\b/';
        $replacements[] = $to;
    }

    // Perform the replacement
    $result = preg_replace($patterns, $replacements, $text);

    // Handle special cases (like "he's" -> "she's")
    $result = preg_replace_callback('/(\b[Hh])(e|im|is|er)\b/', function ($matches) use ($gender) {
        if ($gender === 'female') {
            return strtolower($matches[1]) === 'h' ? 'she' : 'She';
        } elseif ($gender === 'male') {
            return strtolower($matches[1]) === 'h' ? 'he' : 'He';
        }
        return $matches[0];
    }, $result);

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

$factors = [
    [
        'FactorName' => 'Verbal Reasoning',
        'factor_id' => 75,
        'Description' => 'The Verbal Reasoning test measures a candidate\'s ability to use and understand language effectively. It is indicative of the ability to grasp complex verbal relationships and concepts. Good verbal reasoning is important in careers that are language based and that call for the ability to deal with such complex verbal relationships.',
        'Superior' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Superior range.',
        'High' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the High range.',
        'Medium' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Average range.',
        'Low' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Below Average range.'
    ],
    [
        'FactorName' => 'Numerical Ability',
        'factor_id' => 77,
        'Description' => 'It measures understanding of numerical relationships and understanding of numerical concepts. This test is a measure of a candidate\'s ability to reason with numbers, to manipulate numerical relationships and to deal intelligently with quantitative materials.',
        'Superior' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Superior range.',
        'High' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the High range.',
        'Medium' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Average range.',
        'Low' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Below Average range.'
    ],
    [
        'FactorName' => 'Non-Verbal Reasoning',
        'factor_id' => 79,
        'Description' => 'The Non-Verbal Reasoning test assesses the ability to make inferences, to reason from information provided and to draw correct conclusions',
        'Superior' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Superior range.',
        'High' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the High range.',
        'Medium' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Average range.',
        'Low' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Below Average range.'
    ],
    [
        'FactorName' => 'Critical Reasoning',
        'factor_id' => 78,
        'Description' => 'The critical reason test assesses the ability to skillfully conceptualize, analyze, question, and evaluate ideas and beliefs.',
        'Superior' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Superior range.',
        'High' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the High range.',
        'Medium' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Average range.',
        'Low' => 'Test scores indicate that ' . htmlspecialchars($user->uName) .'\'s has scored in the Below Average range.'
    ]
];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $user->display_name; ?></title>
        <style>
            @page {
                margin-top: 100px;
                margin-bottom: 50px;
            }
            body {
                font-family: 'Times New Roman', Times, serif, sans-serif;
                font-size: 12pt;
                margin: 0;
            }
            .header {
                position: fixed;
                top: -100px;
                left: 0;
                right: 0;
                height: 100px;
                text-align: center;
                padding-top: 20px;
                font-size: 10px;
            }
            .footer {
                position: fixed;
                bottom: -40px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: right;
                background: linear-gradient(to right, #fffbe6, #ffd700);
                padding:30px 20px 0px 0px;
            }
            .content {
                padding: 0px 0px;
            }
        </style>
    </head>
    <body>


        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/cog/spr/g.png') ?>" style="width:30%; height:auto;" />
        </div>

        <!-- Main content -->
        <div class="content" style="text-align:justify; text-align: center;">

            <div ><img src="<?= base_url('img/pdf_images/cog/cr.png') ?>" alt="" width="80%"></div>
            <div style="margin-left: 5%;">
                <H1>COG-2 TEST REPORT</H1>
                <table style="width: 600px; font-family: Arial, sans-serif; border-collapse: separate; border-spacing: 0 10px;">
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4);">
                            <strong>Name:</strong> <?php echo $user->display_name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4);">
                            <strong>Company Name:</strong> <?php echo $user->company_name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4); display: flex; justify-content: space-between;">
                            <span><strong>Age:</strong> <?php echo $user->Age; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span><strong>Gender:</strong> <?php echo $user->gender; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4); display: flex; justify-content: space-between;">
                            <span><strong>Work Experience:</strong> <?php echo $user->experience; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span><strong>Country:</strong> <?php echo $user->country_name; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4);">
                            <strong>User Id:</strong> <span ><?php echo $user->user_id; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 20px; border-radius: 25px; background: linear-gradient(to right, #fcd5ce, #d4f1f4);">
                            <strong>Date of Assessment:</strong> <?php echo formatTimeTaken($user->total_time_taken ?? ''); ?>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
        <div style="  page-break-before: always;"></div>

        <!-- header image -->
        <div class="header">
            <table width="100%" style="color: #ff0000; font-size: 12px;  border-bottom: #ebebeb 1px solid;">
                <tr>
                    <td><?php echo $user->display_name; ?> 
                        <br><span> User ID: <?= $user->user_id; ?> </span> </td>
                    <td style="text-align: right;"><img src="<?= base_url('img/pdf_images/cog/test-cr.jpg') ?>" style="width: 50px;" alt=""></td>
                </tr>
            </table>
        </div>
        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/cog/g.png') ?>" style="width:30%; height:auto;" />
        </div>

        <div class="content">
            <table style="width: 700px; border: 2px solid orange; background-color: #fff3e6; font-family: Georgia, serif; font-size: 16px; padding: 15px;">
                <tr>
                    <td style="color: orange; font-weight: bold; padding-bottom: 10px;">
                        About the Test:
                    </td>
                </tr>
                <tr>
                    <td style="color: #000;">
                        The Cog V2 Test is a test designed to measure the basic cognitive abilities of an individual. It is a comprehensive instrument that profiles 4 key cognitive abilities viz : verbal reasoning, non- verbal reasoning , critical thinking and numerical ability.
                    </td>
                </tr>
            </table>

            <h2 style="text-align: center; color: #8B0000; font-family: Georgia, serif;"><?php echo $user->uName; ?> ’s Cognitive Ability Profile</h2>
            <table style="width: 700px; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;">
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #000; padding: 8px;" width="40%">Skills</th>
                    <th style="border: 1px solid #000; padding: 8px; ">Scores</th>
                    <th style="border: 1px solid #000; padding: 8px;" width="80px">Low</th>
                    <th style="border: 1px solid #000; padding: 8px;">Moderate</th>
                    <th style="border: 1px solid #000; padding: 8px;">High</th>
                    <th style="border: 1px solid #000; padding: 8px;">Superior</th>
                </tr>
                <?php foreach ($factorList as $skills) { ?>
                    <tr style="background-color: #f7945d;">
                        <td style="border: 1px solid #fff; padding: 8px; text-align: left;"><?php echo $skills['factor_name']; ?></td>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #fff;"><?php echo round($skills['total_score']); ?></td>
                        <td style="border: 1px solid #fff; padding: 8px; background-color: #FBC19E;">
                            <?php if ($skills['score'] === 'Low') { ?>
                                <img src="<?= base_url('img/pdf_images/cog/radi.png') ?>" alt="" width="40px">
                            <?php } ?>
                        </td>
                        <td style="border: 1px solid #fff;background-color: #FFA86E;">
                            <?php if ($skills['score'] === 'Medium') { ?>
                                <img src="<?= base_url('img/pdf_images/cog/radi.png') ?>" alt="" width="40px">
                            <?php } ?>
                        </td>
                        <td style="border: 1px solid #fff;">
                            <?php if ($skills['score'] === 'High') { ?>
                                <img src="<?= base_url('img/pdf_images/cog/radi.png') ?>" alt="" width="40px">
                            <?php } ?>
                        </td>
                        <td style="border: 1px solid #fff;">
                            <?php if ($skills['score'] === 'Superior') { ?>
                                <img src="<?= base_url('img/pdf_images/cog/radi.png') ?>" alt="" width="40px">
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

            </table>

            <div style="width: 700px; font-family: Georgia, serif; font-size: 12px; line-height: 1.5;">

                <p style="color: #8B0000; font-weight: bold; font-size: 18px;">Individual Score Descriptions:</p>

                <?php
// Map $factors array by factor_id for easy lookup
                $factorMap = [];
                foreach ($factors as $factor) {
                    $factorMap[$factor['factor_id']] = $factor;
                }
                ?>

                <?php
                foreach ($factorList as $skills) {
                    $factor_id = $skills['factor_id'];
                    $score = $skills['score'] === 'Moderate' ? 'Medium' : $skills['score'];

                    if (isset($factorMap[$factor_id])) {
                        $factor = $factorMap[$factor_id];
                        $description = $factor['Description'];
                        $scoreMessage = $factor[$score] ?? 'No message found.';
                    } else {
                        $description = 'Description not available.';
                        $scoreMessage = 'Score message not available.';
                    }
                    ?>

                    <p style="color: #003366; font-weight: bold; margin: 10px 0 0;">
                        <?php echo $skills['factor_name']; ?>
                    </p>
                    <p>
                        <?php echo $description; ?><br>
                        <span style="color: #006400; font-weight: bold;">** <?php echo $scoreMessage; ?></span>
                    </p>

                <?php } ?>

            </div>
        </div>
        <div style="  page-break-before: always;"></div>

        <!-- header image -->
        <div class="header">
            <table width="100%" style="color: #ff0000; font-size: 12px;  border-bottom: #ebebeb 1px solid;">
                <tr>
                    <td><?= $user->display_name; ?>
                        <br><span> User ID: <?= $user->user_id; ?> </span> </td>
                    <td style="text-align: right;"><img src="<?= base_url('img/pdf_images/cog/test-cr.jpg') ?>" style="width: 50px;" alt=""></td>
                </tr>
            </table>
        </div>
        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/cog/spr/g.png') ?>" style="width:30%; height:auto;" />
        </div>

        <div class="content">

            <?php
            $strengths = [];
            $developments = [];

            foreach ($factorList as $factor) {
                $score = $factor['score'];

                if ($score === 'High' || $score === 'Superior') {
                    $strengths[] = $factor['factor_name'];
                } elseif ($score === 'Low') {
                    $developments[] = $factor['factor_name'];
                }
            }
            ?>

            <table style="width: 700px; border-collapse: collapse; font-family: Georgia, serif; font-size: 16px; text-align: left;">
                <tr style="background-color: #2f2f55;">
                    <th style="padding: 10px; border: 1px solid #ccc;  color: #ffffff;">Strengths</th>
                    <th style="padding: 10px; border: 1px solid #ccc;  color: #ffffff;">Development areas</th>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <?php
                        if (empty($strengths)) {
                            echo 'NA';
                        } else {
                            foreach ($strengths as $item) {
                                echo '<div style="margin-bottom: 5px;">&#9656; ' . ucwords(strtolower($item)) . '</div>';
                            }
                        }
                        ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <?php
                        if (empty($developments)) {
                            echo 'NA';
                        } else {
                            foreach ($developments as $item) {
                                echo '<div style="margin-bottom: 5px;">&#9656; ' . ucwords(strtolower($item)) . '</div>';
                            }
                        }
                        ?>
                    </td>
                </tr>
            </table>


            <div style="text-align: right; padding-top: 100px;">
                <table><tr><td style="width: 65%;"></td><td>

                            <table style="font-family: Georgia, serif; font-size: 12px; line-height: 1.6;">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 10px;">Wishing all the best !</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-bottom: 10px;">
                                        <!-- Signature image placeholder -->
                                        <img src="<?= base_url('img/pdf_images/cog/signature.png') ?>" alt="Signature" style="width: 100px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-weight: bold;">Ms. Anuradha Prabhudesai</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Counseling Psychologist</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-weight: bold;">Reg. No. MSMHA-33/2024</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Associate Mental Health Professional</td>
                                </tr>
                            </table>
                        </td></tr></table>

            </div>
        </div>

    </body>

</html>