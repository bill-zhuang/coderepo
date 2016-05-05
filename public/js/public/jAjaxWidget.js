var jAjaxWidget = (function () {
    var methodPost = 'POST';

    function ajaxErrorFunction() {
        return function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("XMLHttpRequest.status=" + XMLHttpRequest.status +
                "\nXMLHttpRequest.readyState=" + XMLHttpRequest.readyState +
                "\ntextStatus=" + textStatus);
            var contentType = XMLHttpRequest.getResponseHeader("Content-Type");
            if (XMLHttpRequest.status === 200 && contentType.toLowerCase().indexOf("text/html") >= 0) {
                // assume that login has expired - reload our current page
                window.location.reload();
            }
        };
    }

    return {
        additionFunc: function (url, data, successFunc, method) {
            $.ajax({
                url: url,
                type: method || methodPost,
                data: data,
                dataType: 'json',
                success: successFunc,
                error: ajaxErrorFunction()
            });
        },

        formSubmit: function (url, data, successFunc, method) {
            $.ajax({
                url: url,
                type: method || methodPost,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: successFunc,
                error: ajaxErrorFunction()
            });
        }
    }
}());