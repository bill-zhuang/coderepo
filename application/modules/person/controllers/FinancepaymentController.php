<?php

class person_FinancepaymentController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_financecategory
     */
    private $_adapter_finance_category;
    /**
     * @var Application_Model_DBTable_financepayment
     */
    private $_adapter_finance_payment;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_financecategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_financepayment();
        $this->view->assign(
            [
                'parent_categories' => $this->_adapter_finance_category->getAllParentCategory(),
            ]
        );
    }

    public function indexAction()
    {
        // action body
        $current_page = intval($this->_getParam('current_page', Bootstrap::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bootstrap::INIT_PAGE_LENGTH));
        $start = ($current_page - Bootstrap::INIT_START_PAGE) * $page_length;
        $payment_date = trim($this->_getParam('payment_date', ''));

        $conditions = [
            'fp_status' => [
                'compare_type' => '= ?',
                'value' => Bootstrap::VALID_STATUS
            ]
        ];
        if ('' != $payment_date)
        {
            $conditions['fp_payment_date'] = [
                'compare_type' => '= ?',
                'value' => $payment_date
            ];
        }
        $order_by = 'fp_update_time desc';
        $total = $this->_adapter_finance_payment->getFinancepaymentCount($conditions);
        $data = $this->_adapter_finance_payment->getFinancepaymentData($conditions, $page_length, $start, $order_by);
        foreach ($data as $key => $value)
        {
            $data[$key]['category'] = $this->_adapter_finance_category->getFinaceCategoryName($value['fc_id']);
        }


        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bootstrap::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'payment_date' => $payment_date
        ];
        $this->view->assign($view_data);
    }

    public function addfinancepaymentAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['add_financepayment_payment']))
        {
            try 
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $affected_rows = $this->_addFinancepayment();
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modifyfinancepaymentAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['modify_financepayment_fp_id']))
        {
            try
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $affected_rows = $this->_updateFinancepayment();
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function deletefinancepaymentAction()
    {
        $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
        if (isset($_POST['fp_id']))
        {
            try
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fp_id = intval($_POST['fp_id']);
                $update_data = [
                    'fp_status' => Bootstrap::INVALID_STATUS,
                    'fp_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_status=1 and fp_id=?', $fp_id);
                $affected_rows = $this->_adapter_finance_payment->update($update_data, $where);
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bootstrap::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function getfinancepaymentAction()
    {
        $data = [];
        if (isset($_POST['fp_id']))
        {
            $fp_id = intval($_POST['fp_id']);
            if ($fp_id > Bootstrap::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_finance_payment->getFinancepaymentByID($fp_id);
            }
        }

        echo json_encode($data);
        exit;
    }
    
    private function _addFinancepayment()
    {
        $affected_rows = 0;

        $payments = array_filter(explode(',', $_POST['add_financepayment_payment']));
        $payment_date = trim($_POST['add_financepayment_payment_date']);
        $category_id = intval($_POST['add_financepayment_fc_id']);
        $intro = trim($_POST['add_financepayment_intro']);
        $add_time = date('Y-m-d H:i:s');

        $data = [
            'fp_payment_date' => $payment_date,
            'fc_id' => $category_id,
            'fp_detail' => $intro,
            'fp_status' => Bootstrap::VALID_STATUS,
            'fp_create_time' => $add_time,
            'fp_update_time' => $add_time
        ];
        foreach ($payments as $payment)
        {
            $payment = floatval($payment);
            if ($payment > 0)
            {
                $data['fp_payment'] = $payment;
                $affected_rows += $this->_adapter_finance_payment->insert($data);
            }
        }

        return $affected_rows;
    }
    
    private function _updateFinancepayment()
    {
        $fp_id = intval($_POST['modify_financepayment_fp_id']);
        $payment = floatval($_POST['modify_financepayment_payment']);
        $payment_date = trim($_POST['modify_financepayment_payment_date']);
        $category_id = intval($_POST['modify_financepayment_fc_id']);
        $intro = trim($_POST['modify_financepayment_intro']);

        $data = [
            'fp_payment' => $payment,
            'fp_payment_date' => $payment_date,
            'fc_id' => $category_id,
            'fp_detail' => $intro,
            'fp_update_time' => date('Y-m-d H:i:s')
        ];
        $where = $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_id=?', $fp_id);
        $affected_rows = $this->_adapter_finance_payment->update($data, $where);

        return $affected_rows;
    }

}
