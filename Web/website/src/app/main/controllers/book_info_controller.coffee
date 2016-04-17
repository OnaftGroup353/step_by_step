angular.module "articleApp"
  .controller "bookInfoCtrl", ($scope, $rootScope, $state, $server, $modal, $stateParams) ->
     # Заглушка
     bookId = $stateParams.id
     $scope.book = $scope.books[bookId-1]
    

