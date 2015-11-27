$(document).ready(function () {
    //all bad history data by day
    initChart();
});

function initChart() {
    var get_url = '/person/bad-history-chart/ajax-index';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            if (result.data.period.length != 0) {
                initLineChart('bad_history_line_chart_all', result.data.period, result.data.interval);
            }
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}