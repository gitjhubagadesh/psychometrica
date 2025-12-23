<?php $session = session(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Psychometrica -Admin Portal</title>
        <meta name="description" content="psychometric test, online psychometric test, online psychometric assessment, psychometric assessments for recruitment in India, Psychometric assessment for corporate, India" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="stylesheet" href="../libs/assets/animate.css/animate.css" type="text/css" />
        <link rel="stylesheet" href="../libs/assets/font-awesome/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="../libs/assets/simple-line-icons/css/simple-line-icons.css" type="text/css" />
        <link rel="stylesheet" href="../libs/jquery/bootstrap/dist/css/bootstrap.css" type="text/css" />
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/font.css" type="text/css" />
        <link rel="stylesheet" href="css/app.css" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-route.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://unpkg.com/ng-file-upload@12.2.13/dist/ng-file-upload.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="js/app.js"></script>  <!-- Ensure this is after angular-route -->
        <script src="js/controllers/QuestionarreController.js"></script>
        <!-- DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <!-- ✅ Load jQuery First -->

        <!-- SweetAlert2 CSS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .swal2-container {
                backdrop-filter: blur(5px); /* ✅ Blurred background */
            }
            .navi ul.nav li li a {
                padding-left: 25px !important;
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    </head>

    <body ng-app="myApp">
        <div class="app app-header-fixed ">
            <!-- header -->
            <header id="header" class="app-header navbar" role="menu">
                <!-- navbar header -->
                <div class="navbar-header bg-dark">
                    <button class="pull-right visible-xs dk" ui-toggle-class="show" target=".navbar-collapse">
                        <i class="glyphicon glyphicon-cog"></i>
                    </button>
                    <button class="pull-right visible-xs" ui-toggle-class="off-screen" target=".app-aside" ui-scroll="app">
                        <i class="glyphicon glyphicon-align-justify"></i>
                    </button>
                    <!-- brand -->
                    <a href="#/" class="navbar-brand text-lt">
                        <i class="icon-psychometrica me-2"></i>
                        <img src="img/logo.png" alt="." class="hide">
                        <span class="hidden-folded m-l-xs">Psychometrica</span>
                    </a>
                    <!-- / brand -->
                </div>
                <!-- / navbar header -->
                <!-- navbar collapse -->
                <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
                    <!-- nabar right -->
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
                                <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                                    <img src="img/a0.jpg" alt="...">
                                    <i class="on md b-white bottom"></i>
                                </span>
                                <span class="hidden-sm hidden-md"><?= $session->get('username'); ?></span> <b class="caret"></b>
                            </a>
                            <!-- dropdown -->
                            <ul class="dropdown-menu animated fadeInRight w">
                                <li>
                                    <a href="<?= base_url('logout'); ?>">Logout</a>
                                </li>
                            </ul>
                            <!-- / dropdown -->
                        </li>
                    </ul>
                    <!-- / navbar right -->
                </div>
                <!-- / navbar collapse -->
            </header>
            <!-- / header -->
            <!-- aside -->
            <aside id="aside" class="app-aside hidden-xs bg-dark">
                <div class="aside-wrap">
                    <div class="navi-wrap">
                        <!-- user -->
                        <div class="clearfix hidden-xs text-center hide" id="aside-user">
                            <div class="dropdown wrapper">
                                <a href="app.page.profile">
                                    <span class="thumb-lg w-auto-folded avatar m-t-sm">
                                        <img src="img/a0.jpg" class="img-full" alt="...">
                                    </span>
                                </a>
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle hidden-folded">
                                    <span class="clear">
                                        <span class="block m-t-sm">
                                            <strong class="font-bold text-lt">John.Smith</strong> 
                                            <b class="caret"></b>
                                        </span>
                                        <span class="text-muted text-xs block">Art Director</span>
                                    </span>
                                </a>
                                <!-- dropdown -->
                                <ul class="dropdown-menu animated fadeInRight w hidden-folded">
                                    <li class="wrapper b-b m-b-sm bg-info m-t-n-xs">
                                        <span class="arrow top hidden-folded arrow-info"></span>
                                        <div>
                                            <p>300mb of 500mb used</p>
                                        </div>
                                        <div class="progress progress-xs m-b-none dker">
                                            <div class="progress-bar bg-white" data-toggle="tooltip" data-original-title="50%" style="width: 50%"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <a href>Settings</a>
                                    </li>
                                    <li>
                                        <a href="page_profile.html">Profile</a>
                                    </li>
                                    <li>
                                        <a href>
                                            <span class="badge bg-danger pull-right">3</span>
                                            Notifications
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="page_signin.html">Logout</a>
                                    </li>
                                </ul>
                                <!-- / dropdown -->
                            </div>
                            <div class="line dk hidden-folded"></div>
                        </div>
                        <!-- / user -->
                        <!-- nav -->
                        <nav ui-nav class="navi clearfix">
                            <ul class="nav">
                                <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                                    <span>Navigation</span>
                                </li>
                                <li>
                                    <a href="#!">
                                        <i class="glyphicon glyphicon-stats icon text-primary-dker"></i>
                                        <span class="font-bold">Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#!/manage-admin">
                                        <i class="fa fa-users"></i>
                                        <span class="font-bold">Manage Admin</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#!/manage-companies">
                                        <i class="fa fa-building icon text-info-lter"></i>
                                        <span class="font-bold">Manage Company</span>
                                    </a>
                                </li>
                                <li class="line dk"></li>
                                <!--                                <li>
                                                                    <a href="#!/manage-tests">
                                                                        <i class="fa fa-building icon text-info-lter"></i>
                                                                        <span class="font-bold">Manage Test Report</span>
                                                                    </a>
                                                                </li>-->
                                <li>
                                    <a href="#!/manage-test-factor">
                                        <i class="fa fa-building icon text-info-lter"></i>
                                        <span class="font-bold">Manage Test Factor</span>
                                    </a>
                                </li>
                                <li class="line dk"></li>

                                <li>
                                    <a href="#!/questionarre-list">
                                        <i class="fa fa-users"></i>
                                        <span class="font-bold">Questionarre</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#!/create-test">
                                        <i class="fa fa-graduation-cap"></i>
                                        <span class="font-bold">Manage Test</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#!/master-test">
                                        <i class="fa fa-graduation-cap"></i>
                                        <span class="font-bold">Master Test</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#!/generate-user">
                                        <i class="fa fa-users"></i>
                                        <span class="font-bold">Generate User</span>
                                    </a>
                                </li>
                                <li class="line dk"></li>
                                <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                                    <span>Reports</span>
                                </li>

                                <li>
                                    <a href="#!/main-reports">
                                        <i class="fa fa-stack"></i>
                                        <span class="font-bold">Main Report</span>
                                    </a>
                                </li>
                                <!-- <li class="line dk"></li>
                                <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                                    <span>Reports</span> 
                                </li>

                                <?php
                                $groupedReports = [];
                                foreach ($reportMenu as $report) {
                                    $type = $report['report_type'] ?: 'Other';
                                    $groupedReports[$type][] = $report;
                                }
                                ?>

                                <?php foreach ($groupedReports as $type => $reports): ?>
                                        <li>
                                            <a href class="auto">
                                                <span class="pull-right text-muted">
                                                    <i class="fa fa-fw fa-angle-right text"></i>
                                                    <i class="fa fa-fw fa-angle-down text-active"></i>
                                                </span>
                                                <i class="glyphicon glyphicon-cog icon"></i>
                                                <span><?= htmlspecialchars($type) ?></span>
                                            </a>

                                            <ul class="nav nav-sub dk">
                                    <?php foreach ($reports as $report): ?>
                                                        <li>
                                                            <a href="#!/main-reports/<?= $report['id'] ?>">
                                                                <i class="glyphicon glyphicon-file icon"></i>
                                                                <span><?= htmlspecialchars($report['test_report_name']) ?></span>
                                                            </a>
                                                        </li>
                                    <?php endforeach; ?>
                                            </ul>
                                        </li>
                                <?php endforeach; ?>
                                -->
                            </ul>
                        </nav>
                        <!-- nav -->
                    </div>
                </div>
            </aside>
            <!-- / aside -->
            <!-- content -->
            <div id="content" class="app-content" role="main">
                <div class="app-content-body ">
                    <div ng-view></div>
                </div>
            </div>
            <!-- /content -->
            <!-- footer -->
            <footer id="footer" class="app-footer" role="footer">
                <div class="wrapper b-t bg-light">
                    <span class="pull-right">2.2.0 <a href ui-scroll="app" class="m-l-sm text-muted"><i class="fa fa-long-arrow-up"></i></a></span>
                    &copy; <?= date('Y') ?> Copyright.
                </div>
            </footer>
            <!-- / footer -->
        </div>
        <script src="../libs/jquery/jquery/dist/jquery.js"></script>
        <script src="../libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
        <script src="js/ui-load.js"></script>
        <script src="js/ui-jp.config.js"></script>
        <script src="js/ui-jp.js"></script>
        <script src="js/ui-nav.js"></script>
        <script src="js/ui-toggle.js"></script>
        <script src="js/ui-client.js"></script>
    </body>