<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Main Report</h1>
</div>
<div class="wrapper-md">
    <!-- Form Section -->
    <div class="panel panel-default mt-4">
        <div class="panel-heading font-bold">User List</div>
        <div class="panel-body">
            <div class="table-responsive">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control rounded-pill px-4" placeholder="🔍 Search..." ng-model="searchText">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                &nbsp;
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        &nbsp;
                    </div>
                </div>

                <table id="userTable" class="table b-t b-b">
                    <thead>
                        <tr>
                            <th style="width:5%">ID</th>
                            <th style="width:7%">User ID</th>
                            <th style="width:10%">Name</th>
                            <th style="width:8%">Company Name</th>
                            <th style="width:10%">Email</th>
                            <th style="width:5%">Progress</th>
                            <th style="width:6%">
                                Report Date
                            </th>
                            <th style="width:5%">PDF</th>
<!--                            <th style="width:5%">DevInput</th>-->
                            <th style="width:5%">EXCEL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(index, user) in filteredUsers = (users | filter:searchText) | limitTo: pageSize : (currentPage-1) * pageSize" 
                            ng-style="{'background-color': user.color_code}">

                            <td>{{ user.id}}</td>
                            <td>{{ user.user_id}}</td>
                            <td class="wrap-text">
                                {{ user.uName}}
                            </td>
                            <td>{{ user.cName}}</td>
                            <td class="wrap-text">
                                {{ user.email}}
                            </td>

                            <td>
                                {{user.questionTaken}} / {{user.progress}}
                            </td>

                            <td>
                                {{user.reportingDate}}
                            </td>
                            <td class="text-center">
                                <i class="fas fa-file-pdf pdf-icon fa-2x" ng-if="user.questionTaken > 0" 
                                   ng-click="downloadMSPPDFReport(user.id, user.testReportId)" style="cursor: pointer;">
                                </i>
                                <i class="fas fa-file-pdf pdf-icon fa-2x text-muted" 
                                   ng-if="user.questionTaken == 0" 
                                   style="cursor: not-allowed;"></i>
                            </td>

<!--                            <td class="text-center">
                                <i class="fas fa-file-pdf-o pdf-icon fa-2x"></i>
                            </td>-->
                            <td class="text-center">
                                <i class="fas fa-file-excel text-success fa-2x" 
                                   ng-if="user.questionTaken > 0"
                                   ng-click="downloadExcelReport(user.id, user.testReportId)" 
                                   style="cursor: pointer;"></i>
                                <!-- Greyed-out icon when questionTaken is 0 -->
                                <i class="fas fa-file-excel text-muted fa-2x" 
                                   ng-if="user.questionTaken == 0" 
                                   style="cursor: not-allowed;"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php echo view('admin_pages/pagination'); ?>
        </div>
    </div>
</div>
<style>
    .wrap-text {
        word-break: break-word;
        white-space: normal;
    }
    .pdf-icon {
        color: red;
    }
    .bar-container {
        width: 100%;
        height: 20px;
        background-color: white;
        margin: 10px 0;
    }
    .bar {
        height: 100%;
        background-color: green;
    }
    .bar-text {
        position: absolute;
        width: 100%;
        text-align: center;
        color: white;
        line-height: 30px;
        font-weight: bold;
    }
</style>