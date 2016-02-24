$(document).ready(function(){
    var cookie_href =  $.cookie('nav-top');
    if (typeof cookie_href != 'undefined') {
        $('ul.sidebar-menu > li ul li').children('a').each(function(){
            if (cookie_href == $(this).attr('href')) {
                $(this).parent().parent().parent().addClass('active');
                $(this).parent().addClass('active');
            }
        });
    }
});

$('ul.treeview-menu li a').on('click', function(){
    $('li.treeview').removeClass('active');
    $('ul.treeview-menu li').removeClass('active');
    $(this).parent().addClass('active');
    $(this).parent().parent().parent().addClass('active');
    var href = $(this).attr('href');
    $.cookie('nav-top', href, {expires : 1, path : '/'});
});