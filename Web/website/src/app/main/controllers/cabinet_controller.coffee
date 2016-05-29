angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.user= {}
    $scope.search = {}

    $scope.articleSearch = ()->
        $server.articleSearch {name: $scope.search.name}, (data)->
            console.log data


    $scope.getManuals = ()->
        $server.getManuals {}, (data)->
            console.log data
            $scope.$apply ()->
                $scope.books = data
                for book in $scope.books
                    book.date = +book.date * 1000

    $scope.getManualById = (id)->
        $server.getManualById {id: id}, (data)->
            console.log data
            $scope.$apply () ->
              $scope.book = data



    $scope.getManualsByUserId = (id)->
        $server.getManualsByUserId {id: id}, (data)->
            console.log data
            $scope.$apply ()->
                $scope.books = data
                for book in $scope.books
                    book.date = +book.date * 1000





