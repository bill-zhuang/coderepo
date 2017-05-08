$(document).ready(function () {
    loadETFFunds();
    //ajaxIndex();
});

function ajaxIndex() {
    var getUrl = '/crawler/etf-fund-analysis/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#eft_fund_analysis_chart').highcharts({
                chart: {
                    zoomType: 'x',
                    type: 'area'
                },
                title: {
                    text: 'ETF Fund Analysis'
                },
                subtitle: {
                    text: document.ontouchstart === undefined ?
                        '鼠标拖动可以进行缩放' : '手势操作进行缩放'
                },
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: {
                        millisecond: '%H:%M:%S.%L',
                        second: '%H:%M:%S',
                        minute: '%H:%M',
                        hour: '%H:%M',
                        day: '%m-%d',
                        week: '%m-%d',
                        month: '%Y-%m',
                        year: '%Y'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Net Value'
                    }
                },
                tooltip: {
                    type: 'datetime',
                    dateTimeLabelFormats: {
                        millisecond: '%H:%M:%S.%L',
                        second: '%H:%M:%S',
                        minute: '%H:%M',
                        hour: '%H:%M',
                        day: '%Y-%m-%d',
                        week: '%m-%d',
                        month: '%Y-%m',
                        year: '%Y'
                    },
                    shared: true
                },
                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null,
                        fillOpacity: 0.5
                    }
                },
                series: [
                    {
                        name: 'Unit Net Value',
                        data: result.data.unitNetData
                    },
                    {
                        name: 'Accum Net Value',
                        data: result.data.accumNetData
                    }
                ]
            });
            $('#startDate').val(result.searchData.startDate);
            $('#endDate').val(result.searchData.endDate);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

$('#btn_search').on('click', function (event) {
    event.preventDefault();
    ajaxIndex();
});

function loadETFFunds() {
    var getUrl = '/crawler/etf-fund/get-fund-list';
    var getData = {
        "params": {}
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != "undefined") {
            $('#fuid').empty().append('<option value="0">请选择ETF基金</option>');
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $('#fuid').append($('<option>', {
                    value: result.data.items[i]['fuid'],
                    text: result.data.items[i]['name']
                }));
            }
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}
/*  --------------------------------------------------------------------------------------------------------  */