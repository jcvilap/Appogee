/**
	This factory is used to get the projects info from the server via RESTful calls 
*/
arkonLEDApp.
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
});