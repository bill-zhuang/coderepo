function loadLagouMainCategory(selectID, isContainAllOption) {
    var getUrl = '/person/lagou-category/get-main-category';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            if (typeof isContainAllOption != 'undefined' && !isContainAllOption) {
                $('#' + selectID).empty();
            } else {
                $('#' + selectID).empty().append('<option value="0">全部</option>');
            }
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#' + selectID).append($('<option>', {
                    value: result.data.items[i]['caid'],
                    text: result.data.items[i]['name']
                }));
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function loadLagouSubCategory(selectID, pid, isContainAllOption) {
    var getUrl = '/person/lagou-category/get-sub-category';
    var getData = {
        "params": {
            'pid': pid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            if (typeof isContainAllOption != 'undefined' && !isContainAllOption) {
                $('#' + selectID).empty();
            } else {
                $('#' + selectID).empty().append('<option value="0">全部</option>');
            }
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#' + selectID).append($('<option>', {
                    value: result.data.items[i]['caid'],
                    text: result.data.items[i]['name']
                }));
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}