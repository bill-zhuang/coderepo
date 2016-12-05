function loadLagouCity(selectID, firstLetter) {
    var getUrl = '/person/lagou-city/get-city-list';
    var getData = {
        "params": {
            'firstLetter': firstLetter
        }
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#' + selectID).empty();
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#' + selectID).append($('<option>', {
                    value: result.data.items[i]['lg_ctid'],
                    text: result.data.items[i]['name']
                }));
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}