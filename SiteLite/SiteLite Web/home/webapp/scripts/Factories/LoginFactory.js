/**
  This factory takes care of login/out and session management
*/
arkonLEDApp.
// Factory created for session management
factory('sessionService', function(){
	return{
		get: function(key){
			return sessionStorage.getItem(key);
		},
		set: function(key, val){
			return sessionStorage.setItem(key, val);
		},
		unset: function(key){
			return sessionStorage.removeItem(key);
		},
	};
}). 
factory('loginFactory', function($http, $location, sessionService, commonFactory) {
    var factory = {};
    var userCredentials = {email: '', password: ''};
    var baseUrl = commonFactory.baseUrl;

    factory.cacheSession = function(userInfo) {
        sessionService.set('userID', userInfo.userID);
        sessionService.set('firstName', userInfo.firstName);
        sessionService.set('lastName', userInfo.lastName);
        sessionService.set('userType', userInfo.userType);
        sessionService.set('authenticated', true);
    };

    var uncacheSession = function() {
    	sessionService.unset('userID');
        sessionService.unset('firstName');
        sessionService.unset('lastName');
        sessionService.unset('userType');
        sessionService.unset('authenticated');
    };

    factory.login = function(credentials){
    	credentials.password = SHA1(credentials.password);
    	return $http({
            url: baseUrl + "/iOS/AccountInfo/login.php", 
            method: "GET",
            params: credentials
        });
    };

    factory.logout = function(){
    	uncacheSession();
    	$location.path('/login/');
    };

    factory.setCredentials = function(credentials){
        userCredentials = credentials;
    };

    factory.isLoggedIn = function(){
        return sessionService.get('authenticated') == "true";
    };

    return factory;
});