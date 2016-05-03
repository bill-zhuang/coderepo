$('#location').on('keydown', function (event) {
    if (event.keyCode == 13) {
        $('#btb_mark_location').click();
    }
});

$('#btb_mark_location').on('click', function () {
    var location = $.trim($('#location').val());
    if (location != '') {
        var get_url = '/google-map/mark-location';
        var get_data = {
            "params": {
                "location": location
            }
        };
        var method = 'get';
        var success_function = function (result) {
            if (typeof result.data != "undefined") {
                showPosition(result.data.Longitude, result.data.Latitude);
            } else {
                alert(result.error.message);
            }
        };
        jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
    } else {
        alert('location can\'t be empty');
    }
});