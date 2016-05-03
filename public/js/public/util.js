function getRandomColorHex() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function callAjaxWithAlert(url, data, msg_success, msg_error, method, is_reload) {
    var success_function = function (result) {
        if (typeof result.data != 'undefined') {
            if (parseInt(result.data.flag) != 0) {
                alert(msg_success);
            } else {
                alert(msg_error);
            }
        } else {
            alert(result.error.message);
        }
        if (typeof is_reload == 'undefined' || is_reload == true) {
            window.location.reload();
        }
    };

    callAjaxWithFunction(url, data, success_function, method);
}

function callAjaxWithForm(url, data, msg_success, msg_error, method) {
    $.ajax({
        url: url,
        type: method || 'post',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (result) {
            result = JSON.parse(result);
            if (typeof result.data != 'undefined') {
                if (parseInt(result.data.flag) != 0) {
                    alert(msg_success);
                } else {
                    alert(msg_error);
                }
            } else {
                alert(result.error.message);
            }
            window.location.reload();
        },

        error: getAjaxErrorFunction()
    });
}

function callAjaxWithFormAndFunction(url, data, success_function, method) {
    $.ajax({
        url: url,
        type: method || 'post',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: success_function,

        error: getAjaxErrorFunction()
    });
}

function callAjaxWithFunction(url, data, success_function, method) {
    $.ajax({
        url: url,
        type: method || 'post',
        data: data,
        dataType: 'json',
        success: success_function,

        error: getAjaxErrorFunction()
    });
}

function getAjaxErrorFunction() {
    return function (XMLHttpRequest, textStatus, errorThrown) {
        console.log("XMLHttpRequest.status=" + XMLHttpRequest.status +
            "\nXMLHttpRequest.readyState=" + XMLHttpRequest.readyState +
            "\ntextStatus=" + textStatus);
        var contentType = XMLHttpRequest.getResponseHeader("Content-Type");
        if (XMLHttpRequest.status === 200 && contentType.toLowerCase().indexOf("text/html") >= 0) {
            // assume that our login has expired - reload our current page
            window.location.reload();
        }
    };
}