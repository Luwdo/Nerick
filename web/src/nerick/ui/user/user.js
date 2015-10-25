angular.module('user', ['ngRoute', 'datatables']);

angular.module('user').config(['$routeProvider',
    function ($routeProvider) {
	$routeProvider.
	    when('/users', {
		templateUrl: 'modules/user/list.html',
		controller: 'UserTableController',
		requireLogin: true
	    }).
	    when('/users/add', {
		templateUrl: 'modules/user/details.html',
		controller: 'UserController',
		requireLogin: true
	    }).
	    when('/users/edit/:id', {
		templateUrl: 'modules/user/details.html',
		controller: 'UserController',
		requireLogin: true
	    }).
	    when('/users/delete/:id', {
		templateUrl: 'modules/user/delete.html',
		controller: 'UserController',
		requireLogin: true
	    }).
	    otherwise({
		redirectTo: '/'
	    });
    }]).controller('UserController', function($rootScope, $scope, $lightBox, $routeParams, $location) {
	//$scope.user = {email:'test'};
	$scope.title = 'Add User';
	//debugger;
	if($routeParams.id){
	    $scope.title = 'Edit User';
	    var user = Restangular.one('users', $routeParams.id);
	    user.get().then(function(response) {
		$scope.userModel = response.data;
		//debugger;
	    });
	}
	
	$scope.$save = function(userModel){
	    if(typeof userModel != 'undefined' && userModel.id){
		$scope.$edit(userModel);
	    }
	    else{
		$scope.$add(userModel);
	    }
	}
	
	$scope.$add = function(userModel) {
	    if(typeof userModel == 'undefined'){
		userModel = null;
	    }
	    var users = Restangular.one('users');
	    users.put(userModel).then(function(response) {
		$location.path("/users/edit/"+response.data);
	    });
	    
	};
	
	$scope.$edit = function(userModel) {
	    if(typeof userModel == 'undefined'){
		userModel = null;
	    }
	    var users = Restangular.one('users', userModel.id);
	    users.customPOST(userModel).then(function(response) {
		
	    });
	};
	
	$scope.$delete = function(userModel) {
	    if(typeof userModel == 'undefined'){
		userModel = null;
	    }
	    var users = Restangular.one('users', userModel.id);
	    users.customDELETE().then(function(response) {
		$lightBox.close();
		if($rootScope.dtInstance){
		    $rootScope.dtInstance.rerender();
		}
	    });
	};
    }).controller('UserTableController', function($rootScope, $scope, NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	
	$scope.dtOptions = DTOptionsBuilder.newOptions()
	.withOption('ajax', {
	    url: $rootScope.apiURL+'/users/table',
	    type: 'GET'
	})
	.withDataProp('data')
	    .withOption('serverSide', true)
	    .withPaginationType('full_numbers')
	    .withOption('createdRow', createdRow);

	$scope.dtColumns = [
	    DTColumnBuilder.newColumn('id').withTitle('ID'),
	    DTColumnBuilder.newColumn('username').withTitle('Username'),
	    DTColumnBuilder.newColumn('email').withTitle('Email'),//.notVisible()
	    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
            .renderWith(actionsHtml)
	];
	
	DTInstances.getLast().then(function (dtInstance) {
	    $rootScope.dtInstance = dtInstance;
	});
	
	function createdRow(row, data, dataIndex) {
        // Recompiling so we can bind Angular directive to the DT
	    $compile(angular.element(row).contents())($scope);
	}
	function actionsHtml(data, type, full, meta) {
	    var actions = '<a href="#/users/edit/'+data.id+'" class="action" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
	    actions += '<a data-href="/users/delete/'+data.id+'" class="action lg light-box" title="Delete"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
	    return actions;
	}
    });