$(document).ready(function () {
    loadMainCategory('category_parent_id');
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/finance-payment/ajax-index';
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
                        .append($('<td>').text(result.data.items[i]['payment_date']))
                        .append($('<td>').text(result.data.items[i]['payment']))
                        .append($('<td>').text(result.data.items[i]['category']))
                        .append($('<td>').text(result.data.items[i]['detail']))
                        .append($('<td>').text(result.data.items[i]['update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['fpid'], text: '修改'})
                                .click(function () {
                                    modifyFinancePayment(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['fpid'], text: '删除'})
                                .click(function () {
                                    deleteFinancePayment(this.id);
                                })
                            )
                        )
                );
            }
            if (result.data.totalItems == 0) {
                $('#tbl tbody').append($('<tr>')
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
    callAjaxWithFunction(get_url, get_data, success_function, method);
    //load main category
    loadMainCategory('finance_payment_fcid', false);
}
/*  --------------------------------------------------------------------------------------------------------  */
var g_selectpicker = $('#finance_payment_fcid').selectpicker();

$('#btn_add').on('click', function () {
    window.FinancePaymentForm.reset();
    $('#finance_payment_payment_date').val(DateWidget.getCurrentDate());
    g_selectpicker.selectpicker('refresh');
    g_selectpicker.selectpicker('val', $("#finance_payment_fcid option:first").val());
    $('#finance_payment_fpid').val('');
    $('#btn_submit_finance_payment').attr('disabled', false);
    $('#FinancePaymentModal').modal('show');
});

$('#FinancePaymentForm').on('submit', (function (event) {
    event.preventDefault();

    var fpid = $('#finance_payment_fpid').val();
    var type = (fpid == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if (error_num == 0) {
        $('#btn_submit_finance_payment').attr('disabled', true);
        var post_url = '/person/finance-payment/' + type + '-finance-payment';
        var post_data = {
            "params": $('#FinancePaymentForm').serializeObject()
        };
        var msg_success = (fpid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (fpid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function (result) {
            $('#FinancePaymentModal').modal('hide');
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
    }
}));

function modifyFinancePayment(modify_id) {
    var fpid = modify_id.substr('modify_'.length);
    var post_url = '/person/finance-payment/get-finance-payment';
    var post_data = {
        "params": {
            "fpid": fpid
        }
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            $('#finance_payment_payment_date').val(result.data.payment_date);
            $('#finance_payment_payment').val(result.data.payment);
            g_selectpicker.selectpicker('val', result.data.fcids);
            $('#finance_payment_intro').val(result.data.detail);
            $('#finance_payment_fpid').val(result.data.fpid);
            $('#btn_submit_finance_payment').attr('disabled', false);
            $('#FinancePaymentModal').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteFinancePayment(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var fpid = delete_id.substr('delete_'.length);
        var post_url = '/person/finance-payment/delete-finance-payment';
        var post_data = {
            "params": {
                "fpid": fpid
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

function validInput(type) {
    var error_num = 0;
    var payment_date = $.trim($('#finance_payment_payment_date').val());
    var payment = $.trim($('#finance_payment_payment').val());
    if (payment_date == '') {
        error_num = error_num + 1;
        alert(MESSAGE_DATE_ERROR);
    } else if (payment === '') {
        error_num = error_num + 1;
        alert(MESSAGE_MONEY_FORMAT_EMPTY_ERROR);
    }

    return error_num;
}