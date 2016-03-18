function initPagination(total_pages, current_page) {
    $('#div_pagination').empty().append('<ul id="pagination" class="pagination-md"></ul>');
    $('#pagination').twbsPagination({
        totalPages: total_pages,
        startPage: current_page,
        visiblePages: 7,
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '尾页',
        onPageClick: function (event, page) {
            $('#current_page').val(page);
            ajaxIndex();
            $('html, body').animate({'scrollTop' : 0}, 'normal');
        }
    });
}

$('#page_length').on('change', function () {
    $('#current_page').val(1);
    ajaxIndex();
});

$('#btn_search').on('click', function (event) {
    event.preventDefault();
    $('#current_page').val(1);
    ajaxIndex();
});