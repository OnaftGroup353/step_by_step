angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.user= {}
    $scope.search = {}




    $rootScope.userId = 0

    $scope.books = []
    window.s = $scope

    $server.login {token: localStorage.token}, (data)->
        console.log data, data.id
        if data.error

            $scope.logout()
        else
            $rootScope.$apply ()->
                $rootScope.userId = data.id
            $scope.getManualsByUserId(data.id)
            $rootScope.loggIn = true

            $server.getUserInfo {id: data.id}, (data2)->
                $scope.user = data2

    console.log $state.current.name,123
    # Заглушка

    $scope.submitProfile = ()->
        delete $scope.user.banned
        delete $scope.user.scope_name
        delete $scope.user.scope_id
        delete $scope.user.id


        $scope.user.social_network_id = 1
        $scope.user.social_network_type = 1
        $server.updateUser $scope.user, (data)->
            console.log data


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
            if !data.error
              $scope.$apply () ->
                $scope.book = data



    $scope.getManualsByUserId = (id)->
        $server.getManualsByUserId {id: id}, (data)->
            console.log data
            if !data.error
              $scope.$apply ()->
                  $scope.books = data
                  for book in $scope.books
                      book.date = +book.date * 1000





