$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/backend-log/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
                    .append($('<td>').text(result.data[i]['content']))
                    .append($('<td>').text(result.data[i]['name']))
                    .append($('<td>').text(result.data[i]['create_time']))
                    .append($('<td>').text(result.data[i]['update_time']))
            );
        }
        if (result.total == 0) {
            $('#tbl tbody').append($('<tr>')
                .append(
                    $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 5)
                )
            );
        }
        //init pagination
        initPagination(result.total_pages, result.current_page);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}