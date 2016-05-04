function loadMainCategory(selectID, isContainNoneOption) {
    var getUrl = '/person/finance-category/get-finance-main-category';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            if (typeof isContainNoneOption != 'undefined' && !isContainNoneOption) {
                $('#' + selectID).empty();
            } else {
                $('#' + selectID).empty().append('<option value="0">无</option>');
            }
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#' + selectID).append($('<option>', {
                    value: result.data.items[i]['fcid'],
                    text: result.data.items[i]['name']
                }));
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}