<?php
/* @var $module_name string module name */
/* @var $model_names array model name */
/* @var $controller_name string controller name */
/* @var $table_prefix string table name prefix */
/* @var $table_names array table names */
/* @var $primary_id string table primary key */
/* @var $table_data array table fields and default value */
/* @var $form_element_prefix string prefix of form element */
/* @var $tab_types array tab types for select */
/* @var $default_tab_value mixed default selected tab value */

$table_keys = array_keys($table_data);
$status_name = '';
foreach ($table_keys as $table_key)
{
    if (strpos($table_key, 'status') !== false)
    {
        $status_name = $table_key;
        break;
    }
}

echo "<?php\n";
?>

class <?php echo $module_name == '' ? '' : $module_name . '_'; ?><?php echo $controller_name; ?>Controller extends Zend_Controller_Action
{
<?php foreach ($table_names as $key => $table_name)
{
    echo str_repeat(' ', 4 * 1) . '/**' . PHP_EOL;
    echo str_repeat(' ', 4 * 1) . ' * @var Application_Model_DBTable_' . $model_names[$key] . PHP_EOL;
    echo str_repeat(' ', 4 * 1) . ' */' . PHP_EOL;
    echo str_repeat(' ', 4 * 1) . 'private $_adapter_' . str_replace($table_prefix, '', $table_name) . ';' . PHP_EOL;
}
echo PHP_EOL;
?>
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
<?php foreach ($model_names as $key => $model_name)
{
    echo str_repeat(' ', 4 * 2) . '$this->_adapter_' . str_replace($table_prefix, '', $table_names[$key]) . '= new Application_Model_DBTable_' . $model_name . '();' . PHP_EOL;
}
?>
    }

    public function indexAction()
    {
        // action body
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
        exit;
    }

<?php if(!empty($model_names)){ ?>
    public function add<?php echo $controller_name; ?>Action()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['<?php echo $form_element_prefix; ?>_name']))
        {
            try 
            {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $affected_rows = $this->_add<?php echo $model_names[0]; ?>();
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modify<?php echo $controller_name; ?>Action()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['<?php echo $form_element_prefix; ?>_<?php echo $primary_id; ?>']))
        {
            try
            {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $affected_rows = $this->_update<?php echo $model_names[0]; ?>();
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function delete<?php echo $controller_name; ?>Action()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['<?php echo $primary_id; ?>']))
        {
            try
            {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $<?php echo $primary_id; ?> = intval($_POST['<?php echo $primary_id; ?>']);
                $update_data = [
                    //TODO set update data
                ];
                $where = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('status=1 and <?php echo $primary_id; ?>=?', $<?php echo $primary_id; ?>);
                $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($update_data, $where);
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function get<?php echo $controller_name; ?>Action()
    {
        $data = [];
        if (isset($_GET['<?php echo $primary_id; ?>']))
        {
            $<?php echo $primary_id; ?> = intval($_GET['<?php echo $primary_id; ?>']);
            if ($<?php echo $primary_id; ?> > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>ByID($<?php echo $primary_id; ?>);
            }
        }

        echo json_encode($data);
        exit;
    }

    private function _index()
    {
<?php if(!empty($model_names)){ ?>
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $start = ($current_page - Bill_Constant::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));
<?php if(!empty($tab_types)){ ?>
        $tab_type = intval($this->_getParam('tab_type', 1));
<?php } ?>

        $conditions = [
            '<?php echo ($status_name === '') ? 'todo status' : $status_name; ?>' => [
                'compare_type' => '= ?',
                'value' => Bill_Constant::VALID_STATUS
            ]
        ];
        $order_by = '<?php echo $primary_id; ?> ASC'; //TODO reset order by
        $total = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>Count($conditions);
        $data = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>Data($conditions, $page_length, $start, $order_by);

        $json_data = [
            'data' => $data,
            'current_page' => $current_page,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bill_Constant::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
        ];
        return $json_data;
<?php } ?>
    }
    
    private function _add<?php echo $model_names[0]; ?>()
    {
        $data = [
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        echo str_repeat(' ', 4 * 3) . "'" . $key . "' => " . $default_value . "," . PHP_EOL;
    }
}
?>
        ];
        $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->insert($data);
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
        if ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
        {
            $affected_rows += $this->_update<?php echo $model_names[0]; ?>Image($affected_rows, '<?php echo $form_element_prefix; ?>_image');
        }
<?php } ?>

        return $affected_rows;
    }
    
    private function _update<?php echo $model_names[0]; ?>()
    {
        $<?php echo $primary_id; ?> = intval($_POST['<?php echo $form_element_prefix; ?>_<?php echo $primary_id; ?>']);

        $data = [
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id)
    {
        echo str_repeat(' ', 4 * 3) . "'" . $key . "' => " . $default_value . "," . PHP_EOL;
    }
}
?>
        ];
        $where = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('<?php echo $primary_id; ?>=?', $<?php echo $primary_id; ?>);
        $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($data, $where);
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
        if ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
        {
            $affected_rows += $this->_update<?php echo $model_names[0]; ?>Image($<?php echo $primary_id; ?>, '<?php echo $form_element_prefix; ?>_image');
        }
<?php } ?>

        return $affected_rows;
    }
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
    private function _update<?php echo $model_names[0]; ?>Image($<?php echo $primary_id; ?>, $file_id)
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        $image_url = $this->_upload<?php echo $model_names[0]; ?>Image($<?php echo $primary_id; ?>, $file_id);
        if ($image_url != '')
        {
            $update_data = [
                '<?php echo $form_element_prefix; ?>_imgurl' => $image_url,
                '<?php echo $form_element_prefix; ?>_update_time' => date('Y-m-d H:i:s')
            ];
            $where = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('<?php echo $primary_id; ?>=?', $<?php echo $primary_id; ?>);
            $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($update_data, $where);
        }

        return $affected_rows;
    }

    private function _upload<?php echo $model_names[0]; ?>Image($<?php echo $primary_id; ?>, $file_id)
    {
        $aliyun = new Bill_Tools_Uploadfiles();
        $image_url = $aliyun->uploadImageFrom<?php echo $model_names[0]; ?>($file_id, $<?php echo $primary_id; ?>);
        return $image_url;
    }

    private function _processImagesInContent($content)
    {
        $base64_contents = [];
        $preg_img = '/<img.*?src="([^"]+)"/';
        $is_match = preg_match_all($preg_img, $content, $matches);
        if ($is_match > 0)
        {
            foreach ($matches[1] as $value)
            {
                if ('http' != substr($value, 0, 4))
                {
                    $base64_contents[] = $value;
                }
            }
        }
        if (!empty($base64_contents))
        {
            $aliyun_upload = new Bill_Tools_Uploadfiles();
            $img_urls = $aliyun_upload->uploadImageByBase64Content($base64_contents, '{action_object_name}');
            $content = str_replace($base64_contents, $img_urls, $content);
        }

        return $content;
    }
<?php } ?>
}
<?php } ?>
