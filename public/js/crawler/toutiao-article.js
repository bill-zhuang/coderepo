$(document).ready(function () {
    ajaxIndex();
});

function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '/crawler/toutiao-article/ajax-index';
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
                                href: result.data.items[i]['url'],
                                text: result.data.items[i]['title'],
                                target: '_blank'
                            })))
                        .append($('<td>').text(result.data.items[i]['date']))
                        .append($('<td>')
                            .append($('<a>', {href: '#', id: 'modify_' + result.data.items[i]['ttid'], text: '修改'})
                                .click(function () {
                                    modifyToutiaoArticle(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id: 'delete_' + result.data.items[i]['ttid'], text: '删除'})
                                .click(function () {
                                    deleteToutiaoArticle(this.id);
                                })
                            )
                        )
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
/*  --------------------------------------------------------------------------------------------------------  */
$('#formToutiaoArticle').on('submit', (function (event) {
    event.preventDefault();

    if (isValidInput('modify')) {
        $('#btn_submit_toutiao_article').attr('disabled', true);
        var postUrl = '/crawler/toutiao-article/modify-toutiao-article';
        var postData = {
            "params": $('#formToutiaoArticle').serializeObject()
        };
        var method = 'post';
        var successFunc = function (result) {
            $('#modalToutiaoArticle').modal('hide');
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
}));

function modifyToutiaoArticle(modifyId) {
    var ttid = modifyId.substr('modify_'.length);
    var getUrl = '/crawler/toutiao-article/get-toutiao-article';
    var getData = {
        "params": {
            "ttid": ttid
        }
    };
    var method = 'get';
    var successFunc = function (result) {
        if (typeof result.data != 'undefined') {
            $('#toutiao_article_ttid').val(result.data.ttid);
            $('#toutiao_article_date').val(result.data.date);
            $('#toutiao_article_title').val(result.data.title);
            $('#toutiao_article_url').val(result.data.url);
            $('#btn_submit_toutiao_article').attr('disabled', false);
            $('#modalToutiaoArticle').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function deleteToutiaoArticle(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var ttid = deleteId.substr('delete_'.length);
        var postUrl = '/crawler/toutiao-article/delete-toutiao-article';
        var postData = {
            "params": {
                "ttid": ttid
            }
        };
        var method = 'post';
        var successFunc = function (result) {
            if (typeof result.data != 'undefined') {
                alert(result.data.message);
            } else {
                alert(result.error.message);
            }
            ajaxIndex();
        };
        jAjaxWidget.additionFunc(postUrl, postData, successFunc, method);
    }
}

function isValidInput(type) {
    var isVerified = true;
    var date = $('#toutiao_article_date').val();
    var title = $('#toutiao_article_title').val();
    var url = $('#toutiao_article_url').val();

    return isVerified;
}
