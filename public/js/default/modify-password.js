$(document).ready(function () {
    $('#old_password').val('');
});

$('#formModifyPassword').on('submit', function (event) {
    event.preventDefault();
    var oldPassword = $.trim($('#old_password').val());
    var newPassword = $.trim($('#new_password').val());
    var newPasswordRepeat = $.trim($('#new_password_repeat').val());

    if (oldPassword == '') {
        alert('密码不能为空！');
    } else if (newPassword == '') {
        alert('新密码不能为空！');
    } else if (newPasswordRepeat == '') {
        alert('新密码确认不能为空！');
    } else if (newPassword != newPasswordRepeat) {
        alert('两次密码不相同');
    } else {
        var postUrl = '/main/modify-password';
        var postData = {
            "params": $('#formModifyPassword').serializeObject()
        };
        var method = 'post';
        var successFunc = function (result) {
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