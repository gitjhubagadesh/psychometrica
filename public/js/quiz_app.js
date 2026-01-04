var app = angular.module("myApp", ["ngRoute", "ngSanitize"]);
app.config(function ($routeProvider, $locationProvider) {
    $routeProvider
            .when("/", {
                templateUrl: "questionnaire",
                controller: "QuizController"
            })
            .when("/cognitive", {
                templateUrl: "cognitive",
                controller: "QuizCognitiveController"
            })
            .when("/contact-error", {
                templateUrl: "contactError",
                controller: "QuizController"
            })
            .when("/test-finish", {
                templateUrl: "quizFinish",
                controller: "QuizController"
            })
            .when("/cog-quiz-finish", {
                templateUrl: "cogQuizFinish",
                controller: "QuizCognitiveController"
            })
            .otherwise({
                redirectTo: "/"
            });

    $locationProvider.html5Mode(false); // Ensures #! is used
});

app.controller("QuizController", function ($scope, $http, $routeParams, $location, $interval, $sce, $timeout) {
    $scope.quizData = [];  // Initialized as an empty array
    $scope.totalQuestions = 0;  // Total number of questions
    $scope.currentFactorIndex = 0;  // Index of the current factor (set of questions)
    $scope.currentQuestionIndex = -1;  // Start before the first question
    $scope.testName = 'Quiz';
    $scope.isTimerRunning = false;

    // Timer warning and critical states for CSS styling
    $scope.timerWarning = false;
    $scope.timerCritical = false;


    $scope.isOnline = navigator.onLine; // Initial state

    $scope.checkInternetConnectivity = function () {
        $scope.isOnline = navigator.onLine;
    };


    $scope.testName = "MSP";

    $scope.testIds = [
        {
            test_id: "20",
            test_description: `<div style="background-color: #5a587a; padding: 15px; color: white; font-family: Arial, sans-serif;">
                <p><strong style="color: orange;">Instructions:</strong></p>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>For each statement below, select the option that best describes you.</li>
                    <li>The option that reflects how you presently respond, and not how you should ideally respond.</li>
                    <li>Be as honest as you can, as this will give you a clear representation of your managerial strengths and development areas.</li>
                    <li>Some statements may simply seek your agreement, asking you to either agree or disagree with them.</li>
                </ul>
            </div>`
        }
    ];

    // Trust the HTML
    $scope.trustedTestDescription = $sce.trustAsHtml($scope.testIds[0].test_description);


    $interval(function () {
        $scope.checkInternetConnectivity();
    }, 1000); // Check every 1 second (1000 ms)


    let timerInterval;
    let totalSeconds = 60; // default 60 seconds — you can update this dynamically

    // Converts seconds to HH:MM:SS format (consolidated function)
    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    // Start timer function with enhanced warnings and auto-save
    $scope.startTimer = function (duration) {
        // Clean up any existing timers first
        $scope.cleanupAllTimers();

        let currentFactor = $scope.getCurrentFactor();

        // Don't start timer for paragraph questions or factor descriptions
        if (currentFactor?.paragraphQuestion === 'TRUE' || $scope.currentQuestionIndex === -1) {
            return;
        }

        $scope.isTimerRunning = true;
        $scope.timerDisplay = formatTime(duration);

        // Store the timer reference
        timerInterval = $interval(function () {
            duration--;

            // Update display
            $scope.timerDisplay = formatTime(duration);

            // Visual warnings for low time
            if (duration <= 60 && duration > 30) {
                $scope.timerWarning = true;
                $scope.timerCritical = false;
            } else if (duration <= 30) {
                $scope.timerWarning = true;
                $scope.timerCritical = true;
            }

            // Audio/Visual warnings at specific intervals
            if (duration === 300 && !$scope.timerState.warnings[300]) {
                $scope.showTimerWarning("5 minutes remaining!");
                $scope.timerState.warnings[300] = true;
            }

            if (duration === 60 && !$scope.timerState.warnings[60]) {
                $scope.showTimerWarning("1 minute remaining!");
                $scope.timerState.warnings[60] = true;
            }

            if (duration === 30 && !$scope.timerState.warnings[30]) {
                $scope.showTimerWarning("30 seconds remaining!");
                $scope.timerState.warnings[30] = true;
            }

            // Timer expired - save answers and move to next factor
            if (duration <= 0) {
                $scope.handleTimerExpired();
            }
        }, 1000);

        // Track this timer
        $scope.timerState.activeTimers.push(timerInterval);
    };

    // Handle timer expiration with answer saving
    $scope.handleTimerExpired = function () {
        $scope.cleanupAllTimers();

        // Save all pending answers before moving
        $scope.flushRemainingAnswers();

        // Calculate answered vs total questions for current factor
        let currentFactor = $scope.getCurrentFactor();
        let totalQuestions = currentFactor ? currentFactor.questions.length : 0;
        let answeredCount = currentFactor ? currentFactor.questions.filter(q =>
            $scope.selectedAnswer[q.id] && $scope.selectedAnswer[q.id].option_id
        ).length : 0;
        let unansweredCount = totalQuestions - answeredCount;

        let factorName = currentFactor ? currentFactor.factorName : 'This section';
        let messageText = unansweredCount > 0
                ? `Time has expired for <strong>${factorName}</strong>.<br><br>You answered <strong>${answeredCount} of ${totalQuestions}</strong> questions.<br><strong>${unansweredCount} question(s)</strong> will be skipped.`
                : `Time has expired for <strong>${factorName}</strong>.<br><br>All <strong>${totalQuestions} questions</strong> have been answered.`;

        Swal.fire({
            title: "⏰ Time's Up!",
            html: `<div style="text-align: center;">${messageText}<br><br><p style="margin-top: 15px; color: #666;">Moving to the next section...</p></div>`,
            icon: "warning",
            timer: 5000,
            showConfirmButton: false,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then(function () {
            $scope.$apply(function () {
                $scope.autoNext = true;
                $scope.moveToNextFactor();
            });
        });
    };

    // Show timer warning toast notification
    $scope.showTimerWarning = function (message) {
        Swal.fire({
            title: message,
            icon: "warning",
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true
        });
    };

    $scope.moveToNextFactor = function () {
        // Clean up timers before moving
        $scope.cleanupAllTimers();

        $scope.currentFactorIndex++;
        $scope.currentQuestionIndex = -1;
        $scope.autoNext = true;

        // Check if we've completed all factors
        if ($scope.currentFactorIndex >= $scope.quizData.length) {
            console.log("Quiz complete. Answers:", $scope.answers);
            return;
        }

        // Start appropriate timer for the new factor
        $scope.startFactorTimer();
    };

    $scope.hasDisplayedInstruction = false; // Initialize at the beginning

    $scope.startQuiz = function () {
        console.log("*** START QUIZ CALLED ***");
        console.log("Before: currentQuestionIndex =", $scope.currentQuestionIndex);
        console.log("Before: autoNext =", $scope.autoNext);
        $scope.hasDisplayedInstruction = true; // Mark instruction as displayed
        $scope.currentQuestionIndex = 0;       // Start from the first question
        $scope.autoNext = false;               // ✅ FIX: Enable validation for first question
        console.log("After: currentQuestionIndex =", $scope.currentQuestionIndex);
        console.log("After: autoNext =", $scope.autoNext);
        console.log("*************************");
        $scope.startFactorTimer();              // Start timer for factors if necessary
    };

    $scope.initSelectedAnswers = function () {
        $scope.selectedAnswer = {};

        angular.forEach($scope.quizData, function (factor) {
            angular.forEach(factor.questions, function (question) {
                if (question.selected_option_id) {
                    $scope.selectedAnswer[question.id] = {
                        option_id: question.selected_option_id
                    };
                }
            });
        });
    };



    // Store the start time when the quiz starts
    $scope.startTime = null;

    // This will store the total time taken to complete the quiz
    $scope.totalTimeTaken = null;

    // Function to load quiz data (captures start time)
    $scope.isLoading = true; // Set to true initially

    $scope.loadQuizData = function () {
        $scope.startTime = new Date().getTime();  // Capture start time in milliseconds

        $http.get('/quiz/questionDetails')
                .then(function (response) {
                    $scope.quizData = response.data.questionDetails;
                    // Initialize pre-selected answers
                    $scope.initSelectedAnswers();
                    $scope.testName = response.data.testName;
                    $scope.testInstruction = $sce.trustAsHtml(response.data.testInstruction);
                    $scope.totalQuestions = $scope.quizData.reduce((total, factor) => total + factor.questions.length, 0);
                    // ✅ Safely bind HTML from testIds[0].test_description
                    $scope.testInstruction = $sce.trustAsHtml(response.data.testIds[0].test_description);

                    $scope.currentFactorIndex = 0;
                    $scope.currentQuestionIndex = -1;

                    $scope.startFactorTimer(); // Start the timer for the first factor
                })
                .catch(function (error) {
                    console.error("Error fetching quiz data:", error);
                    window.location.href = "test#!/contact-error";
                })
                .finally(function () {
                    $scope.isLoading = false; // Hide loader after request completes (success or error)
                });
    };

    $scope.flushRemainingAnswers = function () {
        if ($scope.answerBuffer.length > 0) {
            $scope.saveBufferedAnswers(); // save whatever is left
        }
    };

    // Function to calculate time taken and show results after submission
    $scope.submitQuiz = function () {
        const endTime = Date.now();
        $scope.flushRemainingAnswers();

        const totalTimeInMillis = endTime - $scope.startTime;

        const hours = Math.floor(totalTimeInMillis / 3600000);
        const minutes = Math.floor((totalTimeInMillis % 3600000) / 60000);
        const seconds = Math.floor((totalTimeInMillis % 60000) / 1000);

        $scope.totalTimeTaken = `${hours} hours, ${minutes} minutes, ${seconds} seconds`;

        Swal.fire({
            title: "Test Completed!",
            text: `You completed the quiz in ${$scope.totalTimeTaken}. Thank you!`,
            icon: "success",
            confirmButtonText: "Finish"
        }).then(() => {
            // Call backend AFTER user clicks OK
            $http.get('logoutTime').then(() => {
                // 🔑 Ensure Angular digest runs
                $scope.$applyAsync(() => {
                    $location.path("/cog-quiz-finish")
                            .search({timeTaken: $scope.totalTimeTaken});
                });
            });
        });
    };


    // Call load on view load
    $scope.$on('$viewContentLoaded', function () {
        $scope.isLoading = true; // Set to true initially
        $scope.loadQuizData();
    });

// Initialize selectedAnswer to store answers for each question
    $scope.selectedAnswer = {}; // Empty object for selected answers
    $scope.answers = [];
    $scope.autoNext = true;
    // Add this to your controller init
    $scope.firstTimeInFactor = true;

    // Add this at the beginning of your controller
    $scope.timerState = {
        activeTimers: [],
        isMemoryTimerActive: false,
        warnings: {30: false, 60: false, 300: false} // Track timer warnings
    };

// Completely clean up all timers
    $scope.cleanupAllTimers = function () {
        // Cancel all interval timers
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval);
            timerInterval = null;
        }
        if (angular.isDefined(memoryImageInterval)) {
            $interval.cancel(memoryImageInterval);
            memoryImageInterval = null;
        }

        // Reset all timer states
        $scope.isTimerRunning = false;
        $scope.timerDisplay = null;
        $scope.countdownTime = 0;
        $scope.timerWarning = false;
        $scope.timerCritical = false;
        $scope.timerState.warnings = {30: false, 60: false, 300: false};
    };

    $scope.goToNext = function () {
        let currentFactor = $scope.getCurrentFactor();

        // Reset display flags
        $scope.showParagraphText = false;
        $scope.showMemoryImage = false;

        // If we're at the start of the factor (currentQuestionIndex === -1)
        if ($scope.currentQuestionIndex === -1) {
            console.log("*** FIRST QUESTION INITIALIZATION ***");
            console.log("Setting currentQuestionIndex to 0");
            console.log("Setting autoNext to false");
            $scope.currentQuestionIndex = 0;  // Initialize to first question
            $scope.autoNext = false; // Enable validation for first question
            console.log("autoNext is now:", $scope.autoNext);
            console.log("***********************************");

            // Handle memory image
            if (currentFactor?.memoryQuestion === 'TRUE' && !$scope.memoryImageShown) {
                $scope.memoryImageShown = true;
                $scope.showMemoryImage = true;
                $scope.startMemoryImage();
                return;
            }

            // Handle paragraph question
            if (currentFactor?.paragraphQuestion === 'TRUE' && !$scope.paragraphShown) {
                $scope.cleanupAllTimers(); // Only clean up when showing paragraph
                $scope.paragraphShown = true;
                $scope.showParagraphText = true;
                return;
            }

            // Start timer for regular questions
            $scope.startFactorTimer();
            return; // Return here to show the first question without moving forward
        }

        let currentQuestion = $scope.getCurrentQuestion();
        if (!currentQuestion)
            return;

        // Mandatory question validation
        let currentQuestionId = currentQuestion.id;
        let selectedOption = $scope.selectedAnswer[currentQuestionId];
        let isMandatory = currentFactor?.isMandatory === "1" || currentQuestion?.is_mandatory === "1";
        let isDemo = currentQuestion?.is_demo === "0"; // true if it's a live question

        // DEBUG: Log validation variables
        console.log("=== MANDATORY VALIDATION DEBUG ===");
        console.log("Current Question Index:", $scope.currentQuestionIndex);
        console.log("Question ID:", currentQuestionId);
        console.log("Question is_demo:", currentQuestion?.is_demo);
        console.log("Question is_mandatory:", currentQuestion?.is_mandatory);
        console.log("Factor isMandatory:", currentFactor?.isMandatory);
        console.log("Computed isDemo (is_demo === '0'):", isDemo);
        console.log("Computed isMandatory:", isMandatory);
        console.log("Selected Option:", selectedOption);
        console.log("autoNext flag:", $scope.autoNext);
        console.log("Validation will run:", !isDemo && isMandatory && (!selectedOption || !selectedOption.option_id) && !$scope.autoNext);
        console.log("==================================");

        if (!isDemo && isMandatory && (!selectedOption || !selectedOption.option_id) && !$scope.autoNext) {
            Swal.fire({
                title: "Warning!",
                text: "Please select an answer before moving to the next question!",
                icon: "warning",
                confirmButtonText: 'OK',
                backdrop: true
            });
            return;
        }

        // Save answer if provided
        if (selectedOption && selectedOption.option_id) {
            let existingIndex = $scope.answers.findIndex(ans => ans.questionId === currentQuestionId);
            if (existingIndex === -1) {
                $scope.answers.push({
                    questionId: currentQuestionId,
                    answer: selectedOption
                });
            } else {
                $scope.answers[existingIndex].answer = selectedOption;
            }
        }

        $scope.currentQuestionIndex++;
        let questions = currentFactor.questions;

        // If finished with current factor's questions
        if ($scope.currentQuestionIndex >= questions.length) {
            $scope.cleanupAllTimers(); // Clean up when moving to next factor

            $scope.currentFactorIndex++;
            $scope.currentQuestionIndex = -1;
            $scope.memoryImageShown = false;
            $scope.paragraphShown = false;

            let nextFactor = $scope.getCurrentFactor();

            if (!nextFactor) {
                console.log("Quiz complete. Answers:", $scope.answers);
                return;
            }

            // Check next factor memory image
            if (nextFactor.memoryQuestion === 'TRUE') {
                $scope.showMemoryImage = true;
                $scope.startMemoryImage();
                return;
            }

            // Check next factor paragraph
            if (nextFactor.paragraphQuestion === 'TRUE') {
                $scope.showParagraphText = true;
                return;
            }

            // Regular questions with possible timer
            $scope.currentQuestionIndex = 0;
            $scope.startFactorTimer();
        }

        // Reset autoNext
        $scope.autoNext = false;
    };


    $scope.startFactorTimer = function () {
        const currentQuestion = $scope.getCurrentQuestion();
        const factor = $scope.getCurrentFactor();

        // Don't start timer if:
        // - No question
        // - It's a paragraph question
        // - It's a memory question
        // - We're on factor description page
        if (!currentQuestion ||
                (factor && factor.paragraphQuestion === 'TRUE') ||
                (factor && factor.memoryQuestion === 'TRUE') ||
                $scope.currentQuestionIndex === -1) {
            $scope.cleanupAllTimers();
            return;
        }

        // Only start if factor has a valid timer
        if (factor && factor.factor_timer && factor.factor_timer !== "00:00:00") {
            const duration = parseTimerToSeconds(factor.factor_timer);
            $scope.startTimer(duration);
        } else {
            $scope.cleanupAllTimers();
        }
    };


    $scope.showMemoryImage = false;
    $scope.countdownTime = 0;
    let memoryImageInterval = null; // Store the interval reference globally

    $scope.startMemoryImage = function () {
        const factor = $scope.getCurrentFactor();
        $scope.cleanupAllTimers(); // Stop all existing timers

        if (factor.memoryQuestion === 'TRUE') {
            // Decide timer source
            let rawTime = ($scope.currentQuestionIndex === -1)
                    ? (factor.disapearTime?.trim() || factor.factor_timer)
                    : factor.factor_timer;

            $scope.countdownTime = parseTimerToSeconds(rawTime);
            $scope.timerDisplay = formatTime($scope.countdownTime);
            $scope.showMemoryImage = true;
            $scope.timerState.isMemoryTimerActive = true;

            memoryImageInterval = $interval(function () {
                if ($scope.countdownTime > 0) {
                    $scope.countdownTime--;
                    $scope.timerDisplay = formatTime($scope.countdownTime);
                } else {
                    $scope.cleanupAllTimers();
                    $scope.showMemoryImage = false;

                    // ✅ Decide next step
                    if ($scope.currentQuestionIndex === -1) {
                        // Memory image phase just ended
                        $scope.currentQuestionIndex = 0;
                        $scope.startMemoryImage(); // Start questions with factor_timer
                    } else {
                        // Question timer just ended
                        $scope.forceMoveToNextFactor();
                    }
                }
            }, 1000);

            $scope.timerState.activeTimers.push(memoryImageInterval);
        }
    };


// Add this new function to handle forced factor skipping
    $scope.forceMoveToNextFactor = function () {
        // Move to next factor
        $scope.currentFactorIndex++;
        $scope.currentQuestionIndex = -1; // Reset to factor start
        $scope.memoryImageShown = false;
        $scope.paragraphShown = false;

        // Check if quiz is complete
        if ($scope.currentFactorIndex >= $scope.quizData.length) {
            $scope.submitQuiz();
            return;
        }

        // Handle next factor
        const nextFactor = $scope.getCurrentFactor();

        if ($scope.currentQuestionIndex === -1) {
            $scope.currentQuestionIndex = -1;
            $scope.startMemoryImage();
        }
        if (nextFactor.memoryQuestion === 'TRUE') {
            $scope.showMemoryImage = true;
            $scope.startMemoryImage();
        } else if (nextFactor.paragraphQuestion === 'TRUE') {
            $scope.showParagraphText = true;
        } else {
            // Start regular questions for next factor
            $scope.currentQuestionIndex = 0;
            $scope.startFactorTimer();
        }
    };

// Add this cleanup function  
    $scope.stopMemoryImageTimer = function () {
        if (memoryImageInterval) {
            $interval.cancel(memoryImageInterval);
            memoryImageInterval = null;
        }
        $scope.timerDisplay = null;
    };

// Make sure to clean up when controller is destroyed
    $scope.$on('$destroy', function () {
        $scope.stopMemoryImageTimer();
    });


    function parseTimerToSeconds(timerStr) {
        const parts = timerStr.split(":");
        return (+parts[0]) * 3600 + (+parts[1]) * 60 + (+parts[2]);
    }


    $scope.getCurrentFactor = function () {
        return $scope.quizData?.[$scope.currentFactorIndex] || null;
    };

    $scope.getCurrentQuestion = function () {
        let factor = $scope.getCurrentFactor();
        if (!factor || $scope.currentQuestionIndex < 0)
            return null;
        return factor.questions?.[$scope.currentQuestionIndex] || null;
    };


    // Calculate the progress bar percentage based on the current question and total questions
    $scope.calculateProgress = function () {
        if (!$scope.quizData || !$scope.totalQuestions || $scope.totalQuestions === 0)
            return 0;

        let currentQuestionNumber = 0;

        for (let i = 0; i < $scope.currentFactorIndex; i++) {
            currentQuestionNumber += $scope.quizData[i].questions.length || 0;
        }

        if ($scope.currentQuestionIndex === -1) {
            // Only count description screen if it's NOT the very first factor
            if ($scope.currentFactorIndex > 0) {
                currentQuestionNumber += 1;
            }
        } else {
            // User is on a question within the factor
            currentQuestionNumber += ($scope.currentQuestionIndex || 0) + 1;
        }

        let progress = (currentQuestionNumber / $scope.totalQuestions) * 100;
        return Math.min(Math.round(progress), 100);
    };



    $scope.getCurrentQuestionNumber = function () {
        if ($scope.currentQuestionIndex === -1)
            return 0; // Still on intro

        const currentQuestion = $scope.quizData[$scope.currentFactorIndex].questions[$scope.currentQuestionIndex];

        // Do not show number for demo question
        if (currentQuestion.is_demo == '0') {
            return null;
        }

        let questionNumber = 0;

        // Count only test questions from previous factors
        for (let i = 0; i < $scope.currentFactorIndex; i++) {
            questionNumber += $scope.quizData[i].questions.filter(q => q.is_demo == '1').length;
        }

        // Count only test questions in current factor up to currentQuestionIndex
        const currentFactorQuestions = $scope.quizData[$scope.currentFactorIndex].questions;
        for (let j = 0; j <= $scope.currentQuestionIndex; j++) {
            if (currentFactorQuestions[j].is_demo == '1') {
                questionNumber++;
            }
        }

        return questionNumber;
    };

    $scope.goToPrevious = function () {
        // Case 1: Go to previous question in the same factor
        if ($scope.currentQuestionIndex > 0) {
            $scope.currentQuestionIndex--;
            return;
        }

        // Case 2: At the first question, go back to factor description
        if ($scope.currentQuestionIndex === 0) {
            $scope.currentQuestionIndex = -1;

            // Stop timer only when returning to factor description
            if ($scope.isTimerRunning && angular.isDefined(timerInterval)) {
                $interval.cancel(timerInterval);
                timerInterval = null;
                $scope.timerDisplay = null;
                $scope.isTimerRunning = false;
            }

            return;
        }

        // Case 3: At the factor description, go back to the previous factor
        if ($scope.currentQuestionIndex === -1 && $scope.currentFactorIndex > 0) {
            $scope.currentFactorIndex--;

            const previousFactor = $scope.quizData[$scope.currentFactorIndex];
            $scope.currentQuestionIndex = previousFactor.questions.length - 1;

            // Optionally restart timer for the previous factor if needed
            // $scope.startFactorTimer(); // Uncomment if you want to auto-restart

            return;
        }

        // Case 4: Already at the first factor description — stay there
        // Optionally show alert or just do nothing
        console.log("Already at the beginning of the quiz");
    };


// Stop Timer Function to be used when going back
    $scope.stopTimer = function () {
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval); // Clear the interval to stop the timer
            timerInterval = null;
        }
        $scope.timerDisplay = null; // Reset timer display
        $scope.isTimerRunning = false; // Timer is no longer running
    };

    $scope.skipAndContinue = function () {
        // Stop the countdown timer
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval);
            timerInterval = null;
        }
        $scope.timerDisplay = null; // Clear the timer display
        $scope.showMemoryImage = false; // Hide the image and countdown timer

        // Move to the next question
        $scope.goToNext(); // Call the function to go to the next question
    };


    $scope.selectedAnswer = {};
    $scope.answerBuffer = [];
    $scope.autoSaveThreshold = 5;

// Function to save the selected answer
    $scope.saveAnswer = function (questionId, option) {
        $scope.selectedAnswer[questionId] = option; // Store the selected option for the current question
    };

    $scope.selectOption = function (questionId, option) {
        const current = $scope.selectedAnswer[questionId];
        if (current && current.option_id === option.id)
            return;

        const answer = {
            question_id: questionId,
            option_id: option.id,
        };

        $scope.selectedAnswer[questionId] = answer;
        $scope.answerBuffer.push(answer);

        console.log("Buffered Answer:", answer);

        if ($scope.answerBuffer.length >= $scope.autoSaveThreshold) {
            $scope.saveBufferedAnswers();
        }
    };

    window.addEventListener('beforeunload', function () {
        if ($scope.answerBuffer.length > 0) {
            navigator.sendBeacon('/quiz/saveAnswers', JSON.stringify({
                answers: $scope.answerBuffer
            }));
        }
    });

    $scope.saveBufferedAnswers = function () {
        if ($scope.answerBuffer.length === 0)
            return;

        document.getElementById("loader").style.display = "block";

        const payload = {
            answers: angular.copy($scope.answerBuffer)  // clone before clearing
        };

        $http.post('/quiz/saveAnswers', payload)
                .then(function (response) {
                    if (response.data.status === 'error') {
                        Swal.fire({
                            title: "Session Expired",
                            text: response.data.message,
                            icon: "warning"
                        }).then(() => {
                            window.location.href = response.data.redirect;
                        });
                    } else {
                        document.getElementById("loader").style.display = "none";
                        $scope.answerBuffer = []; // clear buffer only on success
                    }
                })
                .catch(function (error) {
                    console.error("Error saving batch answers:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "There was a problem saving your answers. Please try again.",
                        icon: "error"
                    });
                });
    };



});


app.controller("QuizCognitiveController", function ($scope, $http, $routeParams, $location, $interval, $sce) {
    $scope.quizData = [];  // Initialized as an empty array
    $scope.totalQuestions = 0;  // Total number of questions
    $scope.testName = 'Quiz';
    $scope.isTimerRunning = false;
    $scope.showTestInstruction = true;
    $scope.currentFactorIndex = 0;  // first factor
    $scope.currentQuestionIndex = 0; // no question yet

    // Timer warning and critical states for CSS styling
    $scope.timerWarning = false;
    $scope.timerCritical = false;


    $scope.isOnline = navigator.onLine; // Initial state

    $scope.getAlphabetLabel = function (num) {
        if (!num)
            return '';
        // Convert 1 → A, 2 → B, 27 → AA (supports > 26)
        let str = '';
        while (num > 0) {
            num--;
            str = String.fromCharCode(65 + (num % 26)) + str;
            num = Math.floor(num / 26);
        }
        return str;
    };

    $scope.getNextQuestion = function () {
        const factor = $scope.getCurrentFactor();
        if (!factor || !$scope.currentQuestionIndex)
            return null;
        return factor.questions[$scope.currentQuestionIndex + 1] || null;
    };


    $scope.isDemoToTestTransition = function () {
        const currentQuestion = $scope.getCurrentQuestion?.();
        const nextQuestion = $scope.getNextQuestion?.();

        // If current is demo and next is non-demo → show "Start Test"
        if (currentQuestion && nextQuestion) {
            return currentQuestion.is_demo == 0 && nextQuestion.is_demo == 1;
        }

        return false;
    };


    $scope.checkInternetConnectivity = function () {
        $scope.isOnline = navigator.onLine;
    };

    $interval(function () {
        $scope.checkInternetConnectivity();
    }, 1000); // Check every 1 second (1000 ms)

    $scope.getOptionLabel = function (index) {
        return String.fromCharCode(97 + index); // 97 = 'a'
    };


    let timerInterval;
    let totalSeconds = 60; // default 60 seconds — you can update this dynamically

    // Converts seconds to HH:MM:SS format (consolidated function)
    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    // Start timer function with enhanced warnings and auto-save
    $scope.startTimer = function (duration) {
        // Clean up any existing timers first
        $scope.cleanupAllTimers();

        let currentFactor = $scope.getCurrentFactor();

        // Don't start timer for paragraph questions or factor descriptions
        if (currentFactor?.paragraphQuestion === 'TRUE' || $scope.currentQuestionIndex === -1) {
            return;
        }

        $scope.isTimerRunning = true;
        $scope.timerDisplay = formatTime(duration);

        // Store the timer reference
        timerInterval = $interval(function () {
            duration--;

            // Update display
            $scope.timerDisplay = formatTime(duration);

            // Visual warnings for low time
            if (duration <= 60 && duration > 30) {
                $scope.timerWarning = true;
                $scope.timerCritical = false;
            } else if (duration <= 30) {
                $scope.timerWarning = true;
                $scope.timerCritical = true;
            }

            // Audio/Visual warnings at specific intervals
            if (duration === 300 && !$scope.timerState.warnings[300]) {
                $scope.showTimerWarning("5 minutes remaining!");
                $scope.timerState.warnings[300] = true;
            }

            if (duration === 60 && !$scope.timerState.warnings[60]) {
                $scope.showTimerWarning("1 minute remaining!");
                $scope.timerState.warnings[60] = true;
            }

            if (duration === 30 && !$scope.timerState.warnings[30]) {
                $scope.showTimerWarning("30 seconds remaining!");
                $scope.timerState.warnings[30] = true;
            }

            // Timer expired - save answers and move to next factor
            if (duration <= 0) {
                $scope.handleTimerExpired();
            }
        }, 1000);

        // Track this timer
        $scope.timerState.activeTimers.push(timerInterval);
    };

    // Handle timer expiration with answer saving
    $scope.handleTimerExpired = function () {
        $scope.cleanupAllTimers();

        // Save all pending answers before moving
        $scope.flushRemainingAnswers();

        // Calculate answered vs total questions for current factor
        let currentFactor = $scope.getCurrentFactor();
        let totalQuestions = currentFactor ? currentFactor.questions.length : 0;
        let answeredCount = currentFactor ? currentFactor.questions.filter(q =>
            $scope.selectedAnswer[q.id] && $scope.selectedAnswer[q.id].option_id
        ).length : 0;
        let unansweredCount = totalQuestions - answeredCount;

        let factorName = currentFactor ? currentFactor.factorName : 'This section';
        let messageText = unansweredCount > 0
                ? `Time has expired for <strong>${factorName}</strong>.<br><br>You answered <strong>${answeredCount} of ${totalQuestions}</strong> questions.<br><strong>${unansweredCount} question(s)</strong> will be skipped.`
                : `Time has expired for <strong>${factorName}</strong>.<br><br>All <strong>${totalQuestions} questions</strong> have been answered.`;

        Swal.fire({
            title: "⏰ Time's Up!",
            html: `<div style="text-align: center;">${messageText}<br><br><p style="margin-top: 15px; color: #666;">Moving to the next section...</p></div>`,
            icon: "warning",
            timer: 5000,
            showConfirmButton: false,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then(function () {
            $scope.$apply(function () {
                $scope.autoNext = true;
                $scope.moveToNextFactor();
            });
        });
    };

    // Show timer warning toast notification
    $scope.showTimerWarning = function (message) {
        Swal.fire({
            title: message,
            icon: "warning",
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true
        });
    };

    $scope.moveToNextFactor = function () {
        // Clean up timers before moving
        $scope.cleanupAllTimers();

        $scope.currentFactorIndex++;
        $scope.currentQuestionIndex = -1;
        $scope.autoNext = true;

        // Check if we've completed all factors
        if ($scope.currentFactorIndex >= $scope.quizData.length) {
            console.log("Quiz complete. Answers:", $scope.answers);
            return;
        }

        // Start appropriate timer for the new factor
        $scope.startFactorTimer();
    };

    $scope.hasDisplayedInstruction = false; // Initialize at the beginning

    $scope.startQuiz = function () {
        console.log("*** START QUIZ CALLED (Cognitive) ***");
        console.log("Before: currentQuestionIndex =", $scope.currentQuestionIndex);
        console.log("Before: autoNext =", $scope.autoNext);
        $scope.hasDisplayedInstruction = true;
        $scope.currentQuestionIndex = 0;
        $scope.autoNext = false;               // ✅ FIX: Enable validation for first question
        console.log("After: currentQuestionIndex =", $scope.currentQuestionIndex);
        console.log("After: autoNext =", $scope.autoNext);
        console.log("**************************************");

        let currentQuestion = $scope.getCurrentQuestion();

        // ✅ If first question is non-demo, start timer
        if (currentQuestion && currentQuestion.is_demo === "1" && !$scope.timerRunning) {
            $scope.startFactorTimer();
            $scope.timerRunning = true;
        } else {
            $scope.cleanupAllTimers();
            $scope.timerDisplay = '';
        }
    };



    $scope.initSelectedAnswers = function () {
        $scope.selectedAnswer = {};

        angular.forEach($scope.quizData, function (factor) {
            angular.forEach(factor.questions, function (question) {
                if (question.selected_option_id) {
                    $scope.selectedAnswer[question.id] = {
                        option_id: question.selected_option_id
                    };
                }
            });
        });
    };



    // Store the start time when the quiz starts
    $scope.startTime = null;

    // This will store the total time taken to complete the quiz
    $scope.totalTimeTaken = null;

    // Function to load quiz data (captures start time)
    $scope.isLoading = true; // Set to true initially
    $scope.questionSubmitDetails = null;

    $scope.loadQuizData = function () {
        $scope.startTime = new Date().getTime();  // Capture start time in milliseconds

        $http.get('/quiz/questionDetails')
                .then(function (response) {
                    $scope.quizData = response.data.questionDetails;
                    // Initialize pre-selected answers
                    $scope.initSelectedAnswers();
                    $scope.testName = response.data.testName;
                    $scope.testInstruction = response.data.testInstruction;
                    $scope.totalQuestions = $scope.quizData.reduce((total, factor) => {
                        return total + factor.questions.filter(q => q.is_demo == '1').length;
                    }, 0);

                    $scope.currentFactorIndex = 0;
                    $scope.currentQuestionIndex = -1;
                    $scope.questionSubmitDetails = response.data.questionDetails;

                    $scope.startFactorTimer(); // Start the timer for the first factor
                })
                .catch(function (error) {
                    console.error("Error fetching quiz data:", error);
                    window.location.href = "test#!/contact-error";
                })
                .finally(function () {
                    $scope.isLoading = false; // Hide loader after request completes (success or error)
                });
    };

    $scope.prepareFactors = function () {
        $scope.isLoading = true;

        $http.get('/quiz/questionDetails')
                .then(function (response) {
                    // Assign the response
                    $scope.questionSubmitDetails = response.data.questionDetails;
                    $scope.quizData = response.data.questionDetails;

                    $scope.testName = response.data.testName;
                    $scope.testInstruction = response.data.testInstruction;

                    // Count only demo questions
                    $scope.totalQuestions = $scope.quizData.reduce((total, factor) => {
                        return total + factor.questions.filter(q => q.is_demo == '1').length;
                    }, 0);

                    $scope.currentFactorIndex = 0;
                    $scope.currentQuestionIndex = -1;

                    // Group questions by factorId
                    const factorMap = {};
                    $scope.questionSubmitDetails.forEach(item => {
                        const factorId = item.factorId || 'unknown';
                        if (!factorMap[factorId]) {
                            factorMap[factorId] = {
                                factorId: factorId,
                                factorName: item.factorName || 'Unnamed Factor',
                                sectionNo: item.sectionNo || 1,
                                questions: []
                            };
                        }

                        if (item.questions && item.questions.length > 0) {
                            factorMap[factorId].questions.push(...item.questions);
                        }
                    });

                    $scope.groupedFactors = Object.values(factorMap);

                    $scope.startFactorTimer(); // Start the timer for the first factor
                })
                .catch(function (error) {
                    console.error("Error fetching test data:", error);
                    window.location.href = "test#!/contact-error";
                })
                .finally(function () {
                    $scope.isLoading = false; // Hide loader after request completes
                });
    };


    if ($location.url().startsWith('/cog-quiz-finish')) {
        $scope.prepareFactors();
    }

    $scope.getAnsweredCountForFactor = function (factor) {
        return factor.questions.filter(q => q.selected_option_id != null).length;
    };

    $scope.getTotalQuestionsForFactor = function (factor) {
        return factor.questions.length;
    };


    $scope.flushRemainingAnswers = function () {
        if ($scope.answerBuffer.length > 0) {
            $scope.saveBufferedAnswers(); // save whatever is left
        }
    };

    // Function to calculate time taken and show results after submission
    $scope.submitQuiz = function () {
        // Capture end time
        const endTime = new Date().getTime();
        $scope.flushRemainingAnswers();

        // Calculate total time in milliseconds
        const totalTimeInMillis = endTime - $scope.startTime;

        // Convert to hours, minutes, seconds
        const hours = Math.floor(totalTimeInMillis / 3600000);
        const minutes = Math.floor((totalTimeInMillis % 3600000) / 60000);
        const seconds = Math.floor((totalTimeInMillis % 60000) / 1000);

        // Store the formatted time
        $scope.totalTimeTaken = `${hours} hours, ${minutes} minutes, ${seconds} seconds`;

        // Ensure Angular updates the view
        $scope.$applyAsync(() => {
            Swal.fire({
                title: "Test Completed!",
                text: `You completed the quiz in ${$scope.totalTimeTaken}. Thank you!`,
                icon: "success"
            });
        });

        $http.get('logoutTime', {
            params: {data: null}
        }).then(function (response) {
            $scope.$applyAsync(() => {
                $location.path("/cog-quiz-finish").search('timeTaken', $scope.totalTimeTaken);
            });
        }, function (error) {
            console.error("Error fetching data", error);
        });

    };



    // Call load on view load
    $scope.$on('$viewContentLoaded', function () {
        $scope.isLoading = true; // Set to true initially
        $scope.loadQuizData();
    });

// Initialize selectedAnswer to store answers for each question
    $scope.selectedAnswer = {}; // Empty object for selected answers
    $scope.answers = [];
    $scope.autoNext = true;
    // Add this to your controller init
    $scope.firstTimeInFactor = true;

    // Add this at the beginning of your controller
    $scope.timerState = {
        activeTimers: [],
        isMemoryTimerActive: false,
        warnings: {30: false, 60: false, 300: false} // Track timer warnings
    };

// Completely clean up all timers
    $scope.cleanupAllTimers = function () {
        // Cancel all interval timers
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval);
            timerInterval = null;
        }
        if (angular.isDefined(memoryImageInterval)) {
            $interval.cancel(memoryImageInterval);
            memoryImageInterval = null;
        }

        // Reset all timer states
        $scope.isTimerRunning = false;
        $scope.timerDisplay = null;
        $scope.countdownTime = 0;
        $scope.timerWarning = false;
        $scope.timerCritical = false;
        $scope.timerState.warnings = {30: false, 60: false, 300: false};
    };

    $scope.startQuizAutomatically = function () {
        // Automatically start quiz when factor loads
        if ($scope.getCurrentFactor().memoryQuestion !== 'TRUE') {
            $scope.startQuiz();
        }
    };

// Call this when factor description page loads
    $scope.$watch('currentFactorIndex', function (newVal) {
        if (newVal !== undefined && $scope.getCurrentFactor()) {
            $scope.startQuizAutomatically();
        }
    });

    $scope.goToNext = function () {
        let currentFactor = $scope.getCurrentFactor();

        // Clear validation message
        $scope.feedbackMessage = null;

        // Reset display flags
        $scope.showParagraphText = false;
        $scope.showMemoryImage = false;

        // If we're at the start of the factor
        if ($scope.currentQuestionIndex === -1) {

            // ✅ Show paragraph text first if paragraphQuestion is TRUE
            if (currentFactor?.paragraphQuestion === 'TRUE' && !$scope.paragraphShown) {
                $scope.cleanupAllTimers();
                $scope.paragraphShown = true;
                $scope.showParagraphText = true;
                return;
            }

            // ✅ Handle memory image first if memoryQuestion is TRUE
            if (currentFactor?.memoryQuestion === 'TRUE' && !$scope.memoryImageShown) {
                $scope.memoryImageShown = true;
                $scope.showMemoryImage = true;
                $scope.startMemoryImage();
                return;
            }

            // Start first question
            $scope.currentQuestionIndex = 0;
            $scope.autoNext = false; // Enable validation for first question
            $scope.startFactorTimer();
            $scope.loadCurrentQuestion();
            return; // Return here to show the first question without moving forward
        }

        let currentQuestion = $scope.getCurrentQuestion();
        if (!currentQuestion)
            return;

        // ✅ Mandatory question validation
        let currentQuestionId = currentQuestion.id;
        let selectedOption = $scope.selectedAnswer[currentQuestionId];
        let isMandatory = currentFactor?.isMandatory === "1" || currentQuestion?.is_mandatory === "1";
        let isDemo = currentQuestion?.is_demo === "0"; // demo question check

        if (!isDemo && isMandatory && (!selectedOption || !selectedOption.option_id) && !$scope.autoNext) {
            Swal.fire({
                title: "Warning!",
                text: "Please select an answer before moving to the next question!",
                icon: "warning",
                confirmButtonText: 'OK',
                backdrop: true
            });
            return;
        }

        // ✅ Save answer
        if (selectedOption && selectedOption.option_id) {
            let existingIndex = $scope.answers.findIndex(ans => ans.questionId === currentQuestionId);
            if (existingIndex === -1) {
                $scope.answers.push({
                    questionId: currentQuestionId,
                    answer: selectedOption
                });
            } else {
                $scope.answers[existingIndex].answer = selectedOption;
            }
        }

        // ✅ If this is the last non-demo question, submit quiz
        if ($scope.getCurrentQuestionNumber() === $scope.totalQuestions) {
            $scope.submitQuiz();
            return;
        }

        $scope.currentQuestionIndex++;

        let questions = currentFactor.questions;

        // ✅ Finished current factor → move to next factor
        if ($scope.currentQuestionIndex >= questions.length) {
            $scope.cleanupAllTimers();

            // Capture **previous factor details before moving to next**
            let completedFactor = currentFactor;

            // Move to next factor
            $scope.currentFactorIndex++;
            $scope.currentQuestionIndex = -1;
            $scope.memoryImageShown = false;
            $scope.paragraphShown = false;
            $scope.descriptionShown = false;

            let nextFactor = $scope.getCurrentFactor();

            // If no more factors, just finish quiz
            if (!nextFactor) {
                console.log("Quiz complete. Answers:", $scope.answers);
                return;
            }

            // ✅ Show popup for the **completed factor**
            // Check if completedFactor exists and has a valid factorName
            if (completedFactor && completedFactor.factorName) {
                Swal.fire({
                    title: completedFactor.factorName, // Use the first-level factorName
                    html: `Section Completed!`,
                    icon: "success",
                    confirmButtonText: "Continue to Next Section",
                    backdrop: true
                }).then(() => {
                    $scope.$apply(() => {
                        // Stay at description page for next factor
                        $scope.currentQuestionIndex = -1;
                    });
                });
            }


            return;
        }

        // ✅ Load next question after increment
        // $scope.loadCurrentQuestion();

        let nextQuestion = $scope.getCurrentQuestion();
        if (nextQuestion) {
            if (nextQuestion.is_demo === "1") {
                if (!$scope.timerRunning) {
                    $scope.startFactorTimer();
                    $scope.timerRunning = true;
                }
            } else {
                $scope.timerDisplay = ''; // Hide timer for demo
            }
        }

        // Reset autoNext
        $scope.autoNext = false;
    };







    $scope.startFactorTimer = function () {
        const currentQuestion = $scope.getCurrentQuestion();
        const factor = $scope.getCurrentFactor();

        // Don't start timer if:
        // - No question
        // - It's a paragraph question
        // - It's a memory question
        // - We're on factor description page
        if (!currentQuestion ||
                (factor && factor.paragraphQuestion === 'TRUE') ||
                (factor && factor.memoryQuestion === 'TRUE') ||
                $scope.currentQuestionIndex === -1) {
            $scope.cleanupAllTimers();
            return;
        }

        // Only start if factor has a valid timer
        if (factor && factor.factor_timer && factor.factor_timer !== "00:00:00") {
            const duration = parseTimerToSeconds(factor.factor_timer);
            $scope.startTimer(duration);
        } else {
            $scope.cleanupAllTimers();
        }
    };


    $scope.showMemoryImage = false;
    $scope.countdownTime = 0;
    let memoryImageInterval = null; // Store the interval reference globally

    $scope.startMemoryImage = function () {
        const factor = $scope.getCurrentFactor();
        $scope.cleanupAllTimers(); // Stop all existing timers

        if (factor.memoryQuestion === 'TRUE') {
            // Decide timer source
            let rawTime = ($scope.currentQuestionIndex === -1)
                    ? (factor.disapearTime?.trim() || factor.factor_timer)
                    : factor.factor_timer;

            $scope.countdownTime = parseTimerToSeconds(rawTime);
            $scope.timerDisplay = formatTime($scope.countdownTime);
            $scope.showMemoryImage = true;
            $scope.timerState.isMemoryTimerActive = true;

            memoryImageInterval = $interval(function () {
                if ($scope.countdownTime > 0) {
                    $scope.countdownTime--;
                    $scope.timerDisplay = formatTime($scope.countdownTime);
                } else {
                    $scope.cleanupAllTimers();
                    $scope.showMemoryImage = false;

                    // ✅ Decide next step
                    if ($scope.currentQuestionIndex === -1) {
                        // Memory image phase just ended
                        $scope.currentQuestionIndex = 0;
                        $scope.startMemoryImage(); // Start questions with factor_timer
                    } else {
                        // Question timer just ended
                        $scope.forceMoveToNextFactor();
                    }
                }
            }, 1000);

            $scope.timerState.activeTimers.push(memoryImageInterval);
        }
    };


// Add this new function to handle forced factor skipping
    $scope.forceMoveToNextFactor = function () {
        // Move to next factor
        $scope.currentFactorIndex++;
        $scope.currentQuestionIndex = -1; // Reset to factor start
        $scope.memoryImageShown = false;
        $scope.paragraphShown = false;

        // Check if quiz is complete
        if ($scope.currentFactorIndex >= $scope.quizData.length) {
            $scope.submitQuiz();
            return;
        }

        // Handle next factor
        const nextFactor = $scope.getCurrentFactor();

        if ($scope.currentQuestionIndex === -1) {
            $scope.currentQuestionIndex = -1;
            $scope.startMemoryImage();
        }
        if (nextFactor.memoryQuestion === 'TRUE') {
            $scope.showMemoryImage = true;
            $scope.startMemoryImage();
        } else if (nextFactor.paragraphQuestion === 'TRUE') {
            $scope.showParagraphText = true;
        } else {
            // Start regular questions for next factor
            $scope.currentQuestionIndex = 0;
            $scope.startFactorTimer();
        }
    };

// Add this cleanup function  
    $scope.stopMemoryImageTimer = function () {
        if (memoryImageInterval) {
            $interval.cancel(memoryImageInterval);
            memoryImageInterval = null;
        }
        $scope.timerDisplay = null;
    };

// Make sure to clean up when controller is destroyed
    $scope.$on('$destroy', function () {
        $scope.stopMemoryImageTimer();
    });


    function parseTimerToSeconds(timerStr) {
        const parts = timerStr.split(":");
        return (+parts[0]) * 3600 + (+parts[1]) * 60 + (+parts[2]);
    }


    $scope.getCurrentFactor = function () {
        if (!$scope.quizData || $scope.currentFactorIndex < 0 || $scope.currentFactorIndex >= $scope.quizData.length) {
            return {};
        }
        return $scope.quizData[$scope.currentFactorIndex];
    };

    $scope.getCurrentQuestion = function () {
        let factor = $scope.getCurrentFactor();
        if (!factor || $scope.currentQuestionIndex < 0)
            return null;

        let q = factor.questions[$scope.currentQuestionIndex] || null;

        // Handle paragraph placeholder
        if (q && q.paragraphQuestion === true) {
            $scope.showParagraphText = true;
            $scope.paragraphText = q.paragraphText;
            return null; // No question to render
        }

        // Handle memory image placeholder
        if (q && q.memoryImagePath) {
            $scope.showMemoryImage = true;
            $scope.memoryImagePath = q.memoryImagePath;
            return null;
        }

        return q;
    };



    $scope.calculateProgress = function () {
        if (!$scope.quizData || !$scope.totalQuestions || $scope.totalQuestions === 0)
            return 0;

        // Calculate completed question number (including current position) for non-demo questions
        let currentQuestionNumber = 0;

        // Count all non-demo questions from previous factors
        for (let i = 0; i < $scope.currentFactorIndex; i++) {
            currentQuestionNumber += $scope.quizData[i].questions.filter(q => q.is_demo === "1").length;
        }

        // Count within current factor (if not description page)
        if ($scope.currentQuestionIndex >= 0) {
            const currentQuestions = $scope.getCurrentFactor().questions;
            for (let j = 0; j <= $scope.currentQuestionIndex; j++) {
                if (currentQuestions[j].is_demo === "1") {
                    currentQuestionNumber++;
                }
            }
        }

        // Calculate progress percentage based on only non-demo questions
        let progress = (currentQuestionNumber / $scope.totalQuestions) * 100;
        return Math.min(Math.round(progress), 100);
    };



    $scope.startTest = function () {
        if (!$scope.quizData || $scope.quizData.length === 0) {
            console.warn("Quiz data not loaded yet.");
            return;
        }

        $scope.showTestInstruction = false; // Hide instructions
        $scope.currentFactorIndex = 0;      // First factor
        $scope.currentQuestionIndex = -1;   // Show factor description first
    };


    $scope.getCurrentQuestionNumberBar = function () {
        if (!$scope.quizData || !$scope.quizData[$scope.currentFactorIndex]) {
            return 0; // No data yet
        }

        if ($scope.currentQuestionIndex === -1) {
            return 0; // On factor intro
        }

        const factor = $scope.quizData[$scope.currentFactorIndex];
        if (!factor.questions || !$scope.quizData[$scope.currentFactorIndex].questions[$scope.currentQuestionIndex]) {
            return 0; // Prevent undefined access
        }

        const currentQuestion = factor.questions[$scope.currentQuestionIndex];

        // Skip numbering for demo questions
        if (currentQuestion.is_demo === '0') {
            return null;
        }

        let questionNumber = 0;
        const currentFactorQuestions = factor.questions;

        for (let j = 0; j <= $scope.currentQuestionIndex; j++) {
            if (currentFactorQuestions[j].is_demo === '1') {
                questionNumber++;
            }
        }

        return questionNumber;
    };


    $scope.getCurrentQuestionNumber = function () {
        if (!$scope.quizData)
            return 0;

        // Count total non-demo questions
        let totalNonDemoQuestions = 0;
        $scope.quizData.forEach(factor => {
            totalNonDemoQuestions += factor.questions.filter(q => q.is_demo === "1").length;
        });

        if (totalNonDemoQuestions === 0)
            return 0;

        // Calculate current position based on non-demo questions
        let currentNonDemoIndex = 0;

        for (let i = 0; i < $scope.currentFactorIndex; i++) {
            currentNonDemoIndex += $scope.quizData[i].questions.filter(q => q.is_demo === "1").length;
        }

        // Add current question if it's non-demo and not description
        if ($scope.currentQuestionIndex >= 0) {
            const currentQuestions = $scope.getCurrentFactor().questions;
            for (let j = 0; j <= $scope.currentQuestionIndex; j++) {
                if (currentQuestions[j].is_demo === "1") {
                    currentNonDemoIndex++;
                }
            }
        }


        return currentNonDemoIndex;
    };







    $scope.getCurrentFactorTotalQuestions = function () {
        if (!$scope.quizData || !$scope.quizData[$scope.currentFactorIndex]) {
            return 0;
        }

        const currentFactorId = $scope.quizData[$scope.currentFactorIndex].factorId;

        // Combine all questions from same factorId
        let combinedQuestions = [];
        $scope.quizData.forEach(factor => {
            if (factor.factorId === currentFactorId) {
                combinedQuestions = combinedQuestions.concat(factor.questions);
            }
        });

        return combinedQuestions.filter(q => q.is_demo === '1').length;
    };


    $scope.getCurrentFactorTestQuestionCount = function () {
        const factor = $scope.getCurrentFactor();
        if (!factor || !factor.questions) {
            return 0; // Return 0 if no factor or questions exist
        }
        return factor.questions.filter(q => q.is_demo === '1').length;
    };


    $scope.isFirstTestQuestion = function () {
        const factor = $scope.getCurrentFactor();
        for (let i = 0; i < $scope.currentQuestionIndex; i++) {
            if (factor.questions[i].is_demo != '0') {
                return false; // Found an earlier test question
            }
        }
        return true; // No previous test question
    };

    $scope.getCurrentFactorQuestionNumber = function () {
        const factor = $scope.getCurrentFactor();
        const currentIndex = $scope.currentQuestionIndex;

        if (currentIndex === -1)
            return {demo: 0, test: 0}; // Intro page

        let demoNumber = 0;
        let testNumber = 0;

        for (let i = 0; i <= currentIndex; i++) {
            if (factor.questions[i].is_demo === '0') {
                demoNumber++;
            } else {
                testNumber++;
            }
        }

        return {
            currentDemo: (factor.questions[currentIndex].is_demo === '0') ? demoNumber : 0,
            currentTest: (factor.questions[currentIndex].is_demo !== '0') ? testNumber : 0
        };
    };






    $scope.goToPrevious = function () {
        // Case 1: Go to previous question in the same factor
        if ($scope.currentQuestionIndex > 0) {
            $scope.currentQuestionIndex--;
            return;
        }

        // Case 2: At the first question, go back to factor description
        if ($scope.currentQuestionIndex === 0) {
            $scope.currentQuestionIndex = -1;

            // Stop timer only when returning to factor description
            if ($scope.isTimerRunning && angular.isDefined(timerInterval)) {
                $interval.cancel(timerInterval);
                timerInterval = null;
                $scope.timerDisplay = null;
                $scope.isTimerRunning = false;
            }

            return;
        }

        // Case 3: At the factor description, go back to the previous factor
        if ($scope.currentQuestionIndex === -1 && $scope.currentFactorIndex > 0) {
            $scope.currentFactorIndex--;

            const previousFactor = $scope.quizData[$scope.currentFactorIndex];
            $scope.currentQuestionIndex = previousFactor.questions.length - 1;

            // Optionally restart timer for the previous factor if needed
            // $scope.startFactorTimer(); // Uncomment if you want to auto-restart

            return;
        }

        // Case 4: Already at the first factor description — stay there
        // Optionally show alert or just do nothing
        console.log("Already at the beginning of the quiz");
    };


// Stop Timer Function to be used when going back
    $scope.stopTimer = function () {
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval); // Clear the interval to stop the timer
            timerInterval = null;
        }
        $scope.timerDisplay = null; // Reset timer display
        $scope.isTimerRunning = false; // Timer is no longer running
    };

    $scope.skipAndContinue = function () {
        // Stop the countdown timer
        if (angular.isDefined(timerInterval)) {
            $interval.cancel(timerInterval);
            timerInterval = null;
        }
        $scope.timerDisplay = null; // Clear the timer display
        $scope.showMemoryImage = false; // Hide the image and countdown timer

        // Move to the next question
        $scope.goToNext(); // Call the function to go to the next question
    };


    $scope.selectedAnswer = {};
    $scope.answerBuffer = [];
    $scope.autoSaveThreshold = 5;

// Function to save the selected answer
    $scope.saveAnswer = function (questionId, option) {
        $scope.selectedAnswer[questionId] = option; // Store the selected option for the current question
    };

    // Function to update feedback for the current question
    // Update feedback for the current question
    $scope.updateFeedbackForCurrentQuestion = function () {
        const currentQuestion = $scope.getCurrentQuestion();
        if (!currentQuestion)
            return;

        // Only show feedback for demo questions
        if (currentQuestion.is_demo === '0') {
            const selected = $scope.selectedAnswer[currentQuestion.id];
            if (selected) {
                const selectedOption = currentQuestion.options.find(opt => opt.id === selected.option_id);
                if (selectedOption) {
                    $scope.feedbackMessage = (selectedOption.is_correct === '1')
                            ? {type: 'success', text: '✅ Correct! Well done.'}
                    : {type: 'error', text: '❌ Wrong answer. Try again!'};
                } else {
                    $scope.feedbackMessage = null;
                }
            } else {
                // No selection yet → hide feedback
                $scope.feedbackMessage = null;
            }
        } else {
            // Test mode question: hide feedback
            $scope.feedbackMessage = null;
        }
    };

// Selecting an option
    $scope.selectOption = function (questionId, option) {
        const currentQuestion = $scope.getCurrentQuestion();
        if (!currentQuestion)
            return;

        // Save the user's selected answer (override previous if any)
        $scope.selectedAnswer[questionId] = {
            question_id: questionId,
            option_id: option.id
        };

        // Buffer for auto-save
        $scope.answerBuffer.push($scope.selectedAnswer[questionId]);
        if ($scope.answerBuffer.length >= $scope.autoSaveThreshold) {
            $scope.saveBufferedAnswers();
        }

        // Show feedback only for demo questions after selection
        $scope.updateFeedbackForCurrentQuestion();
    };

// Load a question by index and auto-update feedback
    $scope.loadQuestion = function (index) {
        $scope.currentQuestionIndex = index;

        // **Do not pre-select demo answers**
        const currentQuestion = $scope.getCurrentQuestion();
        if (currentQuestion && currentQuestion.is_demo === '0') {
            delete $scope.selectedAnswer[currentQuestion.id]; // Reset selection
        }

        $scope.updateFeedbackForCurrentQuestion();
    };

// Navigation functions
    $scope.nextQuestion = function () {
        if ($scope.currentQuestionIndex < $scope.quizData[$scope.currentFactorIndex].questions.length - 1) {
            $scope.loadQuestion($scope.currentQuestionIndex + 1);
        }
    };

    $scope.prevQuestion = function () {
        if ($scope.currentQuestionIndex > 0) {
            $scope.loadQuestion($scope.currentQuestionIndex - 1);
        }
    };




    window.addEventListener('beforeunload', function () {
        if ($scope.answerBuffer.length > 0) {
            navigator.sendBeacon('/quiz/saveAnswers', JSON.stringify({
                answers: $scope.answerBuffer
            }));
        }
    });

    $scope.saveBufferedAnswers = function () {
        if ($scope.answerBuffer.length === 0)
            return;

        document.getElementById("loader").style.display = "block";

        const payload = {
            answers: angular.copy($scope.answerBuffer)  // clone before clearing
        };

        $http.post('/quiz/saveAnswers', payload)
                .then(function (response) {
                    if (response.data.status === 'error') {
                        Swal.fire({
                            title: "Session Expired",
                            text: response.data.message,
                            icon: "warning"
                        }).then(() => {
                            window.location.href = response.data.redirect;
                        });
                    } else {
                        document.getElementById("loader").style.display = "none";
                        $scope.answerBuffer = []; // clear buffer only on success
                    }
                })
                .catch(function (error) {
                    console.error("Error saving batch answers:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "There was a problem saving your answers. Please try again.",
                        icon: "error"
                    });
                });
    };



});

app.filter('properCase', function () {
    return function (input) {
        if (!input)
            return '';
        return input
                .toLowerCase() // Convert all to lowercase
                .replace(/\b\w/g, function (match) {
                    return match.toUpperCase(); // Capitalize first letter of each word
                });
    };
});


app.directive('ngLoad', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('load', function () {
                scope.$apply(attrs.ngLoad);
            });
        }
    };
});

