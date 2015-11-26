$(document).ready(function () {
    loadMainCategory('category_parent_id');
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/finance-payment/ajax-index';
    var get_data = {
        "params": getFormObjectData('formSearch')
    };
    var method = 'get';
    var success_function = function (result) {
        $('#tbl tbody').empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#tbl tbody').append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>').text(result.data.items[i]['fp_payment_date']))
                        .append($('<td>').text(result.data.items[i]['fp_payment']))
                        .append($('<td>').text(result.data.items[i]['category']))
                        .append($('<td>').text(result.data.items[i]['fp_detail']))
                        .append($('<td>').text(result.data.items[i]['fp_update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['fp_id'], text: '修改'})
                                .click(function () {
                                    modifyFinancePayment(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['fp_id'], text: '删除'})
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
    loadMainCategory('finance_payment_fc_id', false);
}
/*  --------------------------------------------------------------------------------------------------------  */
var g_selectpicker = $('#finance_payment_fc_id').selectpicker();

$('#btn_add').on('click', function () {
    window.FinancePaymentForm.reset();
    $('#finance_payment_payment_date').val(getCurrentDate());
    g_selectpicker.selectpicker('refresh');
    g_selectpicker.selectpicker('val', $("#finance_payment_fc_id option:first").val());
    $('#finance_payment_fp_id').val('');
    $('#btn_submit_finance_payment').attr('disabled', false);
    $('#FinancePaymentModal').modal('show');
});

$('#FinancePaymentForm').on('submit', (function (event) {
    event.preventDefault();

    var fp_id = $('#finance_payment_fp_id').val();
    var type = (fp_id == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if (error_num == 0) {
        $('#btn_submit_finance_payment').attr('disabled', true);
        var post_url = '/person/finance-payment/' + type + '-finance-payment';
        var post_data = new FormData(this);
        var msg_success = (fp_id == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (fp_id == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function (result) {
            $('#FinancePaymentModal').modal('hide');
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

function modifyFinancePayment(modify_id) {
    var fp_id = modify_id.substr('modify_'.length);
    var post_url = '/person/finance-payment/get-finance-payment';
    var post_data = {
        'fp_id': fp_id
    };
    var method = 'get';
    var success_function = function (result) {
        $('#finance_payment_payment_date').val(result.fp_payment_date);
        $('#finance_payment_payment').val(result.fp_payment);
        g_selectpicker.selectpicker('val', result.fc_ids);
        $('#finance_payment_intro').val(result.fp_detail);
        $('#finance_payment_fp_id').val(result.fp_id);
        $('#btn_submit_finance_payment').attr('disabled', false);
        $('#FinancePaymentModal').modal('show');
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteFinancePayment(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var fp_id = delete_id.substr('delete_'.length);
        var post_url = '/person/finance-payment/delete-finance-payment';
        var post_data = {
            'fp_id': fp_id
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