$(document).ready(function() {
    var cookie_name =  $.cookie('name');
    if (typeof cookie_name != 'undefined') {
        $('#remember').prop('checked', true);
        $('#username').val(cookie_name);
    }
});

$('#login').on('submit', function(event) {
    event.preventDefault();
    loginCheck();
});

function loginCheck() {
    var name = $.trim($('#username').val());
    var password = $.trim($('#password').val());

    if (name == '') {
        alert('用户名不能为空！');
    } else if (password == '') {
        alert('密码不能为空！');
    } else {
        if($('#remember').prop('checked')) {
            $.cookie('name', name, {expires : 1, path : '/'});
        }
        $('#login').get(0).submit();
    }
}