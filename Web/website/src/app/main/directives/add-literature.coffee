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


  .directive 'articleHide', ($timeout, $window, $rootScope, $server, $state) ->
    return (scope, element, attrs) ->
      $(element).click (e)->
        if $(".article-container").hasClass("article-container") && $state.includes("cabinet.makeArticle") && !$rootScope.down
          $(".article-container").css("transform": "translateX(0px)" )
          $state.go('cabinet')
          #console.log "ok3"
          #$(".article-container").removeClass("article-container")

  .directive 'cabinetArticle', ($timeout, $window, $rootScope, $server, $state) ->
    return (scope, element, attrs) ->
      $(element).click (e)->
          if $(".article-container").hasClass("article-container")
            e.stopPropagation()
            e.preventDefault()


#      debounce = (func, threshold, execAsap) ->
#        timeout = false
#
#        return debounced = ->
#          obj = this
#          args = arguments
#
#          delayed = ->
#            func.apply(obj, args) unless execAsap
#            timeout = null
#
#          if timeout
#            clearTimeout(timeout)
#          else if (execAsap)
#            func.apply(obj, args)
#
#          timeout = setTimeout delayed, threshold || 100
#
#      $rootScope.down = false
#      pageX = 0
#      left = 0
#      $(element).mousedown (e)->
#        if $(".article-container").hasClass("article-container")
#          pageX = e.pageX
#          left = $(element).css("transform")
#          left = parseInt(left.split(',')[4],10)
#          $rootScope.down = true
#
#      $(element).mouseup (e)->
#        if $(".article-container").hasClass("article-container")
#          e.stopPropagation()
#          e.preventDefault()
#          $rootScope.down = false
#
#      $(element).mousemove debounce (e)->
#        if $(".article-container").hasClass("article-container")
#          if $rootScope.down
#            #console.log e.pageX
#            d = e.pageX - pageX
#            if left+d > 0 && left+d < 260
#              $(element).css("transform": "translateX("+(left + d)+"px)" )
#      , 1







