app.controller('QuestionnaireController', function ($scope, $http, $routeParams, $location, PaginationService) {
    $scope.questionnaireForm = {};
    $scope.isImage = function (url) {
        return (typeof url === 'string') && url.match(/\.(jpeg|jpg|gif|png|svg)$/) !== null;
    };

    $scope.getTrustedImageUrl = function (url) {
        return $sce.trustAsResourceUrl(url);
    };

    $scope.createNew = function () {
        $location.path('manage-questionnaire/');
    };

    $scope.createMemoryQuestion = function () {
        $location.path('memory-questionnaire/');
    };

    $scope.createParagraphQuestion = function () {
        $location.path('paragraph-questionnaire/');
    };

    $scope.pagination = PaginationService.getPagination();
    $scope.fetchData = function () {
        $http.get('/admin/getQuestionnaireList', {
            params: {
                limit: $scope.pagination.limit,
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit,
                search: $scope.searchText
            }
        }).then(function (response) {
            $scope.questionnaires = response.data.data;

            // Set the total pages based on the response
            $scope.pagination.totalPages = Math.ceil(response.data.total / $scope.pagination.limit);

            // Ensure Angular detects the update
            if (!$scope.$$phase) {
                $scope.$apply();
            }
        }, function (error) {
            console.error("Error fetching data", error);
        });
    };

    let searchTimeout;
    $scope.$watch('searchText', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            searchTimeout = setTimeout(() => {
                $scope.pagination.currentPage = 1;
                $scope.fetchData();
            }, 300); // 300ms debounce
        }
    });


    $scope.updateRowsPerPage = function () {
        $scope.pagination.currentPage = 1; // Reset to first page
        $scope.fetchData(); // Reload data with new limit
    };

    // Navigation Methods
    $scope.nextPage = function () {
        if ($scope.pagination.currentPage < $scope.pagination.totalPages) {
            $scope.pagination.currentPage++;
            $scope.fetchData();
        }
    };

    $scope.prevPage = function () {
        if ($scope.pagination.currentPage > 1) {
            $scope.pagination.currentPage--;
            $scope.fetchData();
        }
    };

    $scope.goToPage = function (page) {
        $scope.pagination.currentPage = page;
        $scope.fetchData();
    };

    $scope.getPageNumbers = function () {
        const totalPages = $scope.pagination.totalPages;
        const currentPage = $scope.pagination.currentPage;
        const maxVisible = 5; // Max visible pages at once (excluding ellipses and first/last)
        const pages = [];

        // If total pages <= maxVisible + 2 (accounting for first/last), show all
        if (totalPages <= maxVisible + 2) {
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
            return pages;
        }

        // Always show first page
        pages.push(1);

        // Calculate the range around the current page
        let startPage = Math.max(2, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages - 1, currentPage + Math.floor(maxVisible / 2));

        // Adjust if we're at the beginning or end
        if (currentPage <= Math.floor(maxVisible / 2) + 1) {
            endPage = maxVisible + 1;
        } else if (currentPage >= totalPages - Math.floor(maxVisible / 2)) {
            startPage = totalPages - maxVisible;
        }

        // Add ellipsis or pages between first and current range
        if (startPage > 2) {
            pages.push('...');
        } else {
            // If no ellipsis needed, fill in the gap
            for (let i = 2; i < startPage; i++) {
                pages.push(i);
            }
        }

        // Add the calculated range around current page
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }

        // Add ellipsis or pages between current range and last
        if (endPage < totalPages - 1) {
            pages.push('...');
        } else {
            // If no ellipsis needed, fill in the gap
            for (let i = endPage + 1; i < totalPages; i++) {
                pages.push(i);
            }
        }

        // Always show last page
        pages.push(totalPages);

        return pages;
    };








// Initial Data LoadEditCompanyController
    $scope.fetchData(true);


    $scope.editQuestionnaire = function (questionId) {
        Swal.fire({
            title: "Under construction!!",
            text: "",
            icon: "error",
            backdrop: false
        });
        return false;
        $location.path('manage-questionnaire/' + questionId);
    };

    $scope.toggleStatus = function (questionnaire) {
        let url = '/admin/updateQuestionnareStatus';
        $http.post(url, {
            question_id: questionnaire.id,
            status: questionnaire.status
        }, {
            headers: {'Content-Type': 'application/json'}
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: "Question status successfully updated!",
                icon: "success",
                backdrop: false
            });
        }).catch(function (error) {
            Swal.fire({
                title: "Error!",
                text: "Failed to save Question. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };

    $scope.deleteQuestionnaire = function (questionId) {
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            backdrop: false
        }).then((result) => {
            if (result.isConfirmed) {
                $http.post("/admin/deleteQuestionnaire", JSON.stringify({question_id: questionId}), {
                    headers: {'Content-Type': 'application/json'}
                }).then(function (response) {
                    $scope.fetchData();
                    Swal.fire({
                        title: "Deleted!",
                        text: "Testname has been removed.",
                        icon: "success",
                        backdrop: false
                    });
                }, function (error) {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong.",
                        icon: "error",
                        backdrop: false
                    });
                });
            }
        });
    };

    $scope.viewMemoryImage = function (memory_main_id) {
        Swal.fire({
            title: "Loading...",
            html: `<img src="/path-to-your-loader.gif" style="width: 50px; height: 50px;" />`,
            showConfirmButton: false,
            allowOutsideClick: false,
        });

        $http.get("/admin/getMemoryImageDetails", {params: {memory_main_id: memory_main_id}})
                .then(function (response) {
                    if (response.data.status) {
                        let question = response.data.data;

                        let memoryImageHtml = question.question_image
                                ? `<img src="${question.question_image}" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;" />`
                                : `<p style="color: red;">No image available</p>`;

                        // ✅ Instead of opening a new Swal, update the existing one
                        Swal.update({
                            title: "Memory Image",
                            html: `
                        <div style="text-align: center;">
                            ${memoryImageHtml}
                            <hr>
                        </div>
                    `,
                            showConfirmButton: true,
                            confirmButtonText: "Close",
                        });
                    } else {
                        Swal.fire("Error", "Question data not found!", "error");
                    }
                })
                .catch(function () {
                    Swal.fire("Error", "Failed to fetch data!", "error");
                });
    };



    $scope.viewQuestion = function (questionId) {
        Swal.fire({
            title: "Loading...",
            html: `<img src="/path-to-your-loader.gif" style="width: 50px; height: 50px;" />`,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $http.get("/admin/getQuestionDetails", {params: {question_id: questionId}})
                .then(function (response) {
                    if (response.data.status) {
                        let question = response.data.data;
                        const optionLetters = ["A", "B", "C", "D", "E", "F"];

                        let questionContent = question.question_text
                                ? `<p style="font-size: 18px; font-weight: bold; text-align: center;">${question.question_text}</p>`
                                : `<img src="${question.question_image}" style="max-width:100%; display:block; margin: auto;" />`;

                        let longOptionExists = question.options.some(opt => opt.option_text && opt.option_text.length > 80);
                        let optionsHtml = longOptionExists
                                ? `<div style="display: flex; flex-direction: column; gap: 10px; text-align: left;">`
                                : `<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left;">`;

                        question.options.forEach((opt, index) => {
                            let optionLetter = optionLetters[index] || String.fromCharCode(65 + index); // A, B, C, D...

                            if (opt.option_text) {
                                optionsHtml += `
                                <div style="font-size: 16px; padding: 5px;">
                                    <strong>${optionLetter}.</strong> ${opt.option_text}?
                                    ${opt.is_correct === "1" ? '<span style="color: green; font-weight: bold;"> (Correct)</span>' : ""}
                                    ${opt.option_mark > 0 ? `<span style="color: blue; font-weight: bold;"> (+${opt.option_mark})</span>` : ""}
                                </div>`;
                            } else if (opt.option_image) {
                                optionsHtml += `
                                <div style="text-align: left; padding: 5px;">
                                    <strong>${optionLetter}.</strong> 
                                    <img src="${opt.option_image}" style="max-width: 80px; height: auto; border-radius: 5px; margin-left: 10px;" />?
                                    ${opt.is_correct === "1" ? '<span style="color: green; font-weight: bold;"> (Correct)</span>' : ""}
                                    ${opt.option_mark > 0 ? `<span style="color: blue; font-weight: bold;"> (+${opt.option_mark})</span>` : ""}
                                </div>`;
                            }
                        });


                        optionsHtml += `</div>`;

                        Swal.fire({
                            html: `
                        <div style="text-align: center;">
                            ${questionContent}
                            <hr>
                            <p><strong>Options:</strong></p>
                            ${optionsHtml}
                        </div>
                    `,
                            showConfirmButton: true,
                            confirmButtonText: "Close",
                        });
                    } else {
                        Swal.fire("Error", "Question data not found!", "error");
                    }
                })
                .catch(function () {
                    Swal.fire("Error", "Failed to fetch data!", "error");
                });
    };
});

app.controller('EditQuestionnaireController', function ($scope, $http, $routeParams, $location) {

    $scope.questionnaireForm = {};

    $scope.cancelEdit = function () {
        $location.path('/questionarre-list'); // Adjust this path based on your routing configuration
    };

    $scope.isImage = function (url) {
        return (typeof url === 'string') && url.match(/\.(jpeg|jpg|gif|png|svg)$/) !== null;
    };
// Function to submit the form data
    $scope.isSubmitting = false;
    $scope.saveQuestionnaire = function (questionnaireId = null) {
        if ($scope.isSubmitting) {
            return; // Prevent multiple submissions
        }

        $scope.isSubmitting = true; // Disable button
        var formData = new FormData();

        formData.append("testFactorId", $scope.questionnaireForm.testFactorId);
        formData.append("languageId", $scope.questionnaireForm.languageId);
        formData.append("questionType", $scope.questionnaireForm.questionType);
        formData.append("questionMark", $scope.questionnaireForm.questionMark);
        formData.append("is_demo", $scope.questionnaireForm.isDemo);
        formData.append("status", $scope.questionnaireForm.status);

        // Check if text-based or image-based question
        if ($scope.questionnaireForm.questionType === '1' || $scope.questionnaireForm.questionType === '2') {
            formData.append("textQuestion", $scope.questionnaireForm.textQuestion || "");
        } else if ($scope.questionnaireForm.questionType === '3' || $scope.questionnaireForm.questionType === '4') {
            let imageFile = $scope.questionnaireForm.imageQuestion;
            if (imageFile) {
                formData.append("imageQuestion", imageFile);
                console.log("Image added to formData:", imageFile.name);
            } else {
                console.error("No image selected.");
            }
        }

        // Handle Multiple Options (Including Image Uploads)
        angular.forEach($scope.questionnaireForm.questions, function (question, index) {
            formData.append("options[" + index + "][text]", question.text || "");
            // Append marks
            formData.append("options[" + index + "][option_mark]", question.option_mark || "0");

            // Check if an image is selected for this option
            if (question.image) {
                formData.append("options[" + index + "][image]", question.image);
                console.log("Option Image " + (index + 1) + " added:", question.image.name);
            } else {
                console.log("No image selected for option " + (index + 1));
            }
        });

        formData.append("correctAnswer", $scope.questionnaireForm.correctAnswer);

        // Send Data to Backend
        $http.post("/admin/saveQuestionnaire", formData, {
            headers: {'Content-Type': undefined}
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: "Question added successfully!",
                icon: "success",
                backdrop: false
            });
            $location.path('/questionarre-list');
        }).catch(function (error) {
            console.error("Error saving data:", error);
        }).finally(function () {
            $scope.isSubmitting = false; // Enable button again
        });
    };

    $scope.setCorrectAnswer = function (index) {
        $scope.questionnaireForm.correctAnswer = index; // Set correct answer
    };

    $scope.isImage = function (value) {
        if (!value)
            return false; // Handle null or empty values
        return /\.(jpeg|jpg|gif|png|webp|svg)$/i.test(value); // Regex to check if it's an image URL
    };

    $scope.getTestsFactor = function () {
        $http.get('/admin/getAllTestFactor')
                .then(function (response) {
                    $scope.test_factor_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getTestsFactor();

    $scope.getLanguage = function () {
        $http.get('/admin/getLanguage')
                .then(function (response) {
                    $scope.language_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getLanguage();

    $scope.questionTypes = [
        {id: '1', name: 'Text Question'},
        {id: '2', name: 'Text Question with Image Option'},
        {id: '3', name: 'Image Question with Image Option'},
        {id: '4', name: 'Image Question with Text Option'},
    ];

    $scope.noOfQuestionOptions = Array.from({length: 10}, (_, i) => i + 1); // [1,2,3,4...10]

    $scope.questionnaireForm = {
        questionType: '', // Selected question type
        noOfQuestions: 1, // Default number of questions
        questions: [] // Stores text inputs or file uploads dynamically
    };

    // Watch for changes in noOfQuestions and update the questions array
    $scope.$watch('questionnaireForm.noOfQuestions', function (newVal) {
        $scope.questionnaireForm.questions = new Array(parseInt(newVal) || 0).fill().map(() => ({
                text: '',
                image: '',
                correct: false // Default correct answer
            }));
    });

    // Function to set the correct answer
    $scope.setCorrectAnswer = function (index) {
        $scope.questionnaireForm.questions.forEach((question, i) => {
            question.correct = i === index; // Ensure only one correct answer is selected
        });
    };

    $scope.loadQuestionnaire = function (questionId) {
        $http.get('/admin/loadQuestionnaire/' + questionId)
                .then(function (response) {
                    if (response.data.question) {
                        let langId = response.data.question.language_id ? Number(response.data.question.language_id) : null;

                        $scope.questionnaireForm = {
                            id: response.data.question.id,
                            questionType: response.data.question.question_type.toString(),
                            textQuestion: response.data.question.question_text || '',
                            imageQuestion: response.data.question.question_image || '',
                            imagePreview: response.data.question.question_image ? response.data.question.question_image : '', // Ensure correct path
                            testFactorId: response.data.question.test_factor_id,
                            languageId: langId,
                            questionMark: response.data.question.question_mark.toString(),
                            noOfQuestions: response.data.options.length,
                            questions: response.data.options.map((option, index) => ({
                                    id: option.id,
                                    text: option.option_text || '',
                                    image: option.option_image || '',
                                    imagePreview: option.option_image ? option.option_image : '', // Ensure preview loads
                                    correct: option.is_correct === "1" ? index : null
                                }))
                        };

                        $scope.questionnaireForm.correctAnswer = $scope.questionnaireForm.questions.findIndex(q => q.correct !== null);
                    }
                })
                .catch(function (error) {
                    console.error("Error fetching question:", error);
                });
    };



    $scope.previewImage = function (event, modelName) {
        let reader = new FileReader();
        reader.onload = function () {
            $scope.$apply(function () {
                if (modelName === 'imageQuestion') {
                    $scope.questionnaireForm.imagePreview = reader.result;
                } else if (modelName.startsWith('questions[')) {
                    let index = parseInt(modelName.match(/\d+/)[0]); // Extract index
                    $scope.questionnaireForm.questions[index].image = reader.result;
                    $scope.questionnaireForm.questions[index].imagePreview = reader.result; // Ensure preview updates
                }
            });
        };
        reader.readAsDataURL(event.target.files[0]);
    };




    if ($routeParams.questionnaireId) {
        $scope.loadQuestionnaire($routeParams.questionnaireId);
    }
});

app.controller('MemoryQuestionnaireController', function ($scope, $http, $routeParams, $location, $timeout) {
    $scope.quiz = {
        image: '',
        questions: []
    };

    $scope.uploadImage = function (input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                $scope.$apply(function () {
                    $scope.quiz.main_image_preview = e.target.result; // ✅ Base64 for preview
                    $scope.quiz.main_image = file; // ✅ File object for upload
                });
            };

            reader.readAsDataURL(file);
        }
    };

    $scope.cancelEdit = function () {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Any unsaved changes will be lost!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'No, Stay'
        }).then((result) => {
            if (result.isConfirmed) {
                $timeout(function () {
                    $location.path('/questionarre-list');
                });
            }
        });
    };


    $scope.quiz = $scope.quiz || {}; // Ensure quiz object exists
    $scope.quiz.questions = $scope.quiz.questions || []; // Ensure questions array exists

    $scope.questionCounter = 1; // Initialize counter

    $scope.addQuestion = function () {
        if (!$scope.quiz.questions) {
            $scope.quiz.questions = []; // Ensure array is initialized
        }

        $scope.quiz.questions.push({
            question_id: $scope.questionCounter++, // Increment on each addition
            question_image: null, // Store uploaded image
            options: [{text: ''}, {text: ''}, {text: ''}, {text: ''}], // Default 4 options
            correctOption: 0
        });
    };


    // Remove a question
    $scope.removeQuestion = function (index) {
        $scope.quiz.questions.splice(index, 1);
    };

    // Save the quiz
    $scope.saveQuiz = function () {
        console.log("Quiz Saved:", $scope.quiz);
        alert("Quiz data saved successfully!");
    };


    $scope.imageQuestionArray = []; // Array to store images linked to questions

    $scope.uploadQuestionImage = function (file, question) {
        console.log("Received File:", file);
        console.log("Received Question:", question);

        if (!file) {
            console.error("❌ File is missing!");
        }

        if (!question) {
            console.error("❌ Question object is missing!");
        }

        if (!file || !question) {
            console.error("Invalid file or question object:", file, question);
            return;
        }

        question.image = file; // Assign the file to the question
        console.log("✅ Image added to question:", question);
        $scope.$apply(); // Ensure Angular updates the UI
    };

    $scope.isSubmitting = false;
    $scope.saveMemoryQuestionnaire = function (quizId) {
        $scope.isSubmitting = true;
        var formData = new FormData();
        formData.append("testFactorId", $scope.quiz.testFactorId);
        formData.append("languageId", $scope.quiz.languageId);
        formData.append("questionType", 5);
        formData.append("questionMark", $scope.quiz.questionMark);
        formData.append("is_demo", $scope.quiz.isDemo);
        formData.append("status", $scope.quiz.status);
        formData.append("disapearingTime", $scope.quiz.disapearingTime);

        if ($scope.quiz.main_image instanceof File) {
            formData.append('memory_main_image', $scope.quiz.main_image);
        }


        angular.forEach($scope.quiz.questions, function (question, qIndex) {
            formData.append(`questions[${qIndex}][question_id]`, question.question_id);
            formData.append(`questions[${qIndex}][correctOption]`, question.correctOption);

            // ✅ Ensure file is correctly appended
            if (question.image instanceof File) {
                formData.append(`questions[${qIndex}][image]`, question.image, question.image.name);
            } else {
                console.error(`❌ Invalid file format for question ${qIndex}`, question.image);
            }

            angular.forEach(question.options, function (option, optIndex) {
                formData.append(`questions[${qIndex}][options][${optIndex}]`, option.text);
            });
        });

        // ✅ Debug FormData
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]); // Ensure file appears
        }

        // ✅ Send Data to Backend
        $http.post('admin/saveMemoryQuestions', formData, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: "Question added successfully!",
                icon: "success",
                backdrop: false
            });
            $scope.isSubmitting = false;
            $location.path('/questionarre-list');
        }, function (error) {
            $scope.isSubmitting = false;
            console.error('Upload failed:', error);
        });
    };
    $scope.getTestsFactor = function () {
        $http.get('/admin/getAllTestFactor')
                .then(function (response) {
                    $scope.test_factor_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getTestsFactor();

    $scope.getLanguage = function () {
        $http.get('/admin/getLanguage')
                .then(function (response) {
                    $scope.language_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getLanguage();

    $scope.questionTypes = [
        {id: '1', name: 'Text Question'},
        {id: '2', name: 'Text Question with Image Option'},
        {id: '3', name: 'Image Question with Image Option'},
        {id: '4', name: 'Image Question with Text Option'},
    ];



});


app.controller('ParagraphQuestionnaireController', function ($scope, $http, $routeParams, $location, PaginationService, $timeout) {

    $scope.paragraph = '';
    $scope.questions = [];

    $scope.quiz = {
        image: '',
        questions: []
    };

    $scope.getTestsFactor = function () {
        $http.get('/admin/getAllTestFactor')
                .then(function (response) {
                    $scope.test_factor_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getTestsFactor();
    
    $scope.cancelEdit = function () {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Any unsaved changes will be lost!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'No, Stay'
        }).then((result) => {
            if (result.isConfirmed) {
                $timeout(function () {
                    $location.path('/questionarre-list');
                });
            }
        });
    };

    $scope.getLanguage = function () {
        $http.get('/admin/getLanguage')
                .then(function (response) {
                    $scope.language_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };
    $scope.getLanguage();

    $scope.questionTypes = [
        {id: '1', name: 'Text Question'},
        {id: '2', name: 'Text Question with Image Option'},
        {id: '3', name: 'Image Question with Image Option'},
        {id: '4', name: 'Image Question with Text Option'},
    ];


    $scope.addQuestion = function () {
        $scope.questions.push({text: '', options: [{text: ''}]});
    };

    $scope.addOption = function (question) {
        question.options.push({text: ''});
    };

    $scope.removeOption = function (question, index) {
        question.options.splice(index, 1);
    };

    $scope.submit = function () {
        $scope.submittedData = {
            paragraph: $scope.paragraph,
            questions: angular.copy($scope.questions)
        };
    };

    $scope.removeQuestion = function (index) {
        $scope.questions.splice(index, 1);
    };

    $scope.saveParagraphQuestionnaire = function () {
        $scope.isSubmitting = true;

        // Collect all necessary data
        var dataToSend = {
            testFactorId: $scope.quiz.testFactorId,
            disapearingTime: $scope.quiz.disapearingTime,
            isDemo: $scope.quiz.isDemo,
            languageId: $scope.quiz.languageId,
            questionMark: $scope.quiz.questionMark,
            status: $scope.quiz.status,
            paragraph: $scope.paragraph,
            questions: $scope.questions
        };

        $http.post('admin/saveParagraphQuestions', dataToSend)
                .then(function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Question added successfully!",
                        icon: "success",
                        backdrop: false
                    });
                    $scope.isSubmitting = false;
                    $location.path('/questionarre-list');

                }, function (error) {
                    $scope.isSubmitting = false;
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to save questionarrie. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: false
                    });
                    console.error(error);
                });
    };

});


app.service('PaginationService', function () {
    var pagination = {
        currentPage: 1,
        limit: 10,
        totalPages: 1
    };

    return {
        getPagination: function () {
            return pagination;
        },
        setTotalPages: function (total) {
            pagination.totalPages = Math.ceil(total / pagination.limit);
        }
    };
});

