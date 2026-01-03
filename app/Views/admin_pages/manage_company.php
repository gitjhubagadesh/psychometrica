<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Company</h1>
</div>
<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-10">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{ pageTitle}}</div>
                <div class="panel-body">
                    <form ng-submit="saveCompany(company.id)" name="companyForm">
                        <div class="row">
                            <div class="col-md-6">
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
                                <div class="form-group">
                                    <label>Contact Name</label>
                                    <input type="text" class="form-control border-primary" ng-model="company.contact_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Phone</label>
                                    <input type="text" 
                                           name="contact_phone" 
                                           class="form-control border-primary" 
                                           ng-model="company.contact_phone" 
                                           ng-pattern="/^[0-9]*$/" 
                                           required>
                                    <!-- Validation Messages -->
                                    <span class="error" ng-show="companyForm.contact_phone.$error.required && companyForm.contact_phone.$touched">
                                        Phone number is required.
                                    </span>
                                    <span class="error" ng-show="companyForm.contact_phone.$error.pattern && companyForm.contact_phone.$touched">
                                        Invalid phone number. Must be exactly 10 digits.
                                    </span>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Email</label>
                                    <input type="email" class="form-control border-primary" ng-model="company.contact_email" required>
                                </div>
                                <div class="form-group">
                                    <label>Branding</label><br>

                                    <label class="me-3">
                                        <input type="radio" 
                                               name="branding" 
                                               ng-model="company.branding" 
                                               value="white label report" 
                                               required>
                                        White Label
                                    </label>

                                    <label>
                                        <input type="radio" 
                                               name="branding" 
                                               ng-model="company.branding" 
                                               value="co branding report" 
                                               required>
                                        Co Branding
                                    </label>
                                </div>

                                <div class="form-group" ng-if="company.logo_image_path">
                                    <label>Current Logo</label><br>
                                    <img ng-src="{{company.logo_image_path}}" width="100" height="50" alt="Company Logo">
                                </div>
                                <div class="form-group">
                                    <label>Logo</label>
                                    <input type="file" id="logo_image_path" name="logo_image_path">
                                </div>
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
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                <button type="button" class="btn btn-sm btn-danger" ng-click="cancelEdit()">Cancel</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
