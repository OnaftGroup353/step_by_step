angular.module "articleApp"
  .controller "MakeArticleCtrl", ($scope, $rootScope, $state, $server, $modal) ->
    $scope.book = {text:'text', media:'picture', chapters: []}
    $scope.addChapter = ()->
        $scope.book.chapters.push({})
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


