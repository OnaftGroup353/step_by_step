angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.user= {}
    $server.login {token: localStorage.token}, (data)->
        console.log data
        if data.error
		    
            #$scope.logout()
        else
            $server.getUserInfo {id: data.id}, (data2)->
                $scope.user = data2

    console.log $state.current.name,123
    # Заглушка
    $scope.books = [
     	{
     		id: 1
     		direction: "Компьютерные науки"
     		name: "3 курс Лабораторные работы"
     		modified: 1460930831000
     	}
     	{
     		id: 2
     		direction: "Компьютерные науки"
     		name: "4 курс пособие для курсовых работ"
     		modified: 1460910831000
     	}
    ]
    $scope.submitProfile = ()->
        delete $scope.user.banned
        delete $scope.user.scope_name
        delete $scope.user.scope_id
        delete $scope.user.id
        
        
        $scope.user.social_network_id = 1
        $scope.user.social_network_type = 1
        $server.updateUser $scope.user, (data)->
            console.log data
    

