angular.module "articleApp"
  .controller "MakeArticleCtrl", ($scope, $rootScope, $state, $server, $modal, $sce) ->

    $scope.trustSrc = (src)->
      return $sce.trustAsResourceUrl(src)
    $scope.addChapter = ()->
      $scope.book.chapters.push({name: 'Раздел'})
    $scope.addLiterature = (link)->
      $scope.$apply ()->
        $scope.book.literatures.push(link)
    $scope.addText = ()->
      
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'text', key: '', data:''}

    $scope.addCode = ()->
      
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'text', key: '', data:''}

    $scope.addPicture = (link)->
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'picture', link: link, title:'Картинка'}

    $scope.addVideo = (link)->
      
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'video', link: link, title: 'Видео'}

    $scope.addAudio = ()->
      
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'text', key: '', data:''}


    $scope.deleteChapter = (index)->
      $scope.book.chapters.splice(index,1)
    $scope.chooseChapter = (index)->
      $scope.activeChapter = index

    if localStorage.article
      $scope.book = JSON.parse(localStorage.article)
    if !$scope.book
      $scope.book = {
        date: moment().valueOf()
        chapters: []
        literatures: []
        header: {}
        tableOfContents: {}
        metadata: {}
      }
      $scope.addChapter()
    window.s = $scope
    
    
    $scope.activeChapter = 0
    $scope.getTranslate = (text)->
        console.log text
        voc = {
           'code':'Код'
           'text':'Текст'
           'video':'Видео'
           'audio':'Аудио'
           'picture':'Картинка'
           
        }
        return voc[text] || ''


    $scope.createManual = ()->
      $server.createManual $scope.book, (data)->
        console.log data
    updateBook = setInterval ()->
      localStorage.article = JSON.stringify($scope.book)
    ,5000


