angular.module 'articleApp', ['ui.router', 'ngRoute', 'ui.bootstrap', 'ngAnimate', 'ui.date']
  .run ['$rootScope', '$state', '$stateParams', '$timeout', ($rootScope,   $state,   $stateParams, $timeout) ->

    $rootScope.$state = $state
    $rootScope.$stateParams = $stateParams
  ]
  .config ['$routeProvider', '$locationProvider', '$stateProvider', '$urlRouterProvider',
  ($routeProvider, $locationProvider, $stateProvider, $urlRouterProvider) ->

    $urlRouterProvider.when('','/index')
    $urlRouterProvider.otherwise ($injector, $location) ->
      $location = "404"

    $stateProvider
      .state '404',
        url:'/404',
        templateUrl: "app/main/404.html"
      .state 'index',
        url: '/index',
        templateUrl: "app/main/index.html"

      .state 'admin',
        url: '/admin',
        templateUrl: 'app/main/admin.html',
        controller: 'adminCtrl'

      .state 'cabinet',
        url: '/cabinet',
        templateUrl: 'app/main/cabinet.html',
        controller: 'cabinetCtrl'

      .state 'cabinet.bookInfo',
        url: '/book{id}',
        templateUrl: 'app/main/bookInfo.html',
        controller: 'bookInfoCtrl'

  ]
