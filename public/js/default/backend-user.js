$(document).ready(function() {
    ajaxIndex();
});

function ajaxIndex() {
    var get_url = '/backend-user/ajax-index';
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
                        .append($('<td>').text(result.data.items[i]['name']))
                        .append($('<td>').text(result.data.items[i]['role']))
                        .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data.items[i]['buid'], text: '修改'})
                            .click(function(){modifyBackendUser(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id:'delete_' + result.data.items[i]['buid'], text: '删除'})
                            .click(function(){deleteBackendUser(this.id);})
                        )
                    )
                );
            }
            if (result.data.totalItems == 0) {
                $('#tbl tbody').append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 4)
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
    window.formBackendUser.reset();
    $('#backend_user_buid').val('');
    $('#btn_submit_backend_user').attr('disabled', false);
    var get_url = '/backend-role/get-all-roles';
    var get_data = {
        'params': {}
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            var roleSelect = '';
            for (var brid in result.data) {
                roleSelect = roleSelect + '<option value="' + brid + '">' + result.data[brid] + '</option>';
            }
            $('#backend_user_brid').empty().append(roleSelect);
            $('#modalBackendUser').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);

});

$('#formBackendUser').on('submit', (function(event){
    event.preventDefault();

    var buid = $('#backend_user_buid').val();
    var type = (buid == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if(error_num == 0) {
        $('#btn_submit_backend_user').attr('disabled', true);
        var post_url = '/backend-user/' + type +'-backend-user';
        var post_data = {
            "params": $('#formBackendUser').serializeObject()
        };
        var msg_success = (buid == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (buid == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function(result){
            $('#modalBackendUser').modal('hide');
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

function modifyBackendUser(modify_id) {
    var buid = modify_id.substr('modify_'.length);
    var get_url = '/backend-user/get-backend-user';
    var get_data = {
        "params": {
            "buid" : buid
        }
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            var roleSelect = '';
            for (var brid in result.data.roles) {
                roleSelect = roleSelect + '<option value="' + brid + '">' + result.data.roles[brid] + '</option>';
            }
            $('#backend_user_buid').val(result.data.buid);
            $('#backend_user_name').val(result.data.name);
            $('#backend_user_brid').empty().append(roleSelect).val(result.data.brid);
            $('#btn_submit_backend_user').attr('disabled', false);
            $('#modalBackendUser').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function deleteBackendUser(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var buid = delete_id.substr('delete_'.length);
        var post_url = '/backend-user/delete-backend-user';
        var post_data = {
            "params": {
                "buid" : buid
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
    var name = $.trim($('#backend_user_name').val());
    if (name === '') {
        alert('user name can\'t empty.');
        error_num = error_num + 1;
    }

    return error_num;
}