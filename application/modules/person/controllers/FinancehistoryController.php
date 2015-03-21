<?php

class person_FinancehistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_Financecategory
     */
    private $_adapter_finance_category;
    /**
     * @var Application_Model_DBTable_Financepayment
     */
    private $_adapter_finance_payment;
    private $_categories;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_Financecategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_Financepayment();
        $this->_categories = $this->_adapter_finance_category->getAllParentCategory();
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

        //choose last 60 days data.
        $fetch_days = 30;
        $start_date = date('Y-m-d', strtotime('- ' . $fetch_days . ' day'));
        $all_chart_data = $this->_getAllPaymentHistoryDataByDay($start_date);
        $sort_chart_data = [];
        if (count($all_chart_data['period']) != $fetch_days)
        {
            for($i = 1; $i <= $fetch_days; $i++)
            {
                $period_date = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' day'));
                $sort_chart_data['period'][] = $period_date;
                if (!in_array($period_date, $all_chart_data['period']))
                {
                    $sort_chart_data['payment'][] = 0;
                }
                else
                {
                    $period_key = array_search($period_date, $all_chart_data['period']);
                    $sort_chart_data['payment'][] = $all_chart_data['payment'][$period_key];
                }
            }
            $all_chart_data = $sort_chart_data;
        }
        //choose last one year data.
        $start_date = date('Y-m-d', strtotime('- 1 year'));
        $category_data = $this->_getAllPaymentHistoryDataByCategory($start_date);//print_r($category_data);exit;

        $this->view->chart_data = json_encode($chart_data);
        $this->view->all_chart_data = json_encode($all_chart_data);
        $this->view->category_chart_data = json_encode($category_data);
    }

    private function _getAllPaymentHistoryDataByDay($start_date)
    {
        $all_chart_data = [
            'period' => [],
            'payment' => [],
        ];
        $all_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByDay($start_date);
        foreach ($all_data as $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['payment'][] = $all_value['payment'];
        }

        return $all_chart_data;
    }

    private function _getAllPaymentHistoryDataByCategory($start_date)
    {
        $all_chart_data = [
            'category' => [],
            'payment' => [],
        ];
        $all_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByCategory($start_date);
        foreach ($all_data as $all_value)
        {
            $all_chart_data['category'][] = $this->_categories[$all_value['fc_id']];
            $all_chart_data['payment'][] = $all_value['payment'];
        }

        return $all_chart_data;
    }

}
