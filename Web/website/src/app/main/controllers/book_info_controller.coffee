angular.module "articleApp"
  .controller "bookInfoCtrl", ($scope, $rootScope, $state, $server, $modal, $stateParams) ->
     # Заглушка
     if $state.params.id
      $scope.getManualById($state.params.id)
     #$scope.book = $scope.books[bookId-1]
     $scope.deleteManual = ()->
     	$server.deleteManualById {id: $state.params.id}, (data)->
     		console.log data
     		$scope.getManualsByUserId($rootScope.userId)

