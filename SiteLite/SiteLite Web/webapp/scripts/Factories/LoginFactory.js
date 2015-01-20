/**
  This factory takes care of login/out and session management
*/
arkonLEDApp.
// Factory created for session management, only used here so far
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
factory('loginFactory', function($http, $location, sessionService) {
    var factory = {};
    var userCredentials = {email: '', password: ''};

    factory.cacheSession = function() {
    	sessionService.set('authenticated', true);
    };

    var uncacheSession = function() {
    	sessionService.unset('authenticated');
    };

    factory.login = function(credentials){
    	credentials.password = SHA1(credentials.password);
    	return $http.post("http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/AccountInfo/login.php", credentials);
    };

    factory.logout = function(){
    	uncacheSession();
    	$location.path('/login');
    };

    factory.setCredentials = function(credentials){
        userCredentials = credentials;
    };

    factory.isLoggedIn = function(){
        return sessionService.get('authenticated');
    };

    return factory;
});