$(document).ready(function() {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/backend-role/ajax-index';
    var get_data = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var success_function = function(result){
        $('#tbl tbody').empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#tbl tbody').append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>').text(result.data.items[i]['role']))
                        .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data.items[i]['brid'], text: '修改'})
                            .click(function(){modifyBackendRole(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id:'delete_' + result.data.items[i]['brid'], text: '删除'})
                            .click(function(){deleteBackendRole(this.id);})
                        )
                    )
                );
            }
            if (result.data.totalItems == 0) {
                $('#tbl tbody').append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 3)
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
}
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function(){
    window.formBackendRole.reset();
    $('#backend_role_brid').val('');
    $('#btn_submit_backend_role').attr('disabled', false);
    $('#modalBackendRole').modal('show');
});

$('#formBackendRole').on('submit', (function(event){
    event.preventDefault();

    var brid = $('#backend_role_brid').val();
    var type = (brid == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if(error_num == 0) {
        $('#btn_submit_backend_role').attr('disabled', true);
        var post_url = '/backend-role/' + type +'-backend-role';
        var post_data = {
            "params": $('#formBackendRole').serializeObject()
        };
        var msg_success = (brid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (brid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function(result){
            $('#modalBackendRole').modal('hide');
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

function modifyBackendRole(modify_id) {
    var brid = modify_id.substr('modify_'.length);
    var get_url = '/backend-role/get-backend-role';
    var get_data = {
        "params": {
            "brid" : brid
        }
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            $('#backend_role_brid').val(result.data.brid);
            $('#backend_role_role').val(result.data.role);
            $('#btn_submit_backend_role').attr('disabled', false);
            $('#modalBackendRole').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function deleteBackendRole(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var brid = delete_id.substr('delete_'.length);
        var post_url = '/backend-role/delete-backend-role';
        var post_data = {
            "params": {
                "brid" : brid
            }
        };
        var method = 'post';
        var success_function = function(result){
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

function validInput(type)
{
    var error_num = 0;
    var role = $.trim($('#backend_role_role').val());
    if (role == '') {
        alert(MESSAGE_ROLE_NAME_EMPTY_ERROR);
        error_num = error_num + 1;
    }

    return error_num;
}
