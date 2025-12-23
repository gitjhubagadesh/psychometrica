<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Paragraph Questionnaire</h1>
</div>

<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-10">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{ pageTitle}}</div>
                <form name="quiz" ng-submit="saveParagraphQuestionnaire()">
                    <div class="panel-body">


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


                        <!-- Paragraph Input -->
                        <div class="form-group">
                            <label for="paragraph"><strong>Enter Paragraph</strong></label>
                            <textarea id="paragraph" class="form-control" ng-model="paragraph" rows="5" placeholder="Enter the paragraph or passage here..." required></textarea>
                        </div>

                        <hr class="custom-hr">

                        <!-- Questions -->


                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold"></label>
                                <div class="col-sm-12">

                                    <!-- Repeat each Question -->
                                    <div class="panel panel-info mb-3" ng-repeat="question in questions track by $index">
                                        <div class="panel-heading">
                                            <strong>Question {{$index + 1}}</strong>
                                        </div>
                                        <div class="panel-body">
                                            <!-- Question Text -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Question Text:</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" ng-model="question.text" placeholder="Enter question text" required />
                                                </div>
                                            </div>

                                            <!-- Options List -->
                                            <div class="form-group row" ng-repeat="option in question.options track by $index">
                                                <!-- Label only on first option -->
                                                <label class="col-sm-2 col-form-label" ng-if="$index === 0">Options:</label>
                                                <label class="col-sm-2 col-form-label" ng-if="$index !== 0"></label>

                                                <div class="col-sm-10">
                                                    <div class="row">
                                                        <!-- Option Text -->
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control" ng-model="option.text" placeholder="Option {{$index + 1}}" required />
                                                        </div>

                                                        <!-- Correct Option Radio -->
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <label class="mb-0">
                                                                <input type="radio"
                                                                       ng-model="question.correctOption"
                                                                       ng-value="$index"
                                                                       name="correctOption_{{$parent.$index}}" />
                                                                Correct
                                                            </label>
                                                        </div>

                                                        <!-- Remove Option Button -->
                                                        <div class="col-md-3">
                                                            <button type="button" class="btn btn-danger" ng-click="removeOption(question, $index)">
                                                                <i class="fa fa-times"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">&nbsp;&nbsp;</label>
                                                <div class="col-sm-10">
                                                    <button type="button" class="btn btn-sm btn-success" ng-click="addOption(question)">
                                                        <i class="fa fa-plus"></i> Add Option
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">&nbsp;&nbsp;</label>
                                                <div class="col-sm-10">
                                                    <button type="button" class="btn btn-warning" style="float:right;" ng-click="removeQuestion($index)">
                                                        <i class="fa fa-trash"></i> Remove This Question
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Add New Question -->
                                <button type="button" class="btn btn-primary" ng-click="addQuestion()">
                                    <i class="fa fa-plus"></i> Add New Question
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="custom-hr">

                    <!-- Action Buttons -->
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-success" ng-disabled="isSubmitting">
                            <span ng-if="!isSubmitting"><i class="fa fa-check"></i> Create Paragraph Question</span>
                            <span ng-if="isSubmitting"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
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
<style>
    .mt-3 {
        margin-top: 1rem;
    }
    .mt-4 {
        margin-top: 1.5rem;
    }
    .mb-2 {
        margin-bottom: 0.5rem;
    }
</style>
