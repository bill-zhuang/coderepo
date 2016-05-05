$(document).ready(function () {
    var cookieHref = $.cookie('nav-top');
    if (typeof cookieHref != 'undefined') {
        $('ul.sidebar-menu > li ul li').children('a').each(function () {
            if (cookieHref == $(this).attr('href')) {
                $(this).parent().parent().parent().addClass('active');
                $(this).parent().addClass('active');
            }
        });
    }
    //scroll up
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#go-top').stop().animate({
                bottom: '25px'
            }, 500);
        } else {
            $('#go-top').stop().animate({
                bottom: '-100px'
            }, 500);
        }
    });
});

$('ul.treeview-menu li a').on('click', function () {
    $('li.treeview').removeClass('active');
    $('ul.treeview-menu li').removeClass('active');
    $(this).parent().addClass('active');
    $(this).parent().parent().parent().addClass('active');
    var href = $(this).attr('href');
    $.cookie('nav-top', href, {expires: 1, path: '/'});
});

$('#go-top').click(function () {
    $('html, body').stop().animate({
        scrollTop: 0
    }, 500, function () {
        $('#go-top').stop().animate({
            bottom: '-100px'
        }, 500);
    });
});