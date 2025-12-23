<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Company</h1>
</div>
<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{ pageTitle}}</div>
                <div class="panel-body">
                    <form ng-submit="saveCompany(company.id)" name="companyForm">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control border-primary" ng-model="company.company_name" required>
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input type="url" class="form-control border-primary" ng-model="company.website" required>
                            <span class="error" ng-show="company.website.$error.required && company.website.$touched">
                                URL is required.
                            </span>
                            <span class="error" ng-show="company.website.$error.url && company.website.$touched">
                                Invalid URL format.
                            </span>
                        </div>
                        <div class="form-group" ng-if="company.logo_image_path">
                            <label>Current Logo</label><br>
                            <img ng-src="{{company.logo_image_path}}" width="100" height="50" alt="Company Logo">
                        </div>
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" id="logo_image_path" name="logo_image_path">
                        </div>

                        <!-- Status Selection (Active/Inactive) -->
                        <div class="form-group">
                            <label>Status</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" ng-model="company.status" value="1"> Active
                                </label>
                                <label class="form-check-label ml-3">
                                    <input type="radio" class="form-check-input" ng-model="company.status" value="0"> Inactive
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <button type="button" class="btn btn-sm btn-danger" ng-click="cancelEdit()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
