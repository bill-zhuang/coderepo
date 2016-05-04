$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var getUrl = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-period';
    var getData = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        var line_option = {
            responsive: true,
            scaleOverride: true,
            scaleSteps: 5, //y axis length = steps * step_width
            scaleStepWidth: 1, // y axis
            scaleStartValue: 0 // y axis start value
        };
        if (typeof result.data != "undefined") {
            initLineChart('grain_recycle_history_line_chart_all', result.data['period'], result.data['number'], line_option);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthChart() {
    var getUrl = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            var line_option = {
                responsive: true,
                scaleOverride: true,
                scaleSteps: 10, //y axis length = steps * step_width
                scaleStepWidth: 5, // y axis
                scaleStartValue: 0 // y axis start value
            };
            initLineChart('grain_recycle_history_line_chart', result.data['period'], result.data['number'], line_option);
            initBarChart('grain_recycle_history_bar_chart', result.data['period'], result.data['number']);
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