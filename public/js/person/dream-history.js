$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/dream-history/ajax-index';
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
    var postUrl = '/person/dream-history/' + type + '-dream-history';
    var postData = {
        "params": $('#DreamHistoryForm').serializeObject()
    };
    var method = 'post';
    var successFunc = function (result) {
        $('#DreamHistoryModal').modal('hide');
        if (typeof result.data != 'undefined') {
            alert(result.data.message);
        } else {
            alert(result.error.message);
        }
        ajaxIndex();
    };
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}));

function modifyDreamHistory(modify_id) {
    var dhid = modify_id.substr('delete_'.length);
    var postUrl = '/person/dream-history/get-dream-history';
    var postData = {
        "params": {
            "dhid": dhid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
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
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}

function deleteDreamHistory(delete_id) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var dhid = delete_id.substr('delete_'.length);
        var postUrl = '/person/dream-history/delete-dream-history';
        var postData = {
            "params": {
                "dhid": dhid
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