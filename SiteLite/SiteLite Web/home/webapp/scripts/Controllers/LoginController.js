arkonLEDApp.
controller('LoginController', function($scope, $routeParams, $location, loginFactory) {
    $scope.params = $routeParams;
    $scope.credentials = {mail: '', password: ''};

    $scope.login = function() {
        loginFactory.login($scope.credentials).success( function(resp){
            // Login success
            if(resp.success == 1){
                $location.path('/'); 
                loginFactory.cacheSession();
            }
            // Login Failure
            else {
                // Todo: Display success.message when web service is ready to handle the requests
                // For Now: authenticate any user name and pass
                $location.path('/');
                loginFactory.cacheSession();   
            }
        });
    };
});