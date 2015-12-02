function loadMainCategory(selectID, isContainNoneOption) {
    var get_url = '/person/finance-category/get-finance-main-category';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
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
    callAjaxWithFunction(get_url, get_data, success_function, method);
}