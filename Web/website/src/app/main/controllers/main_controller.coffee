angular.module "articleApp"
  .controller "MainController", ($scope, $rootScope, $state, $server, $modal) ->
    console.log "MainController"
    
    closeModals = (callback)->
      if $scope.instance
        $scope.instance.dismiss "cancel"
        if _.isFunction(callback)
          callback()

    $rootScope.showModal = (name, data, isNotClosable, dismissCallback) ->
      closeModals(dismissCallback)
      if isNotClosable
        $scope.instance = modalInstance = $modal.open
          templateUrl: "app/main/modals/#{name}Modal.html"
          controller: "#{name}ModalCtrl",
          backdrop: 'static',
          keyboard: 'false',
          resolve: {
            data: data,
          }
        return
      else
        $scope.instance = modalInstance = $modal.open
          templateUrl: "app/main/modals/#{name}Modal.html"
          controller: "#{name}ModalCtrl",
          resolve: {
            data: data,
          }
        return
    $scope.enter = ()->
      if !localStorage.token
        $rootScope.showModal('login')
      else
        $server.login {token: localStorage.token}, (data)->
          if data.error
            $rootScope.showModal('login')
          else
            console.log data
            if data.scope == "User"
              $state.go("cabinet")
            else
              if data.scope == "Moderator" || data.scope=="Administrator"
                $state.go("admin")

    $scope.logout = ()->
      $server.logout {token:localStorage.token }, (data)->
        delete localStorage.token
        $state.go("index")

    $scope.formatDate = (timestamp)->
      return moment(timestamp).format("DD/MM/YYYY")
      
    $scope.getTranslate = (text)->
        console.log text
        voc = {
           'code':'код'
           'text':'текст'
        }
        return voc[text] || ''


