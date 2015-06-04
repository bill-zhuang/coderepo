<?php

class person_FinancePaymentController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapter_finance_category;
    /**
     * @var Application_Model_DBTable_FinancePayment
     */
    private $_adapter_finance_payment;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_FinancePayment();
        $this->view->assign(
            [
                'parent_categories' => $this->_adapter_finance_category->getAllParentCategory(),
            ]
        );
    }

    public function indexAction()
    {
        // action body
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $start = ($current_page - Bill_Constant::INIT_START_PAGE) * $page_length;
        $payment_date = trim($this->_getParam('payment_date', ''));

        $conditions = [
            'fp_status' => [
                'compare_type' => '= ?',
                'value' => Bill_Constant::VALID_STATUS
            ]
        ];
        if ('' != $payment_date)
        {
            $conditions['fp_payment_date'] = [
                'compare_type' => '= ?',
                'value' => $payment_date
            ];
        }
        $order_by = 'fp_payment_date desc';
        $total = $this->_adapter_finance_payment->getFinancePaymentCount($conditions);
        $data = $this->_adapter_finance_payment->getFinancePaymentData($conditions, $page_length, $start, $order_by);
        foreach ($data as $key => $value)
        {
            $data[$key]['category'] = $this->_adapter_finance_category->getFinaceCategoryName($value['fc_id']);
        }

        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bill_Constant::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'payment_date' => $payment_date
        ];
        $this->view->assign($view_data);
    }

    public function addFinancePaymentAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['finance_payment_payment']))
        {
            try 
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $affected_rows = $this->_addFinancePayment();
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function modifyFinancePaymentAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['finance_payment_fp_id']))
        {
            try
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $affected_rows = $this->_updateFinancePayment();
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function deleteFinancePaymentAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['fp_id']))
        {
            try
            {
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fp_id = intval($_POST['fp_id']);
                $update_data = [
                    'fp_status' => Bill_Constant::INVALID_STATUS,
                    'fp_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_status=1 and fp_id=?', $fp_id);
                $affected_rows = $this->_adapter_finance_payment->update($update_data, $where);
                $this->_adapter_finance_payment->getAdapter()->commit();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $this->_adapter_finance_payment->getAdapter()->rollBack();
            }
        }
        
        echo json_encode($affected_rows);
        exit;
    }
    
    public function getFinancePaymentAction()
    {
        $data = [];
        if (isset($_GET['fp_id']))
        {
            $fp_id = intval($_GET['fp_id']);
            if ($fp_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_finance_payment->getFinancePaymentByID($fp_id);
            }
        }

        echo json_encode($data);
        exit;
    }
    
    private function _addFinancePayment()
    {
        $affected_rows = 0;

        $payments = array_filter(explode(',', $_POST['finance_payment_payment']));
        $payment_date = trim($_POST['finance_payment_payment_date']);
        $category_id = intval($_POST['finance_payment_fc_id']);
        $intro = trim($_POST['finance_payment_intro']);
        $add_time = date('Y-m-d H:i:s');

        $data = [
            'fp_payment_date' => $payment_date,
            'fc_id' => $category_id,
            'fp_detail' => $intro,
            'fp_status' => Bill_Constant::VALID_STATUS,
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
    
    private function _updateFinancePayment()
    {
        $fp_id = intval($_POST['finance_payment_fp_id']);
        $payment = floatval($_POST['finance_payment_payment']);
        $payment_date = trim($_POST['finance_payment_payment_date']);
        $category_id = intval($_POST['finance_payment_fc_id']);
        $intro = trim($_POST['finance_payment_intro']);

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
