if(typeof angular != 'undefined'){
    var myApp = angular.module('primeApp', ['infinite-scroll']);

    // if( $('#groupListlinkNext').length > 0 ) {
        myApp.controller('paginationScrollController', function($attrs, $scope) {
            $scope.loadScroll = function() {
                var  obj = $('#groupListlinkNext');

                if( obj.length > 0 ) {
                    $.directAjaxLink({
                        obj: obj,
                    });
                }
            };
        });
    // }

    var templateObj = $('[ng-controller="listTemplatingCtrl"]');

    if( templateObj.length > 0 ) {
        var listModule = angular.module('primeApp', []);
        var urlList = templateObj.data('url');
        var passkey = templateObj.data('passkey');

        listModule.controller('listTemplatingCtrl', ['$scope', '$http',
            function($scope, $http){
                $http({
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    url: urlList,
                    params: {
                        passkey: passkey,
                        // format: 'jsonp',
                        // json_callback: 'JSON_CALLBACK'
                    }
                }).then(function onSuccess(response) {
                    $scope.searchSomething = '';

                    $scope.getDataList = function() {
                        $scope.listModule = response.data.data.data;
                    };
                    $scope.getDataList();

                    $('.wrapper-ready-template').show().removeClass('hide');
                    $('.progress-section').remove();

                    // console.log('berhasil, list promo bank muncul')
                    // console.log(response)
                }).catch(function onError(response) {
                    // console.log('ugh')
                    $('.wrapper-ready-template').show().removeClass('hide');
                    $('.progress-section').remove();
                });

            }
        ]);
    }
}