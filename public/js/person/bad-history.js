$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/bad-history/ajax-index';
    var get_data = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var success_function = function (result) {
        $('#tbl tbody').empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#tbl tbody').append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>').text(result.data.items[i]['happen_date']))
                        .append($('<td>').text(result.data.items[i]['count']))
                        .append($('<td>').text(result.data.items[i]['create_time']))
                        .append($('<td>').text(result.data.items[i]['update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['bhid'], text: '修改'})
                                .click(function () {
                                    modifyBadHistory(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['bhid'], text: '删除'})
                                .click(function () {
                                    deleteBadHistory(this.id);
                                })
                            )
                        )
                );
            }
            if (result.data.totalItems == 0) {
                $('#tbl tbody').append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 6)
                    )
                );
            }
            //init pagination
            initPagination(result.data.totalPages, result.data.pageIndex);
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function () {
    window.BadHistoryForm.reset();
    $('#bad_history_date').val(DateWidget.getCurrentDate()).attr('disabled', false);
    $('#bad_history_count').val(1);
    $('#bad_history_id').val('');
    $('#btn_submit_bad_history').attr('disabled', false);
    $('#BadHistoryModal').modal('show');
});

$('#BadHistoryForm').on('submit', (function (event) {
    event.preventDefault();

    var bhid = $('#bad_history_id').val();
    var type = (bhid == '') ? 'add' : 'modify';
    $('#btn_submit_bad_history').attr('disabled', true);
    var post_url = '/person/bad-history/' + type + '-bad-history';
    var post_data = {
        "params": $('#BadHistoryForm').serializeObject()
    };
    var msg_success = (bhid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
    var msg_error = (bhid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
    var method = 'post';
    var success_function = function (result) {
        $('#BadHistoryModal').modal('hide');
        if (typeof result.data != 'undefined') {
            if (parseInt(result.data.affectedRows) != 0) {
                alert(msg_success);
            } else {
                alert(msg_error);
            }
        } else {
            alert(result.error.message);
        }
        ajaxIndex();
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}));

function modifyBadHistory(modify_id) {
    var bhid = modify_id.substr('delete_'.length);
    var post_url = '/person/bad-history/get-bad-history';
    var post_data = {
        "params": {
            "bhid": bhid
        }
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            $('#bad_history_date').val(result.data.happen_date).attr('disabled', true);
            $('#bad_history_count').val(result.data.count);
            $('#bad_history_id').val(result.data.bhid);
            $('#btn_submit_bad_history').attr('disabled', false);
            $('#BadHistoryModal').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteBadHistory(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var bhid = delete_id.substr('delete_'.length);
        var post_url = '/person/bad-history/delete-bad-history';
        var post_data = {
            "params": {
                "bhid": bhid
            }
        };
        var method = 'post';
        var success_function = function (result) {
            if (typeof result.data != 'undefined') {
                if (parseInt(result.data.affectedRows) != 0) {
                    alert(MESSAGE_DELETE_SUCCESS);
                } else {
                    alert(MESSAGE_DELETE_ERROR);
                }
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        callAjaxWithFunction(post_url, post_data, success_function, method);
    }
}