(function(){angular.module("articleApp",["ui.router","ngRoute","ui.bootstrap","ngAnimate","ui.date","ngResource","ngSanitize"]).run(["$rootScope","$state","$stateParams","$timeout",function(e,t,a,o){return e.$state=t,e.$stateParams=a}]).config(["$routeProvider","$locationProvider","$stateProvider","$urlRouterProvider","$httpProvider",function(e,t,a,o,i){return i.defaults.headers.common={},i.defaults.headers.post={},i.defaults.headers.put={},i.defaults.headers.patch={},o.when("","/index"),o.otherwise(function(e,t){return console.log("herte"),t="404"}),t.html5Mode(!0),a.state("404",{url:"/404",templateUrl:"app/main/404.html"}).state("index",{url:"/",templateUrl:"app/main/index.html"}).state("admin",{url:"/admin",templateUrl:"app/main/admin.html",controller:"adminCtrl"}).state("cabinet",{url:"/cabinet",templateUrl:"app/main/cabinet.html",controller:"cabinetCtrl"}).state("makeArticle",{url:"/article",templateUrl:"app/main/makeArticle.html",controller:"MakeArticleCtrl"}).state("cabinet.bookInfo",{url:"/book{id}",templateUrl:"app/main/bookInfo.html",controller:"bookInfoCtrl"}).state("cabinet.profile",{url:"/profile",templateUrl:"app/main/profile.html"}).state("admin.profile",{url:"/profile",templateUrl:"app/main/profile.html"}).state("university",{url:"/university",templateUrl:"app/main/university.html"})}])}).call(this),function(){angular.module("articleApp").factory("$server",["$state","$modal",function(e,t){var a,o,i,r,n,l,s;for(o={},i=function(e){var t;return t=document.cookie.match(new RegExp("(?:^|; )"+e.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")+"=([^;]*)")),t?decodeURIComponent(t[1]):void 0},s=["login","insertUser","logout","updateUser","getUserInfo","createManual"],a=function(e){return o[e]=function(t,a){var o,i;return t=JSON.parse(JSON.stringify(t)),t.token=localStorage.getItem("token"),o="api.m-creater.s-host.net",i=$.ajax({url:"http://"+o+"/"+e,method:"POST",data:JSON.stringify(t),dataType:"json"}),i.done(function(e){return a(e)}),i.fail(function(e){return a(e.responseJSON||{error:"empty server response"})})}},r=0,n=s.length;n>r;r++)l=s[r],a(l);return o}])}.call(this),function(){angular.module("articleApp").directive("changeEdit",["$timeout","$window","$rootScope","$server",function(e,t,a,o){return function(e,t,a){return t.on("click",function(t){return t.preventDefault(),t.stopPropagation(),e.$apply(function(){return e.edit=!e.edit})})}}])}.call(this),function(){angular.module("articleApp").directive("addLiterature",["$timeout","$window","$rootScope","$server",function(e,t,a,o){return function(e,t,a){return t.on("keydown",function(a){var o;return o=a.which,13===o?(e.addLiterature(t.val()),t.val("")):void 0})}}]).directive("addTags",["$timeout","$window","$rootScope","$server",function(e,t,a,o){return function(e,t,a){return t.on("keydown",function(a){var o;return o=a.which,13===o?(e.addTags(t.val()),t.val("")):void 0})}}])}.call(this),function(){angular.module("articleApp").controller("loginModalCtrl",["$scope","$rootScope","$state","$server","$modal","$modalInstance",function(e,t,a,o,i,r){return e.user={},e.cancel=function(){return r.dismiss("cancel")},e.login=function(){return o.login({email:e.user.login,password:e.user.password},function(t){return console.log(t),t.error?alert(t.error):(e.cancel(),localStorage.token=t.token,console.log(t.scope),"User"===t.scope?a.go("cabinet"):"Moderator"===t.scope||"Administrator"===t.scope?a.go("admin"):void 0)})},window.ulog=function(e){var t;return t=$.ajax({url:"http://ulogin.ru/token.php?token="+e,method:"GET",dataType:"jsonp"}),t.done(function(e){return console.log("data2",JSON.parse(e)),o.login(JSON.parse(e),function(e){return e.error?console.log("Не удалось войти"):(localStorage.token=e.token,a.go("cabinet"))})}),t.fail(function(e){return console.log(e.responseJSON)})}}]).controller("registerModalCtrl",["$scope","$rootScope","$state","$server","$modal","$modalInstance",function(e,t,a,o,i,r){return e.cancel=function(){return r.dismiss("cancel")},e.register=function(){return console.log("register here"),e.user.password!==e.user.password2?alert("Пароли не совпадают"):e.user.password.length<3?alert("Длина пароля не менее 3 символов"):o.insertUser(e.user,function(t){return console.log(t),t.error?void 0:o.login(e.user,function(t){return console.log(t),t.error?alert(t.error):(e.cancel(),localStorage.token=t.token,"User"===t.scope?a.go("cabinet"):"Moderator"===t.scope||"Administrator"===t.scope?a.go("admin"):void 0)})})}}])}.call(this),function(){angular.module("articleApp").controller("MakeArticleCtrl",["$scope","$rootScope","$state","$server","$modal","$sce",function(e,t,a,o,i,r){var n;return e.trustSrc=function(e){return r.trustAsResourceUrl(e)},e.chooseChapter=function(t){return e.activeChapter=t},e.addChapter=function(){return e.book.chapters.push({name:"Раздел"}),e.chooseChapter(e.book.chapters.length-1)},e.addLiterature=function(t){return 0!==t.length?(-1===t.indexOf("http")&&(t="http://"+t),e.$apply(function(){return e.book.literatures.push(t)})):void 0},e.addTags=function(t){return e.$apply(function(){return e.book.tags.push(t)})},e.addText=function(){var t;return t=e.objectLength(e.book.chapters[e.activeChapter]),e.book.chapters[e.activeChapter][t]={type:"text",title:"",data:""}},e.addCode=function(){var t;return t=e.objectLength(e.book.chapters[e.activeChapter]),e.book.chapters[e.activeChapter][t]={type:"text",title:"",data:""}},e.addPicture=function(t){var a;return a=e.objectLength(e.book.chapters[e.activeChapter]),e.book.chapters[e.activeChapter][a]={type:"picture",data:t,title:"Картинка"}},e.addVideo=function(t){var a,o;if(0!==t.length)return-1!==t.indexOf("youtube")&&-1===t.indexOf("embed")&&(a=t.split("=")[1],t="https://www.youtube.com/embed/"+a),o=e.objectLength(e.book.chapters[e.activeChapter]),e.book.chapters[e.activeChapter][o]={type:"video",data:t,title:"Видео"}},e.addAudio=function(){var t;return t=e.objectLength(e.book.chapters[e.activeChapter]),e.book.chapters[e.activeChapter][t]={type:"audio",title:"Аудио",data:""}},e.deleteChapter=function(t){return e.book.chapters.splice(t,1)},localStorage.article&&(e.book=JSON.parse(localStorage.article)),e.book||(e.book={date:moment().valueOf(),chapters:[],literatures:[],tags:[],header:{},tableOfContents:{},metadata:{}},e.addChapter()),window.s=e,e.activeChapter=0,e.getTranslate=function(e){var t;return console.log(e),t={code:"Код",text:"Текст",video:"Видео",audio:"Аудио",picture:"Картинка"},t[e]||""},e.moveDown=function(t){var a;return a=e.book.chapters[t],e.book.chapters[t]=e.book.chapters[t+1],e.book.chapters[t+1]=a},e.moveUp=function(t){var a;return a=e.book.chapters[t],e.book.chapters[t]=e.book.chapters[t-1],e.book.chapters[t-1]=a},e.createManual=function(){return console.log("data: ",JSON.stringify(e.book,null,4),e.book,o.createManual(e.book,function(e){return console.log(e)}))},n=setInterval(function(){return localStorage.article=JSON.stringify(e.book)},5e3)}])}.call(this),function(){angular.module("articleApp").controller("MainController",["$scope","$rootScope","$state","$server","$modal",function(e,t,a,o,i){var r;return console.log("MainController"),r=function(t){return e.instance&&(e.instance.dismiss("cancel"),_.isFunction(t))?t():void 0},t.showModal=function(t,a,o,n){var l;r(n),e.instance=l=i.open(o?{templateUrl:"app/main/modals/"+t+"Modal.html",controller:t+"ModalCtrl",backdrop:"static",keyboard:"false",resolve:{data:a}}:{templateUrl:"app/main/modals/"+t+"Modal.html",controller:t+"ModalCtrl",resolve:{data:a}})},e.enter=function(){return localStorage.token?o.login({token:localStorage.token},function(e){return e.error?t.showModal("login"):(console.log(e),"User"===e.scope?a.go("cabinet"):"Moderator"===e.scope||"Administrator"===e.scope?a.go("admin"):void 0)}):t.showModal("login")},e.logout=function(){return o.logout({token:localStorage.token},function(e){return delete localStorage.token,a.go("index")})},e.formatDate=function(e){return moment(e).format("DD/MM/YYYY")},e.getTranslate=function(e){var t;return console.log(e),t={code:"код",text:"текст"},t[e]||""},e.objectLength=function(e){var t,a;a=0;for(t in e)a++;return a}}])}.call(this),function(){angular.module("articleApp").controller("cabinetCtrl",["$scope","$rootScope","$state","$server","$modal",function(e,t,a,o,i){return e.user={},o.login({token:localStorage.token},function(t){return console.log(t),t.error?void 0:o.getUserInfo({id:t.id},function(t){return e.user=t})}),console.log(a.current.name,123),e.books=[{id:1,direction:"Компьютерные науки",name:"3 курс Лабораторные работы",modified:1460930831e3},{id:2,direction:"Компьютерные науки",name:"4 курс пособие для курсовых работ",modified:1460910831e3}],e.submitProfile=function(){return delete e.user.banned,delete e.user.scope_name,delete e.user.scope_id,delete e.user.id,e.user.social_network_id=1,e.user.social_network_type=1,o.updateUser(e.user,function(e){return console.log(e)})}}])}.call(this),function(){angular.module("articleApp").controller("bookInfoCtrl",["$scope","$rootScope","$state","$server","$modal","$stateParams",function(e,t,a,o,i,r){var n;return n=r.id,e.book=e.books[n-1]}])}.call(this),function(){angular.module("articleApp").controller("adminCtrl",["$scope","$rootScope","$state","$server","$modal",function(e,t,a,o,i){return o.login({token:localStorage.token},function(t){return console.log(t),t.error?e.logout():void 0})}])}.call(this),function(){angular.module("articleApp").service("webDevTec",function(){var e,t;e=[{title:"AngularJS",url:"https://angularjs.org/",description:"HTML enhanced for web apps!",logo:"angular.png"},{title:"BrowserSync",url:"http://browsersync.io/",description:"Time-saving synchronised browser testing.",logo:"browsersync.png"},{title:"GulpJS",url:"http://gulpjs.com/",description:"The streaming build system.",logo:"gulp.png"},{title:"Jasmine",url:"http://jasmine.github.io/",description:"Behavior-Driven JavaScript.",logo:"jasmine.png"},{title:"Karma",url:"http://karma-runner.github.io/",description:"Spectacular Test Runner for JavaScript.",logo:"karma.png"},{title:"Protractor",url:"https://github.com/angular/protractor",description:"End to end test framework for AngularJS applications built on top of WebDriverJS.",logo:"protractor.png"},{title:"Bootstrap",url:"http://getbootstrap.com/",description:"Bootstrap is the most popular HTML, CSS, and JS framework for developing responsive, mobile first projects on the web.",logo:"bootstrap.png"},{title:"Less",url:"http://lesscss.org/",description:"Less extends the CSS language, adding features that allow variables, mixins, functions and many other techniques.",logo:"less.png"},{title:"CoffeeScript",url:"http://coffeescript.org/",description:"CoffeeScript, 'a little language that compiles into JavaScript'.",logo:"coffeescript.png"}],t=function(){return e},this.getTec=t})}.call(this),function(){angular.module("articleApp").directive("acmeNavbar",function(){var e,t;return e=function(e){var t;t=this,t.relativeDate=e(t.creationDate).fromNow()},t={restrict:"E",templateUrl:"app/components/navbar/navbar.html",scope:{creationDate:"="},controller:e,controllerAs:"vm",bindToController:!0}})}.call(this),function(){angular.module("articleApp").directive("acmeMalarkey",function(){var e,t,a;return e=function(e,t){var a,o,i;i=this,a=function(){return o().then(function(){e.info("Activated Contributors View")})},o=function(){return t.getContributors(10).then(function(e){return i.contributors=e,i.contributors})},i.contributors=[],a()},a=function(e,t,a,o){var i,r;r=void 0,i=malarkey(t[0],{typeSpeed:40,deleteSpeed:40,pauseDelay:800,loop:!0,postfix:" "}),t.addClass("acme-malarkey"),angular.forEach(e.extraValues,function(e){i.type(e).pause()["delete"]()}),r=e.$watch("vm.contributors",function(){angular.forEach(o.contributors,function(e){i.type(e.login).pause()["delete"]()})}),e.$on("$destroy",function(){r()})},t={restrict:"E",scope:{extraValues:"="},template:"&nbsp;",link:a,controller:e,controllerAs:"vm"}})}.call(this),function(){angular.module("articleApp").factory("githubContributor",["$log","$http",function(e,t){var a,o,i;return a="https://api.github.com/repos/Swiip/generator-gulp-angular",o=function(o){var i,r;return i=function(e){return e.data},r=function(t){e.error("XHR Failed for getContributors.\n"+angular.toJson(t.data,!0))},o||(o=30),t.get(a+"/contributors?per_page="+o).then(i)["catch"](r)},i={apiHost:a,getContributors:o}}])}.call(this),angular.module("articleApp").run(["$templateCache",function(e){e.put("app/main/404.html",'<div class="errpage"><img src="assets/images/404.png" style="width: 800px; height: 600px;" alt="404 ! Page not Found!"></div>'),e.put("app/main/admin.html",'<div class="ui-view content"></div>'),e.put("app/main/bookInfo.html",'<div class="book"><ul><li>ID: {{book.id}}</li><li>Название: {{book.name}}</li><li>Направление: {{book.direction}}</li><li>Авторы: {{book.authors}}</li><li>Дата: {{formatDate(book.modified)}}</li></ul></div>'),e.put("app/main/cabinet.html",'<div class="cabinet"><div class="aside"><p class="title">Мои методички</p><ul><li ng-repeat="book in books" ng-click="$state.go(\'cabinet.bookInfo\', {id: book.id})"><p>Направление: <span class="bold">{{book.direction}}</span></p><p>Название: <span class="bold">{{book.name}}</span></p><p>Дата: <span class="bold">{{formatDate(book.modified)}}</span></p></li></ul></div><div class="ui-view content"><div class="no-select" ng-if="$state.is(\'cabinet\')"><img src="assets/images/empty_book.png" alt=""><p>Вы не выбрали ни одной методички</p></div></div></div>'),e.put("app/main/index.html",'<div class="home"><div class="content"><div class="wrapper"><div id="myCarousel" class="carousel slide" data-ride="carousel"><ol class="carousel-indicators"><li data-target="#myCarousel" data-slide-to="0" class="active"></li><li data-target="#myCarousel" data-slide-to="1"></li><li data-target="#myCarousel" data-slide-to="2"></li></ol><div class="carousel-inner"><div class="item active"><img src="assets/images/1.jpg" alt="First slide"><div class="container"><div class="carousel-caption"><h1>Example headline.</h1><p>Note: If you\'re viewing this page via a URL, the "next" and "previous" Glyphicon buttons on the left and right might not load/display properly due to web browser security rules.</p></div></div></div><div class="item"><img src="assets/images/2.jpg" alt="Second slide"><div class="container"><div class="carousel-caption"><h1>Another example headline.</h1><p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p></div></div></div><div class="item"><img src="assets/images/3.jpg" alt="Third slide"><div class="container"><div class="carousel-caption"><h1>One more for good measure.</h1><p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p></div></div></div></div><a class="left carousel-control" href="#myCarousel" data-slide="prev"><span>++</span></a> <a class="right carousel-control" href="#myCarousel" data-slide="next"><span>++</span></a></div><button class="green" ng-click="$state.go(\'makeArticle\')">Создать методичку</button><div class="about featurtte-heading row featurtte"><div class="col-md-7"><h1>Онлайн методички</h1><h3>Благодаря нашему сервису вы можете создавать и скачивать методички.</h3>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Provident vel dolor facere tempore in sint nisi consequatur ut, recusandae ipsam suscipit sequi voluptate, consequuntur vero molestiae obcaecati sapiente nulla ducimus! Rem consectetur ab porro expedita corporis non quisquam opti o beatae laudantium atque iusto ipsam amet enim, eaque omnis esse eveniet tenetur consequatur nisi nulla voluptates debitis nihil. Expedita, maiores, commodi!</div><p class="center col-md-5"><img <img="" class="featurette-image img-responsive" src="assets/images/yellow_book.png" alt=""></p></div><div class="news row"><div class="col-lg-4" align="center"><img src="assets/images/018.png" style="width: 120px; height: 120px;" class="img-circle" data-src="holder.js/140x140" alt="120x120"><h2>Heading</h2><p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna.</p><p><a class="btn btn-default" href="#" role="button">View details »</a></p></div><div class="col-lg-4" align="center"><img src="assets/images/018.png" style="width: 120px; height: 120px;" class="img-circle" data-src="holder.js/140x140" alt="120x120"><h2>Heading</h2><p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p><p><a class="btn btn-default" href="#" role="button">View details »</a></p></div><div class="col-lg-4" align="center"><img src="assets/images/018.png" style="width: 120px; height: 120px;" class="img-circle" data-src="holder.js/120x120" alt="120x120"><h2>Heading</h2><p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p><p><a class="btn btn-default" href="#" role="button">View details »</a></p></div></div></div></div></div>'),e.put("app/main/makeArticle.html",'<div class="make-article"><div id="toolbar"><ul><li id="add-chapter" ng-click="addChapter()">+</li><li ng-click="textShow=!textShow;mediaShow=false"><p>Текст</p><ul ng-class="{\'active\': textShow}"><li ng-click="book.text=\'text\';addText()">Текст</li><li ng-click="book.text=\'code\';addCode()">Код</li></ul></li><li ng-click="mediaShow=!mediaShow;textShow=false;"><p>Мультимедиа</p><ul ng-class="{\'active\': mediaShow}"><li ng-click="book.media=\'picture\';picturePick=true;">Картинка</li><li ng-click="book.media=\'video\';videoPick=true;">Видео</li><li ng-click="book.media=\'audio\';addAudio()">аудио</li></ul></li><li>+</li><li>+</li><li>+</li></ul></div><div class="box header"><div class="edit" ng-click="editHeader = !editHeader" ng-class="{\'active\': editHeader}"></div><h2>Заголовок</h2><table><tr><td>Название</td><td><input ng-show="editHeader" type="text" ng-model="book.header.name"><p ng-show="!editHeader">{{book.header.name}}</p></td></tr><tr><td>Дата</td><td>{{formatDate(book.header.date)}}</td></tr><tr><td>Автор</td><td><p type="text" ng-bind="book.header.author"></p></td></tr></table></div><div class="box table-of-contents"><h2>Содержание</h2><table><tr><td>Заголовок</td><td>1</td></tr><tr ng-repeat="chapter in book.chapters track by $index"><td>{{chapter.name}}</td><td>{{$index+2}}</td></tr></table></div><div class="box chapter" ng-repeat="chapter in book.chapters track by $index" ng-init="edit=true" ng-click="chooseChapter($index); edit=true" ng-class="{\'active\':$index==activeChapter}"><div class="edit" change-edit="" ng-class="{\'active\': edit}"></div><div class="close" ng-click="deleteChapter($index)"></div><div class="controls" ng-show="book.chapters.length>1"><span class="glyphicon glyphicon-arrow-down" ng-show="!$last" ng-click="moveDown($index)"></span> <span class="glyphicon glyphicon-arrow-up" ng-show="!$first" ng-click="moveUp($index)"></span></div><h2 ng-show="!edit">{{chapter.name}}</h2><input ng-show="edit" type="text" class="chapter-title" ng-model="chapter.name"><table class="w100"><tr ng-repeat="pr in chapter"><td ng-if="pr.type==\'text\'" colspan="2"><input ng-show="edit" class="w100" style="margin-bottom: 20px;" placeholder="Введите зоголовок (можно оставить пустым)" type="text" ng-model="pr.title"><p ng-if="!edit" class="center">{{pr.title}}</p><textarea class="w100" ng-show="edit" type="text" placeholder="Введите текст" ng-model="pr.data"></textarea><p ng-show="!edit" class="center">{{pr.data}}</p></td><td ng-if="pr.type==\'picture\'"><input ng-show="edit" type="text" ng-model="pr.title"><p ng-show="!edit">{{pr.title}}</p></td><td ng-if="pr.type==\'picture\'"><img class="image-box pull-right" ng-src="{{pr.data}}"></td><td ng-if="pr.type==\'video\'"><input ng-show="edit" type="text" ng-model="pr.title"><p ng-show="!edit">{{pr.title}}</p></td><td ng-if="pr.type==\'video\'"><iframe class="pull-right" width="200" height="160" src="{{trustSrc(pr.data)}}" frameborder="0" allowfullscreen=""></iframe></td></tr></table></div><div class="box table-of-contents"><h2>Список литературы</h2><input class="w100" ng-model="lit" add-literature="" placeholder="Введите ссылку"> <a style="display: block;" href="{{link}}" ng-repeat="link in book.literatures track by $index">{{link}}</a></div><div class="box table-of-contents"><h2>Список Тегов</h2><input class="w100" ng-model="tag2" add-tags="" placeholder="Введите тег"><p style="display: block;" ng-repeat="tag in book.tags track by $index">{{tag}}</p></div><div class="link-picker" ng-show="picturePick"><div class="close" ng-click="picturePick=false;"></div><p>Введите ссылку фотографии</p><input type="text" ng-model="link"> <button class="green" ng-click="addPicture(link);picturePick=false">Добавить</button></div><div class="link-picker" ng-show="videoPick"><div class="close" ng-click="videoPick=false;"></div><p>Введите ссылку Видео</p><input type="text" ng-model="link"> <button class="green" ng-click="addVideo(link);videoPick=false">Добавить</button></div><button class="green" ng-click="createManual()">Создать</button></div>'),e.put("app/main/profile.html",'<form ng-submit="submitProfile()" class="profile"><table><tr><td><label for="">Фамилия</label></td><td><input type="text" ng-model="user.last_name"></td></tr><tr><td><label for="">Имя</label></td><td><input type="text" ng-model="user.first_name"></td></tr><tr><td><label for="">Отчество</label></td><td><input type="text" ng-model="user.middle_name"></td></tr><tr><td><label for="">Место работы</label></td><td><input type="text" ng-model="user.interest"></td></tr><tr><td><label for="">Должность</label></td><td><input type="text" ng-model="user.position"></td></tr></table><button type="submit" class="green">Сохранить</button></form>'),e.put("app/main/university.html",'<div class="inuv">UNIVERSE</div>'),e.put("app/components/navbar/navbar.html",'<nav class="navbar navbar-static-top navbar-inverse"><div class="container-fluid"><div class="navbar-header"><a class="navbar-brand" href="https://github.com/Swiip/generator-gulp-angular"><span class="glyphicon glyphicon-home"></span> Gulp Angular</a></div><div class="collapse navbar-collapse" id="bs-example-navbar-collapse-6"><ul class="nav navbar-nav"><li class="active"><a ng-href="#">Home</a></li><li><a ng-href="#">About</a></li><li><a ng-href="#">Contact</a></li></ul><ul class="nav navbar-nav navbar-right acme-navbar-text"><li>Application was created {{ vm.relativeDate }}.</li></ul></div></div></nav>'),e.put("app/main/modals/loginModal.html",'<div class="modal-wrapper"><p class="cancel" ng-click="cancel()">&times;</p><div class="modal-head"><h2>Вход</h2><h3>Для работы с методичками необходимо выполнить вход.</h3></div><div class="modal-body"><form ng-submit="login()"><div class="wrap"><label for="email">Электронный адрес:</label><input type="text" id="email" ng-model="user.login"></div><div class="wrap"><label for="password">Пароль:</label><input type="password" id="password" ng-model="user.password"></div><div class="wrap"><button class="green" type="submit">Войти</button></div></form><div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,email;providers=vkontakte,google,facebook;hidden=;redirect_uri=;callback=ulog"></div></div></div>'),e.put("app/main/modals/registerModal.html",'<div class="modal-wrapper"><p class="cancel" ng-click="cancel()">&times;</p><div class="modal-head"><h2>Регистрация</h2><h3>Для работы с методичками необходимо зарегистрироваться.</h3></div><div class="modal-body"><div class="wrap"><label for="email">Электронный адрес:</label><input type="text" id="email" ng-model="user.email"></div><div class="wrap"><label for="password">Пароль:</label><input type="password" id="password" ng-model="user.password"></div><div class="wrap"><label for="password2">Повторите пароль:</label><input type="password" id="password2" ng-model="user.password2"></div><div class="wrap"><button class="green" ng-click="register()">Зарегистрироваться</button></div></div></div>')}]);