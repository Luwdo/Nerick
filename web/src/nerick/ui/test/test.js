var ngm = angular.module(module.exports = 'nerick.ui.test', [
    'ngRoute'
]);




ngm.config(function($routeProvider) {
    $routeProvider
	.when('/test', {
	    templateUrl: 'html/nerick/ui/test/test.html',
	    controller: 'TestController',
	})
	.otherwise('/');
});


ngm.controller('TestController', function ($scope, lightCrate) {
    
});
