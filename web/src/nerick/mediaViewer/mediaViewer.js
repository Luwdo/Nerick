var ngm = angular.module(module.exports = 'nerick.mediaViewer', [
    'ngRoute'
]);

ngm.directive('mediaViewer', function ($rootScope, $http) {
    return {
	restrict: 'AC',
	link: function (scope, element, attrs) {
	    var src = '';

	    var style = $(element).attr('style');

	    var imageList = [
		'image/jpeg',
		'image/gif',
		'image/png',
		'image/tiff',
		'image/svg+xml'
	    ];

	    var videoList = [
		'video/mp4',
		'video/ogg',
		'video/webm'
	    ];
	    //debugger;
	    scope.$watch(function () {
		return element.attr('data-src');
	    }, function (value) {
		if (typeof value !== "undefined") {
		    src = value;
		    updateView();
		}
	    });

	    scope.$watch(function () {
		return element.attr('data-mediaId');
	    }, function (value) {
		if (typeof value !== "undefined" && value != null && value != 0) {
		    src = $rootScope.apiURL + '/uploads/' + value + '/serve'
		    updateView();
		}
	    });

	    function updateView() {
		if (src == "" || typeof src == "undefined") {
		    showNotFound();
		    return;
		}
		//debugger;
		$http.head(src).
			success(function (data, status, headers, config) {
			    var mimeType = headers('Content-Type');
			    if ($.inArray(mimeType, imageList) !== -1) {
				showImage(mimeType);
				return true;
			    }
			    if ($.inArray(mimeType, videoList) !== -1) {
				showVideo(mimeType);
				return true;
			    }
			    showNotFound();
			    return true;
			}).
			error(function (data, status, headers, config) {
			    showNotFound();
			});
	    }

	    function showNotFound() {
		element.html('Media Not Found');
	    }

	    function showImage(mimeType) {
		var $image = $('<img class="media-viewer-image img-responsive" >');
		$image.attr('src', src);
		if (typeof style !== "undefined") {
		    $image.attr('style', style);
		}
		element.html($image);
	    }

	    function showVideo(mimeType) {
		var $video = $('<video class="media-viewer-video embed-responsive-item" ></video>');
		$video.append('<source src="' + src + '" type="' + mimeType + '">');
		if (typeof style !== "undefined") {
		    $video.attr('style', style);
		}
		element.html($video);
	    }

//		function getSrc(){
//		    //debugger;
//		    if(typeof $(element).attr('data-src') !== "undefined"){
//			return $(element).attr('data-src');
//		    }
//		    if(typeof $(element).attr('data-mediaId') !== "undefined"){
//			var mediaId = $(element).attr('data-mediaId');
//			if(mediaId == null || mediaId == 0)
//			    return '';
//		    //debugger;
//			return $rootScope.apiURL+'/uploads/'+mediaId+'/serve'
//		    }
//		    return '';
//		};
	}
    };
});