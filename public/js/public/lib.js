function httpRequest(url) {
    var xmlhttp;

    try {
        xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
        } catch (e) {
            try {
                xmlhttp = new XMLHttpRequest();
            } catch (e) {

            }
        }
    }

    xmlhttp.open("get", url);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var result = xmlhttp.responseText;
                //todo
            } else {
                //todo
            }
        }
    }

    xmlhttp.send(null);
    return;
}

//real-time refresh
var seconds = 30;
setInterval(function() { }, seconds * 1000);

function popupwindow(url) {
    window.open(url, 'newwindow', 'height=800, width=550, top=0,left=200, toolbar=no, menubar=no, scrollbars=yes, resizable=no,location=no, status=no');
}