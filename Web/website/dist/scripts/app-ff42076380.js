(function(){angular.module("articleApp",["ui.router","ngRoute","ui.bootstrap","ngAnimate","ui.date"]).run(["$rootScope","$state","$stateParams","$timeout",function(t,e,o,a){return t.$state=e,t.$stateParams=o}]).config(["$routeProvider","$locationProvider","$stateProvider","$urlRouterProvider","$httpProvider",function(t,e,o,a,r){return r.defaults.headers.common={},r.defaults.headers.post={},r.defaults.headers.put={},r.defaults.headers.patch={},a.when("","/index"),a.otherwise(function(t,e){return console.log("herte"),e="404"}),o.state("404",{url:"/404",templateUrl:"app/main/404.html"}).state("index",{url:"/index",templateUrl:"app/main/index.html"}).state("admin",{url:"/admin",templateUrl:"app/main/admin.html",controller:"adminCtrl"}).state("cabinet",{url:"/cabinet",templateUrl:"app/main/cabinet.html",controller:"cabinetCtrl"}).state("makeArticle",{url:"/article",templateUrl:"app/main/makeArticle.html",controller:"MakeArticleCtrl"}).state("cabinet.bookInfo",{url:"/book{id}",templateUrl:"app/main/bookInfo.html",controller:"bookInfoCtrl"}).state("cabinet.profile",{url:"/profile",templateUrl:"app/main/profile.html"}).state("admin.profile",{url:"/profile",templateUrl:"app/main/profile.html"})}])}).call(this),function(){angular.module("articleApp").factory("$server",["$state","$modal",function(t,e){var o,a,r,i,n,l,s;for(a={},r=function(t){var e;return e=document.cookie.match(new RegExp("(?:^|; )"+t.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")+"=([^;]*)")),e?decodeURIComponent(e[1]):void 0},s=["login","insertUser","logout","updateUser","getUserInfo"],o=function(t){return a[t]=function(e,o){var a,r;return e=JSON.parse(JSON.stringify(e)),a="api.m-creater.s-host.net",r=$.ajax({url:"http://"+a+"/"+t,method:"POST",data:JSON.stringify(e),dataType:"json"}),r.done(function(t){return o(t)}),r.fail(function(t){return o(t.responseJSON||{error:"empty server response"})})}},i=0,n=s.length;n>i;i++)l=s[i],o(l);return a}])}.call(this),function(){angular.module("articleApp").controller("loginModalCtrl",["$scope","$rootScope","$state","$server","$modal","$modalInstance",function(t,e,o,a,r,i){return t.user={},t.cancel=function(){return i.dismiss("cancel")},t.login=function(){return a.login({email:t.user.login,password:t.user.password},function(e){return console.log(e),e.error?alert(e.error):(t.cancel(),localStorage.token=e.token,console.log(e.scope),"User"===e.scope?o.go("cabinet"):"Moderator"===e.scope||"Administrator"===e.scope?o.go("admin"):void 0)})},window.ulog=function(t){var e;return e=$.ajax({url:"http://ulogin.ru/token.php?token="+t,method:"GET",dataType:"jsonp"}),e.done(function(t){return console.log("data2",JSON.parse(t)),a.login(JSON.parse(t),function(t){return t.error?console.log("Не удалось войти"):(localStorage.token=t.token,o.go("cabinet"))})}),e.fail(function(t){return console.log(t.responseJSON)})}}]).controller("registerModalCtrl",["$scope","$rootScope","$state","$server","$modal","$modalInstance",function(t,e,o,a,r,i){return t.cancel=function(){return i.dismiss("cancel")},t.register=function(){return console.log("register here"),t.user.password!==t.user.password2?alert("Пароли не совпадают"):t.user.password.length<3?alert("Длина пароля не менее 3 символов"):a.insertUser(t.user,function(e){return console.log(e),e.error?void 0:a.login(t.user,function(e){return console.log(e),e.error?alert(e.error):(t.cancel(),localStorage.token=e.token,"User"===e.scope?o.go("cabinet"):"Moderator"===e.scope||"Administrator"===e.scope?o.go("admin"):void 0)})})}}])}.call(this),function(){angular.module("articleApp").controller("MakeArticleCtrl",["$scope","$rootScope","$state","$server","$modal",function(t,e,o,a,r){return t.book={text:"text",media:"picture",chapters:[]},t.addChapter=function(){return t.book.chapters.push({})},t.getTranslate=function(t){var e;return console.log(t),e={code:"Код",text:"Текст",video:"Видео",audio:"Аудио",picture:"Картинка"},e[t]||""}}])}.call(this),function(){angular.module("articleApp").controller("MainController",["$scope","$rootScope","$state","$server","$modal",function(t,e,o,a,r){var i;return console.log("MainController"),i=function(e){return t.instance&&(t.instance.dismiss("cancel"),_.isFunction(e))?e():void 0},e.showModal=function(e,o,a,n){var l;i(n),t.instance=l=r.open(a?{templateUrl:"app/main/modals/"+e+"Modal.html",controller:e+"ModalCtrl",backdrop:"static",keyboard:"false",resolve:{data:o}}:{templateUrl:"app/main/modals/"+e+"Modal.html",controller:e+"ModalCtrl",resolve:{data:o}})},t.enter=function(){return localStorage.token?a.login({token:localStorage.token},function(t){return t.error?e.showModal("login"):(console.log(t),"User"===t.scope?o.go("cabinet"):"Moderator"===t.scope||"Administrator"===t.scope?o.go("admin"):void 0)}):e.showModal("login")},t.logout=function(){return a.logout({token:localStorage.token},function(t){return delete localStorage.token,o.go("index")})},t.formatDate=function(t){return moment(t).format("DD/MM/YYYY")},t.getTranslate=function(t){var e;return console.log(t),e={code:"код",text:"текст"},e[t]||""}}])}.call(this),function(){angular.module("articleApp").controller("cabinetCtrl",["$scope","$rootScope","$state","$server","$modal",function(t,e,o,a,r){return t.user={},a.login({token:localStorage.token},function(e){return console.log(e),e.error?void 0:a.getUserInfo({id:e.id},function(e){return t.user=e})}),console.log(o.current.name,123),t.books=[{id:1,direction:"Компьютерные науки",name:"3 курс Лабораторные работы",modified:1460930831e3},{id:2,direction:"Компьютерные науки",name:"4 курс пособие для курсовых работ",modified:1460910831e3}],t.submitProfile=function(){return delete t.user.banned,delete t.user.scope_name,delete t.user.scope_id,delete t.user.id,t.user.social_network_id=1,t.user.social_network_type=1,a.updateUser(t.user,function(t){return console.log(t)})}}])}.call(this),function(){angular.module("articleApp").controller("bookInfoCtrl",["$scope","$rootScope","$state","$server","$modal","$stateParams",function(t,e,o,a,r,i){var n;return n=i.id,t.book=t.books[n-1]}])}.call(this),function(){angular.module("articleApp").controller("adminCtrl",["$scope","$rootScope","$state","$server","$modal",function(t,e,o,a,r){return a.login({token:localStorage.token},function(e){return console.log(e),e.error?t.logout():void 0})}])}.call(this),function(){angular.module("articleApp").service("webDevTec",function(){var t,e;t=[{title:"AngularJS",url:"https://angularjs.org/",description:"HTML enhanced for web apps!",logo:"angular.png"},{title:"BrowserSync",url:"http://browsersync.io/",description:"Time-saving synchronised browser testing.",logo:"browsersync.png"},{title:"GulpJS",url:"http://gulpjs.com/",description:"The streaming build system.",logo:"gulp.png"},{title:"Jasmine",url:"http://jasmine.github.io/",description:"Behavior-Driven JavaScript.",logo:"jasmine.png"},{title:"Karma",url:"http://karma-runner.github.io/",description:"Spectacular Test Runner for JavaScript.",logo:"karma.png"},{title:"Protractor",url:"https://github.com/angular/protractor",description:"End to end test framework for AngularJS applications built on top of WebDriverJS.",logo:"protractor.png"},{title:"Bootstrap",url:"http://getbootstrap.com/",description:"Bootstrap is the most popular HTML, CSS, and JS framework for developing responsive, mobile first projects on the web.",logo:"bootstrap.png"},{title:"Less",url:"http://lesscss.org/",description:"Less extends the CSS language, adding features that allow variables, mixins, functions and many other techniques.",logo:"less.png"},{title:"CoffeeScript",url:"http://coffeescript.org/",description:"CoffeeScript, 'a little language that compiles into JavaScript'.",logo:"coffeescript.png"}],e=function(){return t},this.getTec=e})}.call(this),function(){angular.module("articleApp").directive("acmeNavbar",function(){var t,e;return t=function(t){var e;e=this,e.relativeDate=t(e.creationDate).fromNow()},e={restrict:"E",templateUrl:"app/components/navbar/navbar.html",scope:{creationDate:"="},controller:t,controllerAs:"vm",bindToController:!0}})}.call(this),function(){angular.module("articleApp").directive("acmeMalarkey",function(){var t,e,o;return t=function(t,e){var o,a,r;r=this,o=function(){return a().then(function(){t.info("Activated Contributors View")})},a=function(){return e.getContributors(10).then(function(t){return r.contributors=t,r.contributors})},r.contributors=[],o()},o=function(t,e,o,a){var r,i;i=void 0,r=malarkey(e[0],{typeSpeed:40,deleteSpeed:40,pauseDelay:800,loop:!0,postfix:" "}),e.addClass("acme-malarkey"),angular.forEach(t.extraValues,function(t){r.type(t).pause()["delete"]()}),i=t.$watch("vm.contributors",function(){angular.forEach(a.contributors,function(t){r.type(t.login).pause()["delete"]()})}),t.$on("$destroy",function(){i()})},e={restrict:"E",scope:{extraValues:"="},template:"&nbsp;",link:o,controller:t,controllerAs:"vm"}})}.call(this),function(){angular.module("articleApp").factory("githubContributor",["$log","$http",function(t,e){var o,a,r;return o="https://api.github.com/repos/Swiip/generator-gulp-angular",a=function(a){var r,i;return r=function(t){return t.data},i=function(e){t.error("XHR Failed for getContributors.\n"+angular.toJson(e.data,!0))},a||(a=30),e.get(o+"/contributors?per_page="+a).then(r)["catch"](i)},r={apiHost:o,getContributors:a}}])}.call(this),angular.module("articleApp").run(["$templateCache",function(t){t.put("app/main/404.html","<div>page not found!</div>"),t.put("app/main/admin.html",'<div class="ui-view content"></div>'),t.put("app/main/bookInfo.html",'<div class="book"><ul><li>ID: {{book.id}}</li><li>Название: {{book.name}}</li><li>Направление: {{book.direction}}</li><li>Авторы: {{book.authors}}</li><li>Дата: {{formatDate(book.modified)}}</li></ul></div>'),t.put("app/main/cabinet.html",'<div class="cabinet"><div class="aside"><p class="title">Мои методички</p><ul><li ng-repeat="book in books" ng-click="$state.go(\'cabinet.bookInfo\', {id: book.id})"><p>Направление: <span class="bold">{{book.direction}}</span></p><p>Название: <span class="bold">{{book.name}}</span></p><p>Дата: <span class="bold">{{formatDate(book.modified)}}</span></p></li></ul></div><div class="ui-view content"><div class="no-select" ng-if="$state.is(\'cabinet\')"><img src="assets/images/empty_book.png" alt=""><p>Вы не выбрали ни одной методички</p></div></div></div>'),t.put("app/main/index.html",'<div class="home"><div class="content"><div class="wrapper"><div class="about"><h1>Онлайн методички</h1><h2>Благодаря нашему сервису вы можете создавать и скачивать методички.</h2><h3><div>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Provident vel dolor facere tempore in sint nisi consequatur ut, recusandae ipsam suscipit sequi voluptate, consequuntur vero molestiae obcaecati sapiente nulla ducimus!</div><div>Rem consectetur ab porro expedita corporis non quisquam optio beatae laudantium atque iusto ipsam amet enim, eaque omnis esse eveniet tenetur consequatur nisi nulla voluptates debitis nihil. Expedita, maiores, commodi!</div></h3><p class="center"><img src="assets/images/yellow_book.png" alt=""></p></div><div class="news"><ul><li><img src="assets/images/018.png" alt=""><p class="title">Heading</p><p class="desription">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore incidunt eligendi laboriosam earum at error, animi repudiandae odit aperiam. Velit deleniti ipsam aliquam a quaerat iste dicta ad consectetur modi.</p><button class="btn-default btn">Посмотреть</button></li><li><img src="assets/images/018.png" alt=""><p class="title">Heading</p><p class="desription">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore incidunt eligendi laboriosam earum at error, animi repudiandae odit aperiam. Velit deleniti ipsam aliquam a quaerat iste dicta ad consectetur modi.</p><button class="btn-default btn">Посмотреть</button></li><li><img src="assets/images/018.png" alt=""><p class="title">Heading</p><p class="desription">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore incidunt eligendi laboriosam earum at error, animi repudiandae odit aperiam. Velit deleniti ipsam aliquam a quaerat iste dicta ad consectetur modi.</p><button class="btn-default btn">Посмотреть</button></li></ul></div></div></div><footer class="main-footer"><div class="wrapper"><p class="copyright">&copy; Онлайн методички - 2016</p></div></footer></div>'),t.put("app/main/makeArticle.html",'<div class="make-article"><div id="toolbar"><ul><li id="add-chapter" ng-click="addChapter()">+</li><li ng-click="textShow=!textShow"><p>Текст</p><ul ng-class="{\'active\': textShow}"><li ng-click="book.text=\'text\'">Текст</li><li ng-click="book.text=\'code\'">Код</li></ul></li><li ng-click="mediaShow=!mediaShow"><p>Мультимедиа</p><ul ng-class="{\'active\': mediaShow}"><li ng-click="book.media=\'picture\'">Картинка</li><li ng-click="book.media=\'video\'">Видео</li><li ng-click="book.media=\'audio\'">аудио</li></ul></li><li>+</li><li>+</li><li>+</li></ul></div><div class="box header"><h2>Заголовок</h2><table><tr><td>Название</td><td><input type="text" ng-model="book.name"></td></tr><tr><td>Дата</td><td><p ng-bind="book.date"></p></td></tr><tr><td>Автор</td><td><p type="text" ng-bind="book.author"></p></td></tr></table></div><div class="box table-of-contents"><h2>Содержание</h2><table><tr><td>Заголовок .......................</td><td>1</td></tr><tr ng-repeat="title in book.chapters"><td>.......................</td><td><input type="text" ng-model="title.page"></td></tr></table></div><div class="box chapter"><h2>Раздел</h2><table><tr><td>Заголовок .......................</td><td>1</td></tr><tr ng-repeat="title in book.chapters"><td>.......................</td><td><input type="text" ng-model="title.page"></td></tr></table></div><button ng-click="addChapter();"></button></div>'),t.put("app/main/profile.html",'<form ng-submit="submitProfile()" class="profile"><table><tr><td><label for="">Фамилия</label></td><td><input type="text" ng-model="user.last_name"></td></tr><tr><td><label for="">Имя</label></td><td><input type="text" ng-model="user.first_name"></td></tr><tr><td><label for="">Отчество</label></td><td><input type="text" ng-model="user.middle_name"></td></tr><tr><td><label for="">Место работы</label></td><td><input type="text" ng-model="user.interest"></td></tr><tr><td><label for="">Должность</label></td><td><input type="text" ng-model="user.position"></td></tr></table><button type="submit" class="green">Сохранить</button></form>'),t.put("app/components/navbar/navbar.html",'<nav class="navbar navbar-static-top navbar-inverse"><div class="container-fluid"><div class="navbar-header"><a class="navbar-brand" href="https://github.com/Swiip/generator-gulp-angular"><span class="glyphicon glyphicon-home"></span> Gulp Angular</a></div><div class="collapse navbar-collapse" id="bs-example-navbar-collapse-6"><ul class="nav navbar-nav"><li class="active"><a ng-href="#">Home</a></li><li><a ng-href="#">About</a></li><li><a ng-href="#">Contact</a></li></ul><ul class="nav navbar-nav navbar-right acme-navbar-text"><li>Application was created {{ vm.relativeDate }}.</li></ul></div></div></nav>'),t.put("app/main/modals/loginModal.html",'<div class="modal-wrapper"><p class="cancel" ng-click="cancel()">&times;</p><div class="modal-head"><h2>Вход</h2><h3>Для работы с методичками необходимо выполнить вход.</h3></div><div class="modal-body"><form ng-submit="login()"><div class="wrap"><label for="email">Электронный адрес:</label><input type="text" id="email" ng-model="user.login"></div><div class="wrap"><label for="password">Пароль:</label><input type="password" id="password" ng-model="user.password"></div><div class="wrap"><button class="green" type="submit">Войти</button></div></form><div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,email;providers=vkontakte,google,facebook;hidden=;redirect_uri=;callback=ulog"></div></div></div>'),t.put("app/main/modals/registerModal.html",'<div class="modal-wrapper"><p class="cancel" ng-click="cancel()">&times;</p><div class="modal-head"><h2>Регистрация</h2><h3>Для работы с методичками необходимо зарегистрироваться.</h3></div><div class="modal-body"><div class="wrap"><label for="email">Электронный адрес:</label><input type="text" id="email" ng-model="user.email"></div><div class="wrap"><label for="password">Пароль:</label><input type="password" id="password" ng-model="user.password"></div><div class="wrap"><label for="password2">Повторите пароль:</label><input type="password" id="password2" ng-model="user.password2"></div><div class="wrap"><button class="green" ng-click="register()">Зарегистрироваться</button></div></div></div>')}]);