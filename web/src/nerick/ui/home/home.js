var ngm = angular.module(module.exports = 'nerick.ui.home', [
    'ngRoute',
    require('../../lightCrate'),
    require('../login'),
]);

ngm.config(function($routeProvider) {
    $routeProvider
	.when('/', {
	    templateUrl: 'html/nerick/ui/home/home.html',
	    controller: 'HomeController',
	})
	.otherwise('/');
});

ngm.controller('HomeController', function ($scope, lightCrate, nLoginDialog) {
	$scope.sliderArtwork = [];
	
	$scope.openbox = function(){
	    nLoginDialog.show();
	    
	    
//	    lightCrate.show({
//		route: '/test',
//		//templateUrl: 'html/nerick/ui/home/home.html',
//		locals: {
//		    lcResolve: function(result) {
//			    lightCrate.hide();
//			    resolve(result);
//		    },
//		    lcReject: function(result) {
//			    lightCrate.hide();
//			    reject(result);
//		    },
//		},
//		resolve: {
//		    crateStuff : function($location){
//			    return Promise.resolve('crateStuff');
//		    }
//		},
//		controller: CrateController
//	    });
	};
	
//	function CrateController(lcResolve, lcReject, crateStuff){
//	    debugger;
//	};
	
	//$scope.sliderArtworkSrc = [];
/*	var gallery = Restangular.one('gallery', 'mainslider');
	gallery.get().then(function(response) {
	    //debugger;
	    $scope.sliderArtworkSrc = [];
	    $scope.sliderArtwork = response.data;
	    for (var i = 0; i < $scope.sliderArtwork.length; i++) { 
		$scope.sliderArtworkSrc.push($rootScope.apiURL+'/uploads/'+$scope.sliderArtwork[i].uploadId+'/serve');
	    }
	});*/
});
