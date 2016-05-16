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
/* @var $using_datetime_picker bool use datetimepicker or not */
/* @var $tab_types array tab types for select */
/* @var $default_tab_value mixed default selected tab value */

$table_keys = array_keys($table_data);
?>
<?php if($using_datetime_picker){ ?>
<link href="/assets/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen"/>
<?php } ?>
<link href="/css/common.css" rel="stylesheet"/>
<title><?php echo $page_title_name; ?></title>
<div class="panel panel-warning">
    <!-- panel heading -->
    <div class="panel-heading">
        <h2><?php echo $page_title_name; ?></h2>
    </div>
    <!-- panel body -->
    <div class="panel-body">
        <div class="row">
<?php if ($primary_id[0] !== ''){ ?>
            <form action="#" method="get" id="formSearch" class="form-inline">
                <div class="col-sm-10 col-md-10 col-lg-10">
                    关键字: <input type="text" class="form-control" id="keyword" name="keyword"/>
                    <button class="btn btn-primary" type="submit" id="btn_search">
                        <span class="glyphicon glyphicon-search"></span>
                        <span>搜索</span>
                    </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success" type="button" id="btn_add">
                        <span class="glyphicon glyphicon-plus"></span>
                        <span>新增</span>
                    </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
<?php if($all_batch_id !== ''){ ?>
                    <button class="btn btn-danger" type="button" id="btn_batch_delete">
                        <span class="glyphicon glyphicon-trash"></span>
                        <span>批量删除</span>
                    </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
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
<?php if(!empty($tab_types)){ ?>
                <input type="hidden" id="tab_type" name="tab_type"/>
<?php } ?>
            </form>
<?php } ?>
        </div>
        <hr>
<?php if($primary_id[0] !== ''){ ?>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
<?php if(!empty($tab_types)){ ?>
                <nav class="navbar nav-tabs" role="navigation">
                    <div>
                        <ul class="nav nav-tabs" id="ul_tab_type">
<?php foreach ($tab_types as $key => $value) { ?>
                            <li id="li_tab_type_<?php echo '' . $key; ?>" <?php echo ($key == $default_tab_value) ? 'class="active"' : ''; ?>><a href="#"><?php echo $value; ?></a></li><?php echo PHP_EOL; ?>
<?php } ?>
                        </ul>
                    </div>
                </nav>
<?php } ?>
                <table id="tbl" class="table table-striped table-bordered bill_table text-center">
                    <thead>
                    <tr>
<?php if($all_batch_id !== ''){ ?>
                        <td><input type="checkbox" id="<?php echo $all_batch_id; ?>" name="<?php echo $all_batch_id; ?>"/></td>
<?php } ?>
<?php
echo str_repeat(' ', 4 * 6) . '<td>序号</td>' . PHP_EOL;
foreach ($table_row_data as $key => $value)
{
    echo str_repeat(' ', 4 * 6) . '<td>' . $key . '</td>' . PHP_EOL;
}
?>
                        <td>操作</td>
                    </tr>
                    </thead>
                    <tbody>
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
            <div id="div_pagination" class="col-sm-6 col-md-6 col-lg-6 text-right">
            </div>
        </div>
    </div>
<?php } ?>
</div>
<?php if ($primary_id[0] !== ''){ ?>
<!-- modal -->
<div id="modal<?php echo $form_name_postfix; ?>" class="modal fade">
    <div class="modal-dialog bill_modal_<?php echo $view_modal_size; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <span>新增/修改</span>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form id="form<?php echo $form_name_postfix; ?>" action="#" method="post" enctype="multipart/form-data" class="form-inline">
                <div class="modal-body">
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id[0] && strpos($key, 'create_time') === false && strpos($key, 'update_time') === false && strpos($key, 'status') === false)
    {
        echo str_repeat(' ', 4 * 5) . '<div class="input-group">' . PHP_EOL;
        echo str_repeat(' ', 4 * 6) . '<span class="input-group-addon">' . $key . '：</span>' . PHP_EOL;
        echo str_repeat(' ', 4 * 6) . '<input type="text" name="' . $form_element_prefix . '_' . $key . '" id="' . $form_element_prefix. '_'  . $key . '" class="form-control"/>' . PHP_EOL;
        echo str_repeat(' ', 4 * 5) . '</div>' . PHP_EOL;
        echo str_repeat(' ', 4 * 5) . '<br />' . PHP_EOL;
    }
}
?>
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
                        图片：
                        <input type="file" name="<?php echo $form_element_prefix; ?>_image" id="<?php echo $form_element_prefix; ?>_image" accept="image/*"/><br /><br />
<?php } ?>
<?php if ($using_ckeditor){ ?>
                        简介：
                        <textarea class="ckeditor" id="ck_<?php echo $form_element_prefix; ?>_intro"></textarea>
                        <input type="hidden" id="<?php echo $form_element_prefix; ?>_intro" name="<?php echo $form_element_prefix; ?>_intro"/>
<?php } ?>
                        <input type="hidden" id="<?php echo $form_element_prefix; ?>_<?php echo $primary_id[0]; ?>" name="<?php echo $form_element_prefix; ?>_<?php echo $primary_id[0]; ?>"/>
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
<script src="/assets/pagination/jquery.twbsPagination.min.js"></script>
<?php if ($using_ckeditor){ ?>
<script src="/assets/ckeditor/ckeditor.js"></script>
<?php } ?>
<?php if($using_datetime_picker){ ?>
<script src="/assets/datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="/assets/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script src="/js/public/datetimepicker.js"></script>
<?php } ?>
<script src="/assets/jquery-serialize-object/js/jquery.serialize-object.min.js"></script>
<script src="/js/public/jAjaxWidget.js"></script>
<script src="/js/public/jCommon.js"></script>
<script src="/js/public/alertMessage.js"></script>
<?php if ($primary_id[0] !== ''){ ?>
<script src="/js/public/pagination.js"></script>
<?php } ?>
<script src="/js/<?php echo ($module_name == '') ? 'default' : $module_name; ?>/<?php echo strtolower($controller_name); ?>.js"></script>