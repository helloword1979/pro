angular.module('app.site').controller('announceCtrl',
function(httpSvc,popupSvc,$http,$scope,$rootScope,$compile,APP_CONFIG,siteService){
    //获取站点
    $scope.siteId = function (site_index_id) {
        announcementService.getDropSelect(site_index_id).then(function (response) {
            $scope.sharedJson = response.data;
        });
    };
    $scope.siteId();
    //删选收齐
    $scope.toggleAdd = function () {
        if (!$scope.newTodo) {
            $scope.newTodo = {
                state: 'Important'
            };
        } else {
            $scope.newTodo = undefined;
        }
    };
    //公告列表
    var GetAllEmployee = function () {
        var postData = {
            pageIndex: $scope.paginationConf.currentPage,
            pageSize: $scope.paginationConf.itemsPerPage,
            site: $scope.site_index_id,
            group: $scope.group
        };
        announcementService.setSystemNoticeList(postData).then(function (response) {
            $scope.paginationConf.totalItems = response.data.meta.count;
            $scope.list = response.data.data;
        })
    };
    $scope.paginationConf = {
        currentPage: 1,
        itemsPerPage: APP_CONFIG.PAGE_SIZE_DEFAULT
    };
    $scope.$watch('paginationConf.currentPage + paginationConf.itemsPerPage', GetAllEmployee);

    //搜索
    $scope.search = function () {
        GetAllEmployee();
    };


    //获取类型
    $scope.setType = function () {
        announcementService.setAddType().then(function (response) {
            $scope.TypeJson = response.data.data;
        });
    };
    $scope.setType();

    //添加公告
    $scope.add=function(){
        var postData = {
            site:$scope.addData.site,
            type:$scope.addData.type*1,
            title:$scope.addData.title,
            content:$scope.addData.content
        };
        announcementService.postAddNotice(postData).then(function (response) {
            if (response){
                popupSvc.smallBox("success",$rootScope.getWord("success"));
                GetAllEmployee();
            }else {
                popupSvc.smallBox("fail",response.msg);
            }
        })
    };

    $scope.modify=function(id){
        $scope.siteId();
        $scope.setType();

        var postData ={
            id:id
        };
        announcementService.getNoticeNews(postData).then(function (response) {
            console.log(response.data.data.title)
            $scope.site = response.data.site;
            $scope.type = response.data.type;
            $scope.title = response.data.data.title;
            $scope.content = response.data.data.content;
        })
    };
    $scope.modifySubmit=function(){
        var postData = {
            site:$scope.site,
            type:$scope.type,
            title:$scope.title,
            content:$scope.content
        };

        announcementService.putNoticeNews(postData).then(function (response) {
            if (response){
                popupSvc.smallBox("success",$rootScope.getWord("success"));
                GetAllEmployee();
            }else {
                popupSvc.smallBox("fail",response.msg);
            }
        })
    };

    //删除公告
    $scope.delete = function (id) {
        var postData = {
            id:id
        };
        var del = function () {
            announcementService.delSystemNotice(postData).then(function (response) {
                if (response){
                    popupSvc.smallBox("success",$rootScope.getWord("success"));
                    GetAllEmployee();
                }else {
                    popupSvc.smallBox("fail",response.msg);
                }
            })
        };
        popupSvc.smartMessageBox($rootScope.getWord("confirmationOperation"),del);
    }

});

