$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/eject-history/ajax-index';
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
                        .append($('<td>').text(result.data.items[i]['type']))
                        .append($('<td>').text(result.data.items[i]['create_time']))
                        .append($('<td>').text(result.data.items[i]['update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['ehid'], text: '修改'})
                                .click(function () {
                                    modifyEjectHistory(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['ehid'], text: '删除'})
                                .click(function () {
                                    deleteEjectHistory(this.id);
                                })
                            )
                        )
                );
            }
            if (result.data.totalItems == 0) {
                $tblTbody.append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 7)
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

$('#ul_tab_type').find('li').on('click', function () {
    var tabValue = parseInt($(this).attr('id').substr('li_tab_type_'.length));
    tabValue = isNaN(tabValue) ? 0 : tabValue;
    $('#tab_type').val(tabValue);
    $('#ul_tab_type').find('li').removeClass('active');
    $('#li_tab_type_' + tabValue).addClass('active');
    $('#current_page').val(1);
    ajaxIndex();
});
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function () {
    window.formEjectHistory.reset();
    $('#eject_history_ehid').val('');
    $('#btn_submit_eject_history').attr('disabled', false);
    $('#modalEjectHistory').modal('show');
});

$('#formEjectHistory').on('submit', (function (event) {
    event.preventDefault();

    var ehid = $('#eject_history_ehid').val();
    var type = (ehid == '') ? 'add' : 'modify';
    if (isValidInput(type)) {
        $('#btn_submit_eject_history').attr('disabled', true);
        var postUrl = '/person/eject-history/' + type + '-eject-history';
        var postData = {
            "params": $('#formEjectHistory').serializeObject()
        };
        var method = 'post';
        var successFunc = function (result) {
            $('#modalEjectHistory').modal('hide');
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
}));

function modifyEjectHistory(modifyId) {
    var ehid = modifyId.substr('modify_'.length);
    var getUrl = '/person/eject-history/get-eject-history';
    var getData = {
        "params": {
            "ehid": ehid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != 'undefined') {
            $('#eject_history_ehid').val(result.data.ehid);
            $('#eject_history_happen_date').val(result.data.happen_date);
            $('#eject_history_count').val(result.data.count);
            $('#eject_history_type').val(result.data.type);
            $('#btn_submit_eject_history').attr('disabled', false);
            $('#modalEjectHistory').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function deleteEjectHistory(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var ehid = deleteId.substr('delete_'.length);
        var postUrl = '/person/eject-history/delete-eject-history';
        var postData = {
            "params": {
                "ehid": ehid
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

function isValidInput() {
    var isVerified = true;
    var happen_date = $('#eject_history_happen_date').val();
    var count = parseInt($('#eject_history_count').val());
    if (happen_date == '') {
        alert(alertMessage.DATE_ERROR);
        isVerified = false;
    } else if (count <= 0) {
        alert(alertMessage.CONTENT_ERROR);
        isVerified = false;
    }

    return isVerified;
}
