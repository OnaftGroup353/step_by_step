angular.module "articleApp"
  .controller "loginModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.cancel = () ->
        $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      console.log "login here"    
  .controller "registerModalCtrl", ($scope, $rootScope, $state, $server, $modal, $modalInstance) ->
    $scope.cancel = () ->
      $modalInstance.dismiss 'cancel'
    $scope.login = ()->
      console.log "register here"    
