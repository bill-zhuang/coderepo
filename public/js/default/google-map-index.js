$('#location').on('keydown', function (event) {
    if (event.keyCode == 13) {
        $('#btb_mark_location').click();
    }
});

$('#btb_mark_location').on('click', function () {
    var location = $.trim($('#location').val());
    if (location != '') {
        var getUrl = '/google-map/mark-location';
        var getData = {
            "params": {
                "location": location
            }
        };
        var method = 'get';
        var successFunc = function (result) {
            if (typeof result.data != "undefined") {
                showPosition(result.data.Longitude, result.data.Latitude);
            } else {
                alert(result.error.message);
            }
        };
        jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
    } else {
        alert('location can\'t be empty');
    }
});