$(document).ready(function () {
    var cookieName = $.cookie('name');
    if (typeof cookieName != 'undefined') {
        $('#remember').prop('checked', true);
        $('#username').val(cookieName);
    }
});

$('#formLogin').on('submit', function (event) {
    event.preventDefault();
    var name = $.trim($('#username').val());
    var password = $.trim($('#password').val());

    if (name === '') {
        alert('用户名不能为空！');
    } else if (password === '') {
        alert('密码不能为空！');
    } else {
        if ($('#remember').prop('checked')) {
            $.cookie('name', name, {expires: 1, path: '/'});
        }
        var postUrl = '/login/login';
        var postData = {
            "params": $('#formLogin').serializeObject()
        };
        var method = 'post';
        var successFunc = function (result) {
            if (typeof result.data != 'undefined') {
                window.location.href = result.data.redirectUrl;
            } else {
                alert(result.error.message);
                $('#username').val('').focus();
                $('#password').val('');
            }
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
});
