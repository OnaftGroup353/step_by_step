angular.module "articleApp"
  .directive 'addLiterature', ($timeout, $window, $rootScope, $server) ->
    return (scope, element, attrs) ->
      element.on "keydown", (e) ->
        key = e.which
        if key==13
          scope.addLiterature(element.val())
          element.val('')


  .directive 'addTags', ($timeout, $window, $rootScope, $server) ->
    return (scope, element, attrs) ->
      element.on "keydown", (e) ->
        key = e.which
        if key==13
          scope.addTags(element.val())
          element.val('')
