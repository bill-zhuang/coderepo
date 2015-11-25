$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var get_url = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-period';
    var get_data = $.param($('#formSearchDay').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        var line_option = {
            responsive: true,
            scaleOverride: true,
            scaleSteps: 5, //y axis length = steps * step_width
            scaleStepWidth: 1, // y axis
            scaleStartValue: 0 // y axis start value
        };
        initLineChart('grain_recycle_history_line_chart_all', result['period'], result['number'], line_option);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthChart() {
    var get_url = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-month';
    var get_data = $.param($('#formSearchMonth').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        var data_period = result['period'];
        var data_number = result['number'];
        var line_option = {
            responsive: true,
            scaleOverride: true,
            scaleSteps: 10, //y axis length = steps * step_width
            scaleStepWidth: 5, // y axis
            scaleStartValue: 0 // y axis start value
        };
        initLineChart('grain_recycle_history_line_chart', data_period, data_number, line_option);
        initBarChart('grain_recycle_history_bar_chart', data_period, data_number);
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