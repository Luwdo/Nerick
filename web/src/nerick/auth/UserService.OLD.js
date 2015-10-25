//angular.module('nerick').factory('UserService', function($location, NotifyService){
//    var currentUser = null;
//    var userService = {};
//    
//    userService.getUser = function(){
//	return currentUser;
//    };
//    userService.setUser = function(user){
//	currentUser = user;
//    };
//    userService.clearUser = function(){
//	currentUser = null;
//    };
//    
//    userService.initUser = function(){
//	var checkLogin = Restangular.one('login_check');
//	return checkLogin.post().then(function(response) {
//	    if(response.data == false){
//		userService.clearUser();
//	    }
//	    else{
//		userService.setUser(response.data);
//	    }
//	});
//    };
//    
//    userService.logout = function(){
//	var checkLogin = Restangular.one('logout');
//	//debugger;
//	checkLogin.post().then(function(response) {
//	    userService.clearUser();
//	    $location.path("/");
//	});
//    };
//    
//    userService.isLoggedIn = function(){
//	return currentUser !== null;
//    };
//    
//    return userService;
//});