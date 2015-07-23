$(document).ready(function() {
    var cookie_href =  $.cookie('nav-top');
    if (typeof cookie_href == 'undefined') {

    } else {
        $('ul.nav.nav-sidebar li ul li').children('a').each(function(){
            if (cookie_href == $(this).attr('href')) {
                $(this).css('color', 'red').css('text-decoration', 'underline');
                $(this).parent().parent().parent().siblings().children('ul').addClass('dropdown-menu');
                $(this).parent().parent().removeClass('dropdown-menu').show();
            }
        });
    }
});

$('#main > ul.nav.nav-sidebar li a').on('click', function() {
    if ($(this).siblings('ul').hasClass('dropdown-menu')) {
        $(this).siblings('ul').removeClass('dropdown-menu').slideDown(300).css('display', 'block');
    } else {
        $(this).siblings('ul').slideUp(300).addClass('dropdown-menu').css('display', 'none');
    }
});

$('ul.nav.nav-sidebar li ul li a').on('click', function() {
    var href = $(this).attr('href');
    $.cookie('nav-top', href, {expires : 1, path : '/'});
});