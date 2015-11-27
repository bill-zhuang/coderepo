$(document).ready(function () {
    initPlotMarkers();
});

function initPlotMarkers() {
    var get_url = '/google-map/ajax-multiple-location';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            markMultiPosition(result.data.coordinates, 'map_canvas_nocluster');
            markMultiPostionAndCluster(result.data.coordinates, 'map_canvas_cluster');
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);

}