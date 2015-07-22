$(document).ready(function(){
    ajaxIndex();
});

function ajaxIndex()
{
    var get_url = '/person/backend-log/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function(result){
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

/*  --------------------------------------------------------------------------------------------------------  */
function initPagination(total_pages, current_page) {
    $('#pagination').twbsPagination({
        totalPages: total_pages,
        startPage: current_page,
        visiblePages: 7,
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '尾页',
        onPageClick: function (event, page) {
            $('#current_page').val(page);
            ajaxIndex();
        }
    });
}

$('#page_length').on('change', function(){
    $('#current_page').val(1);
    ajaxIndex();
});

$('#btn_search').on('click', function(event){
    event.preventDefault();
    $('#current_page').val(1);
    ajaxIndex();
});