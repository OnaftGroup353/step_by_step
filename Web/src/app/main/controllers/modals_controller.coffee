angular.module "articleApp"
  .controller "loginModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.user = {}
    $scope.cancel = () ->
        $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      console.log "login here"
      if $scope.user.login == 'admin' && $scope.user.password == '123'
        localStorage.setItem("articleToken", "g90uh0fguh0s9ugh09su5h")
        $scope.cancel()
        $state.go('admin')    
  .controller "registerModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.cancel = () ->
      $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      console.log "register here"    
