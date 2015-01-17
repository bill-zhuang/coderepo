<?php

class {module_name}_TemplateController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
    }

    public function indexAction()
    {
        // action body
        $current_page = intval($this->_getParam('current_page', Bootstrap::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bootstrap::INIT_PAGE_LENGTH));
        $start = ($current_page - Bootstrap::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));
        $start_date = $this->_getParam('start_date');

        $conditions = [
            '{table_prefix}_status' => [
                'compare_type' => '= ?',
                'value' => Bootstrap::VALID_STATUS
            ]
        ];
        if ('' !== $keyword)
        {
            $conditions['{table_prefix}_name'] = [
                'compare_type' => 'like ?',
                'value' => '%' . $keyword . '%'
            ];
        }
        if ('' != $start_date)
        {
            $conditions['{table_prefix}_create_time'] = [
                'compare_type' => '>= ?',
                'value' => $start_date
            ];
        }
        $order_by = '{table_prefix}_update_time desc';
        $total = $this->_adapter_{main}->get{action_object_name_first_big_letter}Count($conditions);
        $data = $this->_adapter_{main}->get{action_object_name_first_big_letter}Data($conditions, $page_length, $start, $order_by);

        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bootstrap::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'keyword' => $keyword,
            'start_date' => $start_date
        ];
        $this->view->assign($view_data);
    }

    public function add{action_object_name}Action()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['add_{action_object_name}_name']))
        {
            try 
            {
                $this->_adapter_{main}->getAdapter()->beginTransaction();
                $affected_rows = $this->_add{action_object_name_first_big_letter}();
                $this->_adapter_{main}->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_{main}->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modify{action_object_name}Action()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['modify_{action_object_name}_{primary_key}']))
        {
            try
            {
                $this->_adapter_{main}->getAdapter()->beginTransaction();
                $affected_rows = $this->_update{action_object_name_first_big_letter}();
                $this->_adapter_{main}->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_{main}->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function delete{action_object_name}Action()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['{primary_key}']))
        {
            try
            {
                $this->_adapter_{main}->getAdapter()->beginTransaction();
                ${primary_key} = intval($_POST['{primary_key}']);
                $update_data = [
                    '{table_prefix}_status' => Bootstrap::INVALID_STATUS,
                    '{table_prefix}_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_{main}->getAdapter()->quoteInto('status=1 and {primary_key}=?', ${primary_key});
                $affected_rows = $this->_adapter_{main}->update($update_data, $where);
                $this->_adapter_{main}->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_{main}->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function get{action_object_name}Action()
    {
        $data = [];
        if (isset($_POST['{primary_key}']))
        {
            ${primary_key} = intval($_POST['{primary_key}']);
            if (${primary_key} > Bootstrap::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_{main}->get{action_object_name_first_big_letter}ByID(${primary_key});
            }
        }

        echo json_encode($data);
        exit;
    }
    
    private function _add{action_object_name_first_big_letter}()
    {
        $name = trim($_POST['add_{action_object_name}_name']);
        $intro = $this->_processImagesInContent($_POST['add_{action_object_name}_intro']);
        $weight = intval($_POST['add_{action_object_name}_weight']);
        $add_time = date('Y-m-d H:i:s');

        $data = [
            '{table_prefix}_name' => $name,
            '{table_prefix}_intro' => $intro,
            '{table_prefix}_weight' => $weight,
            '{table_prefix}_status' => Bootstrap::VALID_STATUS,
            '{table_prefix}_create_time' => $add_time,
            '{table_prefix}_update_time' => $add_time
        ];
        $affected_rows = $this->_adapter_{main}->insert($data);
        if ($affected_rows > Bootstrap::INIT_AFFECTED_ROWS)
        {
            $affected_rows += $this->_update{action_object_name_first_big_letter}Image($affected_rows, 'add_{action_object_name}_image');
        }

        return $affected_rows;
    }
    
    private function _update{action_object_name_first_big_letter}()
    {
        ${primary_key} = intval($_POST['modify_{action_object_name}_{primary_key}']);
        $name = trim($_POST['modify_{action_object_name}_name']);
        $intro = $this->_processImagesInContent($_POST['modify_{action_object_name}_intro']);
        $weight = intval($_POST['modify_{action_object_name}_weight']);

        $data = [
            '{table_prefix}_name' => $name,
            '{table_prefix}_intro' => $intro,
            '{table_prefix}_weight' => $weight,
            '{table_prefix}_update_time' => date('Y-m-d H:i:s')
        ];
        $where = $this->_adapter_{main}->getAdapter()->quoteInto('{primary_key}=?', ${primary_key});
        $affected_rows = $this->_adapter_{main}->update($data, $where);
        if ($affected_rows > Bootstrap::INIT_AFFECTED_ROWS)
        {
            $affected_rows += $this->_update{action_object_name_first_big_letter}Image(${primary_key}, 'modify_{action_object_name}_image');
        }

        return $affected_rows;
    }

    private function _update{action_object_name_first_big_letter}Image(${primary_key}, $file_id)
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        $image_url = $this->_upload{action_object_name_first_big_letter}Image(${primary_key}, $file_id);
        if ($image_url != '')
        {
            $update_data = [
                '{table_prefix}_imgurl' => $image_url,
                '{table_prefix}_update_time' => date('Y-m-d H:i:s')
            ];
            $where = $this->_adapter_{main}->getAdapter()->quoteInto('{primary_key}=?', ${primary_key});
            $affected_rows = $this->_adapter_{main}->update($update_data, $where);
        }

        return $affected_rows;
    }

    private function _upload{action_object_name_first_big_letter}Image(${primary_key}, $file_id)
    {
        $aliyun = new Bill_Tools_Uploadfiles();
        $image_url = $aliyun->uploadImageFrom{action_object_name_first_big_letter}($file_id, ${primary_key});
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
}

