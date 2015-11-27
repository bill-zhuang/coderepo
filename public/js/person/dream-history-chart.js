$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var get_url = '/person/dream-history-chart/ajax-dream-history-period';
    var get_data = {
        "params": getFormObjectData('formSearchDay')
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            initLineChart('dream_history_line_chart_all', result.data.period, result.data.interval);
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthChart() {
    var get_url = '/person/dream-history-chart/ajax-dream-history-month';
    var get_data = {
        "params": getFormObjectData('formSearchMonth')
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            var line_option = {
                responsive: true,
                scaleOverride: true,
                scaleSteps: 9, //y axis length = steps * step_width
                scaleStepWidth: 1, // y axis
                scaleStartValue: 0 // y axis start value
            };
            initLineChart('dream_history_line_chart', result.data['period'], result.data['number'], line_option);
            initBarChart('dream_history_bar_chart', result.data['period'], result.data['number']);
        } else {
            alert(result.error.message);
        }
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

$('#btn_search_day').on('click', function (event) {
    event.preventDefault();
    initPeriodChart();
});

$('#btn_search_month').on('click', function (event) {
    event.preventDefault();
    initMonthChart();
});