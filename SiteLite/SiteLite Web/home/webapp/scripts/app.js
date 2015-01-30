window.arkonLEDApp = angular.module('arkonLEDApp',['ngRoute', 'ui.bootstrap']).
directive('onFinishRender', function($timeout){
    return{
        restrict: 'A',
        link: function (scope, element, attr){
            if(scope.$last === true){
                scope.$evalAsync(attr.onFinishRender);
            }
        }
    }
}).
run(function($rootScope, $location, loginFactory){
    var routesThatRequireAuth = ['', '/', '/home'];
    $rootScope.$on('$routeChangeStart', function(event, next, current){
        var path = $location.path();
        if(_.contains(routesThatRequireAuth, path) && !loginFactory.isLoggedIn()){
            $location.path('/login/');
        }
        else if(path.indexOf("login") > -1 && loginFactory.isLoggedIn()){
            $location.path('/');
        }
    });
}).
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
    when('/login/:message', {
        templateUrl: 'webapp/partials/login.html',
        controller: 'LoginController'
    }).
    otherwise({
        redirectTo: '/'
    }); 
}]);