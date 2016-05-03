angular.module "articleApp"
  .controller "loginModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.user = {}
    $scope.cancel = () ->
        $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      $server.login {email:$scope.user.login, password: $scope.user.password}, (data)->
        console.log data
        if data.error
          alert(data.error)
        else
          $scope.cancel()
          localStorage.token = data.token
          console.log data.scope
          if data.scope == "User"
            $state.go("cabinet")
          else
            if data.scope == "Moderator" || data.scope=="Administrator"
              $state.go("admin")



          
        
  .controller "registerModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.cancel = () ->
      $modalInstance.dismiss 'cancel'
    $scope.register = ()->
      console.log "register here"
      if $scope.user.password != $scope.user.password2
        alert("Пароли не совпадают")
      else
        if $scope.user.password.length < 3
          alert("Длина пароля не менее 3 символов")
        else 
          $server.insertUser $scope.user, (data)->
            console.log data
            if !data.error
              $server.login $scope.user, (data)->
                console.log data
                if data.error
                  alert(data.error)
                else
                  $scope.cancel()
                  localStorage.token = data.token
                  if data.scope == "User"
                    $state.go("cabinet")
                  else
                    if data.scope == "Moderator" || data.scope=="Administrator"
                      $state.go("admin")

