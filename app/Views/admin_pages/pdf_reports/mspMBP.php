<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>PDF Page</title>
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
            }

            .footer {
                position: fixed;
                bottom: -70px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: center;
            }

            .content {
                padding: 0 30px;
            }
        </style>
    </head>
    <body>

        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mainhead.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/footer.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Main content -->
        <div class="content" style="text-align:justify">

            <div><img src="<?= base_url('img/pdf_images/MP/Home.png') ?>" alt=""></div>
            <table style="width: 100%; margin: auto; border-collapse: separate; border-spacing: 0 12px;">
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Name: Mr. Ganni Venkatesh
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Company: Oro24
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Designation/Rank: Manager
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Age: 33 Years &nbsp;&nbsp;&nbsp;&nbsp; Country: United Arab Emirates
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Work Experience: 8 Years &nbsp;&nbsp;&nbsp;&nbsp; Gender: Male
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Date of Assessment: 20 May 2025 &nbsp;&nbsp;&nbsp;&nbsp; User Id: MPM_1SG4J7
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: linear-gradient(to right, #ffffff, #eaeaea);
                        border-left: 6px solid #004b87; border-right: 6px solid #004b87;
                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-weight: bold;">
                        Time Taken for completing test: 57 Minutes 9 Seconds
                    </td>
                </tr>
            </table>




        </div>
        <!-- home page end-->

        <div style="  page-break-before: always;"></div>
        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mpH.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/mpF.png') ?>" style="width:100%; height:auto;" />
        </div>
        <div class="content" style="text-align:justify">
            <div style="color:#ff0000; font-size: 0.8rem;">
                <p>Mr. Ganni Venkatesh<br>User ID: MPM_1SG4J7</p>
            </div>

            <h3 style="text-align:center;">ABOUT THE TESTS :</h3>
            <p>
                <strong>MindFrame:</strong> The Workplace Personality Test (WPT) is a tool designed to gain insights into every person’s unique personality. It is a comprehensive instrument that profiles the individual on various workplace personality traits critical for success.
            </p>
            <p>
                The Managerial Skills Profiler (MSP), is a psychometric instrument that helps profile an employee’s skills pertaining
                to his ability to perform a managerial role. The test focuses on those interpersonal, intra personal and professional
                skills that are critical for success as a manager across industries, sectors, organizations and job profiles.

            </p>
            <p>
                Taken together, the Manager BluePrint(MB), will help you decide which of your managers are best suited for
                promotions. It will help you identify the strengths of your managers, as well as understand their development areas.

            </p>
            <p>
                The results for both the tests are presented in the form of standard scores. This practice not only makes the results
                more robust and reliable, but also provides organisations with an understanding of where their managers stand with
                respect to market benchmarks. The standardisation sample for the MSP comprises of 600 managers across industries,
                organisations and sectors. The scores are presented in the form of percentile ranks, ranging from 0-100. The
                standardisation sample for the MF comprises of 400 managers, randomly selected from various domains, functions
                and industries. The scores are presented in the form of percentiles , ranging from 5-100.
            </p>
        </div>
        <div style="  page-break-before: always;"></div>



        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mpH.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/mpF.png') ?>" style="width:100%; height:auto;" />
        </div>
        <div class="content" style="text-align:justify">

            <div style="color:#ff0000; font-size: 0.8rem;">
                <p>Mr. Ganni Venkatesh<br>User ID: MPM_1SG4J7</p>
            </div>


            <h3 style="text-align:center; color: darkgreen;"> Section 1 : Managerial Skills Profiler</h3>
            <p style="color:#336699">Ganni’s Performance on the Managerial Skills Test : </p>

            <div>
                <table style="width: 90%; border-collapse: collapse; margin: auto; text-align: center; font-size: 14px;" cellpadding="10">
                    <tr>
                        <th style="border: 1px solid #999; background: #fff;"></th>
                        <th style="border: 1px solid #999; background: #fff;">Skills</th>
                        <th style="border: 1px solid #999; background: #fff;">Score</th>
                        <th style="border: 1px solid #999; background: #fff;">Low</th>
                        <th style="border: 1px solid #999; background: #fff;">Moderate</th>
                        <th style="border: 1px solid #999; background: #fff;">High</th>
                    </tr>

                    <!-- Managing Others -->
                    <tr>
                        <td rowspan="5" style="  background-color: #5ba1cf;  font-weight: bold; border: 1px solid #999; text-align: center;  vertical-align: middle;  padding: 5px;"><img src="<?= base_url('img/pdf_images/ManagingOthers.svg') ?>" alt=""></td>
                        <td style="background-color: #21825b; color: white; border: 1px solid #999;">Communication Skills</td>
                        <td style="border: 1px solid #999;">P76</td>
                        <td style="background-color: #C3E3CE; border: 1px solid #999;"></td>
                        <td style="background-color: #99D2B1; border: 1px solid #999;"></td>
                        <td style="background-color: #7FC896; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/g1.png') ?>" alt=""></td>
                    </tr>
                    <tr>
                        <td style="background-color: #21825b; color: white; border: 1px solid #999;">Conflict Management</td>
                        <td style="border: 1px solid #999;">P72</td>
                        <td style="background-color: #C3E3CE; border: 1px solid #999;"></td>
                        <td style="background-color: #99D2B1; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/g1.png') ?>" alt=""></td>
                        <td style="background-color: #7FC896; border: 1px solid #999;"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #21825b; color: white; border: 1px solid #999;">Delegation Skills</td>
                        <td style="border: 1px solid #999;">P31</td>
                        <td style="background-color: #C3E3CE; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/g1.png') ?>" alt=""></td>
                        <td style="background-color: #99D2B1; border: 1px solid #999;"></td>
                        <td style="background-color: #7FC896; border: 1px solid #999;"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #21825b; color: white; border: 1px solid #999;">Negotiation Skills</td>
                        <td style="border: 1px solid #999;">P26</td>
                        <td style="background-color: #C3E3CE; border: 1px solid #999;"></td>
                        <td style="background-color: #99D2B1; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/g1.png') ?>" alt=""></td>
                        <td style="background-color: #7FC896; border: 1px solid #999;"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #21825b; color: white; border: 1px solid #999;">People Management</td>
                        <td style="border: 1px solid #999;">P61</td>
                        <td style="background-color: #C3E3CE; border: 1px solid #999;"></td>
                        <td style="background-color: #99D2B1; border: 1px solid #999;"></td>
                        <td style="background-color: #7FC896; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/g1.png') ?>" alt=""></td>
                    </tr>

                    <!-- Self Management -->
                    <tr>
                        <td rowspan="5" style="writing-mode: vertical-rl; transform: rotate(180deg); background-color: #c3d1e3; color: black; font-weight: bold; border: 1px solid #999;">

                            <img src="<?= base_url('img/pdf_images/SelfManagement.svg') ?>" alt="">
                        </td>
                        <td style="background-color: #816394; color: white; border: 1px solid #999;">Decision Making skills</td>
                        <td style="border: 1px solid #999;">P72</td>
                        <td style="background-color: #DECCE1; border: 1px solid #999;"></td>
                        <td style="background-color: #D4B6D6; border: 1px solid #999;"></td>
                        <td style="background-color: #BCA0CB; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/b1.png') ?>" alt=""></td>
                    </tr>
                    <tr>
                        <td style="background-color: #816394; color: white; border: 1px solid #999;">Information Management</td>
                        <td style="border: 1px solid #999;">P88</td>
                        <td style="background-color: #DECCE1; border: 1px solid #999;"></td>
                        <td style="background-color: #D4B6D6; border: 1px solid #999;"></td>
                        <td style="background-color: #BCA0CB; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/b1.png') ?>" alt=""></td>
                    </tr>
                    <tr>
                        <td style="background-color: #816394; color: white; border: 1px solid #999;">Problem Solving Skills</td>
                        <td style="border: 1px solid #999;">P63</td>
                        <td style="background-color: #DECCE1; border: 1px solid #999;"></td>
                        <td style="background-color: #D4B6D6; border: 1px solid #999;"></td>
                        <td style="background-color: #BCA0CB; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/b1.png') ?>" alt=""></td>
                    </tr>
                    <tr>
                        <td style="background-color: #816394; color: white; border: 1px solid #999;">Stress Management</td>
                        <td style="border: 1px solid #999;">P36</td>
                        <td style="background-color: #DECCE1; border: 1px solid #999;"></td>
                        <td style="background-color: #D4B6D6; border: 1px solid #999;"></td>
                        <td style="background-color: #BCA0CB; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/b1.png') ?>" alt=""></td>
                    </tr>
                    <tr>
                        <td style="background-color: #816394; color: white; border: 1px solid #999;">Time Management Skills</td>
                        <td style="border: 1px solid #999;">P57</td>
                        <td style="background-color: #DECCE1; border: 1px solid #999;"></td>
                        <td style="background-color: #D4B6D6; border: 1px solid #999;"></td>
                        <td style="background-color: #BCA0CB; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/b1.png') ?>" alt=""></td>
                    </tr>

                    <!-- Situation Management -->
                    <tr>
                        <td rowspan="2" style="writing-mode: vertical-rl; transform: rotate(180deg); background-color: #7DCEA0; color: black; font-weight: bold; border: 1px solid #999;">

                            <img src="<?= base_url('img/pdf_images/sManagement.svg') ?>" alt="">
                        </td>
                        <td style="background-color: #e6b800; color: black; border: 1px solid #999;">Change Management</td>
                        <td style="border: 1px solid #999;">P71</td>
                        <td style="background-color: #FBE9B2; border: 1px solid #999;"></td>
                        <td style="background-color: #FEE093; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/c1.png') ?>" alt=""></td>
                        <td style="background-color: #F9D47A; border: 1px solid #999;"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #e6b800; color: black; border: 1px solid #999;">Crisis Management</td>
                        <td style="border: 1px solid #999;">P84</td>
                        <td style="background-color: #FBE9B2; border: 1px solid #999;"></td>
                        <td style="background-color: #FEE093; border: 1px solid #999;"></td>
                        <td style="background-color: #F9D47A; border: 1px solid #999;"><img src="<?= base_url('img/pdf_images/MP/c1.png') ?>" alt=""></td>
                    </tr>
                </table>
            </div>



        </div>
        <div style="  page-break-before: always;"></div>

        <!-- Next-->
        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mpH.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/mpF.png') ?>" style="width:100%; height:auto;" />
        </div>
        <div class="content" style="text-align:justify">

            <div style="color:#ff0000; font-size: 0.8rem;">
                <p>Mr. Ganni Venkatesh<br>User ID: MPM_1SG4J7</p></div>


            <h3 style="text-align:center; color: darkgreen;"> Section 2 : MindFrames : Personality Test</h3>
            <p style="color:#336699; text-align: center;">INDIVIDUAL'S SCORES PLOTTER </p>

            <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; text-align: center; font-weight: bold; font-size: 11px;">
                <tr>
                    <td style="background-color: #95bcbc; color: black; padding: 8px;">
                        LEFT SCORE MEANING
                    </td>
                    <td style="background-color: #ead2a5; color: black; padding: 8px;">
                        INDIVIDUAL FACTOR RANGE
                    </td>
                    <td style="background-color: #c0c0c0; color: black; padding: 8px;">
                        RIGHT SCORE MEANING
                    </td>
                </tr>
            </table> <br>

            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 11px;">


                <!-- Trait Row Template -->
                <!-- Repeat for each trait -->

                <!-- Row: Self Esteem -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Hesitant</b>: Unsure of one's worthiness, low self belief, does not trust one’s own abilities.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 5px;">
                        <div style="margin-bottom: 6px; "><b>Self Esteem</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2; width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center; width: 100px">
                                    <span  style="  background-color: #fff;  color: #333;  align-items: center; justify-content: center;  font-weight: bold;  font-size: 15px;"> 7 </span>
                                </td>
                                <td style="background-color: #cccccc; width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Confident</b>: Feeling of worthiness, believes in self, trusts own abilities, loves self.</td>
                </tr>

                <!-- Row: Adapting To Change -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Rigid</b>: Unable to accept change easily, deals with change in a rigid manner.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Adapting To Change</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2; width:80px"> <span style="  background-color: #fff;  color: #333;  align-items: center; justify-content: center;  font-weight: bold;  font-size: 15px;"> 8 </span></td>
                                <td style="background-color: #eed6a3; text-align: center; width: 100px">

                                </td>
                                <td style="background-color: #cccccc; width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Changer</b>: Accepts and adapts to change easily and effectively.</td>
                </tr>

                <!-- Row: Achievement Orientation -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Underachiever</b>: Low risk taking, no drive to achieve success.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 5px;">
                        <div style="margin-bottom: 6px;"><b>Achievement Orientation</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2; width:80px" ></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">7</div>
                                </td>
                                <td style="background-color: #cccccc;  width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Achiever</b>: High need to achieve, goal-oriented.</td>
                </tr>

                <!-- Row: Independence -->
                <tr> 
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;">* <b>The Dependent</b>: Submissive, cooperative, relies on others.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Independence</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2; width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">7</div>
                                </td>
                                <td style="background-color: #cccccc; width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 5px;">* <b>The Independent</b>: Assertive, self-driven, autonomous.</td>
                </tr>

                <!-- Row: Balanced Emotionality -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Reactor</b>: Emotionally unstable, easily upset.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Balanced Emotionality</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2;width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">8</div>
                                </td>
                                <td style="background-color: #cccccc;width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Harmonious</b>: Emotionally stable, resilient, positive.</td>
                </tr>

                <!-- Row: Stress Tolerance -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Hassled</b>: Easily stressed and overwhelmed.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Stress Tolerance</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2;width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">7</div>
                                </td>
                                <td style="background-color: #cccccc;width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Tranquil</b>: Calm, composed, handles stress well.</td>
                </tr>

                <!-- Row: Orderliness -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Spontaneous</b>: Unstructured, easily distracted.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Orderliness</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2;width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">5</div>
                                </td>
                                <td style="background-color: #cccccc;width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Orderly</b>: Detail-oriented, well-organized.</td>
                </tr>

                <!-- Row: Extraversion -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Introvert</b>: Prefers solitude, shy, avoids social settings.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Extraversion</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2;width:80px"></td>
                                <td style="background-color: #eed6a3; text-align: center;">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">7</div>
                                </td>
                                <td style="background-color: #cccccc;width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Extrovert</b>: Sociable, outgoing, people-oriented.</td>
                </tr>

                <!-- Row: Trust -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Vigilant</b>: Cautious, skeptical, untrusting.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Trust</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2; text-align: center;width:80px">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">2</div>
                                </td>
                                <td style="background-color: #eed6a3;"></td>
                                <td style="background-color: #cccccc; width:80px"></td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Trusting</b>: Accepting, open, gives benefit of doubt.</td>
                </tr>

                <!-- Row: Initiative -->
                <tr>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Passive</b>: Lacks motivation, reactive.</td>
                    <td style="border: 1px solid #ccc; background-color: #fff; padding: 12px;">
                        <div style="margin-bottom: 6px;"><b>Initiative</b></div>
                        <table style="width: 90%; height: 30px; border-collapse: collapse; margin: 0 auto; table-layout: fixed;">
                            <tr>
                                <td style="background-color: #9ec2c2;width:80px"></td>
                                <td style="background-color: #eed6a3;"></td>
                                <td style="background-color: #cccccc; text-align: center;width:80px">
                                    <div style="display: inline-block; width: 24px; height: 24px; line-height: 24px; background-color: white; border: 2px solid #a88d46; color: #a88d46; font-weight: bold; border-radius: 50%; font-size: 14px;">10</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 10px;">* <b>The Initiator</b>: Proactive, self-starting, energetic.</td>
                </tr>
            </table>
            <p style="background-color: #F68220; color:#000; padding: 5px; font-size: 11px;"> Verification Scale</p>
            <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: auto; margin-top: 20px;" width="45%">

                <tr>
                    <td style=" width: 32%; text-align: center;">Low</td>
                    <td style="width: 32%;  text-align: center; ">Moderate</td>
                    <td style=" width: 32%;  text-align: center;">High</td>
                </tr>
                <tr>
                    <td > &nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="background-color: #9ec2c2; width: 32%; ">&nbsp;</td>
                    <td style="background-color: #eed6a3; width: 32%; text-align: center;">58</td>
                    <td style="background-color: #cccccc; width: 32%;">&nbsp;</td>
                </tr>
            </table>


            <p style="background-color: #F68220; color:#000; padding: 5px; font-size: 11px;">
                Your moderate score indicates that you have done the test reasonably truthfully and without resorting much to giving socially
                desirable answers. Thus the test scores are largely valid and retesting would not be required.
            </p>



        </div>
        <!-- end Next-->





        <div style="page-break-before: always;"></div>
        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mpH.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/mpF.png') ?>" style="width:100%; height:auto;" />
        </div>
        <div class="content" style="text-align:justify">
            <div style="color:#ff0000; font-size: 0.8rem;">
                <p>Mr. Ganni Venkatesh<br>User ID: MPM_1SG4J7</p>
            </div>
            <h2 style="text-align:center; color:#21825b">Section 3 : What the Blue Print will tell you about yourself </h2>
            <div style="text-align: center;">
                <img src="<?= base_url('img/pdf_images/MP/chart.png') ?>" alt="">
            </div>
            <div>
                <p  style="text-align: center; color: #004b87;">
                    An in-depth analysis of Ganni has been done on the following key indexes. Each
                    index focuses on both traits as well as skills.

                </p>
                <p>
                    <span style="color: #97483c00; text-align: left; font-weight: bold;">Others :</span> pinpoints key strengths and developmental areas while handling a team and team
                    functioning
                </p>
                <p>
                    <span style="color: #97483c00; text-align: left;  font-weight: bold;">One Self :</span> pinpoints key intrapersonal skills that affect ones day to day functioning
                    functioning
                </p>
                <p>
                    <span style="color: #97483c00; text-align: left; font-weight: bold; ">Operations & Processes :</span> skills and traits that would influence and impact operations and
                    process

                </p>
                <p>
                    <span style="color: #97483c00; text-align: left; font-weight: bold;">Leadership Dialogue :</span> focuses on dynamic leadership traits and skills and what could be
                    possible developmental areas.
                </p>

            </div>

        </div>

        <div style="page-break-before: always;"></div>
        <!-- Header image -->
        <div class="header">
            <img src="<?= base_url('img/pdf_images/MP/mpH.png') ?>" style="width:100%; height:auto;" />
        </div>

        <!-- Footer image -->
        <div class="footer">
            <img src="<?= base_url('img/pdf_images/MP/mpF.png') ?>" style="width:100%; height:auto;" />
        </div>
        <div class="content" style="text-align:justify">
            <div style="color:#ff0000; font-size: 0.8rem;">
                <p>Mr. Ganni Venkatesh<br>User ID: MPM_1SG4J7</p>
            </div>

            <h2 style="text-align:center; color:#21825b"> <img src="<?= base_url('img/pdf_images/MP/3.png') ?>" alt="" > OTHERS </h2>


            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">


                <!-- 5 rows -->
                <tr>
                    <td style="background-color: #0d6c83; color: white; padding: 10px; border: 1px solid #ccc; width: 200px;">Communication Skills</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center;">P76</td>
                    <td style="background-color: #D2E0F2; border: 1px solid #ccc; text-align: center;  width: 100px; "></td>
                    <td style="background-color: #C2D9F4; border: 1px solid #ccc; text-align: center;  width: 100px;"></td>
                    <td style="background-color: #A5C6F1; border: 1px solid #ccc; text-align: center;  width: 100px;"><img src="<?= base_url('img/pdf_images/MP/b2.png') ?>" alt=""></td>
                </tr>
                <tr>
                    <td style="background-color: #0d6c83; color: white; padding: 10px; border: 1px solid #ccc;">Conflict Management</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center">P72</td>
                    <td style="background-color: #D2E0F2; border: 1px solid #ccc; text-align: center;"></td>
                    <td style="background-color: #C2D9F4; border: 1px solid #ccc; text-align: center;"><img src="<?= base_url('img/pdf_images/MP/b2.png') ?>" alt=""></td>
                    <td style="background-color: #A5C6F1; border: 1px solid #ccc; text-align: center;"></td>
                </tr>
                <tr>
                    <td style="background-color: #0d6c83; color: white; padding: 10px; border: 1px solid #ccc;">People Management</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center">P61</td>
                    <td style="background-color: #D2E0F2; border: 1px solid #ccc; text-align: center;"><img src="<?= base_url('img/pdf_images/MP/b2.png') ?>" alt=""></td>
                    <td style="background-color: #C2D9F4; border: 1px solid #ccc; text-align: center;"></td>
                    <td style="background-color: #A5C6F1; border: 1px solid #ccc; text-align: center;"></td>
                </tr>

            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">


                <!-- 5 rows -->
                <tr>
                    <td style="background-color: #ffffff; color: #000; padding: 10px; border: 1px solid #ccc; width: 200px;">Traits</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center;">Sten</td>
                    <td style=" border: 1px solid #ccc; text-align: center;  width: 100px; ">Low</td>
                    <td style="border: 1px solid #ccc; text-align: center;  width: 100px;">Moderate</td>
                    <td style="border: 1px solid #ccc; text-align: center;  width: 100px;">High</td>
                </tr>
                <tr>
                    <td style="background-color: #EC7936; color: white; padding: 10px; border: 1px solid #ccc;">Extraversion</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center">7</td>
                    <td style="background-color: #FBC19E; border: 1px solid #ccc; text-align: center;"></td>
                    <td style="background-color: #FAB07E; border: 1px solid #ccc; text-align: center;"><img src="<?= base_url('img/pdf_images/MP/o3.png') ?>" alt=""></td>
                    <td style="background-color: #F69350; border: 1px solid #ccc; text-align: center;"></td>
                </tr>
                <tr>
                    <td style="background-color: #EC7936; color: white; padding: 10px; border: 1px solid #ccc;">Trust</td>
                    <td style="padding: 10px; border: 1px solid #ccc; width: 100px; text-align: center">2</td>
                    <td style="background-color: #FBC19E; border: 1px solid #ccc; text-align: center;"></td>
                    <td style="background-color: #FAB07E; border: 1px solid #ccc; text-align: center;"><img src="<?= base_url('img/pdf_images/MP/o3.png') ?>" alt=""></td>
                    <td style="background-color: #F69350; border: 1px solid #ccc; text-align: center;"></td>
                </tr>


            </table>

            <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px;">
                <tr style="background-color: #e6f2f9;">
                    <td style="width: 25%; background-color: #d9f0fb; vertical-align: top; border-bottom:5px solid #ffffff">
                        <strong>Communication Skills</strong><br>
                        <span>High</span>
                    </td>
                    <td style="background-color: #ffffff; vertical-align: top;  border-top:5px solid #e6f2f9;">
                        A high score on this skill indicates that he can communicate using concise and relevant terms and phrases.<br>
                        * He has a good flair for written communication, and his e-mails are usually clear and easy to understand.<br>
                        * He can also communicate verbally in a manner that is forceful, unambiguous and drives his point home.
                    </td>
                </tr>
                <tr style="background-color: #e6f2f9;">
                    <td style="width: 25%; background-color: #d9f0fb; vertical-align: top;">
                        <strong>Conflict Management</strong><br>
                        <span>Moderate</span>
                    </td>
                    <td style="background-color: #ffffff; vertical-align: top;">
                        He has moderate conflict management skills.<br>
                        * He does not get uncomfortable during conflict situations, and tries to resolve differences between team members.<br>
                        * He believes in encouraging team members to express disagreements.<br>
                        * While he tries his best to keep personal preferences aside, at times, his own view may make it challenging for him to be non biased.
                    </td>
                </tr>
            </table>

        </div> 






    </body>
</html>
