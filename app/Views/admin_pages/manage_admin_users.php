<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Admin</h1>
</div>
<div class="wrapper-md" ng-controller="EditUserController">
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Edit User</div>
                <div class="panel-body">
                    <form ng-submit="updateUser(user.id)">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control border-primary" ng-model="user.name" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control border-primary" ng-model="user.username" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control border-primary" ng-model="user.email" required>
                        </div>
                        <div class="form-group">
                            <label>New Password (Optional)</label>
                            <input type="password" class="form-control" ng-model="user.password" placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" ng-model="user.confirmPassword" placeholder="Confirm Password">
                            <small class="text-danger" ng-show="user.password && user.password !== user.confirmPassword">
                                Passwords do not match!
                            </small>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <button type="button" class="btn btn-sm btn-danger" ng-click="cancelEdit()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
