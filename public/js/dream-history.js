$(document).ready(function(){
    ajaxIndex();
});

function ajaxIndex()
{
    var get_url = '/person/dream-history/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function(result){
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
                    .append($('<td>').text(result.data[i]['dh_happen_date']))
                    .append($('<td>').text(result.data[i]['dh_count']))
                    .append($('<td>').text(result.data[i]['dh_create_time']))
                    .append($('<td>').text(result.data[i]['dh_update_time']))
                    .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data[i]['dh_id'], text: '修改'})
                            .click(function(){modifyDreamHistory(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id:'delete_' + result.data[i]['dh_id'], text: '删除'})
                            .click(function(){deleteDreamHistory(this.id);})
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
    window.DreamHistoryForm.reset();
    $('#dream_history_date').val(getCurrentDate());
    $('#dream_history_count').val(1);
    $('#btn_submit_dream_history').attr('disabled', false);
    $('#DreamHistoryModal').modal('show');
});

$('#DreamHistoryForm').on('submit', (function(event){
    event.preventDefault();

    var dh_id = $('#dream_history_id').val();
    var type = (dh_id == '') ? 'add' : 'modify';
    $('#btn_submit_dream_history').attr('disabled', true);
    var post_url = '/person/dream-history/' + type + '-dream-history';
    var post_data = new FormData(this);
    var msg_success = (dh_id == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
    var msg_error = (dh_id == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
    var method = 'post';
    var success_function = function(result){
        $('#DreamHistoryModal').modal('hide');
        if (parseInt(result) != 0) {
            alert(msg_success);
        } else {
            alert(msg_error);
        }
        ajaxIndex();
    };
    callAjaxWithFormAndFunction(post_url, post_data, success_function, method);
}));

function modifyDreamHistory(modify_id)
{
    var dh_id = modify_id.substr('delete_'.length);
    var post_url = '/person/dream-history/get-dream-history';
    var post_data = {
        dh_id: dh_id
    };
    var method = 'get';
    var success_function = function(history_data){
        $('#dream_history_date').val(history_data.dh_happen_date).attr('disabled', true);
        $('#dream_history_count').val(history_data.dh_count);
        $('#dream_history_id').val(history_data.dh_id);
        $('#btn_submit_dream_history').attr('disabled', false);
        $('#DreamHistoryModal').modal('show');
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteDreamHistory(delete_id)
{
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var dh_id = delete_id.substr('delete_'.length);
        var post_url = '/person/dream-history/delete-dream-history';
        var post_data = {
            dh_id: dh_id
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