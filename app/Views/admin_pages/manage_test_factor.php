<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Test Factor</h1>
</div>

<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">New Test Factor</div>
                <div class="panel-body">
                    <form ng-submit="addTestFactor(newTestFactor.factor_id)" name="testFactorForm">
                        <!-- Row 1 -->
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="factorName" class="form-label">Test Factor Name</label>
                                <input type="text" id="factorName" class="form-control" 
                                       ng-model="newTestFactor.factor_name" 
                                       placeholder="Enter Test Factor Name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="prefix" class="form-label">Factor Prefix</label>
                                <input type="text" id="prefix" class="form-control"
                                       ng-model="newTestFactor.prefix"
                                       ng-change="newTestFactor.prefix = newTestFactor.prefix.toUpperCase().slice(0, 3)"
                                       maxlength="3"
                                       placeholder="Enter Factor Prefix"
                                       required>
                            </div>


                        </div>

                        <!-- Row 2 -->
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="factorDescription" class="form-label">Factor Description</label>
                                <textarea id="factorDescription" class="form-control" rows="3"
                                          ng-model="newTestFactor.factor_description"
                                          placeholder="Enter Factor Description"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="timer" class="form-label">Timer</label>
                                <select class="form-control" name="disapearingTime" 
                                        ng-model="newTestFactor.timer">
                                    <option value="" selected>Select a disappearing time</option>
                                    <option ng-repeat="n in [].constructor(120) track by $index" 
                                            ng-value="($index + 1) / 2">
                                        {{ (($index + 1) / 2) | number:1}} Minutes
                                    </option>
                                </select>

                            </div>
                        </div>
                        <!-- Row 2 -->
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="factorDescription" class="form-label">Mandatory</label>
                                <label class="checkbox-inline i-checks"> 
                                    <input type="checkbox" 
                                           ng-model="newTestFactor.is_mandatory" 
                                           ng-true-value="'1'"
                                           ng-false-value="'0'">
                                    <i></i>
                                </label>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-4" style="margin-top: 10px;">
                                <button type="submit" class="btn"  
                                        ng-class="newTestFactor.factor_id ? 'btn-primary' : 'btn-success'">
                                    {{ newTestFactor.factor_id ? 'Update' : 'Add New' }}
                                </button>

                                <button type="button" class="btn btn-danger" 
                                        ng-click="resetForm()" 
                                        ng-show="newTestFactor.factor_id">
                                    Cancel
                                </button>

                                <input type="hidden" id="factortId" class="form-control" ng-model="newTestFactor.id">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <label>Show
                        <select ng-model="pagination.limit" ng-change="updateRowsPerPage()" class="form-control input-sm" style="width: 80px; display: inline-block;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </label>
                </div>
                <div class="col-sm-6 text-right">
                    <input type="text" ng-model="searchText" class="form-control input-sm" placeholder="Search..." style="width: 200px; display: inline-block;">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:30%">Test Factor Name</th>
                        <th style="width:15%">Factor Prefix</th>
                        <th style="width:10%">Status</th>
                        <th>Option</th>

                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="test_factor in test_factors" ng-if="test_factor.status !== undefined">
                        <td>{{ test_factor.id}}</td>
                        <td>{{ test_factor.factor_name}}</td>
                        <td>{{ test_factor.prefix}}</td>
                        <td class="align-middle">
                            <label class="checkbox-inline i-checks">
                                <input type="checkbox"
                                       ng-model="test_factor.status"
                                       ng-true-value="'1'"
                                       ng-false-value="'0'"
                                       ng-change="toggleStatus(test_factor)">
                                <i></i>
                            </label>

                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" ng-click="editTestFactor(test_factor.id)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" ng-click="deleteTestFacor(test_factor)">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
            <!-- Responsive Pagination -->
            <div class="col-sm-12 text-right text-center-xs">
                <ul class="pagination pagination-sm m-t-none m-b-none">
                    <li ng-class="{ 'disabled': pagination.currentPage == 1 }">
                        <a href ng-click="prevPage()"><i class="fa fa-chevron-left"></i></a>
                    </li>

                    <li ng-repeat="page in getPageNumbers()"
                        ng-class="{ 'active': page === pagination.currentPage, 'disabled': page === '...' }">
                        <a href ng-click="page !== '...' && goToPage(page)">{{ page}}</a>
                    </li>

                    <li ng-class="{ 'disabled': pagination.currentPage >= pagination.totalPages }">
                        <a href ng-click="nextPage()"><i class="fa fa-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>