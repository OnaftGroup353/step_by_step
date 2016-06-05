angular.module "articleApp"
  .controller "searchCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.loaded = false
    $scope.books = []
    $scope.pag = {itemPerPage:8, currentPage:1}
    $scope.showItem = (index)->
      return (index >= ($scope.pag.currentPage - 1) * $scope.pag.itemPerPage) && (index<=($scope.pag.currentPage) * $scope.pag.itemPerPage - 1)
    window.s = $scope
    $scope.search = (name)->
      $server.articleSearch {name: name}, (data)->
        console.log data
        $scope.$apply ()->
          $scope.loaded = true
          if !data.error
            $scope.books = data

    $scope.canAdd = ()->
      return $state.includes('cabinet')

    $scope.showArticle = (id)->
      if $state.includes('cabinet')
        $state.go('cabinet.bookInfo', {id:id})
      else
        $state.go('bookInfo', {id:id})

    $scope.addFavorite = (id)->
      $server.addFavorite {manual_id: id}, (data)->
        console.log data
        if !data.error
          $scope.$apply ()->
            $scope.favorites[id] = true

    $scope.search($state.params.name)
