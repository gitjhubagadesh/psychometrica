<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Admin</h1>
</div>
<div class="wrapper-md">
    <div class="panel panel-default">
        <div class="panel-heading">
            <button class="btn btn-success" ng-click="createNewUser()">
                <i class="fas fa-plus"></i> Create New
            </button>
        </div>
        <div class="table-responsive">
            <table id="userTable" class="table table-striped b-t b-b">
                <thead>
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:25%">Name</th>
                        <th style="width:25%">Username</th>
                        <th style="width:15%">Email</th>
                        <th style="width:15%">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="user in users">
                        <td>{{ user.id}}</td>
                        <td>{{ user.name}}</td>
                        <td>{{ user.username}}</td>
                        <td>{{ user.email}}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" ng-click="editUser(user.id)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" ng-click="deleteUser(user)">
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
