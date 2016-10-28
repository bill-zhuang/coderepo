<?php

class person_GrainRecycleHistoryChartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_GrainRecycleHistory
     */
    private $_adapterGrainRecycleHistory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterGrainRecycleHistory = new Application_Model_DBTable_GrainRecycleHistory();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxGrainRecycleHistoryPeriodAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 month'));
        $endDate = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : date('Y-m-d');
        $data = [];
        $dayInterval = intval((strtotime($endDate) - strtotime($startDate)) / 86400);
        for($i = 0; $i <= $dayInterval; $i++) {
            $periodDate = date('Y-m-d', strtotime($startDate . " + {$i} day"));
            $data[$periodDate] = 0;
        }
        $dayData = $this->_adapterGrainRecycleHistory->getTotalGrainRecycleHistoryDataByDay($startDate, $endDate);
        foreach ($dayData as $dayValue) {
            if (isset($data[$dayValue['period']])) {
                $data[$dayValue['period']] = intval($dayValue['number']);
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

    public function ajaxGrainRecycleHistoryMonthAction()
    {
        $params = trim($this->_getParam('params', []));
        $startDate = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $endDate = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];
        $tempData = [];
        $monthData = $this->_adapterGrainRecycleHistory->getTotalGrainRecycleHistoryGroupData($startDate, $endDate);
        foreach ($monthData as $monthValue) {
            $data['months'][] = $monthValue['period'];
            $tempData[$monthValue['period']] = $monthValue['number'];
        }

        $data['months'] = Bill_Util::getMonthRange($data['months']);
        foreach ($data['months'] as $month) {
            if (isset($tempData[$month])) {
                $data['data'][] = intval($tempData[$month]);
            } else {
                $data['data'][] = 0;
            }
        }
        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => $data
        ];

        echo json_encode($jsonArray);
    }
}
