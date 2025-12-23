<style>
    /* Base Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        color: #212529;
        line-height: 1.6;
    }

    /* Header Styles */
    .quiz-header {
        background-color: white;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .logo-container img {
        max-height: 50px;
        transition: opacity 0.3s ease;
    }

    .logo-container img:hover {
        opacity: 0.8;
    }

    .user-welcome {
        font-weight: 600;
        font-size: 1rem;
    }

    .logout-btn {
        background-color: #fe7505;
        color: white;
        font-weight: 500;
        text-transform: uppercase;
        border: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
    }

    /* Timer Styles */
    .timer-container {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 70px;
        z-index: 999;
    }

    .timer-box {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        border-radius: 50px;
        padding: 0.5rem 1rem;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .timer-icon {
        width: 20px;
        margin-right: 8px;
    }

    .timer-display {
        background-color: white;
        color: #212529;
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        font-weight: bold;
        margin-left: 0.5rem;
        min-width: 80px;
        text-align: center;
    }

    /* Main Quiz Container */
    .quiz-main-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 1.5rem auto;
        padding: 2rem;
        max-width: 1200px;
    }

    /* Progress Section */
    .progress-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .quiz-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .question-counter {
        font-weight: 500;
        text-transform: uppercase;
        color: #6c757d;
    }

    .progress-bar-container {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background-color: #fe7505;
        transition: width 0.6s ease;
    }

    /* Question Content */
    .question-content {
        margin: 2rem 0;
    }

    .question-text {
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 2rem;
        color: #212529;
    }

    .question-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem auto;
        display: block;
    }

    /* Answer Options */
    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.25rem;
        margin: 2rem 0;
    }

    .option-card {
        background-color: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .option-card:hover {
        border-color: #fe7505;
        box-shadow: 0 5px 15px rgba(254, 117, 5, 0.1);
    }

    .option-card.selected {
        border-color: #fe7505;
        background-color: rgba(254, 117, 5, 0.05);
    }

    .option-image-container {
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .option-image {
        max-width: 100%;
        max-height: 120px;
        border-radius: 4px;
    }

    .option-text {
        font-weight: 500;
        word-break: break-word;
    }

    /* Navigation Buttons */
    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }

    .nav-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-previous {
        background-color: #6c757d;
        color: white;
    }

    .btn-next {
        background-color: #fe7505;
        color: white;
    }

    .btn-submit {
        background-color: #28a745;
        color: white;
    }

    /* Special Screens */
    .special-screen {
        padding: 3rem 0;
        text-align: center;
    }

    .special-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #212529;
    }

    .special-content {
        font-size: 1.1rem;
        line-height: 1.7;
        max-width: 800px;
        margin: 0 auto 2rem;
        color: #495057;
    }

    .memory-image {
        max-width: 80%;
        height: auto;
        border-radius: 8px;
        margin: 2rem auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .countdown-text {
        font-size: 1.2rem;
        font-weight: 600;
        color: #dc3545;
        margin: 1rem 0;
    }

    /* Loader */
    .loader-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }

    .spinner {
        width: 3rem;
        height: 3rem;
        border: 0.3em solid #f3f3f3;
        border-top: 0.3em solid #fe7505;
        border-radius: 50%;
        animation: spin 0.75s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .options-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .quiz-header {
            padding-bottom: 80px;
        }

        .timer-container {
            top: auto;
            bottom: 20px;
        }

        .options-grid {
            grid-template-columns: 1fr;
        }

        .question-text {
            font-size: 1.3rem;
        }

        .special-title {
            font-size: 1.5rem;
        }

        .memory-image {
            max-width: 100%;
        }
    }
    .training-banner {
        background-color: #fff3cd;
        color: #856404;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ffeeba;
        border-radius: 5px;
        font-weight: bold;
    }
 
</style>
<div class="container-fluid p-3" >
    <div class="row align-items-center">
        <!-- Logo -->
        <div class="col-12 col-md-4 text-start text-md-left mb-2 mb-md-0">
            <a href="
               <?= base_url(); ?>">
                <img src="
                     <?= base_url(session()->get('quiz_company_logo') ?: 'assets/images/logo/logo.png'); ?>" alt="Psychometrica Logo" class="img-fluid" style="max-height: 60px;">
            </a>
        </div>
        <!-- Timer -->
        <div class="col-12 col-md-4 text-end text-md-end">
            <div class="count_box d-flex flex-row justify-content-center align-items-center mt-2 mt-md-0 rounded-pill position-absolute start-50 translate-middle-x" ng-if="timerDisplay && !showParagraphText" style="top: 50px; z-index: 999;">
                <div class="count_clock ps-3">
                    <img src="./assets/images/clock/clock.png" alt="Clock Icon" style="max-width: 24px;">
                </div>
                <div class="count_title px-2">
                    <h4 class="text-white pe-3 pe-md-5">Quiz</h4>
                    <span class="text-white">Time start</span>
                </div>
                <div class="count_number p-2 d-flex justify-content-center align-items-center bg-white rounded-pill countdown_timer">
                    <span class="text-dark fw-bold">{{ timerDisplay}}</span>
                </div>
            </div>
        </div>

        <div ng-if="getCurrentQuestion()?.is_demo == '1'" class="training-banner">
            🛈 Training Mode – This question is for practice only
        </div>

        <!-- Welcome + Logout -->
        <div class="col-12 col-md-4 text-end text-md-end"> <?php if (session()->get('quiz_name')): ?> <div class="d-flex justify-content-end align-items-center gap-3">
                    <span class="fw-bold step_box_desc" style="font-size: 1rem;"> Welcome, <?= esc(session()->get('quiz_name')); ?> 👋 </span>
                    <a href="
                       <?= site_url('quiz/logout') ?>" class="btn btn-warning btn-sm text-uppercase text-white"> Logout </a>
                </div> 
            <?php endif; ?> 
        </div>


    </div>
</div>
<div class="container">
    <form class="multisteps_form" id="wizard">
        <div class="multisteps_form_panel" style="display: block;">
            <!-- Step Header -->
            <div class="step_content d-flex flex-column flex-md-row justify-content-between pt-0 pb-2">
                <h4>{{ testName}}</h4>
                
                <span class="text-uppercase text-md-end"> QUESTION {{ getCurrentQuestionNumber()}} OF {{ totalQuestions}}
                </span>
            </div>
            <!-- Progress Bar -->
            <div class="step_progress_bar mb-3">
                <div class="progress rounded-pill">
                    <div class="progress-bar" role="progressbar" style="width: {{ calculateProgress()}}%" aria-valuenow="{{ calculateProgress()}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div ng-if="!isOnline" class="alert alert-danger text-center">
                No Internet Connection. Please check your network.
            </div>
            <!-- Question Area -->
            <div class="form_content m-2">
                <div ng-if="currentQuestionIndex === - 1" class="py-5 text-center">
                    <!-- Check for Memory Question -->
                    <div ng-if="getCurrentFactor().memoryQuestion === 'TRUE'">
                        <h2 class="text-capitalize">{{ getCurrentFactor().factor_description}}</h2>
                        <div class="text-center mt-4">
                            <h2 class="text-capitalize">
                                <p class="mt-2 text-danger">This image will disappear in {{ countdownTime}} seconds</p>
                                <button class="btn btn-warning mt-3" ng-click="skipAndContinue()">Skip and Continue</button>
                            </h2>
                            <!-- Memory Image Display -->
                            <img ng-src="{{ getCurrentFactor().memoryImagePath}}" alt="Memory Master Image" class="img-fluid" style="width: 60%; height: auto; max-height: 90vh; object-fit: contain;" />
                        </div>
                    </div>
                    <!-- Check for Paragraph Question -->
                    <div ng-if="showParagraphText && currentQuestionIndex === - 1" class="text-center py-5">
                        <h2 class="text-start">{{ getCurrentFactor().paragraphText}}</h2>
                        <p class="mt-3">{{ getCurrentFactor().paragraph_text}}</p>
                        <!-- Just dismiss the paragraph and let goToNext handle the next step -->
                        <button class="btn btn-warning mt-4" ng-click="showParagraphText = false; goToNext();"> Continue to Questions </button>
                    </div>
                    <!-- Display Test Instruction Only If Memory Question Is Not Present -->
                    <p style="text-align: left;" ng-bind-html="testInstruction"></p>
                    <!-- Proceed to Questions (Only Show After Memory Question) -->
                    <button class="btn btn-warning mt-3" ng-if="getCurrentFactor().memoryQuestion !== 'TRUE' && !showParagraphText" ng-click="startQuiz()">Start Test</button>
                </div>
                <!-- Question Area -->
                <div ng-if="getCurrentQuestion()" class="question_title py-4">
                    <div ng-if="getCurrentQuestion().question_text" class="text-center">
                        <h3 class="text-start">
                            {{ getCurrentQuestionNumber()}}. 
                            <span ng-bind-html="getCurrentQuestion().question_text"></span> 
                        </h3>
                    </div>
                    <div ng-if="getCurrentQuestion().question_image" class="text-center my-3">
                        <img ng-src="{{ getCurrentQuestion().question_image}}" class="img-fluid rounded mx-auto" style="max-width: 200px;" alt="Question Image">
                    </div>
                    <div class="row form_items justify-content-center">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3" ng-repeat="opt in getCurrentQuestion().options" ng-style="{'border': selectedAnswer[getCurrentQuestion().id] === opt ? '5px solid #fe7505' : '5px solid #e1e1e1'}" style="margin: 10px; border-radius: 8px;">
                            <label class="step_1 bg-white text-center w-100 h-100 animate__animated animate__fadeInRight" style="cursor: pointer;" ng-click="selectOption(getCurrentQuestion().id, opt)">
                                <div class="step_box_icon" ng-if="opt.option_image">
                                    <div ng-if="opt.option_image" ng-show="!opt.imageLoaded" class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <img ng-if="opt.option_image" ng-src="{{ opt.option_image}}" class="img-fluid" ng-init="opt.imageLoaded = false" ng-load="opt.imageLoaded = true" alt="Option Image" />
                                </div>
                                <span class="step_box_text" ng-if="opt.option_text">
                                    {{ opt.option_text}}
                                </span>
                                <input type="radio"
                                       name="question_{{ getCurrentQuestion().id}}"
                                       ng-model="selectedAnswer[getCurrentQuestion().id].option_id"
                                       ng-value="opt.id"
                                       ng-checked="selectedAnswer[getCurrentQuestion().id].option_id == opt.id"
                                       ng-click="selectOption(getCurrentQuestion().id, opt)" />

                            </label>
                        </div>
                    </div>
                    <div class="pt-4 d-flex justify-content-between">
                        <button class="next_btn text-uppercase text-white" ng-click="goToPrevious()" ng-disabled="currentFactorIndex === 0 && currentQuestionIndex === - 1"> Previous </button>
                        <div>
                            <button class="next_btn btn-warning text-uppercase text-white" ng-click="goToNext()" ng-disabled="getCurrentQuestionNumber() >= totalQuestions" ng-if="getCurrentQuestionNumber() < totalQuestions"> Next </button>
                            <button class="next_btn btn-success text-uppercase text-white" ng-click="submitQuiz()" ng-if="getCurrentQuestionNumber() >= totalQuestions"> Submit </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Loader Spinner -->
            <div id="loader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </form>
</div>
