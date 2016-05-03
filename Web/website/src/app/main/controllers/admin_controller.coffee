angular.module "articleApp"
  .controller "adminCtrl", ($scope, $rootScope, $state, $server, $modal) ->
   
    $server.login {token: localStorage.token}, (data)->
        console.log data
        if data.error
            $scope.logout()

