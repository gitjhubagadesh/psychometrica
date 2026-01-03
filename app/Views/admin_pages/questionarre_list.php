<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">Manage questionnaire</h1>
</div>

<div class="wrapper-md">
    <div class="row">

    </div>

    <div class="panel panel-default mt-4">
        <div class="panel-heading font-bold">Questionnaire</div>
        <div class="panel-body">
            <div class="table-responsive">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control rounded-pill px-4" 
                                   placeholder="🔍 Search..." 
                                   ng-model="searchText"
                                   ng-model-options="{debounce: 300}">  <!-- Add debounce here -->
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-2 d-flex justify-content-end" style="float: right;">
                            <button class="btn btn-dark" ng-click="createNew()">
                                <i class="fas fa-question-circle"></i> New Questionnaire
                            </button>
                        </div>
                        <div class="col-md-2 d-flex justify-content-end" style="float: right;">
                            <button class="btn btn-dark" ng-click="createMemoryQuestion()">
                                <i class="fas fa-question-circle"></i> Memory Questionnaire
                            </button>
                        </div>
                        <div class="col-md-2 d-flex justify-content-end" style="float: right;">
                            <button class="btn btn-dark" ng-click="createParagraphQuestion()">
                                <i class="fas fa-question-circle"></i> Paragraph Questionnaire
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:5%">ID</th>
                            <th style="width:15%">Test Factor Name</th>
                            <th style="width:25%">Question</th>
                            <th style="width:5%">Status</th>
                            <th style="width:10%">Option</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="questionnaire in questionnaires track by questionnaire.id">
                            <td>{{ questionnaire.id}}</td>
                            <td>{{ questionnaire.factor_name}}</td>
                            <td>
                                <span ng-if="questionnaire.isDemo == 0">
                                    <i class="fas fa-vial text-warning" title="Demo question"></i>
                                </span>
                                <span ng-if="questionnaire.isDemo == 1">
                                    <i class="fas fa-check-circle text-success" title="Regular question"></i>
                                </span>

                                <img ng-src="{{ questionnaire.question_display}}" 
                                     alt="Question Image" width="100" height="50" 
                                     onerror="this.style.display='none'">
                                <!-- Check if question_display contains an image (basic check for URLs) -->
                                <span ng-if="!isImage(questionnaire.question_display)">
                                    {{ questionnaire.question_display}}
                                </span>
                                <!-- Render as an image if it's an image URL -->
                                <a>
                                    <i title="view main memory image" class="fas fa-eye" ng-if="questionnaire.memory_main_id" ng-click="viewMemoryImage(questionnaire.memory_main_id)"></i>
                                </a>
                            </td>
                            <td class="align-middle">
                                <label class="checkbox-inline i-checks">
                                    <input type="checkbox" 
                                           ng-model="questionnaire.status" 
                                           ng-true-value="'1'"
                                           ng-false-value="'0'"
                                           ng-change="toggleStatus(questionnaire)">
                                    <i></i>

                                </label>

                            </td>
                            <td>
                                <button class="btn btn-sm btn-success" ng-click="viewQuestion(questionnaire.id)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <!--                                <button class="btn btn-sm btn-warning" ng-click="editQuestionnaire(questionnaire.id)">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>-->
                                <button class="btn btn-sm btn-danger" ng-click="deleteQuestionnaire(questionnaire.id)">
                                    <i class="fas fa-trash-alt"></i> Delete
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



