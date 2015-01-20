window.arkonLEDApp = angular.module('arkonLEDApp',['ngRoute', 'ui.bootstrap']).

// Configs 
config(['$routeProvider', function($routeProvider) {
    $routeProvider.
    when('/', {
        templateUrl: 'webapp/partials/main.html',
        controller: 'MainController'
    }).
    when('/client/:projectId', {
        templateUrl: 'webapp/partials/client.html',
        controller: 'ClientController'
    }).
    when('/tablet/:projectId', {
        templateUrl: 'webapp/partials/tablet.html',
        controller: 'TabletController'
    }).
    when('/login', {
        templateUrl: 'webapp/partials/login.html',
        controller: 'LoginController'
    }).
    otherwise({
        redirectTo: '/'
    }); 
}]).

// Run
run(['$rootScope', '$location', 'loginFactory', function ($rootScope, $location, loginFactory) {   
    var routesThatRequireAuth = ['/'];

    $rootScope.$on('$routeChangeStart', function (event, next, current) {
        if(_.contains(routesThatRequireAuth,$location.path())  && !loginFactory.isLoggedIn()) {
            $location.path('/login');
        }
    });
}]).

// Custom directive
directive('onFinishRender', function($timeout){
    return{
        restrict: 'A',
        link: function (scope, element, attr){
            if(scope.$last === true){
                scope.$evalAsync(attr.onFinishRender);
            }
        }
    }
});