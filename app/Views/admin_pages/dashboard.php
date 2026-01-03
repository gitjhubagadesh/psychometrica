<div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="
                app.settings.asideFolded = false;
                app.settings.asideDock = false;
     ">
    <!-- main -->
    <div class="col">
        <!-- main header -->
        <div class="bg-light lter b-b wrapper-md">
            <div class="row">
                <div class="col-sm-8 col-xs-12">
                    <h1 class="m-n font-thin h3 text-black">Dashboard</h1>
                    <small class="text-muted">Welcome to Psychometrica Portal - Last updated: {{lastUpdated | date:'medium'}}</small>
                </div>
                <div class="col-sm-4 col-xs-12 text-right">
                    <button class="btn btn-sm btn-info" ng-click="refreshDashboard()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-show="loading">
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-3x text-info"></i>
                <p class="text-muted">Loading dashboard data...</p>
            </div>
        </div>
        <div class="wrapper-md" ng-hide="loading">
            <!-- Primary Stats Row -->
            <div class="row m-b-md">
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="pull-left">
                                    <i class="fa fa-list-alt fa-3x text-primary"></i>
                                </div>
                                <div class="pull-right text-right">
                                    <div class="h2 m-t-none m-b-none text-primary">{{total_tests}}</div>
                                    <small class="text-muted text-uppercase">Total Tests</small>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <a href="#!/create-test" class="text-muted">View Details <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="pull-left">
                                    <i class="fa fa-question-circle fa-3x text-success"></i>
                                </div>
                                <div class="pull-right text-right">
                                    <div class="h2 m-t-none m-b-none text-success">{{total_questions}}</div>
                                    <small class="text-muted text-uppercase">Total Questions</small>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <a href="#!/questionarre-list" class="text-muted">View Questions <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="pull-left">
                                    <i class="fa fa-users fa-3x text-info"></i>
                                </div>
                                <div class="pull-right text-right">
                                    <div class="h2 m-t-none m-b-none text-info">{{registered_users}}</div>
                                    <small class="text-muted text-uppercase">Total Users</small>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <a href="#!/generate-user" class="text-muted">Manage Users <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="pull-left">
                                    <i class="fa fa-building fa-3x text-warning"></i>
                                </div>
                                <div class="pull-right text-right">
                                    <div class="h2 m-t-none m-b-none text-warning">{{companies_count}}</div>
                                    <small class="text-muted text-uppercase">Total Companies</small>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <a href="#!/manage-companies" class="text-muted">View Companies <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats Row -->
            <div class="row m-b-md">
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-info">
                        <div class="panel-body text-center">
                            <i class="fa fa-cubes fa-2x"></i>
                            <div class="h3 m-t-sm">{{total_test_factors}}</div>
                            <small class="text-uppercase">Test Factors</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-body text-center">
                            <i class="fa fa-check-circle fa-2x"></i>
                            <div class="h3 m-t-sm">{{attempts_data}}</div>
                            <small class="text-uppercase">Test Attempts (30 Days)</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-warning">
                        <div class="panel-body text-center">
                            <i class="fa fa-clock-o fa-2x"></i>
                            <div class="h3 m-t-sm">{{active_today}}</div>
                            <small class="text-uppercase">Active Today</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="panel panel-danger">
                        <div class="panel-body text-center">
                            <i class="fa fa-percent fa-2x"></i>
                            <div class="h3 m-t-sm">{{completion_rate}}%</div>
                            <small class="text-uppercase">Completion Rate</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart"></i> User Statistics
                        </div>
                        <div class="panel-body">
                            <canvas id="userStatsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bolt"></i> Quick Actions
                        </div>
                        <div class="panel-body">
                            <a href="#!/create-test" class="btn btn-block btn-primary m-b-sm">
                                <i class="fa fa-plus"></i> Create New Test
                            </a>
                            <a href="#!/questionarre-list" class="btn btn-block btn-success m-b-sm">
                                <i class="fa fa-question"></i> Manage Questions
                            </a>
                            <a href="#!/generate-user" class="btn btn-block btn-info m-b-sm">
                                <i class="fa fa-user-plus"></i> Generate Users
                            </a>
                            <a href="#!/main-reports" class="btn btn-block btn-warning">
                                <i class="fa fa-file-text"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-history"></i> Recent Test Completions
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>User Name</th>
                                            <th>Test Name</th>
                                            <th>Company</th>
                                            <th>Completed At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody ng-if="recent_completions.length > 0">
                                        <tr ng-repeat="completion in recent_completions">
                                            <td>{{completion.name}}</td>
                                            <td>{{completion.test_name}}</td>
                                            <td>{{completion.company_name}}</td>
                                            <td>{{completion.test_finish_time | date:'short'}}</td>
                                            <td>
                                                <span class="label label-success">Completed</span>
                                            </td>
                                            <td>
                                                <a href="#!/main-reports" class="btn btn-xs btn-info">
                                                    <i class="fa fa-eye"></i> View Report
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody ng-if="recent_completions.length === 0">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No recent completions found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / main -->
</div>

