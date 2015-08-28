<?php

class person_FinanceHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapter_finance_category;
    /**
     * @var Application_Model_DBTable_FinancePayment
     */
    private $_adapter_finance_payment;
    private $_categories;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_FinancePayment();
        $this->_categories = $this->_adapter_finance_category->getAllParentCategory(true);
    }

    public function indexAction()
    {
        // action body
    }

    public function ajaxFinanceHistoryPeriodAction()
    {
        $period_data = $this->_getFinanceHistoryPeriodData();

        echo json_encode($period_data);
        exit;
    }

    public function ajaxFinanceHistoryMonthAction()
    {
        $month_data = $this->_getFinanceHistoryMonthData();

        echo json_encode($month_data);
        exit;
    }

    public function ajaxFinanceHistoryMonthCategoryAction()
    {
        $month_category_data = $this->_getFinanceHistoryMonthCategoryData();

        echo json_encode($month_category_data);
        exit;
    }

    public function ajaxFinanceHistoryYearCategoryAction()
    {
        $year_category_data = $this->_getFinanceHistoryYearCategoryData();

        echo json_encode($year_category_data);
        exit;
    }

    public function ajaxFinanceHistoryMonthSpentAction()
    {
        $start_date = date('Y-m-d', strtotime('- 29 day'));
        $month_spent = $this->_adapter_finance_payment->getSumPaymentByDate($start_date);

        echo $month_spent;
        exit;
    }

    public function ajaxFinanceHistoryYearSpentAction()
    {
        $start_date = date('Y-m-d', strtotime('- 1 year'));
        $year_spent = $this->_adapter_finance_payment->getSumPaymentByDate($start_date);

        echo $year_spent;
        exit;
    }

    private function _getFinanceHistoryPeriodData()
    {
        //choose last 30 days data.
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

        return $all_chart_data;
    }

    private function _getFinanceHistoryMonthData()
    {
        $data = [
            'period' => [],
            'payment' => [],
        ];
        $month_data = $this->_adapter_finance_payment->getTotalPaymentHistoryGroupData();
        foreach ($month_data as $month_value)
        {
            $data['period'][] = $month_value['period'];
            $data['payment'][] = $month_value['payment'];
        }

        return $data;
    }

    private function _getFinanceHistoryMonthCategoryData()
    {
        $start_date = date('Y-m-d', strtotime('- 29 day'));
        $month_category_data = $this->_getAllPaymentHistoryDataByCategory($start_date);

        return $month_category_data;
    }

    private function _getFinanceHistoryYearCategoryData()
    {
        $start_date = date('Y-m-d', strtotime('- 1 year'));
        $year_category_data = $this->_getAllPaymentHistoryDataByCategory($start_date);

        return $year_category_data;
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
