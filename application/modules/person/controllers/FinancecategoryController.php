<?php

class person_FinanceCategoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapter_finance_category;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
        $this->view->assign(
            [
                'parents' => $this->_adapter_finance_category->getAllParentCategory(),
            ]
        );
    }

    public function indexAction()
    {
        // action body
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $start = ($current_page - Bill_Constant::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));

        $conditions = [
            'fc_status' => [
                'compare_type' => '= ?',
                'value' => Bill_Constant::VALID_STATUS
            ]
        ];
        if ('' !== $keyword)
        {
            $conditions['fc_name'] = [
                'compare_type' => 'like ?',
                'value' => '%' . $keyword . '%'
            ];
        }
        $order_by = 'fc_weight desc';
        $total = $this->_adapter_finance_category->getFinanceCategoryCount($conditions);
        $data = $this->_adapter_finance_category->getFinanceCategoryData($conditions, $page_length, $start, $order_by);

        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bill_Constant::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'keyword' => $keyword,
        ];
        $this->view->assign($view_data);
    }

    public function addFinanceCategoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['finance_category_name']))
        {
            try 
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $affected_rows = $this->_addFinanceCategory();
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modifyFinanceCategoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['finance_category_fc_id']))
        {
            try
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $affected_rows = $this->_updateFinanceCategory();
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function deleteFinanceCategoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['fc_id']))
        {
            try
            {
                $this->_adapter_finance_category->getAdapter()->beginTransaction();
                $fc_id = intval($_POST['fc_id']);
                $update_data = [
                    'fc_status' => Bill_Constant::INVALID_STATUS,
                    'fc_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_finance_category->getAdapter()
                    ->quoteInto('fc_status=1 and (fc_id=? or fc_parent_id=?)', $fc_id);
                $affected_rows = $this->_adapter_finance_category->update($update_data, $where);
                $this->_adapter_finance_category->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_category->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function getFinanceCategoryAction()
    {
        $data = [];
        if (isset($_GET['fc_id']))
        {
            $fc_id = intval($_GET['fc_id']);
            if ($fc_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_finance_category->getFinanceCategoryByID($fc_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function getFinanceSubcategoryAction()
    {
        $data = [];
        if (isset($_GET['parent_id']))
        {
            $parent_id = intval($_GET['parent_id']);
            $data = $this->_adapter_finance_category->getFinanceSubcategory($parent_id);
        }

        echo json_encode($data);
        exit;
    }
    
    private function _addFinanceCategory()
    {
        $affected_rows = 0;

        $name = trim($_POST['finance_category_name']);
        $parent_id = intval($_POST['finance_category_parent_id']);
        $weight = intval($_POST['finance_category_weight']);
        $add_time = date('Y-m-d H:i:s');

        if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, 0))
        {
            $data = [
                'fc_name' => $name,
                'fc_parent_id' => $parent_id,
                'fc_weight' => $weight,
                'fc_status' => Bill_Constant::VALID_STATUS,
                'fc_create_time' => $add_time,
                'fc_update_time' => $add_time
            ];
            $affected_rows = $this->_adapter_finance_category->insert($data);
        }

        return $affected_rows;
    }
    
    private function _updateFinanceCategory()
    {
        $affected_rows = 0;

        $fc_id = intval($_POST['finance_category_fc_id']);
        $name = trim($_POST['finance_category_name']);
        $parent_id = intval($_POST['finance_category_parent_id']);
        $weight = intval($_POST['finance_category_weight']);

        if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, $fc_id))
        {
            $data = [
                'fc_name' => $name,
                'fc_parent_id' => $parent_id,
                'fc_weight' => $weight,
                'fc_update_time' => date('Y-m-d H:i:s')
            ];
            $where = $this->_adapter_finance_category->getAdapter()->quoteInto('fc_id=?', $fc_id);
            $affected_rows = $this->_adapter_finance_category->update($data, $where);
        }

        return $affected_rows;
    }

}
