var ngm = angular.module(module.exports = 'nerick.api', [
]);

//ngm.config(function($httpProvider) {
//    $httpProvider.interceptors.push('nApiHttpInterceptor');
//});

ngm.provider('nApi', ApiProvider);

var apiEndpoint;

function ApiProvider() {
    return {
	setApiEndpoint: function (endpoint) {
	    apiEndpoint = endpoint;
	},
	$get: ApiService
    };
}

function ApiService($http) {
    return {
	request: function (method, url, data) {
	    if (['get', 'post', 'put', 'delete'].indexOf(method) < 0)
		throw "Invalid method " + method;
	    var config = {
		method: method,
		url: apiEndpoint + '/' + url.replace(/^\/+/, ''),
		data: data
	    };
	    return $http(config).then(function(response){
		//do somthing with the messages here
		return response.data;
	    }).catch(function(rejection){
		if (rejection.status == 401 && !$rootScope.loginDialogOpen) {
		    debugger;
		    //open the login dialog again
		    
//		    if (!tmLoginDialog) {
//			tmLoginDialog = $injector.get('tmLoginDialog');
//		    }
//
//		    angular.element(document).trigger('app-loading');
//		    $rootScope.userService.clearUser();
//		    tmLoginDialog.show();
		}
		throw rejection;
	    });
	    
	    //return $http[method](apiEndpoint+'/'+url.replace(/^\/+/, ''), data);
	},
	get: function (url) {
	    return this.request('get', url);
	},
	delete: function (url, data) {
	    return this.request('delete', url, data);
	},
	post: function (url, data) {
	    return this.request('post', url, data);
	},
	put: function (url, data) {
	    return this.request('put', url, data);
	}
    }
}

//ngm.factory('nApiHttpInterceptor', function ($q) {
//    return {
//	// optional method
//	'request': function (config) {
//	    debugger;
//	    // do something on success
//	    return config;
//	},
//	// optional method
//	'requestError': function (rejection) {
//	    debugger;
//	    // do something on error
//	    if (canRecover(rejection)) {
//		return responseOrNewPromise
//	    }
//	    return $q.reject(rejection);
//	},
//	// optional method
//	'response': function (response) {
//	     debugger;
//	    // do something on success
//	    return response;
//	},
//	// optional method
//	'responseError': function (rejection) {
//	    debugger;
//	    // do something on error
//	    if (canRecover(rejection)) {
//		return responseOrNewPromise
//	    }
//	    return $q.reject(rejection);
//	}
//    };
//});


