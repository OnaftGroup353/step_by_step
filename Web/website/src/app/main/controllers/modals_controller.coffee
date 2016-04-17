angular.module "articleApp"
  .controller "loginModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.user = {}
    $scope.cancel = () ->
        $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      if $scope.user.login == 'admin' && $scope.user.password == '123'
        localStorage.setItem("articleToken", "a_g90uh0fguh0s9ugh09su5h")
        $scope.cancel()
        $state.go('admin')

      if $scope.user.login == 'user' && $scope.user.password == '123'
        localStorage.setItem("articleToken", "u_g90uh0fguh0s9ugh09su5h")
        $scope.cancel()
        $state.go('cabinet')    
        
  .controller "registerModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.cancel = () ->
      $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      console.log "register here"    
