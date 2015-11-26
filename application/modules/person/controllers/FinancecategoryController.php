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

    public function addFinanceCategoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['finance_category_name']))
        {
            try 
            {
                $affected_rows = $this->_addFinanceCategory();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From addFinanceCategory');
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
                $affected_rows = $this->_updateFinanceCategory();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From modifyFinanceCategory');
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
                $fc_id = intval($_POST['fc_id']);
                $update_data = [
                    'fc_status' => Bill_Constant::INVALID_STATUS,
                    'fc_update_time' => date('Y-m-d H:i:s')
                ];
                $where = [
                    $this->_adapter_finance_category->getAdapter()->quoteInto('(fc_id=? or fc_parent_id=?)', $fc_id),
                    $this->_adapter_finance_category->getAdapter()->quoteInto('fc_status=?', Bill_Constant::VALID_STATUS),
                ];
                $affected_rows = $this->_adapter_finance_category->update($update_data, $where);
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From deleteFinanceCategory');
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

    public function getFinanceMainCategoryAction()
    {
        $data = $this->_adapter_finance_category->getAllParentCategory();
        $json_data = [
            'data' => [
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        echo json_encode($json_data);
        exit;
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'fc_status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' !== $keyword)
        {
            $conditions['fc_name like ?'] = '%' . $keyword . '%';
        }
        $order_by = 'fc_weight desc';
        $total = $this->_adapter_finance_category->getFinanceCategoryCount($conditions);
        $data = $this->_adapter_finance_category->getFinanceCategoryData($conditions, $page_length, $start, $order_by);
        foreach ($data as &$value)
        {
            $value['parent'] = $value['fc_parent_id'] == 0 ?
                '无' : $this->_adapter_finance_category->getParentCategoryName($value['fc_parent_id']);
        }

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
