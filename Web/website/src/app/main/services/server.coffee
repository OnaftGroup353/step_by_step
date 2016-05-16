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
      'insertUser',
      'logout',
      'updateUser',
      'getUserInfo',
      'createManual',
      'articleSearch',
      'getManuals',
      'getManualById',
      'getManualsByUserId'
    ]

    addMethod = (methodName) ->
      api[methodName] = (data, callback) ->
        data=JSON.parse(JSON.stringify(data))
        data.token = localStorage.getItem "token"
        #domain = 'localhost'
        domain = 'api.m-creater.s-host.net'
        request = $.ajax {
          url: 'http://'+domain+'/'+methodName,
          method: 'POST',
          #contentType: "application/json;charset=utf-8",
          #headers: {
          #  'sessionidcors': localStorage.getItem "token",
          #},
          data: JSON.stringify(data),
          dataType: 'json'

        }

        request.done (data)->
          callback(data)

        request.fail (xhr)->

          callback(xhr.responseJSON || {error:"empty server response"})


    for method in methods
      addMethod method

    return api
