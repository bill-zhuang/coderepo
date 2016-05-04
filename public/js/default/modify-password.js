$(document).ready(function () {
    $('#old_password').val('');
});

$('#formModifyPassword').on('submit', function (event) {
    event.preventDefault();
    var old_password = $.trim($('#old_password').val());
    var new_password = $.trim($('#new_password').val());
    var new_password_repeat = $.trim($('#new_password_repeat').val());

    if (old_password == '') {
        alert('密码不能为空！');
    } else if (new_password == '') {
        alert('新密码不能为空！');
    } else if (new_password_repeat == '') {
        alert('新密码确认不能为空！');
    } else if (new_password != new_password_repeat) {
        alert('两次密码不相同');
    } else {
        var postUrl = '/main/modify-password';
        var postData = {
            "params": $('#formModifyPassword').serializeObject()
        };
        var method = 'post';
        var successFunc = function(result){
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            $('#old_password').val('');
            $('#new_password').val('');
            $('#new_password_repeat').val('');
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
});