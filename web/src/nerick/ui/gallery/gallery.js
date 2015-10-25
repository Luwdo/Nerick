angular.module('gallery', ['ngRoute', 'datatables']);

angular.module('gallery').config(['$routeProvider',
    function ($routeProvider) {
	$routeProvider.
	    when('/gallery', {
		templateUrl: 'modules/gallery/list.html',
		controller: 'GalleryTableController',
		requireLogin: true
	    }).
	    when('/gallery/add', {
		templateUrl: 'modules/gallery/details.html',
		controller: 'GalleryController',
		requireLogin: true
	    }).
	    when('/gallery/edit/:id', {
		templateUrl: 'modules/gallery/details.html',
		controller: 'GalleryController',
		requireLogin: true
	    }).
	    when('/gallery/delete/:id', {
		templateUrl: 'modules/gallery/delete.html',
		controller: 'GalleryController',
		requireLogin: true
	    }).
	    when('/gallery/:galleryId/artwork/remove/:artworkId', {
		templateUrl: 'modules/gallery/removeArtwork.html',
		controller: 'GalleryArtworkController',
		requireLogin: true
	    }).
	    when('/gallery/:id', {
		templateUrl: 'modules/gallery/view.html',
		controller: 'GalleryViewController',
		requireLogin: false
	    }).
	    when('/gallery/:galleryId/artwork/:artworkId', {
		templateUrl: 'modules/gallery/viewArtwork.html',
		controller: 'GalleryArtworkViewController',
		requireLogin: false
	    }).
	    otherwise({
		redirectTo: '/'
	    });
    }]).controller('GalleryController', function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	$scope.galleryModel = {};
	$scope.galleryModel['artworkIds'] = [];
	$scope.artwork = [];
	
	//$scope.items = ["Saab", "Volvo", "BMW"];

	$scope.title = 'Add Gallery';
	//debugger;
	if($routeParams.id){
	    $scope.title = 'Edit Gallery';
	    var gallery = Restangular.one('gallery', $routeParams.id);
	    gallery.get().then(function(response) {
		$scope.galleryModel = response.data;
		$scope.galleryModel['artworkIds'] = [];
		gallery.customGET('artwork').then(function(response){
		    $scope.artwork = response.data;
		    //$scope.artworkTable.reloadData();
		    //createArtworkTable();
		});
	    });
	}else{
	    //`createArtworkTable();
	}
	
	$scope.addArtwork = function(id){
	    var artwork = Restangular.one('artwork', id);
	    artwork.get().then(function(response) {
		$scope.galleryModel.artworkIds.push(id);
		$scope.artwork.push(response.data);
		$scope.artworkTable.reloadData();
	    });
	};
	
	$scope.removeArtwork = function(id){
	    
	    //debugger;
	    
	    var artworkIdsLength = $scope.galleryModel.artworkIds.length;
	    for (var i = 0; i < artworkIdsLength; i++) {
		if($scope.galleryModel.artworkIds[i] == id){
		    $scope.galleryModel.artworkIds.splice(i, 1);
		    break;
		}
	    }
	    
	    var artworkLength = $scope.artwork.length;
	    for (var j = 0; j < artworkLength; j++) {
		if($scope.artwork[j].id == id){
		    $scope.artwork.splice(j, 1);
		    break;
		}
	    }
	    
	    $scope.artworkTable.reloadData();
	};

	//list of artwork ids for added/pending add and already added
	function getArtworkIds(){
	    var allArtworkIds = [];
	    
	    var artworkLength = $scope.artwork.length;
	    for (var i = 0; i < artworkLength; i++) {
		allArtworkIds.push($scope.artwork[i].id);
	    }

	    return allArtworkIds;
	}
	
	$scope.dtOptions = DTOptionsBuilder.newOptions()
	.withOption('ajax', {
	    url: $rootScope.apiURL+'/artwork/table',
	    type: 'GET',
	    data: function (d) {
		//debugger;
		d['excludeArtworkIds'] = $scope.galleryModel['artworkIds'];
		if($routeParams.id){
		    d['excludeGallery'] = $routeParams.id;
		}
	    }
	})
	.withDataProp('data')
	    .withOption('serverSide', true)
	    .withOption('autoWidth', false)
	    .withPaginationType('full_numbers')
	    .withOption('createdRow', createdRow);

	$scope.dtColumns = [
	    DTColumnBuilder.newColumn(null).withTitle('View').notSortable().renderWith(viewHtml).withOption('width', 200),
	    DTColumnBuilder.newColumn('id').withTitle('ID'),
	    DTColumnBuilder.newColumn('title').withTitle('Title'),
	    DTColumnBuilder.newColumn('medium').withTitle('Medium'),
	    DTColumnBuilder.newColumn('mediaType').withTitle('Type'),
	    DTColumnBuilder.newColumn('yearCompleted').withTitle('Produced'),
	    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable().renderWith(actionsHtml)
	];

	DTInstances.getLast().then(function (dtInstance) {
	    $rootScope.dtInstance = dtInstance;
	    $scope.artworkTable = dtInstance;
	});
	function createdRow(row, data, dataIndex) {
	// Recompiling so we can bind Angular directive to the DT
	    $compile(angular.element(row).contents())($scope);
	    $scope.$apply();
	}

	function actionsHtml(data, type, full, meta) {
	    var actions = '<a href="javascript:;" class="button" ng-click="addArtwork('+data.id+');" >Add to Gallery</a>';
	    //actions += '<a data-href="/users/delete/'+data.id+'" class="action light-box" title="Delete"><img src="/images/icons/delete_black_white.svg"/></a>';
	    return actions;
	}
	function viewHtml(data, type, full, meta) {
	    var view = '<div media-viewer data-mediaId="'+data.uploadId+'" style="display: inline-block;" data-style="max-width: 200px"></div>';
	    return view;
	}
	
	
	
	
	
	//end table stuff
	

	$scope.save = function(galleryModel){
	    var artworkOrder = [];
	    for (var i = 0; i < $scope.artwork.length; i++) { 
		artworkOrder.push($scope.artwork[i].id);
	    }
	    
	    galleryModel['artworkOrder'] = artworkOrder;
	    
	    if(galleryModel.id){
		$scope.edit(galleryModel);
	    }
	    else{
		$scope.add(galleryModel);
	    }
	}
	
	$scope.add = function(galleryModel) {
	    if(typeof galleryModel == 'undefined'){
		galleryModel = null;
	    }
	    var gallery = Restangular.one('gallery');
	    gallery.customPUT(galleryModel).then(function(response) {
		//debugger;
		$location.path("/gallery/edit/"+response.data);
	    });
	    
	};
	
	$scope.edit = function(galleryModel) {
	    if(typeof galleryModel == 'undefined'){
		galleryModel = null;
	    }
	    var gallery = Restangular.one('gallery', galleryModel.id);
	    gallery.customPOST(galleryModel).then(function(response) {
		
	    });
	};
	
//	$scope.delete = function(galleryModel) {
//	    return false;
//	    if(typeof userModel == 'undefined'){
//		userModel = null;
//	    }
//	    var users = Restangular.one('users', userModel.id);
//	    users.customDELETE().then(function(response) {
//		$lightBox.close();
//		if($rootScope.dtInstance){
//		    $rootScope.dtInstance.rerender();
//		}
//	    });
//	};
	
    }).controller('GalleryTableController', function($rootScope, $scope, NotifyService, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances) {
	
	$scope.dtOptions = DTOptionsBuilder.newOptions()
	.withOption('ajax', {
	    url: $rootScope.apiURL+'/gallery/table',
	    type: 'GET'
	})
	.withDataProp('data')
	    .withOption('serverSide', true)
	    .withOption('autoWidth', false)
	    .withPaginationType('full_numbers')
	    .withOption('createdRow', createdRow);

	$scope.dtColumns = [
	    DTColumnBuilder.newColumn('id').withTitle('ID'),
	    DTColumnBuilder.newColumn('title').withTitle('Title'),
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
	    var actions = '<a href="#/gallery/edit/'+data.id+'" class="action" title="Edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
	    //actions += '<a data-href="/users/delete/'+data.id+'" class="action light-box" title="Delete"><img src="/images/icons/delete_black_white.svg"/></a>';
	    return actions;
	}
	
    }).controller('GalleryArtworkController', function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile) {
	
	$scope.artworkModel = {};
	//galleryId
	if($routeParams.artworkId){
	    var artwork = Restangular.one('artwork', $routeParams.artworkId);
	    artwork.get().then(function(response) {
		$scope.artworkModel = response.data;
		//debugger;
	    });
	}
	
	$scope.remove = function(artworkModel) {
	    //debugger;
	    if(typeof artworkModel == 'undefined'){
		return false;
	    }
	    
	    $scope.$parent.removeArtwork(artworkModel.id);
	    if($routeParams.galleryId == 0){
		$NotifyService.clear();
		$NotifyService.addGlobal('Artwork removed successfully.', $NotifyService.types.error);
		$NotifyService.show();
		$lightBox.close();
		return;
	    }
	    
	    var galleryArtwork = Restangular.one('gallery', $routeParams.galleryId).one('artwork', artworkModel.id);
	    galleryArtwork.customDELETE().then(function(response) {
		$lightBox.close();
	    });
	};
	
	
    }).controller('GalleryViewController', function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile) {
	    $scope.title = 'Loading';
	    $scope.artwork = [];
	    $scope.images = [];
	    
	    
	    var gallery = Restangular.one('gallery', $routeParams.id);
	    gallery.get().then(function(response) {
		$scope.gallery = response.data;
		$scope.title = $scope.gallery.title;		
		gallery.customGET('artwork').then(function(response){
		    
		    $scope.gallery['artwork'] = response.data;
		    
		    $scope.items = [];
		    
		    //var artworkOrder = [];
		    for (var i = 0; i <  $scope.gallery.artwork.length; i++) {
			var artwork = $scope.gallery.artwork[i];
			
			var src = $rootScope.apiURL+'/uploads/'+artwork.uploadId+'/serve';
			
			var item = {
				index : i,
				src: src,
				msrc: src,
				title: artwork.title,
				w: 1024, // image width
				h: 768, // image height
				type: 'image'
			};
			
			$scope.items.push(item);
			
			artwork['item'] = item;
		    }
		    
		    $scope.artworkClicked = function(artwork, $event){
			//debugger;
			var target = $event.target;
			
			var index = artwork.item.index;
			var pswpElement = $('.pswp').get(0);
			var options = {
			    history:false,
			    index: index,
			    barSize: {top:0,bottom:0},
			    getThumbBoundsFn: function(index) {
				var offset = $(target).offset();
				return {x:offset.left, y:offset.top, w:target.offsetWidth};
			    }
			};
			var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, $scope.items, options);
			gallery.init();
		    };
		    
		    //$rootScope.apiURL+'/uploads/'+value+'/serve'
		    $scope.gallery['artwork'] = response.data;
		});
	    });
	    
	    var screenSize = function (width, height) {
		var x = width ? width : $window.innerWidth;
		var y = height ? height : $window.innerHeight;
		
		return x + 'x' + y;
	    }; 
    })
    .controller('GalleryArtworkViewController', function($rootScope, $scope, $lightBox, $routeParams, $location, $NotifyService, $compile) {
	    $scope.title = 'Loading';
	    $scope.artwork = null;
	    $scope.galleryId = $routeParams.galleryId;
	    var galleryArtwork = Restangular.one('gallery', $routeParams.galleryId).one('artwork', $routeParams.artworkId);
	    galleryArtwork.get().then(function(response) {
		$scope.artwork = response.data.artwork;
		$scope.title = response.data.artwork.title;
		$scope.nextArtworkId = response.data.nextArtworkId;
		$scope.previousArtworkId = response.data.previousArtworkId;
	    });
    });