angular.module "articleApp"
  .controller "MakeArticleCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.book = {text:'text', media:'picture', chapters: [],literatures: []}
    window.s = $scope
    $scope.addChapter = ()->
        $scope.book.chapters.push({})
    $scope.addLiterature = ()->
        $scope.book.literatures.push('')
    $scope.addChapter()
    $scope.addLiterature()
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


