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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>SPSR</title>
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
                text-align: center;
            }
            .content {
                padding: 0px 0px;
            }
            /* Footer styling */
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
                background-image: url('<?= base_url('img/pdf_images/MindFrame/mf1.png') ?>');
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
                background-image: url('<?= base_url('img/pdf_images/MindFrame/g.png') ?>');
                background-repeat: no-repeat;
                background-size: 100% 100%; /* Stretch to full width */
                background-position: bottom left;
                height: 35px;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            /* First page special case */
            @page :first {
                margin-top: 0;
            }
            htmlpagefooter[name=myfooter] div {
                height: 35px; /* Should match your footer height */
            }
            .fixed-table {
                border: 1px solid #999;
                border-collapse: collapse;
                width: 300px;
                max-width: 350px;
                table-layout: fixed; /* Important */
            }
            .fixed-table td {
                width: 100px;
                height: 30px;
                text-align: center;
                color: white;
                font-size: 20px;
                overflow: hidden; /* Prevent expansion */
                white-space: nowrap;
            }
        </style>
    </head>
    <body>

    <htmlpageheader name="myheader">
        <div style="text-align: center;">
            <img src="<?= base_url('img/pdf_images/header.png') ?>" style="width: 100%; height: auto;">
        </div>
    </htmlpageheader>

    <htmlpagefooter name="myfooter">
        <div style="text-align: center;">
            <img src="<?= base_url('img/pdf_images/footer.png') ?>" style="width: 100%; height: auto;">
        </div>
    </htmlpagefooter>

    <!-- Main content -->
    <div class="content" style="text-align:justify">
        <div><img src="<?= base_url('img/pdf_images/MindFrame/spr/header.png') ?>" width="100%" alt=""></div>
        <h1 style="text-align: center; color: #8B0000;">Personaity Assessment Report</h1>
        <table style="width: 600px; font-family: Arial, sans-serif; font-size: 14px; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td colspan="3" style="border: 1px solid #aaa; height: 60px; padding: 15px; border-radius: 25px; margin: 5px; background-color: #f8f8f8;  font-size: 16px; ">
                    <strong>Name:</strong><span> <?php echo $user->display_name; ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #aaa; height: 60px; display: flex; align-items: center; padding-left: 10px;" valign="middle">
                    Company: <span><?php echo $user->company_name; ?></span>
                </td>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Gender: <?php echo $user->gender; ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Age: <?php echo $user->Age; ?> Years
                </td>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    DOB: 25-06-1985
                </td>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Work Experience: <?php echo $user->experience; ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Designation: <?php echo $user->designation; ?>
                </td>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Country: <?php echo $user->country_name; ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Date of Assessment: <?php echo $user->created_date ?? ''; ?>
                </td>
                <td style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    User Id: <?php echo $user->user_id; ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 10px 15px; border: 1px solid #999; border-radius: 12px;">
                    Time Taken for completing test: <?php echo formatTimeTaken($user->total_time_taken ?? ''); ?>
                </td>
            </tr>
        </table>
    </div>
    <div style="  page-break-before: always;"></div>
    <!-- header image -->
    <!--    <div class="header">
            <table width="100%" style="color: #ff0000; font-size: 12px;  border-bottom: #ebebeb 1px solid;">
                <tr>
                    <td>Ms. She DemoUser Id:
                        <br><span> D_PTN_EKBYTC </span> 
                    </td>
                    <td style="text-align: right;"><img src="<?= base_url('img/pdf_images/MindFrame/mf1.png') ?>" style="width: 100px;" alt=""></td>
                </tr>
            </table>
        </div>-->
    <!-- Footer image -->
    <div class="footer">
        <img src="<?= base_url('img/pdf_images/MindFrame/g.png') ?>" style="width:100%; height:auto;" />
    </div>
    <div class="content">
        <table style="text-align: center;" width="100%">
            <tr>
                <td><img src="<?= base_url('img/pdf_images/MindFrame/index.png') ?>" alt=""></td>
            </tr>
            <tr>
                <td><img src="<?= base_url('img/pdf_images/MindFrame/index2.png') ?>" alt=""></td>
            </tr>
        </table>
    </div>
    <div style="  page-break-before: always;"></div>
    <!-- header image -->
    <!--    <div class="header">
            <table width="100%" style="color: #ff0000; font-size: 12px;  border-bottom: #ebebeb 1px solid;">
                <tr>
                    <td>Ms. She DemoUser Id:
                        <br><span> D_PTN_EKBYTC </span> 
                    </td>
                    <td style="text-align: right;"><img src="<?= base_url('img/pdf_images/MindFrame/mf1.png') ?>" style="width: 100px;" alt=""></td>
                </tr>
            </table>
        </div>-->
    <!-- Footer image -->
    <div class="footer">
        <img src="<?= base_url('img/pdf_images/MindFrame/g.png') ?>" style="width:100%; height:auto;" />
    </div>
    <div class="content">
        <table style="text-align: center;" width="100%">
            <tr>
                <td><img src="<?= base_url('img/pdf_images/MindFrame/is.png') ?>" alt=""></td>
            </tr>
        </table>
        <br>
        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; font-size: 10px;">
            <thead>
                <tr style="background-color: #2f6fa3; ">
                    <th style="border: 1px solid #fff; padding: 5px; color: #fff; width: 30%;">Left Score Meaning</th>
                    <th style="border: 1px solid #fff; padding: 5px; color: #fff; width: 6%;">Score</th>
                    <th style="border: 1px solid #fff; padding: 5px; color: #fff;">Individual Factor Range</th>
                    <th style="border: 1px solid #fff; padding: 5px; color: #fff; width: 30%;">Right Score Meaning</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <?php foreach ($factorList as $factor) { ?>
                    <?php
                    $scoreLabel = trim(strtolower($factor['score'])); // "low", "moderate", or "high"
// Initialize empty star strings
                    $lowStar = $moderateStar = $highStar = '';

                    if ($scoreLabel === 'low') {
                        $lowStar = '★';
                    } elseif ($scoreLabel === 'moderate') {
                        $moderateStar = '★';
                    } elseif ($scoreLabel === 'high') {
                        $highStar = '★';
                    }
                    ?>
                    <tr>
                        <td style="border: 1px solid #666; text-align: left; padding: 8px;">
                            <strong>* The Hesitant:</strong> Unsure of one's worthiness, low self belief, does not trust one's own abilities.
                        </td>
                        <td style="border: 1px solid #666; font-size: 20px;"><?php echo number_format($factor['total_score'], 0); ?></td>
                        <td style="border: 1px solid #666; padding: 10px;">
                            <div style="font-weight: bold; margin-bottom: 5px;"><?php echo trim($factor['factor_name']) ?></div>
                            <div style="display: flex; height: 20px;">
                                <div style="flex: 1; background-color: orange;"></div>
                                <div style="flex: 1; background-color: #27c3cc; position: relative; padding:10px;">


                                    <table class="fixed-table">
                                        <tr>
                                            <td style="background: linear-gradient(to right, orange, gold);"><?= $lowStar; ?></td>
                                            <td style="background: #00bcd4;"><?= $moderateStar; ?></td>
                                            <td style="background: linear-gradient(to right, #e53935, #ff8a80);"><?= $highStar; ?></td>
                                        </tr>
                                    </table>

                                </div>
                                <div style="flex: 1; background-color: #ec7a85;"></div>
                            </div>
                        </td>
                        <td style="border: 1px solid #666; text-align: left; padding: 8px;">
                            <strong>* The Confident:</strong> Feeling of worthiness, believes in self, trusts own abilities, loves self.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
            <tr>
                <!-- Left: Gauge Placeholder -->
                <td style="width: 25%; text-align: center; vertical-align: top;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Verification Scale:</div>
                    <table style="border-collapse: collapse; width: 150px; height: 150px; table-layout: fixed; font-size: 10px;">
                        <tr>
                            <!-- Low -->
                            <td style="background-color: orange; text-align: center; vertical-align: middle; color: white; font-weight: bold;">
                                Low
                                <br>&nbsp;
                            </td>
                            <!-- Moderate -->
                            <td style="background-color: #f44336; text-align: center; vertical-align: middle; color: white; font-weight: bold;">
                                Moderate
                                <br>6
                            </td>
                            <!-- High -->
                            <td style="background-color: #00bcd4; text-align: center; vertical-align: middle; color: white; font-weight: bold;">
                                High
                                <br>&nbsp;
                            </td>
                        </tr>
                        <!-- Centered "1" below -->
                        <tr>
                            <td colspan="3" style="text-align: center; font-weight: bold;">
                                <img src="<?= base_url('img/pdf_images/MindFrame/md.svg') ?>" alt="" width="25%">
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Right Description -->
                <td style="padding-left: 15px; vertical-align: top; text-align: justify; color: #2b2b2b; font-size: 12px;">
                    Your moderate score indicates that you have done the test reasonably truthfully and without resorting much to giving socially desirable answers. Thus the test scores are largely valid and retesting would not be required.
                </td>
            </tr>
        </table>
    </div>
    <!-- subfactr scre-->
    <div style="  page-break-before: always;"></div>
    <!-- header image -->
    <div class="header">
        <table width="100%" style="color: #ff0000; font-size: 12px;  border-bottom: #ebebeb 1px solid;">
            <tr>
                <td>Ms. She DemoUser Id:
                    <br><span> D_PTN_EKBYTC </span> 
                </td>
                <td style="text-align: right;"><img src="<?= base_url('img/pdf_images/MindFrame/mf1.png') ?>" style="width: 100px;" alt=""></td>
            </tr>
        </table>
    </div>
    <!-- Footer image -->
    <div class="footer">
        <img src="<?= base_url('img/pdf_images/MindFrame/g.png') ?>" style="width:100%; height:auto;" />
    </div>
    <div class="content">
        <table style="text-align: center;" width="100%">
            <tr>
                <td><img src="<?= base_url('img/pdf_images/MindFrame/sub.png') ?>" alt=""></td>
            </tr>
        </table>
        <table>
            <tr>
                <td><img src="<?= base_url('img/pdf_images/MindFrame/ar.png') ?>" alt=""></td>
                <td style="color: #ff0000; font-weight: bold; font-size: 20px;">Adapting to change</td>
            </tr>
        </table>
        <p>
            Adapting to change measures the individual's ability to accept,deal effectively and learn in the face of newness and changing circumstances at the workplace and/or other fronts.
        </p>
        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;">
            <tr style="background-color: #eee; font-weight: bold;">
                <th style="border: 1px solid #000; padding: 5px;">Personality Factors</th>
                <th style="border: 1px solid #000; padding: 5px;">Score</th>
                <th style="border: 1px solid #000; padding: 5px;">Low</th>
                <th style="border: 1px solid #000; padding: 5px;">Moderate</th>
                <th style="border: 1px solid #000; padding: 5px;">High</th>
            </tr>
            <!-- Row 1 -->
            <tr>
                <td style="border: 1px solid #000; text-align: left; padding: 5px;">Accepting Change</td>
                <td style="border: 1px solid #000;">7</td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">
                    1
                </td>
            </tr>
            <!-- Row 2 -->
            <tr>
                <td style="border: 1px solid #000; text-align: left; padding: 5px;">Learning in the Face of Change</td>
                <td style="border: 1px solid #000;">4</td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">
                    2
                </td>
            </tr>
            <!-- Row 3 -->
            <tr>
                <td style="border: 1px solid #000; text-align: left; padding: 5px;">Dealing with Change Effectively</td>
                <td style="border: 1px solid #000;">3</td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">
                    3
                </td>
            </tr>
        </table>
    </div>
    <!-- end subfactr -->
</body>
</html>