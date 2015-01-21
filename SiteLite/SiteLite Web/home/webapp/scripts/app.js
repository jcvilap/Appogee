window.arkonLEDApp = angular.module('arkonLEDApp',['ngRoute', 'ui.bootstrap']).
factory('projectsFactory', function ($http){
	var factory = {};
    var url = "http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/";

    // Get list of projects from web services
    factory.getProjects = function(callback){
        $http.get(url + "Projects/getProjectNames.php?userID=27").
        success(function(data) {
            callback( data.message );
        });
    };
    // Get Project by ID
    factory.getProject = function(id){
        for (var i = $scope.projects.length - 1; i >= 0; i--) {
            if($scope.projects[i].project_ID == id) 
                return $scope.projects[i]; 
        };
    };

    factory.getProjectPoles = function(id, callback){
        $http.get(url + "LightPoles/getPoles.php?projectID=" + id).
        success(function(data) {
            callback( data.message );
        });
    };

    factory.getProjectStats = function(id, callback){
        $http.get(url + "Projects/calculateCost.php?projectID=" + id).
        success(function(data) {
            callback(data);
        });
    };

	return factory;
}).
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
controller('myController', function($scope, $routeParams) {
     $scope.params = $routeParams;
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
    when('/login', {
        templateUrl: 'webapp/partials/login.html',
        controller: 'LoginController'
    }).
    otherwise({
        redirectTo: '/'
    }); 
}]);