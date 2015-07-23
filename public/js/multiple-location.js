$(document).ready(function(){
    initPlotMarkers();
});

function initPlotMarkers()
{
    var get_url = '/google-map/ajax-multiple-location';
    var get_data = {

    };
    var method = 'get';
    var success_function = function(result){
        markMultiPosition(result.lng_lat, 'map_canvas_nocluster');
        markMultiPostionAndCluster(result.lng_lat, 'map_canvas_cluster');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);

}