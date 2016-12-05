$(document).ready(function () {
    initAlphabat();
    loadLagouMainCategory('mainCaid');
    $('#jobNumIgnore').prop('checked', false);
    ignoreJobNum(false);
});

function ajaxIndex() {
    var joid = $('#joid').val();
    var lgCtid = $('#lgCtid').val();
    if (joid == '' && lgCtid == '') {
        alert('Job和城市不能同时为全部');
        return;
    }

    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/lagou-job-analysis/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            $('#job_analysis_chart').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Job Analysis'
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
                        text: 'Job Num'
                    },
                    min: 0,
                    tickInterval: 1
                },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: '{point.x:%b %e}: {point.y:f} Jobs'
                },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },
                series: result.data.lineData
            });
            $('#job_analysis_bar_chart').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Job Analysis'
                },
                xAxis: {
                    categories: result.data.days,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Job Num'
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
                series: result.data.barData
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

$('#mainCaid').on('change', function() {
    if (this.value > 0) {
        loadLagouSubCategory('subCaid', this.value, true);
    } else {
        $('#subCaid').empty().append('<option value="0">全部</option>');
        $('#joid').empty().append('<option value="0">全部</option>');
    }
});

$('#subCaid').on('change', function() {
    if (this.value > 0) {
        var getUrl = '/person/lagou-job/get-job-list';
        var getData = {
            "params": {
                'caid': this.value
            }
        };
        var method = 'get';
        var successFunc = function (result) {
            if (typeof result.data != "undefined") {
                $('#joid').empty().append('<option value="0">全部</option>');
                for (var i = 0; i < result.data.currentItemCount; i++) {
                    $('#joid').append($('<option>', {
                        value: result.data.items[i]['joid'],
                        text: result.data.items[i]['name']
                    }));
                }
            } else {
                alert(result.error.message);
            }
        };
        jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
    } else {
        $('#joid').empty().append('<option value="0">全部</option>');
    }
});

function initAlphabat() {
    for (var i = 65; i <= 90; i++) {
        $('#cityFirstLetter').append('<option>' + String.fromCharCode(i) + '</option>');
    }
}

$('#cityFirstLetter').on('change', function() {
    if (this.value != '') {
        loadLagouCity('lgCtid', this.value);
    } else {
        $('#lgCtid').empty().append('<option value="0">全部</option>');
    }
});

$('#jobNumIgnore').on('click', function () {
    ignoreJobNum($(this).prop('checked'));
});

function ignoreJobNum(ignore) {
    if (ignore) {
        $('#jobNumMin').prop('disabled', false).val(480);
    } else {
        $('#jobNumMin').prop('disabled', true).val('');
    }
}
/*  --------------------------------------------------------------------------------------------------------  */