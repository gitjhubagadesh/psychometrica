<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Test</h1>
</div>
<div class="wrapper-md">
    <div class="panel panel-default">
        <button class="btn btn-dark" ng-click="createNew()">
            <i class="fas fa-plus"></i> Create New
        </button>
        <div class="table-responsive">
            <table id="userTable" class="table table-striped b-t b-b">
                <thead>
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:15%">Test Name</th>
                        <th style="width:25%">Creator Name</th>
                        <th style="width:25%">Factors</th>
                        <th style="width:15%">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="test in tests">
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
                            <button class="btn btn-sm btn-warning" ng-click="editCompany(company.id)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" ng-click="deleteCompany(company)">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Responsive Pagination -->
            <div class="col-sm-12 text-right text-center-xs">                
                <ul class="pagination pagination-sm m-t-none m-b-none">
                    <li ng-class="{ 'disabled': currentPage == 1 }">
                        <a href ng-click="prevPage()"><i class="fa fa-chevron-left"></i></a>
                    </li>

                    <li ng-repeat="page in getPageNumbers()" 
                        ng-class="{ 'active': page === currentPage }">
                        <a href ng-click="goToPage(page)">{{ page}}</a>
                    </li>

                    <li ng-class="{ 'disabled': currentPage >= totalPages }">
                        <a href ng-click="nextPage()"><i class="fa fa-chevron-right"></i></a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>