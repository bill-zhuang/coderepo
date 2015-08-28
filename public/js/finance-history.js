$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
    initMonthCategoryChart();
    initYearCategoryChart();
    initMonthSpent();
    initYearSpent();
});

function initPeriodChart() {
    var get_url = '/person/finance-history/ajax-finance-history-period';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        initLineChart('payment_history_line_chart_all', result['period'], result['payment']);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthChart() {
    var get_url = '/person/finance-history/ajax-finance-history-month';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        var data_period = result['period'];
        var data_payment = result['payment'];
        var line_canvas_id = 'payment_history_line_chart';
        var bar_canvas_id = 'payment_history_bar_chart';
        initLineChart(line_canvas_id, data_period, data_payment);
        initBarChart(bar_canvas_id, data_period, data_payment);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthCategoryChart() {
    var get_url = '/person/finance-history/ajax-finance-history-month-category';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        var data_category = result['category'];
        var data_payment = result['payment'];
        var line_canvas_id = 'month_category_payment_history_line_chart';
        var bar_canvas_id = 'month_category_payment_history_bar_chart';
        initLineChart(line_canvas_id, data_category, data_payment);
        initBarChart(bar_canvas_id, data_category, data_payment);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initYearCategoryChart() {
    var get_url = '/person/finance-history/ajax-finance-history-year-category';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        var data_category = result['category'];
        var data_payment = result['payment'];
        var line_canvas_id = 'category_payment_history_line_chart';
        var bar_canvas_id = 'category_payment_history_bar_chart';
        initLineChart(line_canvas_id, data_category, data_payment);
        initBarChart(bar_canvas_id, data_category, data_payment);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthSpent() {
    var get_url = '/person/finance-history/ajax-finance-history-month-spent';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        $('#month_spent').text('(' + result + ')');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initYearSpent() {
    var get_url = '/person/finance-history/ajax-finance-history-year-spent';
    var get_data = {

    };
    var method = 'get';
    var success_function = function (result) {
        $('#year_spent').text('(' + result + ')');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}