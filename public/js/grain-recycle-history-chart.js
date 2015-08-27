$(document).ready(function () {
    //all grain recycle history data by day
    initChart();
});

function initChart() {
    var get_url = '/person/grain-recycle-history-chart/ajax-index';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        var chart_data = result.chart_data;
        var data_period = chart_data['period'];
        var data_number = chart_data['number'];
        if (result.start_date != '') {
            $('#start_date').val(result.start_date);
        }
        if (result.end_date != '') {
            $('#start_date').val(result.end_date);
        }
        var line_option = {
            responsive: true,
            scaleOverride: true,
            scaleSteps: 10, //y axis length = steps * step_width
            scaleStepWidth: 5, // y axis
            scaleStartValue: 0 // y axis start value
        };
        initLineChart('grain_recycle_history_line_chart', data_period, data_number, line_option);
        initBarChart('grain_recycle_history_bar_chart', data_period, data_number);

        line_option = {
            responsive: true,
            scaleOverride: true,
            scaleSteps: 5, //y axis length = steps * step_width
            scaleStepWidth: 1, // y axis
            scaleStartValue: 0 // y axis start value
        };
        initLineChart('grain_recycle_history_line_chart_all', result.all_chart_data['period'], result.all_chart_data['interval'], line_option);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

$('#btn_search').on('click', function (event) {
    event.preventDefault();
    ajaxDreamHistoryPeriod();
});

function ajaxDreamHistoryPeriod() {
    var get_url = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-period';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function (result) {
        initLineChart('grain_recycle_history_line_chart_all', result['period'], result['interval']);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}
