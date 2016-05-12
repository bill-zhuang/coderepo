$(document).ready(function () {
    initPeriodChart();
    initMonthChart();
});

function initPeriodChart() {
    var getUrl = '/person/eject-history-chart/ajax-eject-history-period';
    var getData = {
        "params": $('#formSearchDay').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#eject_history_line_chart_all').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Eject History(day)'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: { // don't display the dummy year
                        month: '%e. %b',
                        year: '%b'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Eject Day Interval'
                    },
                    min: 0,
                    tickInterval: 1
                },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: '{point.x:%b %e}: {point.y:f} days'
                },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },
                series: result.data
            });
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function initMonthChart() {
    var getUrl = '/person/eject-history-chart/ajax-eject-history-month';
    var getData = {
        "params": $('#formSearchMonth').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#eject_history_line_chart').highcharts({
                title: {
                    text: 'Eject History(month)'
                },
                xAxis: {
                    categories: result.data.months
                },
                yAxis: {
                    title: {
                        text: 'Eject Count'
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
                series: result.data.data
            });

            $('#eject_history_bar_chart').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Eject History(month)'
                },
                xAxis: {
                    categories: result.data.months,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Eject Count'
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
                series: result.data.data
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