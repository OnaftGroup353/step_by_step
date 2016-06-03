angular.module "articleApp"
  .controller "MakeArticleCtrl", ($scope, $rootScope, $state, $server, $modal, $sce) ->

    $scope.trustSrc = (src)->
      return $sce.trustAsResourceUrl(src)


    $scope.chooseChapter = (index)->
      $scope.activeChapter = index
    $scope.addChapter = ()->
      $scope.book.chapters.push({name: 'Раздел'})
      $scope.chooseChapter($scope.book.chapters.length-1)

    #$scope.addLiterature = (link)->
      #if link.length==0
        #return
      #if link.indexOf("http") == -1
         #link = "http://"+link
       #$scope.$apply ()->
         #$scope.book.literatures.push(link)


    $scope.addLiterature = (link)->
      regtxt = /^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.]*)*\/?$/
      if link.length==0
        return
      if link.match regtxt
        link = link
      else
        link = "**"+link
      $scope.$apply ()->
         $scope.book.literatures.push(link)
      #if link.indexOf("http") == -1
      #  link = "http://"+link
      $scope.$apply ()->
        $scope.book.literatures.push(link)

    $scope.addTags = (tag)->
      $scope.$apply ()->
        $scope.book.tags.push(tag)
    $scope.addText = ()->

      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'text', title: '', data:''}

    $scope.addCode = ()->

      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'text', title: '', data:''}

    $scope.addPicture = (link)->
      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'picture', data: link, title:'Картинка'}

    $scope.addVideo = (link)->
      # https://www.youtube.com/embed/O-aPXj33qKA
      # https://www.youtube.com/watch?v=O-aPXj33qKA
      if link.length == 0
        return
      if link.indexOf("youtube")!=-1 && link.indexOf("embed")==-1
        id = link.split('=')[1]
        link = "https://www.youtube.com/embed/"+id

      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'video', data: link, title: 'Видео'}

    $scope.addAudio = ()->

      ind = $scope.objectLength($scope.book.chapters[$scope.activeChapter])
      $scope.book.chapters[$scope.activeChapter][ind]={type:'audio', title: 'Аудио', data:''}


    $scope.deleteChapter = (index)->
      $scope.book.chapters.splice(index,1)

    $scope.deleteItem = (i, j)->
      j=+j
      console.log(i, j, $scope.book.chapters[i])
      for el, it of $scope.book.chapters[i]
        el = +el
        if el == j
          console.log el, it, 123
          delete $scope.book.chapters[i][el]
          return


    $scope.createEmptyArticle = ()->
      $scope.book = {
        date: moment().valueOf()
        chapters: []
        literatures: []
        tags: []
        header: {}
        tableOfContents: {}
        metadata: {}
      }
      $scope.addChapter()
      localStorage.article = JSON.stringify($scope.book)



    if localStorage.article
      $scope.book = JSON.parse(localStorage.article)
    if !$scope.book
      $scope.book = {
        date: moment().valueOf()
        chapters: []
        literatures: []
        tags: []
        header: {}
        tableOfContents: {}
        metadata: {}
      }
      $scope.addChapter()
    window.s = $scope

    $scope.deteleArticle = ()->
       $scope.createEmptyArticle()

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




    $scope.moveDown = (i) ->
      temp = $scope.book.chapters[i]
      $scope.book.chapters[i] = $scope.book.chapters[i+1]
      $scope.book.chapters[i+1] = temp

    $scope.moveUp = (i) ->
      temp = $scope.book.chapters[i]
      $scope.book.chapters[i] = $scope.book.chapters[i-1]
      $scope.book.chapters[i-1] = temp



    $scope.createManual = ()->
      console.log "data: ", JSON.stringify($scope.book,null,4), $scope.book,
      $server.createManual $scope.book, (data)->
        console.log data
        $scope.deteleArticle()
        $state.go("cabinet")
    updateBook = setInterval ()->
      localStorage.article = JSON.stringify($scope.book)
    ,5000


