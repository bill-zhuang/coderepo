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
        list($start_date, $end_date) = $this->_getSearchParams();
        $period_data = $this->_getFinanceHistoryPeriodData($start_date, $end_date);

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
        $start_date = date('Y-m-d', strtotime('-1 month'));
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

    private function _getFinanceHistoryPeriodData($start_date, $end_date)
    {
        $day_interval = intval((strtotime($end_date) - strtotime($start_date)) / 86400);
        $all_chart_data = $this->_getAllPaymentHistoryDataByDay($start_date, $end_date);
        $sort_chart_data = [];
        if (count($all_chart_data['period']) != $day_interval)
        {
            for($i = 1; $i <= $day_interval; $i++)
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
        foreach ($month_data as $key => $month_value)
        {
            if ($key > 0)
            {
                $previous_month = date('Y-m', strtotime($month_value['period'] . ' - 1 month'));
                while (1)
                {
                    if ($previous_month != end($data['period']))
                    {
                        $data['period'][] = date('Y-m', strtotime(end($data['period']) . ' + 1 month'));
                        $data['payment'][] = 0;
                    }
                    else
                    {
                        $data['period'][] = $month_value['period'];
                        $data['payment'][] = $month_value['payment'];
                        break;
                    }
                }
            }
            else
            {
                $data['period'][] = $month_value['period'];
                $data['payment'][] = $month_value['payment'];
            }
        }

        return $data;
    }

    private function _getFinanceHistoryMonthCategoryData()
    {
        $start_date = date('Y-m-d', strtotime('-1 month'));
        $month_category_data = $this->_getAllPaymentHistoryDataByCategory($start_date);

        return $month_category_data;
    }

    private function _getFinanceHistoryYearCategoryData()
    {
        $start_date = date('Y-m-d', strtotime('- 1 year'));
        $year_category_data = $this->_getAllPaymentHistoryDataByCategory($start_date);

        return $year_category_data;
    }

    private function _getAllPaymentHistoryDataByDay($start_date, $end_date)
    {
        $all_chart_data = [
            'period' => [],
            'payment' => [],
        ];
        $all_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByDay($start_date, $end_date);
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

    private function _getSearchParams()
    {
        $start_date = trim($this->getParam('start_date', date('Y-m-d', strtotime('-1 month'))));
        $end_date = trim($this->getParam('end_date', date('Y-m-d H:i:s')));

        return [
            $start_date,
            $end_date,
        ];
    }
}
