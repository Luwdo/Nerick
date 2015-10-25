var ngm = angular.module(module.exports = 'trimedia.ui.shell', [
    'ngRoute',
    require('../../api'),
]);

ngm.controller('ShellController', ShellController);

function ShellController($scope) {
    // Build handler to open/close a SideNav

    $scope.galleries = [];



//    var gallery = Restangular.one('gallery');
//    gallery.get({menuVisible: true}).then(function(response) {
//	$scope.galleries = response.data;
//    });
}

