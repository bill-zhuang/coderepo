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
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_FinancePayment();
        $this->_categories = $this->_adapter_finance_category->getAllParentCategory(true);
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxFinanceHistoryPeriodAction()
    {
        $params = $this->_getParam('params', []);
        $start_date = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 month'));
        $end_date = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : date('Y-m-d');
        $fcid = (isset($params['day_category_id'])) ? intval($params['day_category_id']) : 0;
        $data = [];
        $day_interval = intval((strtotime($end_date) - strtotime($start_date)) / 86400);
        for($i = 0; $i <= $day_interval; $i++) {
            $period_date = date('Y-m-d', strtotime($start_date . " + {$i} day"));
            $data[$period_date] = 0.00;
        }
        $day_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByDay($start_date, $end_date, $fcid);
        foreach ($day_data as $day_value) {
            if (isset($data[$day_value['period']])) {
                $data[$day_value['period']] = floatval($day_value['payment']);
            }
        }
        $json_array = [
            'data' => [
                'days' => array_keys($data),
                'data' => array_values($data),
            ],
        ];

        echo json_encode($json_array);
    }

    public function ajaxFinanceHistoryMonthAction()
    {
        $params = $this->_getParam('params', []);
        $start_date = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $end_date = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];
        $temp_data = [];
        $month_data = $this->_adapter_finance_payment->getTotalPaymentHistoryGroupData($start_date, $end_date);
        foreach ($month_data as $month_value) {
            $data['months'][] = $month_value['period'];
            $temp_data[$month_value['period']] = $month_value['payment'];
        }
        $data['months'] = Bill_Util::getMonthRange($data['months']);
        foreach ($data['months'] as $month) {
            if (isset($temp_data[$month])) {
                $data['data'][] = floatval($temp_data[$month]);
            } else {
                $data['data'][] = 0.00;
            }
        }
        $json_array = [
            'data' => $data,
        ];

        echo json_encode($json_array);
    }

    public function ajaxFinanceHistoryMonthCategoryAction()
    {
        $start_date = date('Y-m-d', strtotime('-1 month'));
        $month_data = $this->_getAllPaymentHistoryDataByCategory($start_date);
        $month_data['total'] = $this->_adapter_finance_payment->getSumPaymentByDate($start_date);
        $json_array = [
            'data' => $month_data,
        ];

        echo json_encode($json_array);
    }

    public function ajaxFinanceHistoryYearCategoryAction()
    {
        $start_date = date('Y-m-d', strtotime('- 1 year'));
        $year_data = $this->_getAllPaymentHistoryDataByCategory($start_date);
        $year_data['total'] = $this->_adapter_finance_payment->getSumPaymentByDate($start_date);
        $json_array = [
            'data' => $year_data,
        ];

        echo json_encode($json_array);
    }

    private function _getAllPaymentHistoryDataByCategory($start_date)
    {
        $data = [
            'categories' => [],
            'data' => [],
        ];
        $year_data = $this->_adapter_finance_payment->getTotalPaymentHistoryDataByCategory($start_date);
        foreach ($year_data as $year_value) {
            $data['categories'][] = isset($this->_categories[$year_value['fcid']])
                ? $this->_categories[$year_value['fcid']] : '';
            $data['data'][] = floatval($year_value['payment']);
        }

        return $data;
    }
}
