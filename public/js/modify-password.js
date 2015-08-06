$(document).ready(function () {
    $('#old_password').val('');
});
$('#modifypasswordform').on('submit', function (event) {
    event.preventDefault();
    modifyPassword();
});

function modifyPassword() {
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
        $('#modifypasswordform').get(0).submit();
    }
}