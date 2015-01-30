/**
	This factory is used to get the projects info from the server via RESTful calls 
*/
arkonLEDApp.
factory('projectsFactory', function ($http, commonFactory){
	var factory = {};
    var baseUrl = commonFactory.baseUrl;

    // Get list of projects from web services
    factory.getProjects = function(callback){
        $http.get(baseUrl + "/iOS/Projects/getProjectNames.php?userID=27").
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
        $http.get(baseUrl + "/iOS/LightPoles/getPoles.php?projectID=" + id).
        success(function(data) {
            callback( data.message );
        });
    };

    factory.getProjectStats = function(id, callback){
        $http.get(baseUrl + "/iOS/Projects/calculateCost.php?projectID=" + id).
        success(function(data) {
            callback(data);
        });
    };

	return factory;
});