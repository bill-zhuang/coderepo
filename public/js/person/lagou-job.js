$(document).ready(function () {
    loadLagouMainCategory('mainCaid', true);
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/person/lagou-job/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function (result) {
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $tblTbody.append(
                    $('<tr>')
                        .append($('<td>').text(result.data.startIndex + i))
                        .append($('<td>')
                            .append($('<a>', {
                                href: 'https://' + result.data.items[i]['url'],
                                text: result.data.items[i]['name'],
                                target: '_blank'
                            }))
                        )
                        .append($('<td>').text(result.data.items[i]['main']))
                        .append($('<td>').text(result.data.items[i]['sub']))
                );
            }
            if (result.data.totalItems == 0) {
                $tblTbody.append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', 4)
                    )
                );
            }
            //init pagination
            initPagination(result.data.totalPages, result.data.pageIndex);
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

$('#mainCaid').on('change', function() {
    if (this.value > 0) {
        loadLagouSubCategory('subCaid', this.value, true);
    } else {
        $('#subCaid').empty().append('<option value="0">全部</option>');
    }
});
/*  --------------------------------------------------------------------------------------------------------  */