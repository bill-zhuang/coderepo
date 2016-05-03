$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
    initMonthCategoryChart();
    initYearCategoryChart();
    initMonthSpent();
    initYearSpent();
    loadMainCategory('day_category_id');
});

function initPeriodChart() {
    var get_url = '/person/finance-history/ajax-finance-history-period';
    var get_data = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            initLineChart('payment_history_line_chart_all', result.data['period'], result.data['payment']);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function initMonthChart() {
    var get_url = '/person/finance-history/ajax-finance-history-month';
    var get_data = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            var data_period = result.data['period'];
            var data_payment = result.data['payment'];
            var line_canvas_id = 'payment_history_line_chart';
            var bar_canvas_id = 'payment_history_bar_chart';
            initLineChart(line_canvas_id, data_period, data_payment);
            initBarChart(bar_canvas_id, data_period, data_payment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function initMonthCategoryChart() {
    var get_url = '/person/finance-history/ajax-finance-history-month-category';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            var data_category = result.data['category'];
            var data_payment = result.data['payment'];
            var line_canvas_id = 'month_category_payment_history_line_chart';
            var bar_canvas_id = 'month_category_payment_history_bar_chart';
            initLineChart(line_canvas_id, data_category, data_payment);
            initBarChart(bar_canvas_id, data_category, data_payment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function initYearCategoryChart() {
    var get_url = '/person/finance-history/ajax-finance-history-year-category';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            var data_category = result.data['category'];
            var data_payment = result.data['payment'];
            var line_canvas_id = 'category_payment_history_line_chart';
            var bar_canvas_id = 'category_payment_history_bar_chart';
            initLineChart(line_canvas_id, data_category, data_payment);
            initBarChart(bar_canvas_id, data_category, data_payment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function initMonthSpent() {
    var get_url = '/person/finance-history/ajax-finance-history-month-spent';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            $('#month_spent').text('(' + result.data.monthSpent + ')');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

function initYearSpent() {
    var get_url = '/person/finance-history/ajax-finance-history-year-spent';
    var get_data = {
        "params": {}
    };
    var method = 'get';
    var success_function = function (result) {
        if (typeof result.data != "undefined") {
            $('#year_spent').text('(' + result.data.yearSpent + ')');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(get_url, get_data, success_function, method);
}

$('#btn_search_day').on('click', function (event) {
    event.preventDefault();
    initPeriodChart();
});

$('#btn_search_month').on('click', function (event) {
    event.preventDefault();
    initMonthChart();
});

$('#day_category_id').on('change', function(){
    $('#btn_search_day').click();
});