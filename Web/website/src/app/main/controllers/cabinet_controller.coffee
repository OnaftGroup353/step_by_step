angular.module "articleApp"
  .controller "cabinetCtrl", ($scope, $rootScope, $state, $server, $modal) ->
   
    if localStorage.articleToken != "u_g90uh0fguh0s9ugh09su5h"
      $scope.logout()

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
    

