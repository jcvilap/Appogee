arkonLEDApp.
controller('LoginController', function($scope, $routeParams, $rootScope, $location, loginFactory, sessionService, commonFactory) {
    $scope.params = $routeParams;
    $scope.credentials = {email: '', password: ''};
    $scope.baseUrl = commonFactory.baseUrl;
    
    if($scope.params.message !== '403'){
         $("#alert-container").hide();
    }

    $scope.login = function() {
        $('#progressModal').modal("show");
        loginFactory.login($scope.credentials).success( function(resp){
            // Login success
            if(resp.success === 1){
                $location.path('/'); 
                loginFactory.cacheSession(resp);
            }
            // Login Failure
            else if(resp.success === 0){
                $location.path('/login/403');
                $("#alert-container").show();
            }
            $('#progressModal').modal("hide");
        });
    };
    $scope.closeAlert = function() {
        $("#alert-container").hide();
    };
});