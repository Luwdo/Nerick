var ngm = angular.module(module.exports = 'nerick.ui.login', [
    //require('../../notifications'),
    require('../../api'),
    require('../../auth'),
    require('../../lightCrate')
]);

ngm.factory('nLoginDialog', function (lightCrate) {
    return {
	show: function () {
	    return new Promise(function (resolve, reject) {
		lightCrate.show({
		    templateUrl: 'html/nerick/ui/login/loginDialog.html',
		    locals: {
			dResolve: function (result) {
			    lightCrate.hide();
			    resolve(result);
			},
			dReject: function (result) {
			    lightCrate.hide();
			    reject(result);
			},
		    },
		    controller: LoginDialogController
		});
	    });
	}
    };

});

function LoginDialogController($rootScope, $scope, $timeout, dResolve, dReject, nApi, nAuth) {	
	
	$scope.submit = function(model){
		if(typeof model == "undefined"){
			model = null;
		}
		
		nApi.post('/login', model).then(function(response){
			debugger;
			nAuth.setUser(response);
			angular.element(document).trigger('app-loaded');
			$scope.ok();
		});
//		.catch(function(response){
//			debugger
//		});
	};
	
	
	$scope.cancel = function() {
		reject();
	};
	
	$scope.ok = function() {
		resolve();
	};
}
