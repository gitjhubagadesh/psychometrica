<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Psychometrica Assessment Portal</title>
        <meta name="description" content="psychometric test, online psychometric test, online psychometric assessment, psychometric assessments for recruitment in India, Psychometric assessment for corporate, India" />

        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-route.min.js"></script>

        <!-- Include ngSanitize -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-sanitize.min.js"></script>
        <script src="js/quiz_app.js"></script>
        <script src="js/security.js"></script>
        <!-- SweetAlert2 CSS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .swal2-container {
                backdrop-filter: blur(5px); /* ✅ Blurred background */
            }
        </style>

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&family=Lato&display=swap" rel="stylesheet">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">

        <!-- Animate -->
        <link rel="stylesheet" href="<?= base_url('assets/css/animate.min.css'); ?>">

        <!-- Custom Style -->
        <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">

        <style>
            /* Centering the whole page */
            body {
                margin: 0;
                padding: 0;
                font-family: 'Jost', sans-serif;
                background-color: #f9f9f9;
            }

            .login_container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            /* Logo styling */
            .form_logo {
                margin-bottom: 20px;
                background-color: white;
                padding: 10px 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                max-width: 100%;
                width: auto;
            }

            .form_logo img {
                max-width: 250px;
                height: auto;
            }

            /* Login box */
            .login_form {
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                width: 100%;
                max-width: 400px;
            }


            /* Headline */
            .login_form h4 {
                font-weight: 700;
                margin-bottom: 25px;
            }

            /* Input styling */
            .form-control {
                font-size: 14px;
                padding: 10px;
                border-radius: 6px;
                border: 1px solid #ccc;
            }

            .form-control::placeholder {
                color: #888;
                font-style: italic;
            }

            /* Submit button */
            button[type="submit"] {
                height: 45px;
                font-weight: bold;
                border-radius: 6px;
            }

            /* Responsive logo adjustments */
            @media (max-width: 768px) {
                .form_logo {
                    top: 15px;
                    padding: 10px;
                    width: 90%;
                }

                .login_container {
                    padding-top: 120px;
                }

                .login_form {
                    padding: 20px;
                    max-width: 90%;
                }
            }

            @media (max-width: 480px) {
                .form_logo {
                    padding: 8px;
                    font-size: 14px;
                }

                .login_form {
                    padding: 15px;
                    max-width: 95%;
                }
            }

            .customNextBtn {
                font-size: 0.925rem !important;
                padding: 0.425rem;
                font-weight: normal;
                background-color: #ff8e0c;
            }


        </style>
    </head>
<!--    <body ng-app="myApp" ng-controller="SecurityController">-->
<body ng-app="myApp">

<!--        <div ng-if="!isOnline" class="alert alert-danger text-center">
            ⚠️ You are offline. Your progress may not be saved.
        </div>-->


        <div class="wrapper position-relative overflow-hidden">
            <div class="container-md-fluid p-3 p-lg-0 me-5">
                <div ng-view></div>
            </div>

        </div>
        <!-- Scripts -->
        <script src="<?= base_url('assets/js/jquery-3.6.0.min.js'); ?>"></script>
        <!-- Countdown-js include -->
        <script src="<?= base_url('assets/js/countdown.js'); ?>"></script>
        <!-- Bootstrap-js include -->
        <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
        <!-- jQuery-validate-js include -->
        <script src="<?= base_url('assets/js/jquery.validate.min.js'); ?>"></script>
        <!-- Custom-js include -->
        <script src="<?= base_url('assets/js/script.js'); ?>"></script>

    </body>
</html>
