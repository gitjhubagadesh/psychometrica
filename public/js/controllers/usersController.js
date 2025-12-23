app.controller("usersController", function($scope, $http) {
    $scope.users = [];

    $http.get("/api/users").then(function(response) {
        $scope.users = response.data;
    });
});
