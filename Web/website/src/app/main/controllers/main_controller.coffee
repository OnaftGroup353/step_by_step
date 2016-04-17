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

    $scope.logout = ()->
      delete localStorage.articleToken
      $state.go("index")

    $scope.formatDate = (timestamp)->
      return moment(timestamp).format("DD/MM/YYYY")


