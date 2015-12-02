$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/person/finance-category/ajax-index';
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
                        .append($('<td>').text(result.data.items[i]['name']))
                        .append($('<td>').text(result.data.items[i]['parent']))
                        .append($('<td>').text(result.data.items[i]['weight']))
                        .append($('<td>').text(result.data.items[i]['update_time']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['fcid'], text: '修改'})
                                .click(function () {
                                    modifyFinanceCategory(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['fcid'], text: '删除'})
                                .click(function () {
                                    deleteFinanceCategory(this.id);
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
    //load main category
    loadMainCategory('finance_category_parent_id');
}

/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function () {
    window.FinanceCategoryForm.reset();
    $('#finance_category_fcid').val('');
    $('#btn_submit_finance_category').attr('disabled', false);
    $('#FinanceCategoryModal').modal('show');
});

$('#FinanceCategoryForm').on('submit', (function (event) {
    event.preventDefault();

    var fcid = $('#finance_category_fcid').val();
    var type = (fcid == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if (error_num == 0) {
        $('#btn_submit_finance_category').attr('disabled', true);
        var post_url = '/person/finance-category/' + type + '-finance-category';
        var post_data = {
            "params": $('#FinanceCategoryForm').serializeObject()
        };
        var msg_success = (fcid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (fcid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function (result) {
            $('#FinanceCategoryModal').modal('hide');
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

function modifyFinanceCategory(modify_id) {
    var fcid = modify_id.substr('modify_'.length);
    var post_url = '/person/finance-category/get-finance-category';
    var post_data = {
        "params": {
            "fcid": fcid
        }
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            $('#finance_category_name').val(result.data.name);
            $('#finance_category_parent_id').val(result.data.parent_id);
            $('#finance_category_weight').val(result.data.weight);
            $('#finance_category_fcid').val(result.data.fcid);
            $('#btn_submit_finance_category').attr('disabled', false);
            $('#FinanceCategoryModal').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(post_url, post_data, success_function, method);
}

function deleteFinanceCategory(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var fcid = delete_id.substr('delete_'.length);
        var post_url = '/person/finance-category/delete-finance-category';
        var post_data = {
            "params": {
                "fcid": fcid
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
    var name = $.trim($('#finance_category_name').val());
    var weight = $.trim($('#finance_category_weight').val());
    if (name == '') {
        error_num = error_num + 1;
        alert(MESSAGE_NAME_ERROR);
    } else if (!isUnsignedInt(weight)) {
        error_num = error_num + 1;
        alert(MESSAGE_WEIGHT_FORMAT_ERROR);
    }

    return error_num;
}