$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/grain-recycle-history/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
                    .append($('<td>').text(result.data[i]['happen_date']))
                    .append($('<td>').text(result.data[i]['count']))
                    .append($('<td>').text(result.data[i]['create_time']))
                    .append($('<td>').text(result.data[i]['update_time']))
                    .append($('<td>')
                        .append($('<a>', {href: '#', id: 'modify_' + result.data[i]['grhid'], text: '修改'})
                            .click(function () {
                                modifyGrainRecycleHistory(this.id);
                            })
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id: 'delete_' + result.data[i]['grhid'], text: '删除'})
                            .click(function () {
                                deleteGrainRecycleHistory(this.id);
                            })
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
$('#btn_add').on('click', function () {
    window.formGrainRecycleHistory.reset();
    $('#grain_recycle_history_happen_date').val(getCurrentDate());
    $('#grain_recycle_history_grhid').val('');
    $('#btn_submit_grain_recycle_history').attr('disabled', false);
    $('#modalGrainRecycleHistory').modal('show');
});

$('#formGrainRecycleHistory').on('submit', (function (event) {
    event.preventDefault();

    var grhid = $('#grain_recycle_history_grhid').val();
    var type = (grhid == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if (error_num == 0) {
        $('#btn_submit_grain_recycle_history').attr('disabled', true);
        var post_url = '/person/grain-recycle-history/' + type + '-grain-recycle-history';
        var post_data = new FormData(this);
        var msg_success = (grhid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (grhid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function (result) {
            $('#modalGrainRecycleHistory').modal('hide');
            if (parseInt(result) != 0) {
                alert(msg_success);
            } else {
                alert(msg_error);
            }
            ajaxIndex();
        };
        callAjaxWithFormAndFunction(post_url, post_data, success_function, method);
    }
}));

function modifyGrainRecycleHistory(modify_id) {
    var grhid = modify_id.substr('modify_'.length);
    var get_url = '/person/grain-recycle-history/get-grain-recycle-history';
    var get_data = {
        'grhid': grhid
    };
    var method = 'get';
    var success_function = function (result) {
        $('#grain_recycle_history_grhid').val(result.grhid);
        $('#grain_recycle_history_happen_date').val(result.happen_date);
        $('#grain_recycle_history_count').val(result.count);
        $('#btn_submit_grain_recycle_history').attr('disabled', false);
        $('#modalGrainRecycleHistory').modal('show');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function deleteGrainRecycleHistory(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var grhid = delete_id.substr('delete_'.length);
        var post_url = '/person/grain-recycle-history/delete-grain-recycle-history';
        var post_data = {
            'grhid': grhid
        };
        var method = 'post';
        var success_function = function (result) {
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

function validInput(type) {
    var error_num = 0;
    var happen_date = $('#grain_recycle_history_happen_date').val();
    var count = parseInt($('#grain_recycle_history_count').val());
    if (happen_date == '') {
        error_num = error_num + 1;
        alert(MESSAGE_DATE_ERROR)
    } else if (count < 0) {
        error_num = error_num + 1;
        alert(MESSAGE_COUNT_ERROR)
    }
    return error_num;
}
