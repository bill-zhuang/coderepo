$(document).ready(function () {
    var cookie_name = $.cookie('name');
    if (typeof cookie_name != 'undefined') {
        $('#remember').prop('checked', true);
        $('#username').val(cookie_name);
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
        var post_url = '/login/login';
        var post_data = {
            "params": $('#formLogin').serializeObject()
        };
        var method = 'post';
        var success_function = function(result){
            if (typeof result.data != 'undefined') {
                window.location.href = result.data.redirectUrl;
            } else {
                alert(result.error.message);
                $('#username').val('').focus();
                $('#password').val('');
            }
        };
        jAjaxWidget.additionFunc(post_url, post_data, success_function, method);
    }
});
