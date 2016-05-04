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
    var getUrl = '/person/finance-history/ajax-finance-history-period';
    var getData = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            initLineChart('payment_history_line_chart_all', result.data['period'], result.data['payment']);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthChart() {
    var getUrl = '/person/finance-history/ajax-finance-history-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            var dataPeriod = result.data['period'];
            var dataPayment = result.data['payment'];
            var lineCanvasId = 'payment_history_line_chart';
            var barCanvasId = 'payment_history_bar_chart';
            initLineChart(lineCanvasId, dataPeriod, dataPayment);
            initBarChart(barCanvasId, dataPeriod, dataPayment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthCategoryChart() {
    var getUrl = '/person/finance-history/ajax-finance-history-month-category';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            var dataCategory = result.data['category'];
            var dataPayment = result.data['payment'];
            var lineCanvasId = 'month_category_payment_history_line_chart';
            var barCanvasId = 'month_category_payment_history_bar_chart';
            initLineChart(lineCanvasId, dataCategory, dataPayment);
            initBarChart(barCanvasId, dataCategory, dataPayment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initYearCategoryChart() {
    var getUrl = '/person/finance-history/ajax-finance-history-year-category';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            var dataCategory = result.data['category'];
            var dataPayment = result.data['payment'];
            var lineCanvasId = 'category_payment_history_line_chart';
            var barCanvasId = 'category_payment_history_bar_chart';
            initLineChart(lineCanvasId, dataCategory, dataPayment);
            initBarChart(barCanvasId, dataCategory, dataPayment);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthSpent() {
    var getUrl = '/person/finance-history/ajax-finance-history-month-spent';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#month_spent').text('(' + result.data.monthSpent + ')');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initYearSpent() {
    var getUrl = '/person/finance-history/ajax-finance-history-year-spent';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#year_spent').text('(' + result.data.yearSpent + ')');
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

$('#day_category_id').on('change', function(){
    $('#btn_search_day').click();
});