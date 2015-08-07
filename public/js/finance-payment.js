$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/finance-payment/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
                    .append($('<td>').text(result.data[i]['fp_payment_date']))
                    .append($('<td>').text(result.data[i]['fp_payment']))
                    .append($('<td>').text(result.data[i]['category']))
                    .append($('<td>').text(result.data[i]['fp_detail']))
                    .append($('<td>').text(result.data[i]['fp_update_time']))
                    .append($('<td>')
                        .append($('<a>', {href: '#', id: 'modify_' + result.data[i]['fp_id'], text: '修改'})
                            .click(function () {
                                modifyBadHistory(this.id);
                            })
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id: 'delete_' + result.data[i]['fp_id'], text: '删除'})
                            .click(function () {
                                deleteBadHistory(this.id);
                            })
                        )
                    )
            );
        }
        if (result.total == 0) {
            $('#tbl tbody').append($('<tr>')
                .append(
                    $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 7)
                )
            );
        }
        //init pagination
        initPagination(result.total_pages, result.current_page);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}
/*  --------------------------------------------------------------------------------------------------------  */
var g_selectpicker = $('#finance_payment_fc_id').selectpicker();

$('#btn_add').on('click', function () {
    var get_url = '/person/finance-category/get-finance-main-category';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        $('#finance_payment_fc_id').empty();
        for (var fc_id in result) {
            $('#finance_payment_fc_id').append($('<option>', {
                value: fc_id,
                text: result[fc_id]
            }));
        }

        window.FinancePaymentForm.reset();
        $('#finance_payment_payment_date').val(getCurrentDate());
        g_selectpicker.selectpicker('refresh');
        g_selectpicker.selectpicker('val', $("#finance_payment_fc_id option:first").val());
        $('#finance_payment_fp_id').val('');
        $('#btn_submit_finance_payment').attr('disabled', false);
        $('#FinancePaymentModal').modal('show');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
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

function modifyBadHistory(modify_id) {
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

function deleteBadHistory(delete_id) {
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