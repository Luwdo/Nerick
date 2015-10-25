//all modules need to be part of nerick
/*var nerick = angular.module('nerick', [
    'ngRoute',
    'restangular',
    'datatables',
    'flow',
    'ui.sortable',
    'home',
    'login',
    'lightBox',
    'mediaViewer',
    'user',
    'menu',
    'artwork',
    'gallery',
    'slick',
    'ngAnimate'
]);

require('./StaticController.js');
require('./lightBox.js');
require('./NotifyService.js');
require('./MediaViewer.js');

angular.module('nerick').config(function(flowFactoryProvider) {
    flowFactoryProvider.defaults = {
	target: 'http://nerick.com/wc/nerick/api/web/app_dev.php/api/uploads',
	permanentErrors: [400, 401, 403, 404, 500, 501],
	maxChunkRetries: 1,
	chunkRetryInterval: 5000,
	simultaneousUploads: 4
    };
    flowFactoryProvider.on('catchAll', function (event) {
	console.log('catchAll', arguments);
    });
    
    //set datatables to not alert warnings (caused by 403s and the like)
    $.fn.dataTableExt.sErrMode = 'throw';
    
    // Can be used with different implementations of Flow.js
    // flowFactoryProvider.factory = fustyFlowFactory;
});

angular.module('nerick')
    .run(['Restangular', 'UserService', '$location', '$rootScope', 'NotifyService', 
    function (Restangular, UserService, $location, $rootScope, $NotifyService) {
	$rootScope.apiURL = 'http://nerick.com/wc/nerick/api/web/app_dev.php/api';   
	Restangular.setBaseUrl($rootScope.apiURL);
	
	$rootScope.UserService = UserService;
	
	//init auth service
	//ok need liam to look at this, dono if this is even correct
//	UserService.initUser().then(function(){
//	    $rootScope.$on("$routeChangeStart", function (event, next, current) {
//		//debugger;
//		if(typeof next.requireLogin !== 'undefined' && next.requireLogin === true){
//		    //debugger;
//		    if(!UserService.isLoggedIn()){
//			UserService.clearUser();
//			$NotifyService.addGlobal('You must be logged in to complete this action.', $NotifyService.types.error);
//			$location.path("/login");
//		    }
//		}
//	    });
//	});
	
	var initUser = new Promise(function(resolve, reject){
	    //debugger;
	    $rootScope.UserService.initUser().then(function(){ resolve(); });
	});
	
	$rootScope.$on("$routeChangeStart", function (event, next, current) {
	    //debugger;
	    if(typeof next.requireLogin !== 'undefined' && next.requireLogin === true){
		initUser.then(function(){
		    //debugger;
		    if(!$rootScope.UserService.isLoggedIn()){
			$rootScope.UserService.clearUser();
			$NotifyService.addGlobal('You must be logged in to complete this action.', $NotifyService.types.error);
			$location.path("/login");
		    }
		});
	    }
	});
	
	
	Restangular.setErrorInterceptor(
	    function(response){
		//forbidden is pages or actions you should not see if you are not logged in
		if(response.status === 403) {//Forbidden
		    $NotifyService.addGlobal('You must be logged in to complete this action.', $NotifyService.types.error);
		    UserService.clearUser();
		    $location.path("/login");
		    return false;
		}
		//Unauthorized is content that will or will not be there if you are logged in but you can still see the page.
		if(response.status === 401) {//Unauthorized
		    UserService.clearUser();
		    return false;
		}
		
		//don't know if more needs to be done here but meh
		if(response.status === 400) {
		    //debugger;
		    if(response.data.notifications.length > 0){
			$NotifyService.clear();
			$NotifyService.populateNotifications(response.data.notifications);
			$NotifyService.show();
		    }
		    return false;
		}
		//unhandled error
		return true;
	    });
	Restangular.addResponseInterceptor(
	    function(data, operation, what, url, response, deferred) {
		//debugger;
		//intercepts notifications and prints them out.
		if(response.data.notifications.length > 0){
		    $NotifyService.clear();
		    $NotifyService.populateNotifications(response.data.notifications);
		    $NotifyService.show();
		}
		return data;
	    });


    }]);*/

//angular.module('nerick').animation('.footer-hide', [function() {
//  return {
//    // make note that other events (like addClass/removeClass)
//    // have different function input parameters
//    enter: function(element, doneFn) {
//       $('#footer').show();
//       doneFn();
//      // remember to call doneFn so that angular
//      // knows that the animation has concluded
//    },
//
//    leave: function(element, doneFn) {
//       $('#footer').hide();
//       doneFn();
//    }
//  };
//}]);

//angular.module('nerick').animation('.footer-hide', ['$animateCss', function($animateCss) {
//  return {
//    enter: function(element, doneFn) {
//      var animation = $animateCss(element, {
//        event: 'enter'
//      });
//
//      if (animation) {
//	$('#footer').hide();
//        // this will trigger `.slide.ng-enter` and `.slide.ng-enter-active`.
//        var runner = animation.start();
//        runner.done(doneFn);
//      } else { //no CSS animation was detected
//	$('#footer').show();
//        doneFn();
//      }
//    }
//  };
//}]);