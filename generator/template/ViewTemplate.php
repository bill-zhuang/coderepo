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
/* @var $is_blacklist bool use blacklist or not */
/* @var $is_ckeditor bool use ckeditor or not */

$table_keys = array_keys($table_data);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link href="/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen"/>
        <link href="/css/common.css" rel="stylesheet" />
    </head>
    <body>
        <div class="panel panel-warning">
            <!-- panel heading -->
            <div class="panel-heading">
                <h2><?php echo $page_title_name; ?></h2>
            </div>
            <!-- panel body -->
            <div class="panel-body">
                <div class="row">
<?php if ($primary_id !== ''){ ?>
                    <form action="/<?php echo $module_name; ?>/<?php echo strtolower($controller_name); ?>/index" method="get" id="formSearch" class="form-inline">
                        <div class="col-sm-10 col-md-10 col-lg-10">
                            关键字: <input type="text" class="form-control" id="keyword" name="keyword"/>
                            <button class="btn btn-primary" type="submit" id="btn_search">
                                <span class="glyphicon glyphicon-search"></span>
                                <span>搜索</span>
                            </button>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn btn-success" type="button" id="btn_add">
                                <span class="glyphicon glyphicon-plus"></span>
                                <span>新增</span>
                            </button>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($is_blacklist){ ?>
                            <button class="btn btn-warning" type="button" id="btn_blacklist">
                                <span class="glyphicon glyphicon-warning-sign"></span>
                                <span>黑名单</span>
                            </button>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
<?php if($all_batch_id !== ''){ ?>
                            <button class="btn btn-danger" type="button" id="btn_batch_delete">
                                <span class="glyphicon glyphicon-trash"></span>
                                <span>批量删除</span>
                            </button>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 text-right">
                            <select name="page_length" id="page_length" class="form-control">
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="75">75</option>
                                <option value="100">100</option>
                            </select>
                            &nbsp;<label>每页</label>
                        </div>
                        <input type="hidden" id="current_page" name="current_page"/>
                    </form>
<?php } ?>
                </div><hr>
<?php if($primary_id !== ''){ ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <table class="table table-striped table-bordered bill_table text-center">
                            <tr><?php if($all_batch_id !== ''){ echo PHP_EOL; ?>
                                <td><input type="checkbox" id="<?php echo $all_batch_id; ?>" name="<?php echo $all_batch_id; ?>"/></td>
                                <?php } ?>
                                <?php
                                echo PHP_EOL . str_repeat(' ', 4 * 8) . '<td>序号</td>' . PHP_EOL;
                                foreach ($table_row_data as $key => $value)
                                {
                                    echo str_repeat(' ', 4 * 8) . '<td>' . $key . '</td>' . PHP_EOL;
                                }
                                ?>
                                <td>操作</td>
                            </tr>
                            <tbody>
                            <?php echo '<?php for($i = 0, $len = count($this->data); $i < $len; $i++){ ?>' . PHP_EOL; ?>
                                <tr><?php if($batch_id !== ''){ echo PHP_EOL; ?>
                                    <td><input type="checkbox" value="<?php echo '<?php echo $this->data[$i][\'' . $primary_id . '\']; ?>'; ?>" name="<?php echo $batch_id; ?>"/></td>
                                    <?php } ?>
                                    <?php
                                    echo PHP_EOL . str_repeat(' ', 4 * 9) . '<td><?php echo ($this->js_data[\'start\'] + $i + 1); ?></td>' . PHP_EOL;
                                    foreach ($table_row_data as $value)
                                    {
                                        echo str_repeat(' ', 4 * 9) . '<td><?php echo $this->data[$i][\'' . $value . '\']; ?></td>' . PHP_EOL;
                                    }
                                    ?>
                                    <td>
                                        <a href="#" id="<?php echo '<?php echo \'modify_\' . ' . '$this->data[$i][\'' . $primary_id . '\']; ?>'; ?>">修改</a>
                                        <a href="#" id="<?php echo '<?php echo \'delete_\' . ' . '$this->data[$i][\'' . $primary_id . '\']; ?>'; ?>">删除</a>
                                    </td>
                                </tr>
                            <?php echo '<?php } ?>'; ?>

                            <?php echo '<?php if (count($this->data) == 0) { ?>' . PHP_EOL; ?>
                                <tr>
                                    <td colspan="<?php echo (2 + ($all_batch_id ==='' ? 0 : 1) + ($batch_id === '' ? 0 : 1) + count($table_row_data)); ?>" class="bill_table_no_data">对不起,没有符合条件的数据</td>
                                </tr>
                            <?php echo '<?php } ?>' . PHP_EOL; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- panel footer -->
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-6 text-left">
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 text-right">
                        <ul id="pagination" class="pagination-md"></ul>
                    </div>
                </div>
            </div>
<?php } ?>
        </div>
<?php if ($primary_id !== ''){ ?>
        <!-- modal -->
        <div id="modal<?php echo $form_name_postfix; ?>" class="modal fade" >
            <div class="modal-dialog bill_modal_lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <span>新增/修改</span>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <form id="form<?php echo $form_name_postfix; ?>" action="#" method="post" enctype="multipart/form-data" class="form-inline">
                        <div class="modal-body">
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        echo str_repeat(' ', 4 * 7) . '<div class="input-group">' . PHP_EOL;
        echo str_repeat(' ', 4 * 8) . '<span class="input-group-addon">' . $key . '：</span>' . PHP_EOL;
        echo str_repeat(' ', 4 * 8) . '<input type="text" name="' . $form_element_prefix . '_' . $key . '" id="' . $form_element_prefix. '_'  . $key . '" class="form-control"/>' . PHP_EOL;
        echo str_repeat(' ', 4 * 7) . '</div><br />' . PHP_EOL;
    }
}
?>
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
                            图片：
                            <input type="file" name="<?php echo $form_element_prefix; ?>_image" id="<?php echo $form_element_prefix; ?>_image" accept="image/*"/><br /><br />
<?php } ?>
<?php if ($is_ckeditor){ ?>
                            简介：
                            <textarea class="ckeditor" id="ck_<?php echo $form_element_prefix; ?>_intro"></textarea>
                            <input type="hidden" id="<?php echo $form_element_prefix; ?>_intro" name="<?php echo $form_element_prefix; ?>_intro"/>
<?php } ?>
                            <input type="hidden" id="<?php echo $form_element_prefix; ?>_<?php echo $primary_id; ?>" name="<?php echo $form_element_prefix; ?>_<?php echo $primary_id; ?>"/>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="btn_submit_<?php echo $form_element_prefix; ?>">提交</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php } ?>
        <!-- ------------------------------------------javascript--------------------------------------------------- -->
        <script src="/bootstrap/js/jquery-1.11.0.min.js"></script>
        <script src="/pagination/jquery.twbsPagination.min.js"></script>
<?php if ($is_ckeditor){ ?>
        <script src="/ckeditor/ckeditor.js"></script>
<?php } ?>
        <script src="/datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script src="/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
        <script src="/js/datetimepicker.js"></script>
        <script src="/js/util.js"></script>
        <script src="/js/common.js"></script>
        <script src="/js/alertInfo.js"></script>
<?php echo str_repeat(' ', 4 * 2) . '<script>' . PHP_EOL; ?>
        var js_data = '<?php echo '<?php echo json_encode($this->js_data); ?>' ?>';
        $(document).ready(function(){
            $('#keyword').val(js_data.keyword);
            $('#page_length').val(js_data.page_length);
            $('#current_page').val(js_data.current_page);
        });

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
                callAjaxWithForm(post_url, post_data, msg_success, msg_error, method);
            }
        }));

        $('a[id^=modify_]').on('click', function(){
            var <?php echo $primary_id; ?> = $(this).attr('id').substr('modify_'.length);
            var post_url = '/<?php echo $module_name; ?>/<?php echo strtolower($controller_name); ?>/get-<?php echo strtolower($controller_name); ?>';
            var post_data = {
                '<?php echo $primary_id; ?>' : <?php echo $primary_id; ?>
            };
            var method = 'get';
            var success_function = function(result){
<?php foreach ($table_data as $key => $default_value)
{
    echo str_repeat(' ', 4 * 4) . "$('#" . $form_element_prefix . '_' . $key . "').val(result." . $key . ");" . PHP_EOL;
}
?>
<?php if ($is_ckeditor){ ?>
                CKEDITOR.instances.ck_<?php echo $form_element_prefix; ?>_intro.setData(result.{table_prefix}_intro);
<?php } ?>
                $('#btn_submit_<?php echo $form_element_prefix; ?>').attr('disabled', false);
                $('#modal<?php echo $form_name_postfix; ?>').modal('show');
            };
            callAjaxWithFunction(post_url, post_data, success_function, method);
        });

        $('a[id^=delete_]').on('click', function(){
            if (confirm(MESSAGE_DELETE_CONFIRM)) {
                var <?php echo $primary_id; ?> = $(this).attr('id').substr('delete_'.length);
                var url = '/<?php echo $module_name; ?>/<?php echo strtolower($controller_name); ?>/delete-<?php echo strtolower($controller_name); ?>';
                var data = {
                    '<?php echo $primary_id; ?>' : <?php echo $primary_id; ?>
                };
                var msg_success = MESSAGE_DELETE_SUCCESS;
                var msg_error = MESSAGE_DELETE_ERROR;
                var method = 'post';
                callAjaxWithAlert(url, data, msg_success, msg_error, method);
            }
        });

        function validInput(type)
        {
            var error_num = 0;
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){
            echo str_repeat(' ', 4 * 3) . 'var image = $(\'#' . $form_element_prefix . '_image\').val();' . PHP_EOL;
        } else {
            echo str_repeat(' ', 4 * 3) . 'var '. $key . ' = $(\'#' . $form_element_prefix . '_' . $key . '\').val();' . PHP_EOL;
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
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 3) : 'else ') . 'if (type == \'add\' && image == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 4) . 'error_num = error_num + 1;' . PHP_EOL;
            echo str_repeat(' ', 4 * 4) . 'alert(MESSAGE_UPLOAD_IMAGE_ERROR)' . PHP_EOL;
            echo str_repeat(' ', 4 * 3) . '} ';
        } else {
            echo (array_search($key, $table_keys_no_pkid) === 0 ? str_repeat(' ', 4 * 3) : 'else ') . 'if (' . $key . ' == \'\') {' . PHP_EOL;
            echo str_repeat(' ', 4 * 4) . 'error_num = error_num + 1;' . PHP_EOL;
            echo str_repeat(' ', 4 * 4) . 'alert(\'todo set alert message\')' . PHP_EOL;
            echo str_repeat(' ', 4 * 3) . '} ';
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
        /*  --------------------------------------------------------------------------------------------------------  */
<?php if ($primary_id !== ''){ ?>
        $('#pagination').twbsPagination({
            totalPages: js_data.total_pages,
            startPage: js_data.current_page,
            visiblePages: 7,
            first: '首页',
            prev: '上一页',
            next: '下一页',
            last: '尾页',
            onPageClick: function (event, page) {
                $('#current_page').val(page);
                $('#formSearch')[0].submit();
            }
        });

        $('#page_length').on('change', function(){
            $('#current_page').val(1);
            $('#page_length').val(this.value);
            $('#formSearch')[0].submit();
        });
<?php } ?>
<?php echo str_repeat(' ', 4 * 2) . '</script>' . PHP_EOL; ?>
    </body>
</html>