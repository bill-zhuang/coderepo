$(document).ready(function () {
    loadMainCategory('category_parent_id');
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/finance-payment/ajax-index';
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
    //load main category
    loadMainCategory('finance_payment_fcid', false);
}
$('#category_parent_id').on('change', function () {
    $('#current_page').val(1);
    ajaxIndex();
});
/*  --------------------------------------------------------------------------------------------------------  */
var gSelectpicker = $('#finance_payment_fcid').selectpicker();

$('#btn_add').on('click', function () {
    window.FinancePaymentForm.reset();
    $('#finance_payment_payment_date').val(DateWidget.getCurrentDate());
    gSelectpicker.selectpicker('refresh');
    gSelectpicker.selectpicker('val', $("#finance_payment_fcid").find('option:first').val());
    $('#finance_payment_fpid').val('');
    $('#btn_submit_finance_payment').attr('disabled', false);
    $('#FinancePaymentModal').modal('show');
});

$('#FinancePaymentForm').on('submit', (function (event) {
    event.preventDefault();

    var fpid = $('#finance_payment_fpid').val();
    var type = (fpid == '') ? 'add' : 'modify';
    if (isValidInput()) {
        $('#btn_submit_finance_payment').attr('disabled', true);
        var postUrl = '/person/finance-payment/' + type + '-finance-payment';
        var postData = {
            "params": $('#FinancePaymentForm').serializeObject()
        };
        var method = 'post';
        var successFunc = function (result) {
            $('#FinancePaymentModal').modal('hide');
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

function modifyFinancePayment(modifyId) {
    var fpid = modifyId.substr('modify_'.length);
    var postUrl = '/person/finance-payment/get-finance-payment';
    var postData = {
        "params": {
            "fpid": fpid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != 'undefined') {
            $('#finance_payment_payment_date').val(result.data.payment_date);
            $('#finance_payment_payment').val(result.data.payment);
            gSelectpicker.selectpicker('refresh');
            gSelectpicker.selectpicker('val', result.data.fcids);
            $('#finance_payment_intro').val(result.data.detail);
            $('#finance_payment_fpid').val(result.data.fpid);
            $('#btn_submit_finance_payment').attr('disabled', false);
            $('#FinancePaymentModal').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}

function deleteFinancePayment(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var fpid = deleteId.substr('delete_'.length);
        var postUrl = '/person/finance-payment/delete-finance-payment';
        var postData = {
            "params": {
                "fpid": fpid
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
    var paymentDate = $.trim($('#finance_payment_payment_date').val());
    var payment = $.trim($('#finance_payment_payment').val());
    if (paymentDate == '') {
        isVerified = false;
        alert(alertMessage.DATE_ERROR);
    } else if (payment === '') {
        isVerified = false;
        alert(alertMessage.MONEY_FORMAT_EMPTY_ERROR);
    }

    return isVerified;
}