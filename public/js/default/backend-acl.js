$(document).ready(function() {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var get_url = '/backend-acl/ajax-index';
    var get_data = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var success_function = function(result){
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $tblTbody.append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>').text(result.data.items[i]['name']))
                        .append($('<td>').text(result.data.items[i]['module']))
                        .append($('<td>').text(result.data.items[i]['controller']))
                        .append($('<td>').text(result.data.items[i]['action']))
                        .append($('<td>')
                        .append($('<a>', {href: '#', id:'modify_' + result.data.items[i]['baid'], text: '修改'})
                            .click(function(){modifyBackendAcl(this.id);})
                        )
                        .append('  ')
                        .append($('<a>', {href: '#', id:'delete_' + result.data.items[i]['baid'], text: '删除'})
                            .click(function(){deleteBackendAcl(this.id);})
                        )
                    )
                );
            }
            if (result.data.totalItems == 0) {
                $tblTbody.append($('<tr>')
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
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}
/*  --------------------------------------------------------------------------------------------------------  */
$('#formBackendAcl').on('submit', (function(event){
    event.preventDefault();

    var error_num = validInput();
    if(error_num == 0) {
        $('#btn_submit_backend_acl').attr('disabled', true);
        var post_url = '/backend-acl/modify-backend-acl';
        var post_data = {
            "params": $('#formBackendAcl').serializeObject()
        };
        var method = 'post';
        var success_function = function(result){
            $('#modalBackendAcl').modal('hide');
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

function modifyBackendAcl(modify_id) {
    var baid = modify_id.substr('modify_'.length);
    var get_url = '/backend-acl/get-backend-acl';
    var get_data = {
        "params": {
            "baid" : baid
        }
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            $('#backend_acl_baid').val(result.data.baid);
            $('#backend_acl_name').val(result.data.name);
            $('#backend_acl_module').val(result.data.module);
            $('#backend_acl_controller').val(result.data.controller);
            $('#backend_acl_action').val(result.data.action);
            $('#btn_submit_backend_acl').attr('disabled', false);
            $('#modalBackendAcl').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function deleteBackendAcl(delete_id) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var baid = delete_id.substr('delete_'.length);
        var post_url = '/backend-acl/delete-backend-acl';
        var post_data = {
            "params": {
                "baid" : baid
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

function validInput()
{
    var error_num = 0;
    var name = $.trim($('#backend_acl_name').val());
    if (name == '') {
        alert('name can\'t empty');
        error_num = error_num + 1;
    }

    return error_num;
}

$('#btn_load_acl').on('click', function(){
    var get_url = '/backend-acl/load-backend-acl';
    var get_data = {
        'params': {}
    };
    var method = 'get';
    var success_function = function(result){
        if (typeof result.data != 'undefined') {
            alert(result.data.message);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
});