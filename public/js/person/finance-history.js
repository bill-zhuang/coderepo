$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
    initMonthCategoryChart();
    initYearCategoryChart();
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
            $('#payment_history_line_chart_all').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Finance History(day)'
                },
                xAxis: {
                    categories: result.data.days
                },
                yAxis: {
                    title: {
                        text: 'Finance Spent'
                    }
                },
                series: [
                    {
                        name: 'Finance Spent',
                        data: result.data.data
                    }
                ]
            });
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
            $('#payment_history_month_chart').highcharts({
                title: {
                    text: 'Finance History(month)'
                },
                xAxis: {
                    categories: result.data.months,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Finance Spent'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                series: [
                    {
                        type: 'column',
                        name: 'Finance History(Bar)',
                        data: result.data.data
                    },
                    {
                        name: 'Finance History(Line)',
                        data: result.data.data,
                        marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }
                ]
            });
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
            $('#category_payment_history_month_chart').highcharts({
                title: {
                    text: 'Finance History(last 30 days)---' + result.data.total
                },
                xAxis: {
                    categories: result.data.categories,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Finance Spent'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                series: [
                    {
                        type: 'column',
                        name: 'Finance History(Bar)',
                        data: result.data.data
                    },
                    {
                        name: 'Finance History(Line)',
                        data: result.data.data,
                        marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }
                ]
            });
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
            $('#category_payment_history_year_chart').highcharts({
                title: {
                    text: 'Finance History(last year)---' + result.data.total
                },
                xAxis: {
                    categories: result.data.categories,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Finance Spent'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                series: [
                    {
                        type: 'column',
                        name: 'Finance History(Bar)',
                        data: result.data.data
                    },
                    {
                        name: 'Finance History(Line)',
                        data: result.data.data,
                        marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }
                ]
            });
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

$('#day_category_id').on('change', function () {
    $('#btn_search_day').click();
});