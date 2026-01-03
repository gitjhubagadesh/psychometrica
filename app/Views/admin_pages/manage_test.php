<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Test1</h1>
</div>

<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">New Test</div>
                <div class="panel-body">
                    <form ng-submit="addTestName(newTest.test_id)" name="companyForm">
                        <div class="row">
                            <!-- Test Name Input -->
                            <div class="col-md-4">
                                <label for="testName" class="form-label">Test Name</label>
                                <input type="text" id="testName" class="form-control" 
                                       ng-model="newTest.test_name" 
                                       placeholder="Enter Test Name" required>
                            </div>

                            <!-- Parent ID Input -->
                            <div class="col-md-4">
                                <label for="parentId" class="form-label">Parent ID (Optional)</label>
                                <input type="number" id="parentId" class="form-control"
                                       ng-model="newTest.parent_id"
                                       placeholder="Enter Parent ID">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="testDescription" class="form-label">Test Instruction</label>
                                <textarea id="testDescription" class="form-control" rows="3"
                                          ng-model="newTest.test_description"
                                          placeholder="Enter Test Description"></textarea>
                            </div>

                            <!-- Buttons with Margin-Top 24px -->
                            <div class="col-md-4 d-flex flex-wrap gap-2 mt-3" style="margin-top: 24px;">
                                <button type="submit" class="btn ng-binding" 
                                        ng-class="newTest.test_id ? 'btn-primary' : 'btn-success'">
                                    {{ newTest.test_id ? 'Update' : 'Add New' }}
                                </button>

                                <button type="button" class="btn btn-danger" 
                                        ng-click="resetForm()" 
                                        ng-show="newTest.test_id">
                                    Cancel
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:30%">Test Name</th>
                        <th>Parent Name</th>
                        <th style="width:5%">Status</th>
                        <th>Option</th>

                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="test in tests" ng-if="test.status !== undefined">
                        <td>{{ test.id}}</td>
                        <td>{{ test.test_name}}</td>
                        <td>{{ test.parent_name}}</td>
                        <td class="align-middle">
                            <label class="checkbox-inline i-checks">
                                <input type="checkbox" 
                                       ng-model="test.status" 
                                       ng-true-value="'1'"
                                       ng-false-value="'0'"
                                       ng-change="toggleStatus(test)">
                                <i></i>
                            </label>

                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" ng-click="editTestName(test.id)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" ng-click="deleteTestName(test)">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>