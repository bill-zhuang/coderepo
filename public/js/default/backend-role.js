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
                        .append($('<td>').text(result.data.items[i]['role'] + '(' + result.data.items[i]['count'] + ')'))
                        .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data.items[i]['brid'], text: '修改角色名'})
                            .click(function(){modifyBackendRole(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id: 'modifyAcl_' + result.data.items[i]['brid'], text: '修改角色权限'})
                                .click(function(){modifyBackendRoleAcl(this.id);})
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
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
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
        var method = 'post';
        var success_function = function(result){
            $('#modalBackendRole').modal('hide');
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
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
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
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
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
    }
}

function validInput(type)
{
    var error_num = 0;
    var role = $.trim($('#backend_role_role').val());
    if (role == '') {
        alert('角色名不能为空');
        error_num = error_num + 1;
    }

    return error_num;
}

function modifyBackendRoleAcl(modify_id) {
    var brid = modify_id.substr('modifyAcl_'.length);
    var get_url = '/backend-role/get-backend-role-acl';
    var get_data = {
        "params": {
            "brid" : brid
        }
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            var acl_content = '<input type="checkbox" id="ck_all" onclick="batchAclList(this);"/>&nbsp;全选<hr>';
            var aclList = result.data.aclList;
            var actionID;
            for (var module in aclList) {
                for (var controller in aclList[module]) {
                    acl_content = acl_content
                        + '<div><input type="checkbox" onclick="batchControllerAcl(this);"/>&nbsp;'
                        + '<span class="bill_font_bold">' + module + '/' + controller + '</span></br>';
                    for (var itemIndex in aclList[module][controller]) {
                        actionID = aclList[module][controller][itemIndex].id;
                        acl_content = acl_content + '<input type="checkbox" name="backend_role_acl_baid[]" id="acl_'
                            + actionID + '"' + (result.data.roleAcl.indexOf(actionID) === -1 ? '' : 'checked')
                            + ' value="' + actionID + '" '
                            + ' onclick="batchActionAcl(this);"/>&nbsp;' + aclList[module][controller][itemIndex].action + '&nbsp;';
                    }
                    acl_content = acl_content + '</div></br>';
                }
                acl_content = acl_content + '<hr>';
            }
            $('#aclList').empty().append(acl_content);
            $('#backend_role_acl_brid').val(result.data.brid);
            $('#btn_submit_backend_role_acl').attr('disabled', false);
            $('#modalBackendRoleAcl').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function batchAclList(obj) {
    $('#aclList input[type="checkbox"]').prop('checked', obj.checked);
}

function batchControllerAcl(obj) {
    $(obj).siblings('input[type="checkbox"]').prop('checked', obj.checked);
}

function batchActionAcl(obj) {
    if (obj.checked) {
        var actionCheckboxCount = $(obj).siblings('input[type="checkbox"]').size();
        if (((actionCheckboxCount - 1) == $(obj).siblings('input[type="checkbox"]:checked').size())
            && !$(obj).siblings('input[type="checkbox"]').first().prop('checked')) {
            $(obj).siblings('input[type="checkbox"]').first().prop('checked', true);
        } else {
            $(obj).siblings('input[type="checkbox"]').first().prop('checked', false);
        }
    } else {
        $(obj).siblings('input[type="checkbox"]').first().prop('checked', false);
    }
}

$('#formBackendRoleAcl').on('submit', (function(event){
    event.preventDefault();

    $('#btn_submit_backend_role_acl').attr('disabled', true);
    var post_url = '/backend-role/modify-backend-role-acl';
    var post_data = {
        "params": $('#formBackendRoleAcl').serializeObject()
    };
    var method = 'post';
    var success_function = function(result){
        $('#modalBackendRoleAcl').modal('hide');
        if (typeof result.data != 'undefined') {
            alert(result.data.message);
        } else {
            alert(result.error.message);
        }
        ajaxIndex();
    };
    jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
}));