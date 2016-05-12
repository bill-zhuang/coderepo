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
        if (typeof result.data != "undefined") {
            $('#grain_recycle_history_line_chart_all').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Grain Recycle History(day)'
                },
                xAxis: {
                    categories: result.data.days
                },
                yAxis: {
                    title: {
                        text: 'Grain Recycle Count'
                    },
                    plotLines: [
                        {
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }
                    ],
                    tickInterval: 1
                },
                series: [
                    {
                        name: 'Grain Recycle Count',
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
    var getUrl = '/person/grain-recycle-history-chart/ajax-grain-recycle-history-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#grain_recycle_history_month_chart').highcharts({
                title: {
                    text: 'Grain Recycle History(month)'
                },
                xAxis: {
                    categories: result.data.months,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Grain Recycle Count'
                    },
                    tickInterval: 1
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
                        name: 'Grain Recycle History(Bar)',
                        data: result.data.data
                    },
                    {
                        name: 'Grain Recycle History(Line)',
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