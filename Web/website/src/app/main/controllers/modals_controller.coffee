angular.module "articleApp"
  .controller "loginModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.user = {}
    $scope.cancel = () ->
        $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      delete localStorage.token
      $server.login {email:$scope.user.login, password: $scope.user.password}, (data)->
        console.log data
        if data.error
          #alert(data.error)
        else
          $scope.cancel()
          localStorage.token = data.token
          console.log data.scope
          if $rootScope.isArticleSubmit
            $server.createManual JSON.parse(localStorage.article), (data)->
              if data.error
                console.log 'error', data
              $rootScope.isArticleSubmit = false
          if data.scope == "User"
            $state.go("cabinet")
          else
            if data.scope == "Moderator" || data.scope=="Administrator"
              $state.go("admin")
    window.ulog = (token)->

      request = $.ajax {
          url: "http://ulogin.ru/token.php?token="+token,
          method: 'GET',
          dataType: 'jsonp'
          
        }

      request.done (data)->
        delete localStorage.token
        console.log 'data2', JSON.parse(data)
        $server.login JSON.parse(data), (data)->
          if !data.error
            $scope.cancel()
            localStorage.token = data.token
            $state.go('cabinet')
          else
            console.log "Не удалось войти"

      request.fail (xhr)->
        console.log xhr.responseJSON
        #callback(xhr.responseJSON || {error:"empty server response"})
      
      #$.get ("http://ulogin.ru/token.php?token="+token), (data)->
       # console.log data
        #$server.login data, (data)->
        #  if !data.error
        #    localStorage.token = data.token
        #    $state.go('cabinet')
        #  else
        #    console.log "Не удалось войти"

       



          
        
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
          delete localStorage.token
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



