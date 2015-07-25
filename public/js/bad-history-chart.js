$(document).ready(function() {
    //all bad history data by day
    initChart();
});

function initChart() {
    var get_url = '/person/bad-history-chart/ajax-index';
    var get_data = {

    };
    var method = 'get';
    var success_function = function(result){
        var data_period = result.period;
        var data_number = result.interval;
        if (data_period.length != 0) {
            initLineChart('bad_history_line_chart_all', data_period, data_number);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}