angular.module "articleApp"
  .directive 'changeEdit', ($timeout, $window, $rootScope, $server) ->
    return (scope, element, attrs) ->
      element.on "click", (e) ->
        e.preventDefault()
        e.stopPropagation()
        scope.$apply ()->
        	scope.edit=!scope.edit
