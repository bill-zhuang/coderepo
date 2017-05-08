$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var getUrl = '/crawler/house-sale/ajax-house-sale-day';
    var getData = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#house_sale_line_chart_all').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'House Sale(day)'
                },
                xAxis: {
                    categories: result.data.days
                },
                yAxis: {
                    title: {
                        text: 'Sales'
                    }
                },
                series: [
                    {
                        name: 'Sales',
                        data: result.data.data
                    }
                ]
            });
            $('#day_start_date').val(result.searchData.startDate);
            $('#day_end_date').val(result.searchData.endDate);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthChart() {
    var getUrl = '/crawler/house-sale/ajax-house-sale-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#house_sale_month_chart').highcharts({
                title: {
                    text: 'House Sale(month)'
                },
                xAxis: {
                    categories: result.data.months,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Sale'
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
                        name: 'House Sale(Bar)',
                        data: result.data.data
                    },
                    {
                        name: 'House Sale(Line)',
                        data: result.data.data,
                        marker: {
                            lineWidth: 2,
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }
                ]
            });
            $('#month_start_date').val(result.searchData.startDate);
            $('#month_end_date').val(result.searchData.endDate);
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