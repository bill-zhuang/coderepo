$('.form_date').datetimepicker({
    format: 'yyyy-mm-dd',
    todayBtn:  'linked',
    todayHighlight: 1,
    language: 'zh-CN',
    autoclose: 1,
    minView: 2 //needed, or show time
});

$('.form_datetime').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    todayBtn:  'linked',
    todayHighlight: 1,
    language: 'zh-CN',
    autoclose: 1
});