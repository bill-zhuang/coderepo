$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var get_url = '/person/grain-recycle-history/ajax-index';
    var get_data = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var success_function = function (result) {
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $tblTbody.append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>').text(result.data.items[i]['happen_date']))
                        .append($('<td>').text(result.data.items[i]['count']))
                        .append($('<td>').text(result.data.items[i]['create_time']))
                        .append($('<td>').text(result.data.items[i]['update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['grhid'], text: '修改'})
                                .click(function () {
                                    modifyGrainRecycleHistory(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['grhid'], text: '删除'})
                                .click(function () {
                                    deleteGrainRecycleHistory(this.id);
                                })
                            )
                        )
                );
            }
            if (result.data.totalItems == 0) {
                $tblTbody.append($('<tr>')
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
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function () {
    window.formGrainRecycleHistory.reset();
    $('#grain_recycle_history_happen_date').val(DateWidget.getCurrentDate()).attr('disabled', false);
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
        var post_data = {
            "params": $('#formGrainRecycleHistory').serializeObject()
        };
        var method = 'post';
        var success_function = function (result) {
            $('#modalGrainRecycleHistory').modal('hide');
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
    }
}));

function modifyGrainRecycleHistory(modify_id) {
    var grhid = modify_id.substr('modify_'.length);
    var get_url = '/person/grain-recycle-history/get-grain-recycle-history';
    var get_data = {
        "params": {
            "grhid": grhid
        }
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            $('#grain_recycle_history_grhid').val(result.data.grhid);
            $('#grain_recycle_history_happen_date').val(result.data.happen_date);
            $('#grain_recycle_history_count').val(result.data.count);
            $('#btn_submit_grain_recycle_history').attr('disabled', false);
            $('#modalGrainRecycleHistory').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function deleteGrainRecycleHistory(delete_id) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var grhid = delete_id.substr('delete_'.length);
        var post_url = '/person/grain-recycle-history/delete-grain-recycle-history';
        var post_data = {
            "params": {
                "grhid": grhid
            }
        };
        var method = 'post';
        var success_function = function (result) {
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
    }
}

function validInput(type) {
    var error_num = 0;
    var happen_date = $('#grain_recycle_history_happen_date').val();
    var count = parseInt($('#grain_recycle_history_count').val());
    if (happen_date == '') {
        error_num = error_num + 1;
        alert(alertMessage.DATE_ERROR)
    } else if (count < 0) {
        error_num = error_num + 1;
        alert(alertMessage.COUNT_ERROR)
    }
    return error_num;
}
