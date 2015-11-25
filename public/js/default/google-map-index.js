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
            'location': location
        };
        var method = 'get';
        var success_function = function (coordinate_result) {
            console.log(coordinate_result);
            if (coordinate_result == '') {
                alert('error info');
            } else {
                showPosition(coordinate_result.Longitude, coordinate_result.Latitude);
            }
        };
        callAjaxWithFunction(get_url, get_data, success_function, method);
    } else {
        alert('location can\'t be empty');
    }
});