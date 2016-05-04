$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var getUrl = '/person/dream-history-chart/ajax-dream-history-period';
    var getData = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            initLineChart('dream_history_line_chart_all', result.data.period, result.data.interval);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthChart() {
    var getUrl = '/person/dream-history-chart/ajax-dream-history-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            var lineOption = {
                responsive: true,
                scaleOverride: true,
                scaleSteps: 9, //y axis length = steps * step_width
                scaleStepWidth: 1, // y axis
                scaleStartValue: 0 // y axis start value
            };
            initLineChart('dream_history_line_chart', result.data['period'], result.data['number'], lineOption);
            initBarChart('dream_history_bar_chart', result.data['period'], result.data['number']);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

$('#btn_search_day').on('click', function (event) {
    event.preventDefault();
    initPeriodChart();
});

$('#btn_search_month').on('click', function (event) {
    event.preventDefault();
    initMonthChart();
});