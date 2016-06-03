angular.module "articleApp"
  .controller "bookInfoCtrl", ($scope, $rootScope, $state, $server, $modal, $stateParams) ->
     # Заглушка
     if $state.params.id
      $scope.getManualById($state.params.id)
     #$scope.book = $scope.books[bookId-1]


