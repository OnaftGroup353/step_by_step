angular.module "articleApp"
  .directive "dataSlide", ($timeout, $window, $rootScope, $server) ->
    return (scope, element, attrs) ->
      console.log "ggsdfg"
      return

  .directive "nicescroll", ($timeout, $window, $rootScope, $server) ->
    return (scope, element, attrs) ->
      $(element).niceScroll()
