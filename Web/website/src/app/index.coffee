angular.module 'articleApp', ['ui.router', 'ngRoute', 'ui.bootstrap', 'ngAnimate', 'ui.date', 'ngResource','ngSanitize']
  .run ['$rootScope', '$state', '$stateParams', '$timeout', ($rootScope,   $state,   $stateParams, $timeout) ->

    $rootScope.$state = $state
    $rootScope.$stateParams = $stateParams
  ]
  .config ['$routeProvider', '$locationProvider', '$stateProvider', '$urlRouterProvider','$httpProvider'
  ($routeProvider, $locationProvider, $stateProvider, $urlRouterProvider, $httpProvider) ->
    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
    $urlRouterProvider.when('','/index')
    $urlRouterProvider.otherwise ($injector, $location) ->
      console.log "herte"
      $location = "404"
    $locationProvider.html5Mode(true)
    $stateProvider
      .state '404',
        url:'/404',
        templateUrl: "app/main/404.html"
      .state 'index',
        url: '/',
        templateUrl: "app/main/index.html"

      .state 'admin',
        url: '/admin',
        templateUrl: 'app/main/admin.html',
        controller: 'adminCtrl'

      .state 'cabinet',
        url: '/cabinet',
        templateUrl: 'app/main/cabinet.html',
        controller: 'cabinetCtrl'

      .state 'makeArticle',
        url: '/article',
        templateUrl: 'app/main/makeArticle.html',
        controller: 'MakeArticleCtrl'

      .state 'editArticle',
        url: '/edit_article',
        templateUrl: 'app/main/editArticle.html',
        controller: 'EditArticleCtrl'

      .state 'cabinet.bookInfo',
        url: '/book{id}',
        templateUrl: 'app/main/bookInfo.html',
        controller: 'bookInfoCtrl'

      .state 'cabinet.profile',
        url: '/profile',
        templateUrl: 'app/main/profile.html'

      .state 'admin.profile',
        url: '/profile',
        templateUrl: 'app/main/profile.html'

      .state 'university',
        url: '/university',
        templateUrl: 'app/main/university.html'

  ]
