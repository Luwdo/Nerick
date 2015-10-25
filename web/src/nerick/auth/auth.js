var ngm = angular.module(module.exports = 'nerick.auth', [
    'ngRoute',
    require('../api'),
    //require('../ui/login')//cant to this cause login need auth
]);

ngm.factory('nAuth', function(nApi, $location, $q){
    var tmLoginDialog = null;//FIX ME
    
    var currentUser = null;
	var authService = {};
	var deferredUserReady = $q.defer();

	authService.getUser = function () {
		return currentUser;
	};
	authService.setUser = function (user) {
		currentUser = user;
		if(user !== null){
			deferredUserReady.resolve(currentUser);
		}
	};
	authService.clearUser = function () {
		currentUser = null;
	};

	//we need to add a time out and also store the user in local storage
	authService.initUser = function () {
		return nApi.get('/login_check').then(function(response){
			if (response.status != 200 ||  response.data == false) {
				authService.clearUser();
			}
			else {
				authService.setUser(response.data);
			}
		}).catch(function(response){
			if(response.status == 401){
				authService.clearUser();
			}
		});
	};
	
	//this needs to check if we have a user and if not wait untill a checkuser is called, when it is then return the user if there is one else wait till there is one.
	authService.userReady = function() {
		//if we have a user then lets go head and resolve
		if(authService.isLoggedIn()){
			return Promise.resolve(authService.getUser());
		}
		//return the deffered promise that will be resolved when a user is set.
		return deferredUserReady.promise;
	};

	authService.logout = function () {
		return nApi.post('/logout').then(function(response){
			authService.clearUser();
			angular.element(document).trigger('app-loading');
			tmLoginDialog.show();
		}).catch(function(response){
			authService.clearUser();
			angular.element(document).trigger('app-loading');
			tmLoginDialog.show();
		});
	};

	authService.isLoggedIn = function () {
		return currentUser !== null;
	};

	return authService;
});