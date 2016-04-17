angular.module "articleApp"
  .controller "adminCtrl", ($scope, $rootScope, $state, $server, $modal) ->
   
    if localStorage.articleToken != "a_g90uh0fguh0s9ugh09su5h"
      $scope.logout()
    

