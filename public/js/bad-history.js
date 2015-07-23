$(document).ready(function(){
    ajaxIndex();
});

function ajaxIndex()
{
    var get_url = '/person/bad-history/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function(result){
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
                    .append($('<td>').text(result.data[i]['bh_happen_date']))
                    .append($('<td>').text(result.data[i]['bh_count']))
                    .append($('<td>').text(result.data[i]['bh_create_time']))
                    .append($('<td>').text(result.data[i]['bh_update_time']))
                    .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data[i]['bh_id'], text: '修改'})
                            .click(function(){modifyBadHistory(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id:'delete_' + result.data[i]['bh_id'], text: '删除'})
                            .click(function(){deleteBadHistory(this.id);})
                        )
                    )
            );
        }
        if (result.total == 0) {
            $('#tbl tbody').append($('<tr>')
                .append(
                    $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 6)
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
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function(){
    window.BadHistoryForm.reset();
    $('#bad_history_date').val(getCurrentDate());
    $('#bad_history_count').val(1);
    $('#btn_submit_bad_history').attr('disabled', false);
    $('#BadHistoryModal').modal('show');
});

$('#BadHistoryForm').on('submit', (function(event){
    event.preventDefault();

    var bh_id = $('#bad_history_id').val();
    var type = (bh_id == '') ? 'add' : 'modify';
    $('#btn_submit_bad_history').attr('disabled', true);
    var post_url = '/person/bad-history/' + type + '-bad-history';
    var post_data = new FormData(this);
    var msg_success = (bh_id == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
    var msg_error = (bh_id == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
    var method = 'post';
    var success_function = function(result){
        $('#BadHistoryModal').modal('hide');
        if (parseInt(result) != 0) {
            alert(msg_success);
        } else {
            alert(msg_error);
        }
        ajaxIndex();
    };
    callAjaxWithFormAndFunction(post_url, post_data, success_function, method);
}));

function modifyBadHistory(modify_id)
{
    var bh_id = modify_id.substr('delete_'.length);
    var post_url = '/person/bad-history/get-bad-history';
    var post_data = {
        bh_id: bh_id
    };
    var method = 'get';
    var success_function = function(history_data){
        $('#bad_history_date').val(history_data.bh_happen_date).attr('disabled', true);
        $('#bad_history_count').val(history_data.bh_count);
        $('#bad_history_id').val(history_data.bh_id);
        $('#btn_submit_bad_history').attr('disabled', false);
        $('#BadHistoryModal').modal('show');
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteBadHistory(delete_id)
{
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var bh_id = delete_id.substr('delete_'.length);
        var post_url = '/person/bad-history/delete-bad-history';
        var post_data = {
            bh_id: bh_id
        };
        var method = 'post';
        var success_function = function(result){
            if (parseInt(result) > 0) {
                alert(MESSAGE_DELETE_SUCCESS);
            } else {
                alert(MESSAGE_DELETE_ERROR);
            }
            ajaxIndex();
        };
        callAjaxWithFunction(post_url, post_data, success_function, method);
    }
}