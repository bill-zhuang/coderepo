$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/bad-history/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
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
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
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
    var postUrl = '/person/bad-history/' + type + '-bad-history';
    var postData = {
        "params": $('#BadHistoryForm').serializeObject()
    };
    var method = 'post';
    var successFunc = function (result) {
        $('#BadHistoryModal').modal('hide');
        if (typeof result.data != 'undefined') {
            alert(result.data.message);
        } else {
            alert(result.error.message);
        }
        ajaxIndex();
    };
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}));

function modifyBadHistory(modifyId) {
    var bhid = modifyId.substr('delete_'.length);
    var postUrl = '/person/bad-history/get-bad-history';
    var postData = {
        "params": {
            "bhid": bhid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
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
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}

function deleteBadHistory(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var bhid = deleteId.substr('delete_'.length);
        var postUrl = '/person/bad-history/delete-bad-history';
        var postData = {
            "params": {
                "bhid": bhid
            }
        };
        var method = 'post';
        var successFunc = function (result) {
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
}