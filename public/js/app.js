var app = angular.module("myApp", ["ngRoute", "ngFileUpload"]);

app.config(function ($routeProvider) {
    $routeProvider
            .when("/", {
                templateUrl: "dashboard",
                controller: "DashboardController"
            })
            .when("/manage-admin", {
                templateUrl: "admin/adminUser",
                controller: "AdminUserController"
            })
            .when("/manage-admin-user/:id?", {// The `?` makes `id` optional
                templateUrl: function (params) {
                    return params.id ? "admin/manageAdminUser/" + params.id : "admin/manageAdminUser";
                },
                controller: "EditUserController"
            }).when("/manage-companies", {
        templateUrl: "admin/manageCompanies",
        controller: "CompanyController"
    })
            .when("/manage-company/:companyId?", {// The '?' denotes an optional parameter
                templateUrl: function (params) {
                    return params.companyId
                            ? "admin/manageCompany/" + params.companyId
                            : "admin/manageCompany";  // Default view when companyId is not provided
                },
                controller: "EditCompanyController"
            })
            .when("/save-company/:companyId?", {// The '?' denotes an optional parameter
                templateUrl: function (params) {
                    return params.companyId
                            ? "admin/saveCompany/" + params.companyId
                            : "admin/saveCompany";  // Default view when companyId is not provided
                },
                controller: "EditCompanyController"
            })
            .when("/manage-tests", {
                templateUrl: "admin/manageTests",
                controller: "TestController"
            })
            .when("/manage-test-factor", {
                templateUrl: "admin/manageTestFactor",
                controller: "TestFactorController"
            })
            .when("/generate-user", {
                templateUrl: "admin/generateUser",
                controller: "GenerateUserController"
            })
            .when("/create-test", {
                templateUrl: "admin/createTest",
                controller: "CreateTestController"
            })
            .when("/questionarre-list", {
                templateUrl: "admin/questionarreList",
                controller: "QuestionnaireController"
            })
            .when("/manage-questionnaire/:questionnaireId?", {// The '?' denotes an optional parameter
                templateUrl: function (params) {
                    return params.questionnaireId
                            ? "admin/manageQuestionnaire/" + params.questionnaireId
                            : "admin/manageQuestionnaire";  // Default view when companyId is not provided
                },
                controller: "EditQuestionnaireController"
            })
            .when("/memory-questionnaire", {
                templateUrl: "admin/memoryQuestionnaire",
                controller: "MemoryQuestionnaireController"
            })
            .when("/paragraph-questionnaire", {
                templateUrl: "admin/paragraphQuestionnaire",
                controller: "ParagraphQuestionnaireController"
            })
            .when("/master-test", {
                templateUrl: "admin/createMasterTest",
                controller: "CreateMasterTestController"
            })
            .when("/main-reports", {
                templateUrl: "admin/mainReports",
                controller: "ReportController"
            })
            .otherwise({
                redirectTo: "/"
            });
});

app.controller('ReportController', function ($scope, $http, $routeParams, $location, PaginationService) {
    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.reportStats = {
        total_users: 0,
        completed: 0,
        in_progress: 0,
        not_started: 0
    };
    $scope.filters = {
        status: '',
        test_name: '',
        company: '',
        dateFrom: '',
        dateTo: ''
    };
    $scope.availableTests = [];
    $scope.availableCompanies = [];
    $scope.selectAll = false;

    $scope.$on('$routeChangeSuccess', function () {
        $scope.fetchData();
        $scope.loadFilterOptions();
    });

    $scope.fetchData = function () {
        $http.get('/admin/getReportUsersList', {
            params: {
                limit: $scope.pagination.limit,
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit,
                search: $scope.searchText,
                status: $scope.filters.status,
                test_name: $scope.filters.test_name,
                company: $scope.filters.company,
                date_from: $scope.filters.dateFrom,
                date_to: $scope.filters.dateTo
            }
        }).then(function (response) {
            $scope.users = response.data.data;
            $scope.users.forEach(user => {
                user.status = parseInt(user.status, 10);
                user.selected = false;
            });
            PaginationService.setTotalPages(response.data.total);
            $scope.calculateStats();
        }, function (error) {
            console.error("Error fetching data", error);
        });
    };

    $scope.loadFilterOptions = function () {
        $http.get('/admin/getReportFilterOptions').then(function (response) {
            if (response.data) {
                $scope.availableTests = response.data.tests || [];
                $scope.availableCompanies = response.data.companies || [];
            }
        });
    };

    $scope.calculateStats = function () {
        if (!$scope.users) return;

        $scope.reportStats.total_users = $scope.users.length;
        $scope.reportStats.completed = $scope.users.filter(u => u.questionTaken > 0 && u.questionTaken == u.progress).length;
        $scope.reportStats.in_progress = $scope.users.filter(u => u.questionTaken > 0 && u.questionTaken < u.progress).length;
        $scope.reportStats.not_started = $scope.users.filter(u => u.questionTaken == 0).length;
    };

    $scope.applyFilters = function () {
        $scope.pagination.currentPage = 1;
        $scope.fetchData();
    };

    $scope.clearFilters = function () {
        $scope.filters = {
            status: '',
            test_name: '',
            company: '',
            dateFrom: '',
            dateTo: ''
        };
        $scope.searchText = '';
        $scope.applyFilters();
    };

    // Watch for changes in search and reset pagination
    $scope.$watch('searchText', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.pagination.currentPage = 1;
            $scope.fetchData();
        }
    });

    $scope.updateRowsPerPage = function () {
        $scope.pagination.currentPage = 1;
        $scope.fetchData();
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
    $scope.fetchData();

    $scope.bar = function (questionTaken, progress) {
        if (progress > 0) {
            return (questionTaken / progress) * 100;
        }
        return 0; // Prevent division by zero
    };

    $scope.downloadExcelReport = function (userId, testReportId) {
        if (testReportId == null || testReportId === '') {
            Swal.fire({
                title: "Missing Information",
                text: "A report hasn't been set up for this test yet. Please configure the test properly.",
                icon: "warning",
                button: "OK",
            });
            return;
        }

        $scope.isDownloading = true;

        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/report/processExcelReport?userId=' + userId + '&testReportId=' + testReportId;

        iframe.onload = function () {
            $scope.$apply(() => {
                $scope.isDownloading = false;
            });
        };

        document.body.appendChild(iframe);

        setTimeout(() => {
            $scope.$apply(() => {
                $scope.isDownloading = false;
            });
        }, 10000);
    };


    $scope.downloadMSPPDFReport = function (userId, reportId) {
        $scope.isDownloading = true;
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        if (reportId == 1 || reportId == 2) {
            iframe.src = '/report/downloadMSPPDFReport?userId=' + userId;
        }
        if (reportId == 7 || reportId == 8) {
            iframe.src = '/report/downloadMFPDFReport?userId=' + userId;
        }
        if (reportId == 22) {
            iframe.src = '/report/downloadCOGPDFReport?userId=' + userId;
        }
        if (reportId == 23) {
            iframe.src = '/report/downloadCOGCRPDFReport?userId=' + userId;
        }

        iframe.onload = function () {
            $scope.$apply(() => {
                $scope.isDownloading = false;
            });
        };

        document.body.appendChild(iframe);

        setTimeout(() => {
            $scope.$apply(() => {
                $scope.isDownloading = false;
            });
        }, 10000);
    };

    // Helper functions
    $scope.getProgressPercent = function (taken, total) {
        if (total === 0) return 0;
        return Math.round((taken / total) * 100);
    };

    $scope.getStatus = function (taken, total) {
        if (taken === 0) return 'Not Started';
        if (taken < total) return 'In Progress';
        return 'Completed';
    };

    $scope.toggleSelectAll = function () {
        if ($scope.users) {
            $scope.users.forEach(user => {
                user.selected = $scope.selectAll;
            });
        }
    };

    $scope.viewUserDetails = function (user) {
        Swal.fire({
            title: '<strong>User Details</strong>',
            html: `
                <div style="text-align: left;">
                    <p><strong>Name:</strong> ${user.uName}</p>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>User ID:</strong> ${user.user_id}</p>
                    <p><strong>Company:</strong> ${user.cName}</p>
                    <p><strong>Test:</strong> ${user.test_name}</p>
                    <p><strong>Progress:</strong> ${user.questionTaken}/${user.progress} (${$scope.getProgressPercent(user.questionTaken, user.progress)}%)</p>
                    <p><strong>Status:</strong> ${$scope.getStatus(user.questionTaken, user.progress)}</p>
                    <p><strong>Report Date:</strong> ${new Date(user.reportingDate).toLocaleDateString()}</p>
                </div>
            `,
            icon: 'info',
            showCloseButton: true,
            confirmButtonText: 'Close',
            backdrop: false
        });
    };

    $scope.bulkDownloadPDF = function () {
        const selectedUsers = $scope.users.filter(u => u.selected && u.questionTaken > 0);
        if (selectedUsers.length === 0) {
            Swal.fire({
                title: 'No Users Selected',
                text: 'Please select users with completed tests to download reports',
                icon: 'warning',
                backdrop: false
            });
            return;
        }

        Swal.fire({
            title: 'Bulk Download',
            text: `Download PDF reports for ${selectedUsers.length} selected users?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Download',
            cancelButtonText: 'Cancel',
            backdrop: false
        }).then((result) => {
            if (result.isConfirmed) {
                selectedUsers.forEach((user, index) => {
                    setTimeout(() => {
                        $scope.downloadMSPPDFReport(user.id, user.testReportId);
                    }, index * 1000);
                });
            }
        });
    };

    $scope.bulkDownloadExcel = function () {
        const selectedUsers = $scope.users.filter(u => u.selected && u.questionTaken > 0);
        if (selectedUsers.length === 0) {
            Swal.fire({
                title: 'No Users Selected',
                text: 'Please select users with completed tests to download reports',
                icon: 'warning',
                backdrop: false
            });
            return;
        }

        Swal.fire({
            title: 'Bulk Download',
            text: `Download Excel reports for ${selectedUsers.length} selected users?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Download',
            cancelButtonText: 'Cancel',
            backdrop: false
        }).then((result) => {
            if (result.isConfirmed) {
                selectedUsers.forEach((user, index) => {
                    setTimeout(() => {
                        $scope.downloadExcelReport(user.id, user.testReportId);
                    }, index * 1000);
                });
            }
        });
    };

    $scope.exportAllReports = function () {
        Swal.fire({
            title: 'Export All Reports',
            text: 'This feature will be available soon',
            icon: 'info',
            backdrop: false
        });
    };

    // Initialize
    $scope.fetchData();
    $scope.loadFilterOptions();
});
app.controller('CreateMasterTestController', function ($scope, $http, $routeParams, $location, PaginationService, $timeout) {
    $scope.test_names = {};
    $scope.isFormCollapsed = true;
    $scope.loadTests = function () {
        $http.get('/admin/getTestList')
                .then(function (response) {
                    $scope.test_names = response.data.master_test_data || [];
                    $scope.test_reports = response.data.test_reports || [];
                });
    };
    $scope.saveMasterTest = function (testId) {
        // Filter selected test factors and get their IDs
        let selectedTestIds = $scope.test_names
                .filter(factor => factor.selected === '1') // Keep only selected checkboxes
                .map(factor => factor.id); // Extract the factor IDs

        console.log("Selected Factor IDs:", selectedTestIds);
        // Prepare the request payload
        let requestData = {
            creator_name: $scope.test.creator_name,
            test_name: $scope.test.test_name,
            user_prefix: $scope.test.user_prefix,
            test_report_id: $scope.test.test_report_id,
            test_ids: selectedTestIds // Send the selected factor IDs
        };

        let url = testId ? '/admin/saveMasterTest/' + testId : '/admin/saveMasterTest';
        // Send data to the backend
        $http.post(url, requestData)
                .then(function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Test saved successfully!",
                        icon: "success",
                        backdrop: false
                    });
                    $scope.test = {};
                    $scope.loadTests(); // Reload test list
                    $scope.fetchData();
                })
                .catch(function (error) {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to save test. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: false
                    });
                });
    };

    $scope.cancelEdit = function () {
        $scope.test = {}; // Clear form data

        // Uncheck all checkboxes
        $scope.test_names.forEach(factor => {
            factor.selected = '0'; // Set all checkboxes to unchecked
        });
    };

    $scope.deleteMasterTest = function (test) {
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
                $http.post("/admin/deleteMasterTest", {id: test.id})
                        .then(function (response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Test has been removed.",
                                icon: "success",
                                backdrop: false
                            });

                            $scope.test = {};
                            $scope.currentPage = 1;  // Reset to first page
                            $scope.loadTests(); // Reload test list from page 1
                            $scope.fetchData();
                        }, function (error) {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                backdrop: false
                            });
                        });
            }
        });
    };



    $scope.loadTests();

    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.fetchData = function () {
        $http.get('/admin/getMasterTestListData', {
            params: {limit: $scope.pagination.limit, offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit}
        }).then(function (response) {
            $scope.tests = response.data.data;
            PaginationService.setTotalPages(response.data.total); // Update total pages
        }, function (error) {
            console.error("Error fetching data", error);
        });
    };

    // 👇 Delay fetch to ensure pagination is initialized
    $timeout(function () {
        $scope.fetchData();
    }, 0);

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
    $scope.fetchData();

    $scope.editMasterTest = function (testId) {
        $http.get("/admin/getMasterTestRowData/" + testId)
                .then(function (response) {
                    let testData = response.data;

                    // Assign basic test details
                    $scope.test = {
                        id: testData.id,
                        test_name: testData.test_name,
                        creator_name: testData.creator_name,
                        test_report_id: testData.test_report_id,
                        user_prefix: testData.user_prefix,
                    };
                    // Parse and load selected factors
                    let selectedFactors = JSON.parse(testData.test_ids); // ["1", "2", "7", "8"]

                    $scope.test_names.forEach(factor => {
                        factor.selected = selectedFactors.includes(factor.id.toString()) ? '1' : '0';
                    });

                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                })
                .catch(function (error) {
                    console.error("Error fetching test data:", error);
                });
    };

});


app.controller('GenerateUserController', function ($scope, $http, $routeParams, $location, PaginationService) {

    $scope.extendUserValidity = function () {
        // Get selected users
        $scope.selectedUsers = $scope.users.filter(user => user.extendValidity);

        console.log($scope.selectedUsers);

        if ($scope.selectedUsers.length === 0) {
            Swal.fire("No Users Selected", "Please select at least one user.", "warning");
            return;
        }

        // Show Bootstrap-styled SweetAlert2 form
        Swal.fire({
            title: "Update Validity Dates",
            html: `
            <form id="dateForm">
                <div class="mb-3 text-start">
                    <label class="form-label">Start Date:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">End Date:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
            </form>
        `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: "Update",
            cancelButtonText: "Cancel",
            preConfirm: () => {
                return {
                    start_date: document.getElementById('start_date').value,
                    end_date: document.getElementById('end_date').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = {
                    users: $scope.selectedUsers.map(user => user.id), // Get selected user IDs
                    start_date: result.value.start_date,
                    end_date: result.value.end_date
                };

                // Send data to backend
                $http.post("/admin/updateUserValidity", formData)
                        .then(function (response) {
                            if (response.data.status === "success") {
                                Swal.fire("Updated!", response.data.message, "success");

                                // Update UI: Change validity dates for selected users
                                angular.forEach($scope.selectedUsers, function (user) {
                                    user.validity_from = result.value.start_date;
                                    user.validity_to = result.value.end_date;
                                    user.extendValidity = false; // Uncheck checkbox after update
                                });

                                $scope.selectedUsers = []; // Reset selected users
                            } else {
                                Swal.fire("Error!", response.data.message, "error");
                            }
                        }, function (error) {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        });
            }
        });
    };



    $scope.getTests = function () {
        $http.get('/admin/getAllTest')
                .then(function (response) {
                    $scope.test_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };

    $scope.getRemainingDays = function (start, end) {
        if (!start || !end)
            return "N/A";

        // Convert to date-only (strip time part to avoid partial day errors)
        //const startDate = new Date(start);
        const startDate = new Date();
        startDate.setHours(0, 0, 0, 0);
        const endDate = new Date(end);

        // Set both to midnight to avoid time diff skew
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);

        const timeDiff = endDate.getTime() - startDate.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

        return daysDiff < 0 ? 0 : daysDiff; // Avoid negative days
    };


    $scope.getTests();

    $scope.seenGroups = {}; // Initialize tracking object

    $scope.isFirstInGroup = function (user, index) {
        // Check if any previous row has the same color_code
        for (let i = 0; i < index; i++) {
            if ($scope.filteredUsers[i].color_code === user.color_code) {
                return false; // Another row with the same color_code already exists
            }
        }
        return true; // This is the first occurrence of this color_code
    };



    $scope.saveGenerateUser = function (testId) {
        let requestData = {
            user_type: $scope.generate.user_type,
            no_of_users: $scope.generate.no_of_users,
            valitity: $scope.generate.valitity,
            company_id: $scope.generate.company_id ?? null,
            test_id: $scope.generate.testId.id,
            test_name: $scope.generate.testId.test_name,
            is_master: $scope.generate.testId.is_master
        };
        let url = testId ? '/admin/saveGenerateUser/' + testId : '/admin/saveGenerateUser';
        $http.post(url, requestData)
                .then(function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Test saved successfully!",
                        icon: "success",
                        backdrop: false
                    });
                    $scope.generate = {};
                    $scope.currentPage = 1;  // Reset to first page
                    $scope.fetchData();
                })
                .catch(function (error) {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to save test. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: false
                    });
                });
    };
    $scope.getComany = function () {
        $http.get('/admin/getAllCompany')
                .then(function (response) {
                    $scope.company_list = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };

    $scope.getComany();

    $scope.getUserSections = function () {
        $http.get('/admin/getUserSections')
                .then(function (response) {
                    $scope.user_sections = response.data; // API should return an array of test objects
                    setTimeout(function () {
                        $(".chosen-select").trigger("chosen:updated"); // Update Chosen after data loads
                    }, 100);
                })
                .catch(function (error) {
                    console.error("Error fetching test list:", error);
                });
    };

    $scope.downloadUserGroup = function (groupId) {
        window.location.href = '/admin/downloadUserGroup?group_id=' + groupId;
    };

    $scope.selectAll = false; // Default unchecked

    $scope.toggleAllExtendValidity = function () {
        angular.forEach($scope.users, function (user) {
            user.extendValidity = $scope.selectAll; // Set all checkboxes to match master checkbox
        });
    };



    $scope.getUserSections();

    $scope.valid_months = Array.from({length: 12}, (_, i) => (i + 1) + " months");

    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.fetchData = function () {
        $http.get('/admin/getUsersList', {
            params: {
                limit: $scope.pagination.limit, // Use the selected limit
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit,
                search: $scope.searchText // Pass search query to backend
            }
        }).then(function (response) {
            $scope.users = response.data.data;
            $scope.users.forEach(user => {
                user.status = parseInt(user.status, 10); // Convert to integer
            });
            PaginationService.setTotalPages(response.data.total); // Update total pages
        }, function (error) {
            console.error("Error fetching data", error);
        });
    };

    // Watch for changes in search and reset pagination
    $scope.$watch('searchText', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.fetchData(true); // Reset pagination when search changes
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
    $scope.fetchData();

    $scope.deleteUser = function (user) {
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
                $http.post("/admin/deleteUser", {id: user.id})
                        .then(function (response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "User has been removed.",
                                icon: "success",
                                backdrop: false
                            });

                            $scope.test = {};
                            $scope.currentPage = 1;  // Reset to first page
                            $scope.fetchData();
                        }, function (error) {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                backdrop: false
                            });
                        });
            }
        });
    };

});

app.controller('CreateTestController', function ($scope, $http, $routeParams, $location, PaginationService) {
    $scope.test_factors = {};
    $scope.isFormCollapsed = true;
    $scope.loadTests = function () {
        $http.get('/admin/getTestFactorList')
                .then(function (response) {
                    $scope.test_factors = response.data.test_factors || [];
                    $scope.test_reports = response.data.test_reports || [];
                })
                .catch(function (error) {
                    console.error("Error loading test factors:", error);
                });
    };

    $scope.saveTest = function (testId) {
        // Filter selected test factors and get their IDs
        let selectedFactorIds = $scope.test_factors
                .filter(factor => factor.selected === '1') // Keep only selected checkboxes
                .map(factor => factor.id); // Extract the factor IDs

        console.log("Selected Factor IDs:", selectedFactorIds);

        // Prepare the request payload
        let requestData = {
            creator_name: $scope.test.creator_name,
            test_name: $scope.test.test_name,
            test_description: $scope.test.test_description,
            user_prefix: $scope.test.user_prefix,
            test_report_id: $scope.test.test_report_id,
            factor_ids: selectedFactorIds // Send the selected factor IDs
        };

        let url = testId ? '/admin/saveTest/' + testId : '/admin/saveTest';
        // Send data to the backend
        $http.post(url, requestData)
                .then(function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "Test saved successfully!",
                        icon: "success",
                        backdrop: false
                    });
                    $scope.test = {};
                    $scope.loadTests(); // Reload test list
                    $scope.fetchData();
                })
                .catch(function (error) {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to save test. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: false
                    });
                });
    };

    $scope.cancelEdit = function () {
        $scope.test = {}; // Clear form data

        // Uncheck all checkboxes
        $scope.test_factors.forEach(factor => {
            factor.selected = '0'; // Set all checkboxes to unchecked
        });
    };

    $scope.deleteTest = function (test) {
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
                $http.post("/admin/deleteTest", {id: test.id})
                        .then(function (response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Test has been removed.",
                                icon: "success",
                                backdrop: false
                            });

                            $scope.test = {};
                            $scope.currentPage = 1;  // Reset to first page
                            $scope.loadTests(); // Reload test list from page 1
                            $scope.fetchData();
                        }, function (error) {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                backdrop: false
                            });
                        });
            }
        });
    };



    $scope.loadTests();

    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.loading = true;

    $scope.fetchData = function () {
        $scope.loading = true; // Start loader
        $http.get('/admin/getTestListData', {
            params: {
                limit: $scope.pagination.limit,
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit
            }
        }).then(function (response) {
            $scope.tests = response.data.data;
            PaginationService.setTotalPages(response.data.total);
            $scope.loading = false; // Stop loader
        }, function (error) {
            console.error("Error fetching data", error);
            $scope.loading = false; // Ensure it stops on error
        });
    };


    // Watch for changes in search and reset pagination
    $scope.$watch('searchText', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.fetchData(true); // Reset pagination when search changes
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

    $scope.editTest = function (testId) {
        $http.get("/admin/getTestRowData/" + testId)
                .then(function (response) {
                    let testData = response.data;

                    // Assign basic test details
                    $scope.test = {
                        id: testData.id,
                        test_name: testData.test_name,
                        test_description: testData.test_description,
                        creator_name: testData.creator_name,
                        test_report_id: testData.test_report_id,
                        user_prefix: testData.user_prefix,
                    };

                    // Parse and load selected factors
                    let selectedFactors = JSON.parse(testData.factor_ids); // Convert JSON string to array
                    $scope.test_factors.forEach(factor => {
                        factor.selected = selectedFactors.includes(factor.id.toString()) ? '1' : '0';
                    });

                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                })
                .catch(function (error) {
                    console.error("Error fetching test data:", error);
                });
    };



});



app.controller('TestController', function ($scope, $http, $location) {
    $scope.tests = [];
    $scope.newTest = {};

    $scope.resetForm = function () {
        $scope.newTest = {}; // Reset the form by clearing newTest object
    };

    $scope.editTestName = function (testId) {
        $http.get("/admin/getTestData/" + testId)
                .then(function (response) {
                    $scope.newTest.test_name = response.data.test_name;
                    $scope.newTest.parent_id = response.data.parent_id ? parseInt(response.data.parent_id, 10) : null;
                    $scope.newTest.test_id = response.data.id;
                })
                .catch(function (error) {
                    console.error("Error fetching company data:", error);
                });
    };

    $scope.loadTests = function () {
        $http.get('/admin/getTestList')
                .then(function (response) {
                    $scope.tests = response.data;
                });
    };

    $scope.toggleStatus = function (test) {
        let url = '/admin/addTestName/' + test.id;
        $http.post(url, {
            test_name: test.test_name,
            parent_id: test.parent_id,
            status: test.status
        }, {
            headers: {'Content-Type': 'application/json'}
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: "Test disabled successfully!",
                icon: "success",
                backdrop: false
            });
        }).catch(function (error) {
            Swal.fire({
                title: "Error!",
                text: "Failed to save test. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };

    $scope.deleteTestName = function (test) {
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
                $http.post("/admin/deleteTestName", {id: test.id})
                        .then(function (response) {
                            $scope.loadTests();
                            Swal.fire({
                                title: "Deleted!",
                                text: "Testname has been removed.",
                                icon: "success",
                                backdrop: false // Disables the dark background overlay
                            });

                            $scope.fetchData(); // Refresh user list
                        }, function (error) {
                            Swal.fire({
                                title: "Error!",
                                text: "Testname has been removed.",
                                icon: "error",
                                backdrop: false // Disables the dark background overlay
                            });
                        });
            }
        });
    };

    $scope.addTestName = function (testId) {
        let url = testId ? '/admin/addTestName/' + testId : '/admin/addTestName';

        $http.post(url, {
            test_name: $scope.newTest.test_name,
            parent_id: $scope.newTest.parent_id ?? 0,
        }, {
            headers: {'Content-Type': 'application/json'}
        }).then(function (response) {
            $scope.newTest = {};
            $scope.loadTests();
            Swal.fire({
                title: "Success!",
                text: testId ? "Test updated successfully!" : "Test added successfully!",
                icon: "success",
                backdrop: false
            });
        }).catch(function (error) {
            Swal.fire({
                title: "Error!",
                text: testId ? "Failed to update test. Please try again." : "Failed to add test. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };


    $scope.loadTests();
});

app.controller('TestFactorController', function ($scope, $http, $location, $timeout, PaginationService) {
    $scope.test_factors = [];
    $scope.newTestFactor = {};
    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object

    $scope.resetForm = function () {
        $scope.newTestFactor = {}; // Reset the form by clearing newTest object
    };

    // Convert HH:MM:SS to minutes with decimals
    function timeToMinutes(timeStr) {
        if (!timeStr)
            return '';
        let parts = timeStr.split(':');
        let hours = parseInt(parts[0], 10) || 0;
        let minutes = parseInt(parts[1], 10) || 0;
        let seconds = parseInt(parts[2], 10) || 0;
        return hours * 60 + minutes + (seconds / 60);
    }

    $scope.editTestFactor = function (factorId) {
        $http.get("/admin/getFactorData/" + factorId)
                .then(function (response) {
                    console.log("API Response:", response.data); // Debug API response

                    if (response.data) {
                        $scope.newTestFactor = response.data;
                        $scope.newTestFactor.factor_name = response.data.factor_name;
                        $scope.newTestFactor.prefix = response.data.prefix;
                        $scope.newTestFactor.factor_id = response.data.id;
                        $scope.newTestFactor.is_mandatory = response.data.is_mandatory;
                        $scope.newTestFactor.factor_description = response.data.factor_description;
                        $scope.newTestFactor.timer = timeToMinutes($scope.newTestFactor.timer);
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });

                    }
                })
                .catch(function (error) {
                    console.error("Error fetching factor data:", error);
                });
    };

    $scope.loadTests = function () {
        $http.get('/admin/getTestFactorList', {
            params: {
                limit: $scope.pagination.limit,
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit,
                search: $scope.searchText
            }
        }).then(function (response) {
            $scope.test_factors = response.data.test_factors;
            PaginationService.setTotalPages(response.data.total);
        });
    };

    $scope.toggleStatus = function (factor) {
        let url = '/admin/addTestFactor/' + factor.id;
        $http.post(url, {
            factor_name: factor.factor_name,
            prefix: factor.prefix,
            status: factor.status
        }, {
            headers: {'Content-Type': 'application/json'}
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: factor.id ? "Factor updated successfully!" : "Factor added successfully!",
                icon: "success",
                backdrop: false
            });
        }).catch(function (error) {
            Swal.fire({
                title: "Error!",
                text: factor.id ? "Factor updated successfully!" : "Factor added successfully!",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };

    $scope.deleteTestFacor = function (factor) {
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
                $http.post("/admin/deleteTestFacor", {id: factor.id})
                        .then(function (response) {
                            $scope.loadTests();
                            Swal.fire({
                                title: "Deleted!",
                                text: "Factor has been removed.",
                                icon: "success",
                                backdrop: false // Disables the dark background overlay
                            });
                        }, function (error) {
                            Swal.fire({
                                title: "Error!",
                                text: "Factor has been removed.",
                                icon: "error",
                                backdrop: false // Disables the dark background overlay
                            });
                        });
            }
        });
    };

    // Watch for changes in search and reset pagination
    $scope.$watch('searchText', function (newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.pagination.currentPage = 1;
            $scope.loadTests();
        }
    });

    $scope.updateRowsPerPage = function () {
        $scope.pagination.currentPage = 1; // Reset to first page
        $scope.loadTests(); // Reload data with new limit
    };

    // Navigation Methods
    $scope.nextPage = function () {
        if ($scope.pagination.currentPage < $scope.pagination.totalPages) {
            $scope.pagination.currentPage++;
            $scope.loadTests();
        }
    };

    $scope.prevPage = function () {
        if ($scope.pagination.currentPage > 1) {
            $scope.pagination.currentPage--;
            $scope.loadTests();
        }
    };

    $scope.goToPage = function (page) {
        $scope.pagination.currentPage = page;
        $scope.loadTests();
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

    $scope.addTestFactor = function (factorId) {
        let url = factorId ? '/admin/addTestFactor/' + factorId : '/admin/addTestFactor';

        $http.post(url, {
            factor_name: $scope.newTestFactor.factor_name,
            prefix: $scope.newTestFactor.prefix,
            timer: $scope.newTestFactor.timer,
            is_mandatory: $scope.newTestFactor.is_mandatory,
            factor_description: $scope.newTestFactor.factor_description,
        }, {
            headers: {'Content-Type': 'application/json'}
        }).then(function (response) {
            $scope.newTestFactor = {};
            $scope.loadTests();
            Swal.fire({
                title: "Success!",
                text: factorId ? "Test updated successfully!" : "Test added successfully!",
                icon: "success",
                backdrop: false
            });
        }).catch(function (error) {
            Swal.fire({
                title: "Error!",
                text: factorId ? "Failed to update test. Please try again." : "Failed to add test. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };


    $scope.loadTests();
});

app.controller('DashboardController', [
    '$scope', '$http', '$timeout', '$location',
    function ($scope, $http, $timeout, $location) {
        let statsInterval, detailsInterval;
        let dashboardInitialized = false;

        $scope.total_tests = 0;
        $scope.total_questions = 0;
        $scope.total_users = 0;
        $scope.companies_count = 0;
        $scope.total_test_factors = 0;
        $scope.active_today = 0;
        $scope.completion_rate = 0;
        $scope.errorMessage = null;
        $scope.loading = false;
        $scope.userStatsChart = null;
        $scope.recent_completions = [];
        $scope.test_breakdown = [];
        $scope.lastUpdated = new Date();

        $scope.refreshDashboard = function () {
            $scope.getDashboardDetails();
            $scope.getDashboardUsersStats();
            $scope.getRecentCompletions();
            $scope.getTestBreakdown();
            $scope.lastUpdated = new Date();
        };

        $scope.getDashboardDetails = function () {
            $scope.loading = true;
            $http.get("dashboardData")
                    .then(function (response) {
                        if (response.data && response.data.data) {
                            $scope.total_tests = response.data.data.total_tests || 0;
                            $scope.total_questions = response.data.data.total_questions || 0;
                            $scope.total_users = response.data.data.total_users || 0;
                            $scope.registered_users = response.data.data.registered_users || 0;
                            $scope.attempts_data = response.data.data.attempts_data || 0;
                            $scope.companies_count = response.data.data.companies_count || 0;
                            $scope.total_test_factors = response.data.data.total_test_factors || 0;
                            $scope.active_today = response.data.data.active_today || 0;
                            $scope.completion_rate = response.data.data.completion_rate || 0;
                        }
                    })
                    .catch(function (error) {
                        $scope.errorMessage = "Failed to load dashboard data.";
                        console.error("Dashboard error:", error);
                    })
                    .finally(function () {
                        $scope.loading = false;
                    });
        };

        $scope.getRecentCompletions = function () {
            $http.get("getRecentCompletions")
                    .then(function (response) {
                        if (response.data && response.data.data) {
                            $scope.recent_completions = response.data.data;
                        }
                    })
                    .catch(function (error) {
                        console.error("Failed to load recent completions:", error);
                    });
        };

        $scope.getTestBreakdown = function () {
            $http.get("getTestCompletionBreakdown")
                    .then(function (response) {
                        if (response.data && response.data.data) {
                            $scope.test_breakdown = response.data.data;
                        }
                    })
                    .catch(function (error) {
                        console.error("Failed to load test breakdown:", error);
                    });
        };

        $scope.getDashboardUsersStats = function () {
            $scope.loading = true;
            $http.get("getAttemptStats")
                    .then(function (response) {
                        if (response.data && response.data.data) {
                            $scope.stats = response.data.data;
                            $scope.renderUserStatsChart($scope.stats);
                        }
                    })
                    .catch(function (error) {
                        $scope.errorMessage = "Failed to load user stats.";
                        console.error("Dashboard stats error:", error);
                    })
                    .finally(function () {
                        $scope.loading = false;
                    });
        };

        $scope.renderUserStatsChart = function (stats) {
            const ctx = document.getElementById('userStatsChart');
            if (!ctx)
                return;

            const chartData = {
                active: parseInt(stats.active_users || 0),
                completed: parseInt(stats.completed_users || 0),
                loggedIn: parseInt(stats.currently_logged_in || 0),
                today: parseInt(stats.completed_today || 0)
            };

            if (!$scope.userStatsChart) {
                $scope.userStatsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Active Users', 'Completed Users', 'Logged In', 'Completed Today'],
                        datasets: [{
                                label: 'User Stats',
                                data: [chartData.active, chartData.completed, chartData.loggedIn, chartData.today],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(255, 99, 132, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            } else {
                $scope.userStatsChart.data.datasets[0].data = [
                    chartData.active,
                    chartData.completed,
                    chartData.loggedIn,
                    chartData.today
                ];
                $scope.userStatsChart.update();
            }
        };

        function initDashboardIfOnRoot() {
            const currentPath = $location.path(); // gives "/manage-admin", "/" etc.

            // Check if path is exactly "/" i.e., dashboard
            if (currentPath === '/' && !dashboardInitialized) {
                dashboardInitialized = true;

                $scope.getDashboardDetails();
                $scope.getDashboardUsersStats();
                $scope.getRecentCompletions();
                $scope.getTestBreakdown();

                statsInterval = setInterval(function () {
                    $scope.getDashboardUsersStats();
                    $scope.getRecentCompletions();
                    $scope.getTestBreakdown();
                }, 30000); // Refresh every 30 seconds

                detailsInterval = setInterval(function () {
                    $scope.getDashboardDetails();
                }, 300000); // Refresh every 5 minutes
            }
        }

        // Initial load
        initDashboardIfOnRoot();

        // Also handle route changes
        $scope.$on('$routeChangeSuccess', function () {
            initDashboardIfOnRoot();
        });

        $scope.$on('$destroy', function () {
            clearInterval(statsInterval);
            clearInterval(detailsInterval);
        });
    }
]);

app.controller("EditUserController", function ($scope, $routeParams, $http, $location) {
    $scope.userId = $routeParams.id; // Get user ID from URL
    $scope.user = {};

    // Fetch user data from backend
    $http.get("/admin/getAdminUser/" + $scope.userId)
            .then(function (response) {
                $scope.user = response.data;
                // Ensure password fields are empty
                $scope.user.password = "";
                $scope.user.confirmPassword = "";
            })
            .catch(function (error) {
                console.error("Error fetching user data:", error);
            });

    $scope.cancelEdit = function () {
        $location.path('/manage-admin'); // Adjust this path based on your routing configuration
    };



    // Function to update user
    $scope.updateUser = function (userId) {
        if (!userId) {
            alert("User ID is missing!");
            return;
        }

        let updatedUserData = {
            id: userId,
            name: $scope.user.name,
            username: $scope.user.username,
            email: $scope.user.email,
        };

        // Only send password if changed
        if ($scope.user.password) {
            updatedUserData.password = $scope.user.password;
        }

        $http.post("/admin/updateAdminUser", updatedUserData) // Use PUT if updating
                .then(function (response) {
                    Swal.fire({
                        title: "Success!",
                        text: "User updated successfully!",
                        icon: "success",
                        backdrop: false
                    });
                    $location.path("/manage-admin");
                })
                .catch(function (error) {
                    console.error("Error updating user:", error);
                    Swal.fire({
                        title: "Error!",
                        text: error,
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: false
                    });
                });
    };
});

app.controller('AdminUserController', function ($scope, $http, PaginationService, $location) {
    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.users = [];

    $scope.editUser = function (userId) {
        $location.path('manage-admin-user/' + userId);
    };

    $scope.createNewUser = function () {
        $location.path('/manage-admin-user'); // Adjust this path based on your routing configuration
    };

    $scope.deleteUser = function (user) {
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
                $http.post("/admin/deleteAdminUser", {id: user.id})
                        .then(function (response) {
                            Swal.fire("Deleted!", "User has been removed.", "success");
                            $scope.fetchData(); // Refresh user list
                        }, function (error) {
                            Swal.fire("Error!", "Failed to delete user.", "error");
                        });
            }
        });
    };


    $scope.fetchData = function () {
        $http.get('/admin/getAdminList', {
            params: {limit: $scope.pagination.limit, offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit}
        }).then(function (response) {
            $scope.users = response.data.data;
            PaginationService.setTotalPages(response.data.total); // Update total pages
        }, function (error) {
            console.error("Error fetching data", error);
        });
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

    // Initial Data Load
    $scope.fetchData();
});

app.controller('CompanyController', function ($scope, $http, PaginationService, $location) {
    $scope.pagination = PaginationService.getPagination(); // Get shared pagination object
    $scope.users = [];

    $scope.editCompany = function (companyId) {
        $location.path('manage-company/' + companyId);
    };

    $scope.createNew = function () {
        $location.path('manage-company/');
    };



    $scope.deleteCompany = function (company) {
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
                $http.post("/admin/deleteCompany", {id: company.id})
                        .then(function (response) {
                            Swal.fire("Deleted!", "Company has been removed.", "success");
                            $scope.fetchData(); // Refresh user list
                        }, function (error) {
                            Swal.fire("Error!", "Failed to delete company.", "error");
                        });
            }
        });
    };


    $scope.fetchData = function () {
        $http.get('/admin/getCompanyList', {
            params: {limit: $scope.pagination.limit, offset: ($scope.pagination.currentPage - 1) * $scope.pagination.limit}
        }).then(function (response) {
            $scope.companies = response.data.data;
            PaginationService.setTotalPages(response.data.total);

            $timeout(function () {
                $('#companyTable').DataTable().destroy(); // Destroy existing table
                $('#companyTable').DataTable(); // Reinitialize
            }, 0);
        });
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
    $scope.fetchData();
});

app.controller('EditCompanyController', function ($scope, $http, $routeParams, $location) {
    $scope.company = {};
    $scope.companyId = $routeParams.companyId;
    $scope.pageTitle = $scope.companyId ? "Edit Company" : "Add Company";

    $scope.saveCompany = function (id) {
        let formData = new FormData();
        formData.append("company_name", $scope.company.company_name);
        formData.append("website", $scope.company.website);
        formData.append("contact_name", $scope.company.contact_name);
        formData.append("contact_phone", $scope.company.contact_phone);
        formData.append("contact_email", $scope.company.contact_email);
        formData.append("branding", $scope.company.branding);
        formData.append("status", $scope.company.status);

        let fileInput = document.getElementById('logo_image_path');
        if (fileInput.files.length > 0) {
            formData.append("logo_image_path", fileInput.files[0]);
        }

        let url = id ? "/admin/saveCompany/" + id : "/admin/saveCompany";

        $http.post(url, formData, {
            headers: {'Content-Type': undefined},
            transformRequest: angular.identity
        }).then(function (response) {
            Swal.fire({
                title: "Success!",
                text: id ? "Company updated successfully!" : "Company added successfully!",
                icon: "success",
                backdrop: false
            });
            $location.path('/manage-companies');
        }, function (error) {
            Swal.fire({
                title: "Error!",
                text: "Failed to save company. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                backdrop: false
            });
        });
    };

    // Fetch company details when the page loads
    $scope.getCompanyDetails = function () {
        $http.get("/admin/getCompanyData/" + $routeParams.companyId)
                .then(function (response) {
                    $scope.company = response.data;
                })
                .catch(function (error) {
                    console.error("Error fetching company data:", error);
                });
    };
    // Load data when page initializes
    $scope.getCompanyDetails();

    $scope.cancelEdit = function () {
        $location.path('/manage-companies'); // Adjust this path based on your routing configuration
    };

    $scope.saveUser = function () {
        let userData = {
            name: $scope.user.name,
            username: $scope.user.username,
            email: $scope.user.email
        };

        // Only send password if the user provides a new one
        if ($scope.user.password) {
            userData.password = $scope.user.password;
        }

        if ($scope.user.id) {
            // Update existing user
            $http.put("/admin/updateAdminUser/" + $scope.user.id, userData)
                    .then(function (response) {
                        alert("User updated successfully!");
                        $location.path("/manage-admin"); // Redirect after update
                    })
                    .catch(function (error) {
                        console.error("Error updating user:", error);
                    });
        } else {
            // Create new user
            $http.post("/admin/createAdminUser", userData)
                    .then(function (response) {
                        alert("User created successfully!");
                        $location.path("/manage-admin"); // Redirect after creation
                    })
                    .catch(function (error) {
                        console.error("Error creating user:", error);
                    });
        }
    };

// Load user data (edit mode)
    if ($routeParams.id) {
        $http.get("/admin/getAdminUser/" + $routeParams.id)
                .then(function (response) {
                    $scope.user = response.data;

                    // Ensure password fields are empty
                    $scope.user.password = "";
                    $scope.user.confirmPassword = "";
                })
                .catch(function (error) {
                    console.error("Error fetching user data:", error);
                });
    } else {
        // If no ID, initialize empty user object for new user creation
        $scope.user = {
            name: "",
            username: "",
            email: "",
            password: "",
            confirmPassword: ""
        };
    }
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

app.directive("fileModel", function ($parse) {
    return {
        restrict: "A",
        link: function (scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            element.bind("change", function () {
                scope.$apply(function () {
                    modelSetter(scope, element[0].files[0]); // Store File object
                });
            });
        }
    };
});




