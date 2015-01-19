<?php

class person_FinancehistoryController extends Zend_Controller_Action
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
    }

    public function indexAction()
    {
        // action body
        $chart_data = [
            'period' => [],
            'payment' => [],
        ];
        $month_data = $this->_adapter_finance_payment->getTotalPaymentHistoryGroupData();
        foreach ($month_data as $month_value)
        {
            $chart_data['period'][] = $month_value['period'];
            $chart_data['payment'][] = $month_value['payment'];
        }

        $all_chart_data = $this->_getAllPaymentHistoryDataByDay();

        $this->view->chart_data = json_encode($chart_data);
        $this->view->all_chart_data = json_encode($all_chart_data);
    }

    private function _getAllPaymentHistoryDataByDay()
    {
        $all_chart_data = [
            'period' => [],
            'payment' => [],
        ];
        $all_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByDay();
        foreach ($all_data as $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['payment'][] = $all_value['payment'];
        }

        return $all_chart_data;
    }

}
