$(document).ready(function() {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/backend-role/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function(result){
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $tblTbody.append(
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
                $tblTbody.append($('<tr>')
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
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
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
    if(isValidInput()) {
        $('#btn_submit_backend_role').attr('disabled', true);
        var postUrl = '/backend-role/' + type +'-backend-role';
        var postData = {
            "params": $('#formBackendRole').serializeObject()
        };
        var method = 'post';
        var successFunc = function(result){
            $('#modalBackendRole').modal('hide');
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

function modifyBackendRole(modifyId) {
    var brid = modifyId.substr('modify_'.length);
    var getUrl = '/backend-role/get-backend-role';
    var getData = {
        "params": {
            "brid" : brid
        }
    };
    var method = 'get';
    var successFunc = function(result){
        if (typeof result.data != 'undefined') {
            $('#backend_role_brid').val(result.data.brid);
            $('#backend_role_role').val(result.data.role);
            $('#btn_submit_backend_role').attr('disabled', false);
            $('#modalBackendRole').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function deleteBackendRole(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var brid = deleteId.substr('delete_'.length);
        var postUrl = '/backend-role/delete-backend-role';
        var postData = {
            "params": {
                "brid" : brid
            }
        };
        var method = 'post';
        var successFunc = function(result){
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

function isValidInput()
{
    var isVerified = true;
    var role = $.trim($('#backend_role_role').val());
    if (role == '') {
        alert('角色名不能为空');
        isVerified = false;
    }

    return isVerified;
}

function modifyBackendRoleAcl(modifyId) {
    var brid = modifyId.substr('modifyAcl_'.length);
    var getUrl = '/backend-role/get-backend-role-acl';
    var getData = {
        "params": {
            "brid" : brid
        }
    };
    var method = 'get';
    var successFunc = function(result){
        if (typeof result.data != 'undefined') {
            var aclContent = '<input type="checkbox" id="ck_all" onclick="batchAclList(this);"/>&nbsp;全选<hr>';
            var aclList = result.data.aclList;
            var actionID;
            for (var module in aclList) {
                for (var controller in aclList[module]) {
                    aclContent = aclContent
                        + '<div><input type="checkbox" onclick="batchControllerAcl(this);"/>&nbsp;'
                        + '<span class="bill_font_bold">' + module + '/' + controller + '</span></br>';
                    for (var itemIndex in aclList[module][controller]) {
                        actionID = aclList[module][controller][itemIndex].id;
                        aclContent = aclContent + '<input type="checkbox" name="backend_role_acl_baid[]" id="acl_'
                            + actionID + '"' + (result.data.roleAcl.indexOf(actionID) === -1 ? '' : 'checked')
                            + ' value="' + actionID + '" '
                            + ' onclick="batchActionAcl(this);"/>&nbsp;' + aclList[module][controller][itemIndex].action + '&nbsp;';
                    }
                    aclContent = aclContent + '</div></br>';
                }
                aclContent = aclContent + '<hr>';
            }
            $('#aclList').empty().append(aclContent);
            $('#backend_role_acl_brid').val(result.data.brid);
            $('#btn_submit_backend_role_acl').attr('disabled', false);
            $('#modalBackendRoleAcl').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function batchAclList(obj) {
    $('#aclList').find('input[type="checkbox"]').prop('checked', obj.checked);
    //$('#aclList input[type="checkbox"]').prop('checked', obj.checked);
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
    var postUrl = '/backend-role/modify-backend-role-acl';
    var postData = {
        "params": $('#formBackendRoleAcl').serializeObject()
    };
    var method = 'post';
    var successFunc = function(result){
        $('#modalBackendRoleAcl').modal('hide');
        if (typeof result.data != 'undefined') {
            alert(result.data.message);
        } else {
            alert(result.error.message);
        }
        ajaxIndex();
    };
    jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
}));