$(document).ready(function () {
    initChart();
});

function initChart() {
    var get_url = '/person/finance-history/ajax-index';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        //payment history data by month
        initMonthChart(result.month_chart_data);
        //all payment history data by day
        initLineChartAll(result.all_chart_data);
        //payment by category last year
        initYearCategoryChart(result.year_category_chart_data, result.year_spent);
        //payment by category last 30 days
        initMonthCategoryChart(result.month_category_chart_data, result.month_spent);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthChart(month_chart_data) {
    var data_period = month_chart_data['period'];
    var data_payment = month_chart_data['payment'];
    var line_canvas_id = 'payment_history_line_chart';
    var bar_canvas_id = 'payment_history_bar_chart';
    initLineChart(line_canvas_id, data_period, data_payment);
    initBarChart(bar_canvas_id, data_period, data_payment);
}

function initLineChartAll(all_chart_data) {
    var data_period = all_chart_data['period'];
    var data_payment = all_chart_data['payment'];
    var line_canvas_id = 'payment_history_line_chart_all';
    initLineChart(line_canvas_id, data_period, data_payment);
}

function initYearCategoryChart(year_category_chart_data, year_spent) {
    var data_category = year_category_chart_data['category'];
    var data_payment = year_category_chart_data['payment'];
    $('#year_spent').text('(' + year_spent + ')');
    var line_canvas_id = 'category_payment_history_line_chart';
    var bar_canvas_id = 'category_payment_history_bar_chart';
    initLineChart(line_canvas_id, data_category, data_payment);
    initBarChart(bar_canvas_id, data_category, data_payment);
}

function initMonthCategoryChart(month_category_chart_data, month_spent) {
    var data_category = month_category_chart_data['category'];
    var data_payment = month_category_chart_data['payment'];
    $('#month_spent').text('(' + month_spent + ')');
    var line_canvas_id = 'month_category_payment_history_line_chart';
    var bar_canvas_id = 'month_category_payment_history_bar_chart';
    initLineChart(line_canvas_id, data_category, data_payment);
    initBarChart(bar_canvas_id, data_category, data_payment);
}
