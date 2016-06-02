angular.module "articleApp"
.controller "confirmEmailCtrl", ($scope, $rootScope, $state, $server, $modal) ->



  $scope.confirmEmail = ()->
    $server.confirmEmail {confirmationCode: $state.params.confirmationCode}, (data)->
      if data.error
        $scope.$apply ()->
          $scope.confirmationEmailMessage = "Error: " + data.message
      else
        $scope.$apply ()->
          $scope.confirmationEmailMessage = "Confirmation successful!"

  $scope.confirmationEmailMessage = "Confirmation in progress!"
  $scope.confirmEmail()
