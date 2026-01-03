<div class="bg-light lter b-b wrapper-md">
    <div class="row">
        <div class="col-sm-8">
            <h1 class="m-n font-thin h3">Test Reports & Analytics</h1>
            <small class="text-muted">Manage and download user test reports</small>
        </div>
        <div class="col-sm-4 text-right">
            <button class="btn btn-sm btn-info m-t-sm" ng-click="fetchData()">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <button class="btn btn-sm btn-success m-t-sm" ng-click="exportAllReports()">
                <i class="fa fa-download"></i> Export All
            </button>
        </div>
    </div>
</div>

<div class="wrapper-md">
    <!-- Summary Statistics -->
    <div class="row m-b-md">
        <div class="col-lg-3 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <i class="fa fa-users fa-2x text-info"></i>
                    <div class="h3 m-t-sm">{{reportStats.total_users || 0}}</div>
                    <small class="text-uppercase text-muted">Total Users</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel panel-success">
                <div class="panel-body text-center">
                    <i class="fa fa-check-circle fa-2x"></i>
                    <div class="h3 m-t-sm">{{reportStats.completed || 0}}</div>
                    <small class="text-uppercase">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel panel-warning">
                <div class="panel-body text-center">
                    <i class="fa fa-clock-o fa-2x"></i>
                    <div class="h3 m-t-sm">{{reportStats.in_progress || 0}}</div>
                    <small class="text-uppercase">In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel panel-danger">
                <div class="panel-body text-center">
                    <i class="fa fa-times-circle fa-2x"></i>
                    <div class="h3 m-t-sm">{{reportStats.not_started || 0}}</div>
                    <small class="text-uppercase">Not Started</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-filter"></i> Advanced Filters
            <span class="pull-right">
                <button class="btn btn-xs btn-default" ng-click="clearFilters()">
                    <i class="fa fa-times"></i> Clear Filters
                </button>
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" class="form-control" placeholder="Search by name, email, ID..." ng-model="searchText">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Test Status</label>
                        <select class="form-control" ng-model="filters.status" ng-change="applyFilters()">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="not_started">Not Started</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Test Name</label>
                        <select class="form-control" ng-model="filters.test_name" ng-change="applyFilters()">
                            <option value="">All Tests</option>
                            <option ng-repeat="test in availableTests" value="{{test}}">{{test}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Company</label>
                        <select class="form-control" ng-model="filters.company" ng-change="applyFilters()">
                            <option value="">All Companies</option>
                            <option ng-repeat="company in availableCompanies" value="{{company}}">{{company}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" ng-model="filters.dateFrom" ng-change="applyFilters()">
                            <span class="input-group-addon">to</span>
                            <input type="date" class="form-control" ng-model="filters.dateTo" ng-change="applyFilters()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Reports Table -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-list"></i> User Test Reports
            <span class="pull-right text-muted">
                <small>Showing {{(pagination.currentPage - 1) * pagination.limit + 1}} to {{pagination.currentPage * pagination.limit > reportStats.total_users ? reportStats.total_users : pagination.currentPage * pagination.limit}} of {{reportStats.total_users || 0}} users</small>
            </span>
        </div>
        <div class="panel-body">
            <!-- Rows per page selector -->
            <div class="row m-b-md">
                <div class="col-sm-6">
                    <label>Show
                        <select ng-model="pagination.limit" ng-change="updateRowsPerPage()" class="form-control input-sm" style="width: 80px; display: inline-block;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </label>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-sm btn-primary" ng-click="bulkDownloadPDF()">
                        <i class="fa fa-file-pdf-o"></i> Bulk Download PDF
                    </button>
                    <button class="btn btn-sm btn-success" ng-click="bulkDownloadExcel()">
                        <i class="fa fa-file-excel-o"></i> Bulk Download Excel
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover b-t b-b">
                    <thead>
                        <tr>
                            <th style="width:3%">
                                <input type="checkbox" ng-model="selectAll" ng-change="toggleSelectAll()">
                            </th>
                            <th style="width:5%">ID</th>
                            <th style="width:8%">User ID</th>
                            <th style="width:15%">Name</th>
                            <th style="width:12%">Email</th>
                            <th style="width:10%">Company</th>
                            <th style="width:10%">Test Name</th>
                            <th style="width:10%">Progress</th>
                            <th style="width:8%">Status</th>
                            <th style="width:8%">Report Date</th>
                            <th style="width:11%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody ng-if="users && users.length > 0">
                        <tr ng-repeat="user in users" ng-class="{'bg-success-light': user.questionTaken == user.progress}">
                            <td>
                                <input type="checkbox" ng-model="user.selected">
                            </td>
                            <td><strong>{{user.id}}</strong></td>
                            <td>
                                <span class="label label-default">{{user.user_id}}</span>
                            </td>
                            <td>
                                <div class="text-ellipsis">
                                    <strong>{{user.uName}}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="text-ellipsis">
                                    <small>{{user.email}}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{user.cName}}</span>
                            </td>
                            <td>
                                <span class="label label-info">{{user.test_name}}</span>
                            </td>
                            <td>
                                <div class="progress" style="margin-bottom: 0; height: 25px;">
                                    <div class="progress-bar"
                                         ng-class="{
                                             'progress-bar-success': getProgressPercent(user.questionTaken, user.progress) == 100,
                                             'progress-bar-warning': getProgressPercent(user.questionTaken, user.progress) >= 50 && getProgressPercent(user.questionTaken, user.progress) < 100,
                                             'progress-bar-danger': getProgressPercent(user.questionTaken, user.progress) < 50
                                         }"
                                         role="progressbar"
                                         ng-style="{width: getProgressPercent(user.questionTaken, user.progress) + '%'}">
                                        <strong>{{user.questionTaken}}/{{user.progress}}</strong>
                                    </div>
                                </div>
                                <small class="text-muted">{{getProgressPercent(user.questionTaken, user.progress)}}%</small>
                            </td>
                            <td>
                                <span class="label"
                                      ng-class="{
                                          'label-success': user.questionTaken == user.progress && user.questionTaken > 0,
                                          'label-warning': user.questionTaken > 0 && user.questionTaken < user.progress,
                                          'label-default': user.questionTaken == 0
                                      }">
                                    <i class="fa"
                                       ng-class="{
                                           'fa-check-circle': user.questionTaken == user.progress && user.questionTaken > 0,
                                           'fa-clock-o': user.questionTaken > 0 && user.questionTaken < user.progress,
                                           'fa-circle-o': user.questionTaken == 0
                                       }"></i>
                                    {{getStatus(user.questionTaken, user.progress)}}
                                </span>
                            </td>
                            <td>
                                <small>{{user.reportingDate | date:'dd MMM yyyy'}}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-danger"
                                            ng-if="user.questionTaken > 0"
                                            ng-click="downloadMSPPDFReport(user.id, user.testReportId)"
                                            title="Download PDF">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </button>
                                    <button class="btn btn-xs btn-default"
                                            ng-if="user.questionTaken == 0"
                                            disabled
                                            title="No data available">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </button>

                                    <button class="btn btn-xs btn-success"
                                            ng-if="user.questionTaken > 0"
                                            ng-click="downloadExcelReport(user.id, user.testReportId)"
                                            title="Download Excel">
                                        <i class="fa fa-file-excel-o"></i>
                                    </button>
                                    <button class="btn btn-xs btn-default"
                                            ng-if="user.questionTaken == 0"
                                            disabled
                                            title="No data available">
                                        <i class="fa fa-file-excel-o"></i>
                                    </button>

                                    <button class="btn btn-xs btn-info"
                                            ng-click="viewUserDetails(user)"
                                            title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody ng-if="!users || users.length === 0">
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                <i class="fa fa-inbox fa-3x"></i>
                                <p>No users found matching the selected filters</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-sm-12 text-right text-center-xs">
                    <ul class="pagination pagination-sm m-t-none m-b-none">
                        <li ng-class="{ 'disabled': pagination.currentPage == 1 }">
                            <a href ng-click="prevPage()"><i class="fa fa-chevron-left"></i></a>
                        </li>

                        <li ng-repeat="page in getPageNumbers()"
                            ng-class="{ 'active': page === pagination.currentPage, 'disabled': page === '...' }">
                            <a href ng-click="page !== '...' && goToPage(page)">{{page}}</a>
                        </li>

                        <li ng-class="{ 'disabled': pagination.currentPage >= pagination.totalPages }">
                            <a href ng-click="nextPage()"><i class="fa fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div ng-if="isDownloading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
    <div class="text-center" style="background: white; padding: 30px; border-radius: 10px;">
        <i class="fa fa-spinner fa-spin fa-3x text-info"></i>
        <p class="m-t-md"><strong>Generating report...</strong></p>
        <p class="text-muted">Please wait while we prepare your download</p>
    </div>
</div>

<style>
    .text-ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    .bg-success-light {
        background-color: #f0f9ff !important;
    }
    .progress {
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
