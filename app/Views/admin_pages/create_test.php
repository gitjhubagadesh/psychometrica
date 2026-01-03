<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Test</h1>
</div>

<div class="wrapper-md">
    <!-- Form Section -->
    <div class="panel panel-default">
        <div class="panel-heading font-bold" style="cursor: pointer;" ng-click="isFormCollapsed = !isFormCollapsed">
            Create New
            <span class="pull-right">
                <i class="fa" ng-class="{'fa-chevron-down': !isFormCollapsed, 'fa-chevron-up': isFormCollapsed}"></i>
            </span>
        </div>
        <div class="panel-body" ng-show="!isFormCollapsed">
            <form ng-submit="saveTest(test.id)" name="testForm" id="testForm">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label">Creator Name</label>
                            <input type="text" class="form-control border-primary" 
                                   ng-model="test.creator_name" required>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label">Test Name</label>
                            <input type="text" class="form-control border-primary" 
                                   ng-model="test.test_name" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label for="testDescription" class="form-label">Test Instruction</label>
                        <textarea id="testDescription" class="form-control" rows="3"
                                  ng-model="test.test_description"
                                  placeholder="Enter Test Instruction"></textarea>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">User Prefix</label>
                            <input type="text" class="form-control border-primary" 
                                   ng-model="test.user_prefix" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Test Report</label>
                            <select class="form-control"
                                    ng-model="test.test_report_id"
                                    ng-options="report.id as report.test_report_name for report in test_reports"
                                    required>
                                <option value="">-- Select Report --</option>
                            </select>
                        </div>
                    </div>


                </div>
                <hr>
                <div class="form-group">
                    <label class="form-label">Select Test Factors:</label>
                    <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; border: 1px solid #ccc; padding: 10px;">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12" 
                                 ng-repeat="test_factor in test_factors">
                                <label class="i-checks i-checks-lg custom-checkbox">
                                    <input type="checkbox" 
                                           ng-model="test_factor.selected"
                                           ng-true-value="'1'"
                                           ng-false-value="'0'">
                                    <i></i> {{ test_factor.factor_name}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn"  
                        ng-class="test.id ? 'btn-primary' : 'btn-success'">
                    {{ test.id ? 'Update' : 'Add New' }}
                </button>

                <button type="button" class="btn btn-danger" ng-click="cancelEdit()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="panel panel-default mt-4">
        <div class="panel-heading font-bold">Test List</div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="userTable" class="table table-striped b-t b-b">
                    <thead>
                        <tr>
                            <th style="width:8%">ID</th>
                            <th style="width:20%">Test Name</th>
                            <th style="width:25%">Creator Name</th>
                            <th style="width:35%">Factors</th>
                            <th style="width:20%">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loading state -->
                        <tr ng-if="loading">
                            <td colspan="5" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                                <span class="ms-2">Loading data...</span>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr ng-if="!loading && tests.length === 0">
                            <td colspan="5" class="text-center text-muted">No records found</td>
                        </tr>

                        <!-- Data rows -->
                        <tr ng-repeat="test in tests" ng-if="!loading">
                            <td>{{ test.id}}</td>
                            <td>{{ test.test_name}}</td>
                            <td>{{ test.creator_name}}</td>
                            <td>
                                <div class="row">
                                    <div class="col-md-6" 
                                         ng-repeat="factor in test.factor_names.split(', ') track by $index">
                                        <span class="badge bg-primary text-white p-2 m-1">
                                            {{ factor}}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" ng-click="editTest(test.id)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" ng-click="deleteTest(test)">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?= view('admin_pages/pagination') ?>
        </div>
    </div>
</div>
<style>
    td {
        word-break: break-word; /* Force long words to wrap */
        white-space: normal;    /* Allow wrapping */
    }
</style>
