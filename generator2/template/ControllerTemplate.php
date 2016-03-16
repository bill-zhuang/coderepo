<?php
/* @var $module_name string module name */
/* @var $model_names array model name */
/* @var $controller_name string controller name */
/* @var $table_prefix string table name prefix */
/* @var $table_names array table names */
/* @var $primary_id array table primary key */
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
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $data = [
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id[0])
    {
        echo str_repeat(' ', 4 * 5) . "'" . $key . "' => " . $default_value . "," . PHP_EOL;
    }
}
?>
                ];
                $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->insert($data);
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
                if ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS) {
                    $affected_rows += $this->_update<?php echo $model_names[0]; ?>Image($affected_rows, '<?php echo $form_element_prefix; ?>_image');
                }
<?php } ?>
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From add<?php echo $controller_name; ?>');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
        exit;
    }
    
    public function modify<?php echo $controller_name; ?>Action()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $<?php echo $primary_id[0]; ?> = intval($params['<?php echo $form_element_prefix; ?>_<?php echo $primary_id[0]; ?>']);
                if ($<?php echo $primary_id[0]; ?> > Bill_Constant::INVALID_PRIMARY_ID) {
                    $data = [
<?php foreach ($table_data as $key => $default_value)
{
    if ($key != $primary_id[0] && strpos($key, 'create_time') === false && strpos($key, 'status') === false)
    {
        echo str_repeat(' ', 4 * 6) . "'" . $key . "' => " . $default_value . "," . PHP_EOL;
    }
}
?>
                    ];
                    $where = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('<?php echo $primary_id[0]; ?>=?', $<?php echo $primary_id[0]; ?>);
                    $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($data, $where);
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
                    if ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS) {
                        $affected_rows += $this->_update<?php echo $model_names[0]; ?>Image($<?php echo $primary_id[0]; ?>, '<?php echo $form_element_prefix; ?>_image');
                    }
<?php } ?>
                    $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From modify<?php echo $controller_name; ?>');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
        exit;
    }
    
    public function delete<?php echo $controller_name; ?>Action()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->beginTransaction();
                $<?php echo $primary_id[0]; ?> = isset($params['<?php echo $primary_id[0]; ?>']) ? intval($params['<?php echo $primary_id[0]; ?>']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($<?php echo $primary_id[0]; ?> > Bill_Constant::INVALID_PRIMARY_ID) {
                    $update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s'),
                        //TODO set update data
                    ];
                    $where = [
                        $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('<?php echo $primary_id[0]; ?>=?', $<?php echo $primary_id[0]; ?>),
                        $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($update_data, $where);
                    $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->commit();
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From delete<?php echo $controller_name; ?>');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }
    
    public function get<?php echo $controller_name; ?>Action()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $<?php echo $primary_id[0]; ?> = (isset($params['<?php echo $primary_id[0]; ?>'])) ? intval($params['<?php echo $primary_id[0]; ?>']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>ByID($<?php echo $primary_id[0]; ?>);
            if (!empty($data)) {
                $json_array = [
                    'data' => $data,
                ];
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }

    private function _index()
    {
<?php if(!empty($model_names)){ ?>
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';
<?php if(!empty($tab_types)){ ?>
        $tab_type = isset($params['tab_type']) ? intval($params['tab_type']) : 1;
<?php } ?>

        $conditions = [
            '<?php echo ($status_name === '') ? 'todo status' : $status_name; ?> =?' => Bill_Constant::VALID_STATUS
        ];
        $order_by = '<?php echo $primary_id[0]; ?> ASC'; //TODO reset order by
        $total = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>Count($conditions);
        $data = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->get<?php echo $model_names[0]; ?>Data($conditions, $current_page, $page_length, $order_by);

        $json_data = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $page_length),
                'pageIndex' => $current_page,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $page_length,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        return $json_data;
<?php } ?>
    }
    
<?php if(strpos(implode('', $table_keys), 'img') !== false || strpos(implode('', $table_keys), 'image') !== false){ ?>
    private function _update<?php echo $model_names[0]; ?>Image($<?php echo $primary_id[0]; ?>, $file_id)
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        $image_url = $this->_upload<?php echo $model_names[0]; ?>Image($<?php echo $primary_id[0]; ?>, $file_id);
        if ($image_url != '') {
            $update_data = [
                //todo update image field
                '<?php echo $form_element_prefix; ?>_update_time' => date('Y-m-d H:i:s')
            ];
            $where = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->getAdapter()->quoteInto('<?php echo $primary_id[0]; ?>=?', $<?php echo $primary_id[0]; ?>);
            $affected_rows = $this->_adapter_<?php echo str_replace($table_prefix, '', $table_names[0]); ?>->update($update_data, $where);
        }

        return $affected_rows;
    }

    private function _upload<?php echo $model_names[0]; ?>Image($<?php echo $primary_id[0]; ?>, $file_id)
    {
        //todo upload image & get image url
    }
<?php } ?>
}
<?php } ?>
