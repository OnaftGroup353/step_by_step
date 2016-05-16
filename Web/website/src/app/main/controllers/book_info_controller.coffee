angular.module "articleApp"
  .controller "bookInfoCtrl", ($scope, $rootScope, $state, $server, $modal, $stateParams) ->
     # Заглушка
     $scope.getManualById($state.params.id)
     #$scope.book = $scope.books[bookId-1]
    

