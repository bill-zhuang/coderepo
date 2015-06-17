<?php

class person_ConsoleController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        //go int public/ directory & run command: php index.php -i
        $opts = new Zend_Console_Getopt('i');
        $args = $opts->getRemainingArgs();
        echo 'console success';
    }

    public function transferPaymentCategoryAction()
    {
        $adapter_payment = new Application_Model_DBTable_FinancePayment();
        $adapter_payment_map = new Application_Model_DBTable_FinancePaymentMap();

        $payment_data = $adapter_payment->getAllPaymentDataForTransfer();
        foreach ($payment_data as $payment_value)
        {
            $map_data = [
                'fp_id' => $payment_value['fp_id'],
                'fc_id' => $payment_value['fc_id'],
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => $payment_value['fp_create_time'],
                'update_time' => $payment_value['fp_update_time']
            ];
            $adapter_payment_map->insert($map_data);
        }
    }
}