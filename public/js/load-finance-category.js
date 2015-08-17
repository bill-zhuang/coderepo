function loadMainCategory(selectID) {
    var get_url = '/person/finance-category/get-finance-main-category';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        $('#' + selectID).empty().append('<option value="0">无</option>');
        for (var fc_id in result) {
            $('#' + selectID).append($('<option>', {
                value: fc_id,
                text: result[fc_id]
            }));
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}