$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/dream-history/ajax-index';
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
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['dhid'], text: '修改'})
                                .click(function () {
                                    modifyDreamHistory(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['dhid'], text: '删除'})
                                .click(function () {
                                    deleteDreamHistory(this.id);
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
    window.DreamHistoryForm.reset();
    $('#dream_history_date').val(DateWidget.getCurrentDate()).attr('disabled', false);
    $('#dream_history_count').val(1);
    $('#dream_history_id').val('');
    $('#btn_submit_dream_history').attr('disabled', false);
    $('#DreamHistoryModal').modal('show');
});

$('#DreamHistoryForm').on('submit', (function (event) {
    event.preventDefault();

    var dhid = $('#dream_history_id').val();
    var type = (dhid == '') ? 'add' : 'modify';
    $('#btn_submit_dream_history').attr('disabled', true);
    var post_url = '/person/dream-history/' + type + '-dream-history';
    var post_data = {
        "params": $('#DreamHistoryForm').serializeObject()
    };
    var msg_success = (dhid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
    var msg_error = (dhid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
    var method = 'post';
    var success_function = function (result) {
        $('#DreamHistoryModal').modal('hide');
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

function modifyDreamHistory(modify_id) {
    var dhid = modify_id.substr('delete_'.length);
    var post_url = '/person/dream-history/get-dream-history';
    var post_data = {
        "params": {
            "dhid": dhid
        }
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            $('#dream_history_date').val(result.data.happen_date);
            $('#dream_history_count').val(result.data.count);
            $('#dream_history_id').val(result.data.dhid);
            $('#btn_submit_dream_history').attr('disabled', false);
            $('#DreamHistoryModal').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteDreamHistory(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var dhid = delete_id.substr('delete_'.length);
        var post_url = '/person/dream-history/delete-dream-history';
        var post_data = {
            "params": {
                "dhid": dhid
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