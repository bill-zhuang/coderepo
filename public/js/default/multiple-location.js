$(document).ready(function () {
    initPlotMarkers();
});

function initPlotMarkers() {
    var getUrl = '/google-map/ajax-multiple-location';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            markMultiPosition(result.data.coordinates, 'map_canvas_nocluster');
            markMultiPostionAndCluster(result.data.coordinates, 'map_canvas_cluster');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);

}