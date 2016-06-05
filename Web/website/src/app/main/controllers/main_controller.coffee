angular.module "articleApp"
  .controller "MainController", ($scope, $rootScope, $state, $server, $modal) ->
    console.log "MainController"

    closeModals = (callback)->
      if $scope.instance
        $scope.instance.dismiss "cancel"
        if _.isFunction(callback)
          callback()

    $rootScope.showModal = (name, data, isNotClosable, dismissCallback) ->
      closeModals(dismissCallback)
      if isNotClosable
        $scope.instance = modalInstance = $modal.open
          templateUrl: "app/main/modals/#{name}Modal.html"
          controller: "#{name}ModalCtrl",
          backdrop: 'static',
          keyboard: 'false',
          resolve: {
            data: data,
          }
        return
      else
        $scope.instance = modalInstance = $modal.open
          templateUrl: "app/main/modals/#{name}Modal.html"
          controller: "#{name}ModalCtrl",
          resolve: {
            data: data,
          }
        return
    $scope.enter = ()->
      if !localStorage.token
        $rootScope.showModal('login')
      else
        $server.login {token: localStorage.token}, (data)->
          if data.error
            $rootScope.showModal('login')
          else
            console.log data
            if data.scope == "User"
              $state.go("cabinet")
            else
              if data.scope == "Moderator" || data.scope=="Administrator"
                $state.go("admin")

    $scope.logout = ()->
      $server.logout {token:localStorage.token }, (data)->
        delete localStorage.token
        $state.go("index")

    $scope.formatDate = (timestamp)->
      return moment(+timestamp * 1000).format("DD/MM/YYYY")

    $scope.getTranslate = (text)->
        console.log text
        voc = {
           'code':'код'
           'text':'текст'
        }
        return voc[text] || ''



    $scope.objectLength = (obj)->
      res = 0
      for i of obj
        if (+i)
          res = Math.max(res, +i)
      return res+1


    $scope.articleSearch = ()->
      console.log $scope.search
      if $state.includes("cabinet")

        $state.go("cabinet.search", {name: $scope.search.name})
      else
        $state.go("cabinet.search", {name: $scope.search.name})

    $rootScope.errors = {
      '000': 'null',
      '100': 'Ошибка сервера',
      '101': 'Неправильный запрос',
      '102': 'Не авторизировано',
      '103': 'Нет данных',
      '104': 'Не найдено',
      '105': 'Отказано в доступе',
      '106': 'Ошибка входа через соц. сеть',
      '107': 'Нет доступа',
      '200': 'Неправильный код подтверждения',
      '201': 'Неправильный email',
      '202': 'Пользователь с таким Email уже существует',
      '203': 'Такой пользователь не существует',
      '204': 'Неверный пароль',
      '205': 'Сессия истекла',
      '206': 'Неверный uid'
    }





