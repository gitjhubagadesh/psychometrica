<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Memory Questionnaire</h1>
</div>
<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-10">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{ pageTitle}}</div>
                <div class="panel-body">
                    <div class="container mt-10">
                        <form name="quiz" ng-submit="saveMemoryQuestionnaire(quiz.id)" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Factor Name</label>
                                        <select class="form-control border-primary" required
                                                ng-model="quiz.testFactorId"
                                                ng-options="test_factor.id as test_factor.factor_name for test_factor in test_factor_list"
                                                required>
                                            <option value="" disabled>Select a Test Factor</option>
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Disapearing Time</label>
                                                <select class="form-control" name="disapearingTime" 
                                                        ng-model="quiz.disapearingTime" ng-required="true">
                                                    <option value="" disabled selected>Select a time</option>
                                                    <option ng-repeat="n in [].constructor(60) track by $index" value="{{$index + 1}}">
                                                        {{$index + 1}} Minutes
                                                    </option>
                                                </select>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Demo</label>
                                                <select class="form-control border-primary" required
                                                        ng-model="quiz.isDemo"
                                                        required>
                                                    <option value="" disabled>Select an Option</option>
                                                    <option value="0">Demo</option>
                                                    <option value="1">Regular</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Language</label>
                                        <select class="form-control border-primary" required
                                                ng-model="quiz.languageId"
                                                ng-options="language.id as language.language for language in language_list track by language.id"
                                                required>
                                            <option value="" disabled>Select a Language</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mark</label>
                                        <select class="form-control" ng-model="quiz.questionMark" required>
                                            <option value="" disabled selected>Select a mark</option>
                                            <option ng-repeat="n in [].constructor(10) track by $index" value="{{$index + 1}}">
                                                {{$index + 1}}
                                            </option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <div class="d-flex">
                                            <label class="form-check-label mr-3">
                                                <input type="radio" class="form-check-input" ng-model="quiz.status" value="1"> Active
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" ng-model="quiz.status" value="0"> Inactive
                                            </label>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <hr class="custom-hr">
                            <!-- Upload Main Image -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Upload Main Image:</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" required accept="image/*" onchange="angular.element(this).scope().uploadImage(this)">

                                    <!-- ✅ Show preview using Base64 -->
                                    <div class="mt-2" ng-if="quiz.main_image_preview">
                                        <img ng-src="{{quiz.main_image_preview}}" class="img-fluid rounded border mt-2" style="max-width: 200px;" alt="Image Preview">
                                    </div>
                                </div>
                            </div>

                            <hr class="custom-hr">

                            <!-- Questions Section -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Questions:</label>
                                <div class="col-sm-9">
                                    <button type="button" class="btn btn-success" ng-click="addQuestion()">+ Add Question</button>
                                </div>
                            </div>

                            <div class="mt-4" ng-repeat="question in quiz.questions track by $index">
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label">Question {{$index + 1}}:</label>
                                    <div class="col-sm-7">
                                        <input type="file" class="form-control" accept="image/*"
                                               ngf-select="uploadQuestionImage($file, question)">
                                        <!-- Show Image Preview (Only One per Question) -->

                                    </div>
                                    <div class="col-sm-2 text-right">
                                        <button type="button" class="btn btn-danger btn-sm" ng-click="removeQuestion($index)">Remove</button>
                                    </div>
                                </div>

                                <h6 class="ml-3">Options:</h6>
                                <div class="form-group row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <div class="row mb-3" ng-repeat="pairIndex in [0, 1] track by $index">
                                            <!-- First Option -->
                                            <div class="col-sm-1 d-flex align-items-center justify-content-center">
                                                <input type="radio" name="correct{{$parent.$index}}" ng-model="question.correctOption" ng-value="pairIndex * 2">
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control mb-3" ng-model="question.options[pairIndex * 2].text" placeholder="Option {{(pairIndex * 2) + 1}}">
                                            </div>

                                            <!-- Second Option -->
                                            <div class="col-sm-1 d-flex align-items-center justify-content-center">
                                                <input type="radio" name="correct{{$parent.$index}}" ng-model="question.correctOption" ng-value="(pairIndex * 2) + 1">
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control mb-3" ng-model="question.options[(pairIndex * 2) + 1].text" placeholder="Option {{(pairIndex * 2) + 2}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="custom-hr">
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-sm-9">
                                <div class="col-sm-3"></div>
                                <button type="submit" class="btn btn-primary" ng-disabled="isSubmitting">
                                    <span ng-if="!isSubmitting">Create Memory Question</span>
                                    <span ng-if="isSubmitting">
                                        <i class="fa fa-spinner fa-spin"></i> Processing...
                                    </span>
                                </button>
                                <button type="button"
                                        class="btn btn-danger"
                                        ng-click="cancelEdit()">
                                    Cancel
                                </button>

                            </div>

                        </form>




                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<style>
    .custom-hr {
        border-top: 2px solid #cccccc; /* Darker gray color */
    }
</style>