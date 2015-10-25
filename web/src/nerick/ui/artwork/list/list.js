var ngm = angular.module(module.exports = 'nerick.ui.artwork.list', [
	'ngRoute',
	'datatables',
	require('../../api'),
]);


ngm.directive('ArtworkList', function($location, tmNotification) {
    return {
	scope: {
	    api: '=',
	},
	templateUrl: 'html/nerick/ui/artwork/list/list.html',
	controller: ArtworkTableController,
	link: function(scope, element, attrs){
	}
    };
});

function ArtworkTableController($rootScope, $scope, $compile, DTOptionsBuilder, DTColumnBuilder, DTInstances){

    $scope.dtOptions = DTOptionsBuilder.newOptions()
    
    .withOption('ajax', {
	url: window.appConfig.apiEndpoint+'/artwork/table',
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

};