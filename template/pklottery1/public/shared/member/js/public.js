/*加载动画*/
function Loading() {
    var createDiv = document.createElement("div");
    if (cdnUrl == undefined) {
        var cdnUrl = "/template";
    }
    createDiv.id = "mask";
    createDiv.style.position = 'fixed';
    createDiv.style.backgroundColor = 'black';
    createDiv.style.zIndex = '1002';
    createDiv.style.opacity = '0.5';
    createDiv.style.display = 'flex';
    createDiv.style.flex = 'fixed';
    createDiv.style.flexDirection = 'row';
    createDiv.style.justifyContent = 'center';
    createDiv.style.alignItems = 'center';
    createDiv.style.width = '100%';
    createDiv.style.top = '0';
    createDiv.style.left = '0';
    createDiv.style.height = document.documentElement.clientHeight + 'px';
    var bigImg = document.createElement("img");		//创建一个img元素
    bigImg.src = cdnUrl + "/wap/style/images/loading.gif";   //给img元素的src属性赋值
    bigImg.style.margin = '0 auto';
    bigImg.style.display = 'block';
    createDiv.appendChild(bigImg);
    document.body.appendChild(createDiv);
}

/*关闭加载动画*/
function LoadingClose(time) {
    if (!time) {
        time = 500;
    } else {
        time = time * 1000;
    }
    setTimeout(function () {
        $('#mask').remove();
    }, time);
    return;
}
/*********暂无数据展示*********/
function ShowNoData(url, mes) {
    if (cdnUrl == undefined) {
        var cdnUrl = "/template";
    }
    var divll = document.createElement('div');
    divll.setAttribute('class', 'nodate');
    divll.style.color='black';
    var showimg = document.createElement('img');
    if (!url) {
        url = '/wap/style/images/empty@2x.png';
    }
    showimg.src = cdnUrl + url;
    var showp = document.createElement('p');
    if (!mes) {
        mes = '暂无数据';
    }
    showp.innerHTML = mes;
    divll.appendChild(showimg);
    divll.appendChild(showp);
    return divll;
}
//日期处理
//时间戳转日期
function strToDate(data) {
    var nowdate, Y, M, D, h, m, s, data1, data2;
    var datatime;
    if (data === 0 || data === -1) {
        nowdate = new Date();
        nowdate.setDate(nowdate.getDate() + parseInt(data));
    } else {
        nowdate = new Date(parseInt(data) * 1000);//时间戳转换为日期格式*1000
    }
    // console.log(nowdate);
    Y = nowdate.getFullYear() + '-';
    M = (nowdate.getMonth() + 1 < 10 ? '0' + (nowdate.getMonth() + 1) : nowdate.getMonth() + 1) + '-';
    D = nowdate.getDate() + ' ';
    h = nowdate.getHours() + ':';
    m = nowdate.getMinutes() + ':';
    s = nowdate.getSeconds();
    data1 = Y + M + D;
    data2 = h + m + s;
    datatime = [data1, data2];
    if (data === 0 || data === -1) {
        return data1 + data2;
    } else {
        return datatime;
    }

}

//日期转时间戳
function DateToStr(data) {
    var datatime, time1;
    if (data == 0 || data == -1 || data == null || data == -7) {
        datatime = new Date();
        datatime.setHours(0);
        datatime.setMinutes(0);
        datatime.setSeconds(0);
        datatime.setMilliseconds(0);
    } else {
        datatime = new Date(data.replace(/-/g, '/'));// 有三种方式获取，在后面会讲到三种方式的区别
    }
    if (data == -1) {
        time1 = (Date.parse(datatime) / 1000) - 24 * 3600;
    } else if (data == -7) {
        time1 = (Date.parse(datatime) / 1000) - 24 * 3600 * 7;
    } else {
        time1 = Date.parse(datatime) / 1000;
    }
    return time1;
}

//分页
function pageList(metalist, links) {
    console.log(metalist);
    var totalNum = metalist.count;
    var totalPage = metalist.page_count;
    var current_page = metalist.current_page;
    // var page_size = metalist.page_size;
    var htmldata = '<div class="one-foot"><p class="fl">共' + totalNum + '条，共计' + totalPage + '页</p><div class="fr">' +
        '<span class="iconfont icon-back"></span>';
    for (var i = 1; i <= totalPage; i++) {
        if (i == current_page) {
            htmldata += '<span class="one-foot-circle page-active">' + i + '</span>';
        } else {
            htmldata += '<span class="one-foot-circle">' + i + '</span>';
        }
    }
    htmldata += '<span class="iconfont icon-forward"></span>';
    htmldata += '<p>跳转至<select name="select"  class="xla_k ">';

    for (var j = 1; j <= totalPage; j++) {
        if (j == current_page) {
            htmldata += '<option value="' + j + '" selected>' + j + '</option>';
        } else {
            htmldata += '<option value="' + j + '" >' + j + '</option>';
        }

    }
    htmldata += '</select><span>页</span></span></div></div>';
    return htmldata;
}

//分页需要调用的事件
function GetPageData(FuncName, metadata) {
    $('.one-foot .fr').on('click', '.one-foot-circle', function () {
        var ind = $(this).index();
        $(this).addClass('page-active').siblings().removeClass('page-active');
        var page = Number($(this).text());
        FuncName(page);
    });
    $('.xla_k').change(function () {
        var page = $(this).val();
        FuncName(page);
    });
    $('.icon-back').click(function () {
        var page = $(' .xla_k option:selected').val();
        var getpage = Number(page) - 1;
        if (getpage < 1) {
            alert('没有更多内容');
        } else {
            FuncName(getpage);
        }
    });
    $(' .icon-forward').click(function () {
        var page = $('.xla_k option:selected').val();
        var getpage = Number(page) + 1;
        console.log(getpage)
        if (page > (metadata - 1)) {
            alert('没有更多内容');
        } else {
            FuncName(getpage);
        }
    });
}
