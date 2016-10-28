<?php

class person_FinanceHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapterFinanceCategory;
    /**
     * @var Application_Model_DBTable_FinancePayment
     */
    private $_adapterFinancePayment;
    private $_categories;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterFinanceCategory = new Application_Model_DBTable_FinanceCategory();
        $this->_adapterFinancePayment = new Application_Model_DBTable_FinancePayment();
        $this->_categories = $this->_adapterFinanceCategory->getAllParentCategory(true);
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
        $startDate = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 month'));
        $endDate = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : date('Y-m-d');
        $fcid = (isset($params['day_category_id'])) ? intval($params['day_category_id']) : 0;
        $ignoreMoney = (isset($params['payment_ignore'])) ? floatval($params['payment_min']) : 0;
        $data = [];
        $dayInterval = intval((strtotime($endDate) - strtotime($startDate)) / 86400);
        for($i = 0; $i <= $dayInterval; $i++) {
            $periodDate = date('Y-m-d', strtotime($startDate . " + {$i} day"));
            $data[$periodDate] = 0.00;
        }
        $dayData = $this->_adapterFinancePayment->getTotalPaymentHistoryDataByDay($startDate, $endDate, $fcid, $ignoreMoney);
        foreach ($dayData as $dayValue) {
            if (isset($data[$dayValue['period']])) {
                $data[$dayValue['period']] = floatval($dayValue['payment']);
            }
        }
        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => [
                'days' => array_keys($data),
                'data' => array_values($data),
            ],
        ];

        echo json_encode($jsonArray);
    }

    public function ajaxFinanceHistoryMonthAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $endDate = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];
        $tempData = [];
        $monthData = $this->_adapterFinancePayment->getTotalPaymentHistoryGroupData($startDate, $endDate);
        foreach ($monthData as $monthValue) {
            $data['months'][] = $monthValue['period'];
            $tempData[$monthValue['period']] = $monthValue['payment'];
        }
        $data['months'] = Bill_Util::getMonthRange($data['months']);
        foreach ($data['months'] as $month) {
            if (isset($tempData[$month])) {
                $data['data'][] = floatval($tempData[$month]);
            } else {
                $data['data'][] = 0.00;
            }
        }
        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => $data,
        ];

        echo json_encode($jsonArray);
    }

    public function ajaxFinanceHistoryMonthCategoryAction()
    {
        $startDate = date('Y-m-d', strtotime('-1 month'));
        $monthData = $this->_getAllPaymentHistoryDataByCategory($startDate);
        $monthData['total'] = $this->_adapterFinancePayment->getSumPaymentByDate($startDate);
        $jsonArray = [
            'data' => $monthData,
        ];

        echo json_encode($jsonArray);
    }

    public function ajaxFinanceHistoryYearCategoryAction()
    {
        $startDate = date('Y-m-d', strtotime('- 1 year'));
        $yearData = $this->_getAllPaymentHistoryDataByCategory($startDate);
        $yearData['total'] = $this->_adapterFinancePayment->getSumPaymentByDate($startDate);
        $jsonArray = [
            'data' => $yearData,
        ];

        echo json_encode($jsonArray);
    }

    private function _getAllPaymentHistoryDataByCategory($startDate)
    {
        $data = [
            'categories' => [],
            'data' => [],
        ];
        $yearData = $this->_adapterFinancePayment->getTotalPaymentHistoryDataByCategory($startDate);
        foreach ($yearData as $yearValue) {
            $data['categories'][] = isset($this->_categories[$yearValue['fcid']])
                ? $this->_categories[$yearValue['fcid']] : '';
            $data['data'][] = floatval($yearValue['payment']);
        }

        return $data;
    }
}
