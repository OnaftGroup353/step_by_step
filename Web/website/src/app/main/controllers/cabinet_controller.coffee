angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.user= {}
    $scope.search = {}
    $scope.favorites = {}



    $rootScope.userId = 0

    $scope.books = []
    window.s = $scope

    $server.login {token: localStorage.token}, (data)->
        console.log data, data.id,6667
        $rootScope.$apply ()->

          if data.error

              $scope.logout()
          else

              $rootScope.userId = data.id
              $scope.getManualsByUserId(data.id)
              $rootScope.loggIn = true

              $server.getUserInfo {id: data.id}, (data2)->
                  console.log data2
                  $scope.$apply ()->
                   $scope.user = data2


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





    $scope.makeNewArticle = ()->
      delete localStorage.article
      $state.go('cabinet.makeArticle')

    $scope.getManuals = ()->
        $server.getManuals {}, (data)->
            console.log data
            $scope.$apply ()->
                $scope.books = data



    $scope.getManualById = (id)->
        $server.getManualById {id: id}, (data)->
            console.log data
            if !data.error
              $scope.$apply () ->
                $scope.book = data


    $scope.deleteFavorite = (id, e)->
      e.preventDefault()
      e.stopPropagation()
      $server.deleteFavorite {manual_id: id}, (data)->
        if !data.error
          $scope.getMyFavorites()
    $scope.getMyFavorites = ()->
      $server.getMyFavorites {}, (data)->
        if !data.error
          $scope.$apply ()->
            $scope.books = data
#    $scope.getMyFavorites()

    $scope.initFavorites = ()->
      $server.getMyFavorites {}, (data)->
        if !data.error
          $scope.$apply ()->
            for el in data
              $scope.favorites[el.id] = true
    $scope.initFavorites()
    $scope.currentTab = 'my'
    $scope.activeTab = (name)->
      return $scope.currentTab == name

    $scope.toggleTab = (name)->
      $scope.currentTab = name
      if name == 'my'
        $scope.getManualsByUserId($rootScope.userId)
      else if name == 'favorites'
        $scope.getMyFavorites()



    $scope.getManualsByUserId = (id)->
        $server.getManualsByUserId {id: id}, (data)->
            console.log data
            if !data.error
              $scope.$apply ()->
                  $scope.books = data





