<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Generate User's</h1>
</div>

<div class="wrapper-md">
    <!-- Form Section -->
    <div class="panel panel-default">
        <div class="panel-heading font-bold">Generate User</div>
        <div class="panel-body">
            <form ng-submit="saveGenerateUser(generate.id)" name="companyForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">User Type</label>
                            <select class="form-control border-primary chosen-select" 
                                    ng-model="generate.user_type" 
                                    ng-options="user.id as user.section_name for user in user_sections"
                                    required>
                                <option value="" disabled>Select a User Section</option>
                            </select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Test List</label>
                            <select class="form-control border-primary chosen-select"
                                    ng-model="generate.testId"
                                    ng-options="test as test.test_name for test in test_list"
                                    required>
                                <option value="" disabled>Select a Test</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Number Of Users</label>
                            <input type="number" class="form-control border-primary"
                                   ng-model="generate.no_of_users"
                                   min="0" max="999"
                                   required
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)">
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Valid Months</label>
                            <select class="form-control border-primary" 
                                    ng-model="generate.valitity"
                                    ng-options="day for day in valid_months" required>
                                <option value="" disabled>Select a month</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6" ng-if="generate.user_type.toString() !== '1' && generate.user_type.toString() !== '3'">
                        <div class="form-group">
                            <label class="form-label">Company Name</label>
                            <select class="form-control border-primary chosen-select" 
                                    ng-model="generate.company_id" 
                                    ng-options="company.id as company.company_name for company in company_list" 
                                    required>
                                <option value="" disabled>Select a Company</option>
                            </select>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn"  
                        ng-class="generate.id ? 'btn-primary' : 'btn-success'">
                    {{ generate.id ? 'Update User' : 'Generate User' }}
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
                    <div class="col-md-2 d-flex justify-content-end" style="float: right;">
                        <button class="btn btn-dark" ng-click="extendUserValidity()">
                            <i class="fas fa-question-circle"></i> Extend Validity
                        </button>
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
                            <th style="width:10%">User ID</th>
                            <th style="width:10%">User Name</th>
                            <th style="width:10%">Company Name</th>
                            <th style="width:10%">Start<br>End Date</th>
                            <th style="width:10%">Remaining Days</th>
                            <th style="width:5%">
                                <input type="checkbox" ng-model="selectAll" ng-change="toggleAllExtendValidity()">
                            </th>
                            <th style="width:5%"></th>
                            <th style="width:10%">User Type</th>
                            <th style="width:10%">Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(index, user) in filteredUsers = (users | filter:searchText) | limitTo: pageSize : (currentPage-1) * pageSize" 
                            ng-style="{'background-color': user.color_code}">

                            <td>{{ user.id}}</td>
                            <td>{{ user.user_id}}</td>
                            <td>{{ user.user_name}}</td>
                            <td>{{ user.company_name}}</td>
                            <td>
                                {{ user.validity_from}}<br>{{ user.validity_to}}
                            </td>
                            <td>
                                <strong>{{ getRemainingDays(user.validity_from, user.validity_to)}} days</strong>
                            </td>
                            <td>
                                <input type="checkbox" class="center" ng-model="user.extendValidity">
                            </td>
                            <td>
                                <span ng-if="user.status === 1">
                                    <i class="fas fa-check-circle text-success"></i>
                                </span>
                                <span ng-if="user.status === 0">
                                    <i class="fas fa-times-circle text-danger"></i>
                                </span>
                            </td>

                            <td>{{ user.section_name}}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" ng-click="deleteUser(user)">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                                <button class="btn btn-sm btn-success" ng-if="isFirstInGroup(user, index)" ng-click="downloadUserGroup(user.group_id)">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php echo view('admin_pages/pagination'); ?>
        </div>
    </div>
</div>


