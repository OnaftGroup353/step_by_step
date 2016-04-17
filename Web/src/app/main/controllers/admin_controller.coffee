angular.module "articleApp"
  .controller "adminCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.logout = ()->
        delete localStorage.articleToken
        $state.go("index")
    if localStorage.articleToken != "g90uh0fguh0s9ugh09su5h"
      $scope.logout()
    

