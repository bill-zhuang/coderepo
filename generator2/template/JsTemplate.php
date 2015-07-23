<?php
/* @var $module_name string module name */
/* @var $controller_name string controller name */
/* @var $page_title_name string view page title name */
/* @var $all_batch_id string check all checkbox id */
/* @var $batch_id string checkbox id */
/* @var $table_row_data array html table, key is name, value is data key */
/* @var $primary_id string primary key name */
/* @var $table_data array table fields and default value */
/* @var $form_name_postfix string form postfix name*/
/* @var $form_element_prefix string prefix of form element */
/* @var $view_modal_size string modal size */
/* @var $is_blacklist bool use blacklist or not */
/* @var $is_ckeditor bool use ckeditor or not */

$table_keys = array_keys($table_data);
?>
$(document).ready(function() {
    ajaxIndex();
});

<?php if ($primary_id !== ''){ ?>
function ajaxIndex() {
    var get_url = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/ajax-index';
    var get_data = $.param($('#formSearch').serializeArray());
    var method = 'get';
    var success_function = function(result){
        $('#tbl tbody').empty();
        for (var i = 0, len = result.data.length; i < len; i++) {
            $('#tbl tbody').append(
                $('<tr>')
                    .append($('<td>').text(result.start + i + 1))
<?php foreach($table_row_data as $value){ ?>
                    .append($('<td>').text(result.data[i]['<?php echo $value; ?>']))
<?php } ?>
                    .append($('<td>')
                    .append($('<a>', {href: '#', id:'modify_' + result.data[i]['<?php echo $primary_id; ?>'], text: '修改'})
                        .click(function(){modify<?php echo $form_name_postfix; ?>(this.id);})
                    )
                    .append('  ')
                    .append($('<a>', {href: '#', id:'delete_' + result.data[i]['<?php echo $primary_id; ?>'], text: '删除'})
                        .click(function(){delete<?php echo $form_name_postfix; ?>(this.id);})
                    )
                )
            );
        }
        if (result.total == 0) {
            $('#tbl tbody').append($('<tr>')
                .append(
                    $('<td>').text('对不起,没有符合条件的数据').addClass('bill_table_no_data').attr('colspan', <?php echo (2 + ($all_batch_id ==='' ? 0 : 1) + ($batch_id === '' ? 0 : 1) + count($table_row_data)); ?>)
                )
            );
        }
        //init pagination
        initPagination(result.total_pages, result.current_page);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}
/*  --------------------------------------------------------------------------------------------------------  */
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
        }
    });
}

$('#page_length').on('change', function() {
    $('#current_page').val(1);
    ajaxIndex();
});

$('#btn_search').on('click', function(event) {
    event.preventDefault();
    $('#current_page').val(1);
    ajaxIndex();
});
<?php } ?>
/*  --------------------------------------------------------------------------------------------------------  */
$('#btn_add').on('click', function(){
    window.form<?php echo $form_name_postfix; ?>.reset();
<?php if ($is_ckeditor){ ?>
    CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.setData('');
<?php } ?>
    $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', false);
    $('#modal<?php echo $form_name_postfix; ?>').modal('show');
});

$('#form<?php echo $form_name_postfix; ?>').on('submit', (function(event){
    event.preventDefault();

    var <?php echo $primary_id; ?> = $('#<?php echo $form_element_prefix; ?>_<?php echo $primary_id; ?>').val();
    var type = (<?php echo $primary_id; ?> == '') ? 'add' : 'modify';
    var error_num = validInput(type);
    if(error_num == 0) {
        $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', true);
<?php if ($is_ckeditor){ ?>
        var content = $.trim(CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.getData());
        $('#<?php echo $form_element_prefix; ?>_intro').val(content);
<?php } ?>
        var post_url = '/<?php echo $module_name; ?>/<?php echo strtolower($controller_name); ?>/' + type +'-<?php echo strtolower($controller_name); ?>';
        var post_data = new FormData(this);
        var msg_success = (<?php echo $primary_id; ?> == '') ? MESSAGE_ADD_SUCCESS : MESSAGE_MODIFY_SUCCESS;
        var msg_error = (<?php echo $primary_id; ?> == '') ? MESSAGE_ADD_ERROR : MESSAGE_MODIFY_ERROR;
        var method = 'post';
        var success_function = function(result){
            $('#modal<?php echo $form_name_postfix; ?>').modal('hide');
            if (parseInt(result) != 0) {
                alert(msg_success);
            } else {
                alert(msg_error);
            }
            ajaxIndex();
        };
        callAjaxWithFormAndFunction(post_url, post_data, success_function, method);
    }
}));

function modify<?php echo $form_name_postfix; ?>(modify_id) {
    var <?php echo $primary_id; ?> = modify_id.substr('modify_'.length);
    var get_url = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/get-<?php echo strtolower($controller_name); ?>';
    var get_data = {
        '<?php echo $primary_id; ?>' : <?php echo $primary_id . PHP_EOL; ?>
    };
    var method = 'get';
    var success_function = function(result){
<?php foreach ($table_data as $key => $default_value)
{
    echo str_repeat(' ', 4 * 2) . "$('#" . $form_element_prefix . '_' . $key . "').val(result." . $key . ");" . PHP_EOL;
}
?>
<?php if ($is_ckeditor){ ?>
    CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.setData(result.{table_prefix}_intro);
<?php } ?>
        $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', false);
        $('#modal<?php echo $form_name_postfix; ?>').modal('show');
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function delete<?php echo $form_name_postfix; ?>(delete_id) {
    if (confirm(MESSAGE_DELETE_CONFIRM)) {
        var <?php echo $primary_id; ?> = delete_id.substr('delete_'.length);
        var post_url = '<?php echo $module_name == '' ? '' : '/' . $module_name; ?>/<?php echo strtolower($controller_name); ?>/delete-<?php echo strtolower($controller_name); ?>';
        var post_data = {
            '<?php echo $primary_id; ?>' : <?php echo $primary_id . PHP_EOL; ?>
        };
        var method = 'post';
        var success_function = function(result){
            if (parseInt(result) > 0) {
                alert(MESSAGE_DELETE_SUCCESS);
            } else {
                alert(MESSAGE_DELETE_ERROR);
            }
            ajaxIndex();
        };
        callAjaxWithFunction(post_url, post_data, success_function, method);
    }
}

function validInput(type)
{
    var error_num = 0;
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){
            echo str_repeat(' ', 4 * 1) . 'var image = $(\'#' . $form_element_prefix . '_image\').val();' . PHP_EOL;
        } else {
            echo str_repeat(' ', 4 * 1) . 'var '. $key . ' = $(\'#' . $form_element_prefix . '_' . $key . '\').val();' . PHP_EOL;
        }
    }
}
?>
<?php if ($is_ckeditor){ ?>
    var content = $.trim(CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.getData());
<?php } ?>
<?php
$table_keys_no_pkid = [];
foreach ($table_keys as $table_key)
{
    if ($table_key != $primary_id)
    {
        $table_keys_no_pkid[] = $table_key;
    }
}
foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 1) : 'else ') . 'if (type == \'add\' && image == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'error_num = error_num + 1;' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'alert(MESSAGE_UPLOAD_IMAGE_ERROR)' . PHP_EOL;
            echo str_repeat(' ', 4 * 1) . '} ';
        } else {
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 1) : 'else ') . 'if (' . $key . ' == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'error_num = error_num + 1;' . PHP_EOL;
            echo str_repeat(' ', 4 * 2) . 'alert(\'todo set alert message\')' . PHP_EOL;
            echo str_repeat(' ', 4 * 1) . '} ';
        }
    }
}
?>
<?php if ($is_ckeditor){ ?>else if(content == '') {
    error_num = error_num + 1;
    alert(MESSAGE_CONTENT_ERROR);
    }<?php } ?>

    return error_num;
}
<?php if($all_batch_id !== ''){ ?>
/*  --------------------------------------------------------------------------------------------------------  */
$('#<?php echo $all_batch_id; ?>').on('click', function(){
    batchMute(this, '<?php echo $batch_id; ?>');
});

$('input[name="<?php echo $batch_id; ?>"]').on('click', function(){
    closeBatch(this, '<?php echo $all_batch_id; ?>');
});
<?php } ?>