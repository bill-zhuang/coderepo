$('#baidu_music_url').on('keydown', function(event) {
    if (event.keyCode == 13) {
        $('#btn_generate_download_link').click();
    }
});

$('#btn_generate_download_link').on('click', function() {
    var music_url = $.trim($('#baidu_music_url').val());
    if(music_url != '') {
        var get_url = '/index/downloadbaidumusic';
        var get_data = {
            'downlink' : music_url
        };
        var method = 'get';
        var success_function = function(music_result){
            //console.log(music_result);
            if(music_result == '') {
                alert('generate download link failed!');
            } else {
                $('#generated_baidu_music_url').attr('href', music_result).text(music_result);
            }
        };
        callAjaxWithFunction(get_url, get_data, success_function, method);
    } else {
        alert('music url is empty!');
    }
});

$('#url').on('keydown', function(event) {
    if (event.keyCode == 13) {
        $('#btn_generate_url').click();
    }
});

$('#btn_generate_url').on('click', function() {
    var url = $('#url').val();
    if(url != '') {
        url = (url.indexOf('http://') == 0 || url.indexOf('https://') == 0) ? url : ('http://' + url);
        $('#generated_url').attr('href', url).text(url);
    } else {
        alert('url is empty!');
    }
});