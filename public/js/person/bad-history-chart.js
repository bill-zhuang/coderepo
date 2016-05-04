$(document).ready(function () {
    //all bad history data by day
    initChart();
});

function initChart() {
    var getUrl = '/person/bad-history-chart/ajax-index';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            if (result.data.period.length != 0) {
                initLineChart('bad_history_line_chart_all', result.data.period, result.data.interval);
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}