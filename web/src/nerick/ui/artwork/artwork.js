var ngm = angular.module(module.exports = 'nerick.ui.artwork', [
	'ngRoute',
	'datatables',
	require('../api'),
]);




ngm.config(['$routeProvider',
    function ($routeProvider) {
	$routeProvider.
	    when('/artwork', {
		templateUrl: 'modules/artwork/list.html',
		controller: 'ArtworkTableController',
		requireLogin: true
	    }).
	    when('/artwork/add', {
		templateUrl: 'modules/artwork/details.html',
		controller: 'ArtworkController',
		requireLogin: true
	    }).
	    when('/artwork/edit/:id', {
		templateUrl: 'modules/artwork/details.html',
		controller: 'ArtworkController',
		requireLogin: true
	    }).
	    when('/artwork/delete/:id', {
		templateUrl: 'modules/artwork/delete.html',
		controller: 'ArtworkController',
		requireLogin: true
	    }).
	    when('/artwork/uploads/delete/:id', {
		templateUrl: 'modules/artwork/deleteUpload.html',
		controller: 'ArtworkUploadController',
		requireLogin: true
	    }).
	    otherwise({
		redirectTo: '/'
	    });
    }]).controller('ArtworkController',function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	//$scope.user = {email:'test'};
	$scope.artworkModel = {yearCompleted:'2008'};
	$scope.artworkModel['uploadIds'] = {};
	$scope.artworkModel['uploadId'] = 0;
	
	$scope.title = 'Add Artwork';
	//debugger;
	if($routeParams.id){
	    $scope.title = 'Edit Artwork';
	    var artwork = Restangular.one('artwork', $routeParams.id);
	    artwork.get().then(function(response) {
		$scope.artworkModel = response.data;
		$scope.artworkModel['uploadIds'] = {};
		if($rootScope.dtInstance){
		    $rootScope.dtInstance.reloadData();
		}
	    });
	}
	
	
	$scope.$uploadSuccess = function($flow, $file, $message){
	    var $response = JSON.parse($message);
	    $NotifyService.clear();
	    $NotifyService.populateNotifications($response.notifications);
	    $NotifyService.show();
	    $scope.$addUpload($file.uniqueIdentifier, $response.data.uploadId);
	    $rootScope.dtInstance.reloadData();
	    //$scope.$apply();
	    //debugger;
	};
	$scope.$uploadError = function($flow, $file, $message){
	    var $response = JSON.parse($message);
	    $NotifyService.clear();
	    $NotifyService.populateNotifications($response.notifications);
	    $NotifyService.show();
	    //debugger;
	};
	
	$scope.$addUpload = function(key, id){
	    $scope.artworkModel['uploadIds'][key] = id;
	};
	$scope.$removeUpload = function(key){
	    if(typeof $scope.artworkModel['uploadIds'][key] !== 'undefined'){
		delete $scope.artworkModel['uploadIds'][key];
	    }
	};
	$scope.$removeUploads = function(){
	    $scope.artworkModel['uploadIds'] = {};
	};
	
	$scope.filterFiles = function($file){
	    return !$file.isComplete();
	};
	
	
	//table stuff
	$scope.dtOptions = DTOptionsBuilder.newOptions()
	.withOption('ajax', {
	    url: $rootScope.apiURL+'/artwork/uploads/table',
	    type: 'GET',
	    data: function (d) {
		//debugger;
                d['uploadIds'] = $scope.artworkModel.uploadIds;
		
		if(typeof $scope.artworkModel.id !== 'undefined'){
		    d['artworkId'] = $scope.artworkModel.id;
		}
		//debugger;
            }
	})
	.withDataProp('data')
	.withOption('serverSide', true)
	.withOption('autoWidth', false)
	.withPaginationType('full_numbers')
	.withOption('createdRow', createdRow);

	$scope.dtColumns = [
	    DTColumnBuilder.newColumn(null).withTitle('View').notSortable().renderWith(viewHtml).withOption('width', 200),,
	    DTColumnBuilder.newColumn('id').withTitle('ID'),
	    DTColumnBuilder.newColumn('name').withTitle('Name'),
	    DTColumnBuilder.newColumn('type').withTitle('Type'),//.notVisible()
	    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable().renderWith(actionsHtml)
	];
	
	DTInstances.getLast().then(function (dtInstance) {
	    $rootScope.dtInstance = dtInstance;
	});
	
	function createdRow(row, data, dataIndex) {
        // Recompiling so we can bind Angular directive to the DT
	    //console.log('compiling D for ROW');
	    $compile(angular.element(row).contents())($scope);
	}
	function actionsHtml(data, type, full, meta) {
	    var actions = '<a data-href="/artwork/uploads/delete/'+data.id+'" class="action light-box" title="Delete"><img src="/images/icons/delete_black_white.svg"/></a>';
	    return actions;
	}
	function viewHtml(data, type, full, meta) {
	    //myStyle="{border:\'solid 10px rgb(127, 20, 3)\'}"
	    var view = '<div media-viewer data-mediaId="'+data.id+'" style="display: inline-block; cursor: pointer;" data-style="max-width: 200px" ng-click="selectPrimary('+data.id+');" ng-class="{\'selected-upload\': artworkModel.uploadId == '+data.id+'}"></div>';
	    return view;
	}
	
	$scope.selectPrimary = function(id){
	    $scope.artworkModel['uploadId'] = id;
	};
	
	
//	$scope.getSrc = function(id){
//	    //debugger;
//	    if(id == null || id == 0)
//		return '';
//	    return $rootScope.apiURL+'/uploads/'+id+'/serve'
//	};
	
	$scope.getId = function(){
	    var id = $scope.artworkModel.uploadId;
	    if(id == null || id == 0)
		return '';
	    return $scope.artworkModel.uploadId;
	};
	
	
	//end table stuff
	

	$scope.save = function(artworkModel){
	    if(artworkModel.id){
		$scope.edit(artworkModel);
	    }
	    else{
		$scope.add(artworkModel);
	    }
	}
	
	$scope.add = function(artworkModel) {
	    if(typeof artworkModel == 'undefined'){
		artworkModel = null;
	    }
	    var artwork = Restangular.one('artwork');
	    artwork.customPUT(artworkModel).then(function(response) {
		//debugger;
		$location.path("/artwork/edit/"+response.data);
	    });
	    
	};
	
	$scope.edit = function(artworkModel) {
	    if(typeof artworkModel == 'undefined'){
		artworkModel = null;
	    }
	    var artwork = Restangular.one('artwork', artworkModel.id);
	    artwork.customPOST(artworkModel).then(function(response) {
		
	    });
	};
	
	$scope.delete = function(userModel) {
	    if(typeof userModel == 'undefined'){
		userModel = null;
	    }
	    var users = Restangular.one('users', userModel.id);
	    users.customDELETE().then(function(response) {
		$lightBox.close();
		if($rootScope.dtInstance){
		    $rootScope.dtInstance.reloadData();
		}
	    });
	};
    }).controller('ArtworkTableController', function($rootScope, $scope, NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	
	$scope.dtOptions = DTOptionsBuilder.newOptions()
	.withOption('ajax', {
	    url: $rootScope.apiURL+'/artwork/table',
	    type: 'GET'
	})
	.withDataProp('data')
	    .withOption('serverSide', true)
	    .withOption('autoWidth', false)
	    .withPaginationType('full_numbers')
	    .withOption('createdRow', createdRow);

	$scope.dtColumns = [
	    DTColumnBuilder.newColumn(null).withTitle('View').notSortable().renderWith(viewHtml).withOption('width', 200),,
	    DTColumnBuilder.newColumn('id').withTitle('ID'),
	    DTColumnBuilder.newColumn('title').withTitle('Title'),
	    DTColumnBuilder.newColumn('medium').withTitle('Medium'),
	    DTColumnBuilder.newColumn('mediaType').withTitle('Type'),
	    DTColumnBuilder.newColumn('yearCompleted').withTitle('Produced'),
	    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
            .renderWith(actionsHtml)
	];
	
	
	DTInstances.getLast().then(function (dtInstance) {
	    $rootScope.dtInstance = dtInstance;
	});
	
	function createdRow(row, data, dataIndex) {
        // Recompiling so we can bind Angular directive to the DT
	    $compile(angular.element(row).contents())($scope);
	    $scope.$apply();
	}
	
	function actionsHtml(data, type, full, meta) {
	    var actions = '<a href="#/artwork/edit/'+data.id+'" class="action" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
	    //actions += '<a data-href="/users/delete/'+data.id+'" class="action light-box" title="Delete"><img src="/images/icons/delete_black_white.svg"/></a>';
	    return actions;
	}
	function viewHtml(data, type, full, meta) {
	    var view = '<div media-viewer data-mediaId="'+data.uploadId+'" style="display: inline-block;" data-style="max-width: 200px"></div>';
	    return view;
	}
	
    }).controller('ArtworkUploadController', function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	if($routeParams.id){
	    var user = Restangular.one('uploads', $routeParams.id);
	    user.get().then(function(response) {
		$scope.uploadModel = response.data;
		//debugger;
	    });
	}
	
	$scope.$delete = function(uploadModel) {
	    if(typeof uploadModel == 'undefined'){
		uploadModel = null;
	    }
	    var users = Restangular.one('uploads', uploadModel.id);
	    users.customDELETE().then(function(response) {
		$lightBox.close();
		if($rootScope.dtInstance){
		    $rootScope.dtInstance.reloadData();
		}
	    });
	};
	
	
    });