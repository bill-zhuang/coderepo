function isUnsignedInt(num)
{
    if (num === 0 || num === '0') {
        return true;
    }

    return /^\s*[1-9][0-9]*\s*$/.test(num);
}

function isPositiveInt(num)
{
    return /^\s*[1-9][0-9]*\s*$/.test(num);
}

function isUnsignedFloat(num)
{
    if (num === 0 || num === '0') {
        return true;
    }

    return /^\s*[0-9]+(\.[0-9]+)*\s*$/.test(num);
}

function isPositiveFloat(num)
{
    return /^\s*[0-9]+(\.[0-9]+)*\s*$/.test(num);
}

function isFloat(num)
{
    return /^\s*-?[0-9]+(\.[0-9]+)*\s*$/.test(num);
}

function isCellPhone(phone)
{
    return /^\s*1[0-9]{10}\s*$/.test(phone);
}

function isImgExtension(img_name)
{
    return /\.(jpg|png|gif|jpeg|bmp)$/.test(img_name);
}

function getCurrentTime()
{
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var hour = d.getHours();
    var minute = d.getMinutes();
    var second = d.getSeconds();

    return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
}

function getCurrentDate()
{
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();

    if (month < 10) {
        month = '0' + month;
    }
    if (day < 10) {
        day = '0' + day;
    }

    return year + '-' + month + '-' + day;
}

function getRandomColorHex()
{
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function callAjaxWithAlert(url, data, msg_success, msg_error, method, is_reload)
{
    var success_function = function(flag) {
        if (parseInt(flag) != 0) {
            alert(msg_success);
        } else {
            alert(msg_error);
        }
        if (typeof is_reload == 'undefined' || is_reload == true) {
            window.location.reload();
        }
    };

    callAjaxWithFunction(url, data, success_function, method);
}

function callAjaxWithForm(url, data, msg_success, msg_error, method)
{
    $.ajax({
        url: url,
        type: method || 'post',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(flag) {
            if (parseInt(flag) != 0) {
                alert(msg_success);
            } else {
                alert(msg_error);
            }
            window.location.reload();
        },

        error: getAjaxErrorFunction()
    });
}

function callAjaxWithFormAndFunction(url, data, success_function, method)
{
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

function callAjaxWithFunction(url, data, success_function, method)
{
    $.ajax({
        url: url,
        type: method || 'post',
        data: data,
        dataType: 'json',
        success: success_function,

        error: getAjaxErrorFunction()
    });
}

function getAjaxErrorFunction()
{
    return function(XMLHttpRequest, textStatus, errorThrown) {
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