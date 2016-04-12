angular.module "articleApp"
  .factory '$server', ($state, $modal) -> #сервис сервера (теперь в отдельном файле, можешь не благодарить)
    api = {}

    getCookie = (name) ->
      matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
      ))
      return if matches then decodeURIComponent(matches[1]) else undefined

    methods = [  
      'login',
      
    ]

    addMethod = (methodName) ->
      api[methodName] = (data, callback) ->
        domain = 'localhost'

        request = $.ajax {
          url: 'http://'+domain+'/'+methodName,
          method: 'POST',
          contentType: "application/json;charset=utf-8",
          headers: {
            'sessionidcors': localStorage.getItem "token"
          },
          data: JSON.stringify(data),
          dataType: 'json'
        }

        request.done (data)->
          callback(data.err, data.data)

        request.fail (xhr)->
         console.log "server error"
          

    for method in methods
      addMethod method

    return api
