if (!window.appConfig) {
	window.appConfig = require('../../config.js');
}

var ngm = angular.module(module.exports = 'nerick', [
	require('./api'),
	require('./auth'),
	require('./lightCrate'),
	//require('./mediaViewer'),
	//require('./notifications'),
	require('./ui'),
]);

ngm.config(function(nApiProvider) {
	nApiProvider.setApiEndpoint(window.appConfig.apiEndpoint || "api");
});

//deal with logins
ngm.run(function (nApi, nAuth, $location, $rootScope, $timeout) {

	angular.element(document).on('app-loaded', function(){
	    angular.element('#loading-screen').removeClass('show-content');
	    angular.element('#loading-screen').addClass('hide-content');
	    angular.element('#app-content').removeClass('hide-content');
	    angular.element('#app-content').addClass('show-content');
	});
	
	angular.element(document).on('app-loading', function(){
	    angular.element('#loading-screen').removeClass('hide-content');
	    angular.element('#loading-screen').addClass('show-content');
	    angular.element('#app-content').removeClass('show-contentt');
	    angular.element('#app-content').addClass('hide-content');
	});
	
	//angular.element(document).trigger('app-loading');
	
	//$rootScope.nAuth = nAuth;
	
//	var initUser = new Promise(function (resolve, reject) {
//		//debugger;
//		$rootScope.userService.initUser().then(function () {
//			resolve();
//		});
//	});

	$rootScope.$on("$routeChangeStart", function (event, next, current) {
		//debugger;
		//Lots to fix here
		if (typeof next.requireLogin !== 'undefined' && next.requireLogin === true) {
		    
		    if(!next.resolve){
			next.resolve = {};
		    }
		    //make this a require user and a then load app thing
		    next.resolve.nUser = function(nAuth){
			return nAuth.userReady();
		    };
		    
		    //angular.element(document).trigger('app-loading');
		    
		    //debugger;
//		    initUser.then(function () {
//			    //debugger;
//			    if (!$rootScope.userService.isLoggedIn()) {
//				    angular.element(document).trigger('app-loading');
//				    $rootScope.userService.clearUser();
//				    tmLoginDialog.show();
//			    }
//			    else{
//				    angular.element(document).trigger('app-loaded');
//			    }
//		    });
		}
		else{
		    //debugger;
		    $timeout(function(){
			angular.element(document).trigger('app-loaded');
		    });
		    //debugger;
		}
	});
});

//angular.element(document).ready(function() {
//    angular.bootstrap(document, ['nerick']);
//});
    
$(document).ready(function(){
    angular.bootstrap(document, ['nerick']);
});