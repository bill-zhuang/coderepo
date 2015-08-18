function loadMainCategory(selectID, isContainNoneOption) {
    var get_url = '/person/finance-category/get-finance-main-category';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof isContainNoneOption != 'undefined' && !isContainNoneOption) {
            $('#' + selectID).empty();
        } else {
            $('#' + selectID).empty().append('<option value="0">无</option>');
        }
        for (var i = 0, len = result.length; i < len; i++) {
            $('#' + selectID).append($('<option>', {
                value: result[i]['fc_id'],
                text: result[i]['fc_name']
            }));
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}