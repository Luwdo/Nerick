/*angular.module('login', ['ngRoute']);
angular.module('login').config(['$routeProvider',
    function ($routeProvider) {
	$routeProvider.
	    when('/login', {
		templateUrl: 'modules/login/login.html',
		controller: 'LoginController'
	    })
	    .when('/loginLightBox', {
		templateUrl: 'modules/login/loginLightBox.html',
		controller: 'LoginController'
	    }).
	    otherwise({
		redirectTo: '/'
	    });
    }]).controller('LoginController', function($scope, NotifyService, nAuth, $location, $lightBox) {
	$scope.$login = function(loginModel) {
	    if(typeof loginModel == 'undefined'){
		loginModel = null;
	    }
	    var login = Restangular.one('login');
	    login.customPOST(loginModel).then(function(response) {
		$lightBox.close();
		nAuth.setUser(response.data);
		$location.path( "/" );
	    });
	};
    });*/