<?php

class person_FinancecategoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_financecategory
     */
    private $_adapter_finance_category;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_financecategory();
        $this->view->assign(
            [
                'parents' => $this->_adapter_finance_category->getAllParentCategory(),
            ]
        );
    }

    public function indexAction()
    {
        // action body
        $current_page = intval($this->_getParam('current_page', Bootstrap::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bootstrap::INIT_PAGE_LENGTH));
        $start = ($current_page - Bootstrap::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));

        $conditions = [
            'fc_status' => [
                'compare_type' => '= ?',
                'value' => Bootstrap::VALID_STATUS
            ]
        ];
        if ('' !== $keyword)
        {
            $conditions['fc_name'] = [
                'compare_type' => 'like ?',
                'value' => '%' . $keyword . '%'
            ];
        }
        $order_by = 'fc_update_time desc';
        $total = $this->_adapter_finance_category->getFinancecategoryCount($conditions);
        $data = $this->_adapter_finance_category->getFinancecategoryData($conditions, $page_length, $start, $order_by);

        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bootstrap::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'keyword' => $keyword,
        ];
        $this->view->assign($view_data);
    }

    public function addfinancecategoryAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['add_financecategory_name']))
        {
            try 
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $affected_rows = $this->_addFinancecategory();
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modifyfinancecategoryAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['modify_financecategory_fc_id']))
        {
            try
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $affected_rows = $this->_updateFinancecategory();
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function deletefinancecategoryAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['fc_id']))
        {
            try
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $fc_id = intval($_POST['fc_id']);
                $update_data = [
                    'fc_status' => Bootstrap::INVALID_STATUS,
                    'fc_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_finance_category->getAdapter()
                    ->quoteInto('fc_status=1 and (fc_id=? or fc_parent_id=?)', $fc_id);
                $affected_rows = $this->_adapter_finance_category->update($update_data, $where);
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function getfinancecategoryAction()
    {
        $data = [];
        if (isset($_POST['fc_id']))
        {
            $fc_id = intval($_POST['fc_id']);
            if ($fc_id > Bootstrap::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_finance_category->getFinancecategoryByID($fc_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function getfinancesubcategoryAction()
    {
        $data = [];
        if (isset($_POST['parent_id']))
        {
            $parent_id = intval($_POST['parent_id']);
            $data = $this->_adapter_finance_category->getFinanceSubcategory($parent_id);
        }

        echo json_encode($data);
        exit;
    }
    
    private function _addFinancecategory()
    {
        $affected_rows = 0;

        $name = trim($_POST['add_financecategory_name']);
        $parent_id = intval($_POST['add_financecategory_parent_id']);
        $add_time = date('Y-m-d H:i:s');

        if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, 0))
        {
            $data = [
                'fc_name' => $name,
                'fc_parent_id' => $parent_id,
                'fc_status' => Bootstrap::VALID_STATUS,
                'fc_create_time' => $add_time,
                'fc_update_time' => $add_time
            ];
            $affected_rows = $this->_adapter_finance_category->insert($data);
        }

        return $affected_rows;
    }
    
    private function _updateFinancecategory()
    {
        $affected_rows = 0;

        $fc_id = intval($_POST['modify_financecategory_fc_id']);
        $name = trim($_POST['modify_financecategory_name']);
        $parent_id = intval($_POST['modify_financecategory_parent_id']);

        if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, $fc_id))
        {
            $data = [
                'fc_name' => $name,
                'fc_parent_id' => $parent_id,
                'fc_update_time' => date('Y-m-d H:i:s')
            ];
            $where = $this->_adapter_finance_category->getAdapter()->quoteInto('fc_id=?', $fc_id);
            $affected_rows = $this->_adapter_finance_category->update($data, $where);
        }

        return $affected_rows;
    }

}
