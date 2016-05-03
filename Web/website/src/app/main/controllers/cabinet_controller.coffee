angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->

    $server.login {token: localStorage.token}, (data)->
        console.log data
        if data.error
            $scope.logout()

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
    

