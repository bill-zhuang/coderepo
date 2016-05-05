<?php
/* @var $module_name string module name */
/* @var $controller_name string controller name */
/* @var $page_title_name string view page title name */
/* @var $all_batch_id string check all checkbox id */
/* @var $batch_id string checkbox id */
/* @var $table_row_data array html table, key is name, value is data key */
/* @var $primary_id array primary key name */
/* @var $table_data array table fields and default value */
/* @var $form_name_postfix string form postfix name*/
/* @var $form_element_prefix string prefix of form element */
/* @var $view_modal_size string modal size */
/* @var $using_ckeditor bool use ckeditor or not */
/* @var $tab_types array tab types for select */
/* @var $default_tab_value mixed default selected tab value */

$table_keys = array_keys($table_data);
?>
$(document).ready(function () {
    ajaxIndex();
});

<?php if ($primary_id[0] !== ''){ ?>
function ajaxIndex() {
    var $tblTbody = $('#tbl').find('tbody');
    var getUrl = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/ajax-index';
    var getData = {
        "params": $('#formSearch').serializeObject()
    };
    var method = 'get';
    var successFunc = function(result){
        $tblTbody.empty();
        if (typeof result.data != "undefined") {
            for (var i = 0; i < result.data.currentItemCount; i++) {
                $tblTbody.append(
                    $('<tr>')
<?php if($all_batch_id !== ''){ ?>
                        .append(
                            $('<td>').append(
                                $('<input>', {type: 'checkbox', name: '<?php echo $primary_id[0]; ?>', value: result.data.items[i]['<?php echo $primary_id[0]; ?>']})
                                    .click(function(){closeBatch(this, '<?php echo $batch_id; ?>')})
                            )
                        )
<?php } ?>
                        .append($('<td>').text(result.data.startIndex + i))
<?php foreach($table_row_data as $value){ ?>
                        .append($('<td>').text(result.data.items[i]['<?php echo $value; ?>']))
<?php } ?>
                        .append($('<td>')
                            .append($('<a>', {href: '#', id:'modify_' + result.data.items[i]['<?php echo $primary_id[0]; ?>'], text: '修改'})
                                .click(function () {
                                    modify<?php echo $form_name_postfix; ?>(this.id);
                                })
                            )
                            .append('  ')
                            .append($('<a>', {href: '#', id:'delete_' + result.data.items[i]['<?php echo $primary_id[0]; ?>'], text: '删除'})
                                .click(function () {
                                    delete<?php echo $form_name_postfix; ?>(this.id);
                                })
                            )
                    )
                );
            }
            if (result.data.totalItems == 0) {
                $tblTbody.append($('<tr>')
                    .append(
                        $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', <?php echo (2 + ($all_batch_id ==='' ? 0 : 1) + ($batch_id === '' ? 0 : 1) + count($table_row_data)); ?>)
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
<?php } ?>
<?php if(!empty($tab_types)){ ?>

$('#ul_tab_type').find('li').on('click', function () {
    var tabValue = parseInt($(this).attr('id').substr('li_tab_type_'.length));
    tabValue = isNaN(tabValue) ? <?php echo ($default_tab_value == '') ? 0 : $default_tab_value; ?> : tabValue;
    $('#tab_type').val(tabValue);
    $('#ul_tab_type').find('li').removeClass('active');
    $('#li_tab_type_' + tabValue).addClass('active');
    $('#current_page').val(1);
    ajaxIndex();
});
<?php } ?>
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function () {
    window.form<?php echo $form_name_postfix; ?>.reset();
<?php if ($using_ckeditor){ ?>
    CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.setData('');
<?php } ?>
    $('#<?php echo $form_element_prefix; ?>_<?php echo $primary_id[0]; ?>').val('');
    $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', false);
    $('#modal<?php echo $form_name_postfix; ?>').modal('show');
});

$('#form<?php echo $form_name_postfix; ?>').on('submit', (function (event) {
    event.preventDefault();

    var <?php echo $primary_id[0]; ?> = $('#<?php echo $form_element_prefix; ?>_<?php echo $primary_id[0]; ?>').val();
    var type = (<?php echo $primary_id[0]; ?> == '') ? 'add' : 'modify';
    if (isValidInput(type)) {
        $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', true);
<?php if ($using_ckeditor){ ?>
        var content = $.trim(CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.getData());
        $('#<?php echo $form_element_prefix; ?>_intro').val(content);
<?php } ?>
        var postUrl = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/' + type + '-<?php echo strtolower($controller_name); ?>';
        var postData = {
            "params": $('#form<?php echo $form_name_postfix; ?>').serializeObject()
        };
        var method = 'post';
        var successFunc = function(result) {
            $('#modal<?php echo $form_name_postfix; ?>').modal('hide');
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

function modify<?php echo $form_name_postfix; ?>(modifyId) {
    var <?php echo $primary_id[0]; ?> = modifyId.substr('modify_'.length);
    var getUrl = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/get-<?php echo strtolower($controller_name); ?>';
    var getData = {
        "params": {
            "<?php echo $primary_id[0]; ?>" : <?php echo $primary_id[0] . PHP_EOL; ?>
        }
    };
    var method = 'get';
    var successFunc = function(result){
        if (typeof result.data != 'undefined') {
<?php foreach ($table_data as $key => $default_value)
{
    if (strpos($key, 'create_time') === false && strpos($key, 'update_time') === false && strpos($key, 'status') === false)
    {
        echo str_repeat(' ', 4 * 3) . "$('#" . $form_element_prefix . '_' . $key . "').val(result.data." . $key . ");" . PHP_EOL;
    }
}
?>
<?php if ($using_ckeditor){ ?>
        CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.setData(result.data.{table_prefix}_intro);
<?php } ?>
            $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', false);
            $('#modal<?php echo $form_name_postfix; ?>').modal('show');
        } else {
            alert(result.error.message);
        }
    };
    jAjaxWidget.additionFunc(getUrl, getData, successFunc, method);
}

function delete<?php echo $form_name_postfix; ?>(deleteId) {
    if (confirm(alertMessage.DELETE_CONFIRM)) {
        var <?php echo $primary_id[0]; ?> = deleteId.substr('delete_'.length);
        var postUrl = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/delete-<?php echo strtolower($controller_name); ?>';
        var postData = {
            "params": {
                "<?php echo $primary_id[0]; ?>" : <?php echo $primary_id[0] . PHP_EOL; ?>
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
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id[0])
    {
        if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){
            echo str_repeat(' ', 4 * 1) . 'var image = $(\'#' . $form_element_prefix . '_image\').val();' . PHP_EOL;
        } else if (strpos($key, 'create_time') === false && strpos($key, 'update_time') === false && strpos($key, 'status') === false) {
            echo str_repeat(' ', 4 * 1) . 'var '. $key . ' = $(\'#' . $form_element_prefix . '_' . $key . '\').val();' . PHP_EOL;
        }
    }
}
?>
<?php if ($using_ckeditor){ ?>
    var content = $.trim(CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.getData());
<?php } ?>
<?php
$table_keys_no_pkid = [];
foreach ($table_keys as $table_key)
{
    if ($table_key != $primary_id[0])
    {
        $table_keys_no_pkid[] = $table_key;
    }
}
foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id[0])
    {
        if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 1) : 'else ') . 'if (type == \'add\' && image == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'isVerified = false;' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'alert(alertMessage.UPLOAD_IMAGE_ERROR)' . PHP_EOL;
            echo str_repeat(' ', 4 * 1) . '} ';
        } else if (strpos($key, 'create_time') && strpos($key, 'update_time') && strpos($key, 'status') === false) {
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 1) : 'else ') . 'if (' . $key . ' == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'isVerified = false;' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'alert(\'todo set alert message\')' . PHP_EOL;
            echo str_repeat(' ', 4 * 1) . '} ';
        }
    }
}
?>
<?php if ($using_ckeditor){ ?>else if(content == '') {
    isVerified = false;
    alert(alertMessage.CONTENT_ERROR);
    }<?php } ?>

    return isVerified;
}
<?php if($all_batch_id !== ''){ ?>
/*  --------------------------------------------------------------------------------------------------------  */
$('#<?php echo $all_batch_id; ?>').on('click', function () {
    batchMute(this, '<?php echo $batch_id; ?>');
});
<?php } ?>