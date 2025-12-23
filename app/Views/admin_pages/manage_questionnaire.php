<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage Questionnaire</h1>
</div>
<div class="wrapper-md">
    <div class="panel panel-default">
        <div class="panel-heading font-bold">Generate Question</div>
        <div class="panel-body">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white font-weight-bold text-center">
                    {{ pageTitle}}
                </div>
                <div class="card-body">
                    <form ng-submit="saveQuestionnaire(questionnaire.id)" name="questionnaireForm">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Factor Name</label>
                                    <select class="form-control border-primary"
                                            ng-model="questionnaireForm.testFactorId"
                                            ng-options="test_factor.id as test_factor.factor_name for test_factor in test_factor_list"
                                            required>
                                        <option value="" disabled>Select a Test Factor</option>
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Language</label>
                                            <select class="form-control border-primary"
                                                    ng-model="questionnaireForm.languageId"
                                                    ng-options="language.id as language.language for language in language_list track by language.id"
                                                    required>
                                                <option value="" disabled>Select a Language</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Question Type</label>
                                    <select class="form-control border-primary"
                                            ng-model="questionnaireForm.questionType"
                                            ng-options="type.id as type.name for type in questionTypes"
                                            required>
                                        <option value="" disabled>Select Question Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Is Demo</label>
                                            <select class="form-control border-primary"
                                                    ng-model="questionnaireForm.isDemo"
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
                            <!-- Left Column: Question Input -->
                            <div class="col-md-6">
                                <div class="form-group" ng-if="questionnaireForm.questionType">
                                    <!-- Dynamic Label -->
                                    <label ng-if="questionnaireForm.questionType === '1' || questionnaireForm.questionType === '2'">
                                        Enter Your Question:
                                    </label>
                                    <label ng-if="questionnaireForm.questionType === '3' || questionnaireForm.questionType === '4'">
                                        Upload Image Question:
                                    </label>

                                    <!-- Text Input for Question -->
                                    <div ng-if="questionnaireForm.questionType === '1' || questionnaireForm.questionType === '2'">
                                        <input type="text" class="form-control" ng-model="questionnaireForm.textQuestion"
                                               placeholder="Type your question here..." required>
                                    </div>

                                    <!-- File Input for Image Question -->
                                    <div style="margin-top: 10px;" ng-if="questionnaireForm.questionType === '3' || questionnaireForm.questionType === '4'">
                                        <input type="file" class="form-control-file" file-model="questionnaireForm.imageQuestion" onchange="angular.element(this).scope().previewImage(event, 'imageQuestion')">
                                        <img ng-if="questionnaireForm.imageQuestion" ng-src="{{questionnaireForm.imagePreview}}" class="img-thumbnail mt-2" width="150" height="150">

                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Number of Options -->
                            <div class="col-md-6">
                                <div class="form-group" ng-if="questionnaireForm.questionType">
                                    <label>Number of Options</label>
                                    <select class="form-control border-primary" ng-model="questionnaireForm.noOfQuestions"
                                            ng-options="option for option in noOfQuestionOptions">
                                        <option value="" disabled>Select No. of Questions</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" ng-if="questionnaireForm.noOfQuestions">
                            <div class="row mt-3 mb-3" ng-repeat="(index, question) in questionnaireForm.questions track by $index">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="col-md-2">Option {{ index + 1}}:</label>

                                    <!-- Text Input for Option -->
                                    <input type="text" class="form-control w-75" 
                                           ng-if="questionnaireForm.questionType === '1' || questionnaireForm.questionType === '4'"
                                           ng-model="question.text" 
                                           placeholder="Enter the option..."
                                           ng-required="!question.image">  <!-- Required if no image -->

                                    <!-- File Input for Option Image -->
                                    <input type="file" class="w-75"
                                           ng-if="questionnaireForm.questionType === '3' || questionnaireForm.questionType === '2'"
                                           file-model="question.image"
                                           ng-required="!question.text">
                                    <!-- Ensure image is shown if it exists -->
                                    <img ng-if="question.imagePreview && question.imagePreview !== ''" 
                                         ng-src="{{question.imagePreview}}" 
                                         class="img-thumbnail mt-2" 
                                         width="100" height="100">
                                </div>

                                <div class="col-md-2 d-flex align-items-center">
                                    <input type="radio" class="form-check-input ml-2"
                                           ng-model="$parent.questionnaireForm.correctAnswer"
                                           ng-value="index"
                                           ng-change="setCorrectAnswer(index)">
                                    <label class="form-check-label ml-2" style="margin-left: 10px; margin-top: 7px;">Correct</label>

                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <input type="text" class="form-control" ng-model="question.option_mark"
                                               placeholder="Mark" required>

                                </div>
                                <div>&nbsp;&nbsp;&nbsp;</div>
                            </div>

                        </div>



                        <div class="form-group">
                            <label>Status</label>
                            <div class="d-flex">
                                <label class="form-check-label mr-3">
                                    <input type="radio" class="form-check-input" ng-model="questionnaireForm.status" value="1"> Active
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" ng-model="questionnaireForm.status" value="0"> Inactive
                                </label>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-success mr-2" ng-disabled="isSubmitting">
                                {{ isSubmitting ? 'Saving...' : 'Save Question' }}
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" ng-click="cancelEdit()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-group {
        margin-bottom: 15px;
    }
    .form-check-input {
        margin-left: auto;
    }
    .btn-sm {
        padding: 8px 16px;
        font-size: 14px;
    }
    .card-header {
        text-align: center;
        font-size: 18px;
    }
    .form-control {
        border-radius: 5px;
    }
    .d-flex {
        display: flex;
        align-items: center;
    }
    .w-75 {
        width: 75%;
    }
    .mr-2 {
        margin-right: 8px;
    }
</style>
