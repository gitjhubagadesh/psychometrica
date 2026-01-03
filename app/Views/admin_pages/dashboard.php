<div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="
                app.settings.asideFolded = false;
                app.settings.asideDock = false;
     ">
    <!-- main -->
    <div class="col">
        <!-- main header -->
        <div class="bg-light lter b-b wrapper-md">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="m-n font-thin h3 text-black">Dashboard</h1>
                    <small class="text-muted">Welcome to Psychometrica portal</small>
                </div>

            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md">
            <!-- stats -->
            <div class="row">
                <div class="col-md-10">
                    <div class="row row-sm text-center">
                        <div class="col-xs-6">
                            <div class="panel padder-v item">
                                <div class="h1 text-info font-thin h1">{{total_tests}}</div>
                                <span class="text-black text-1x text-capitalize">Total tests</span>
                                <div class="top text-right w-full">
                                    <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <a href class="block panel padder-v bg-primary item">
                                <span class="text-white font-thin h1 block">{{total_questions}}</span>
                                <span class="text-white text-1x text-capitalize">Total questions</span>
                                <span class="bottom text-right w-full">
                                    <i class="fa fa-cloud-upload text-muted m-r-sm"></i>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <a href class="block panel padder-v bg-info item">
                                <span class="text-white font-thin h1 block">{{registered_users}}</span>
                                <span class="text-black text-1x text-capitalize">Total Users</span>
                                <span class="top">
                                    <i class="fa fa-caret-up text-warning m-l-sm m-r-sm"></i>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <div class="panel padder-v item">
                                <div class="font-thin h1">{{attempts_data}}</div>
                                <span class="text-black text-1x text-capitalize">Test Attempts (Last 30 Days)</span>
                                <div class="bottom">
                                    <i class="fa fa-caret-up text-warning m-l-sm m-r-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 m-b-md">
                            <div class="r bg-light dker item hbox no-border">
                                <div class="col dk padder-v r-r">
                                     <canvas id="userStatsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- / stats -->


        </div>
    </div>
    <!-- / main -->
</div>

