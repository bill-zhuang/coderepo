<?php

class person_GrainRecycleHistoryChartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_GrainRecycleHistory
     */
    private $_adapter_grain_recycle_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_grain_recycle_history = new Application_Model_DBTable_GrainRecycleHistory();
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
        $start_date = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 month'));
        $end_date = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : date('Y-m-d');
        $data = [];
        $day_interval = intval((strtotime($end_date) - strtotime($start_date)) / 86400);
        for($i = 0; $i <= $day_interval; $i++) {
            $period_date = date('Y-m-d', strtotime($start_date . " + {$i} day"));
            $data[$period_date] = 0;
        }
        $day_data = $this->_adapter_grain_recycle_history->getTotalGrainRecycleHistoryDataByDay($start_date, $end_date);
        foreach ($day_data as $day_value) {
            if (isset($data[$day_value['period']])) {
                $data[$day_value['period']] = intval($day_value['number']);
            }
        }
        $json_array = [
            'data' => [
                'days' => array_keys($data),
                'data' => [
                    [
                        'name' => 'Grain Recycle Count',
                        'data' => array_values($data),
                    ],
                ],
            ],
        ];

        echo json_encode($json_array);
    }

    public function ajaxGrainRecycleHistoryMonthAction()
    {
        $params = trim($this->_getParam('params', []));
        $start_date = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $end_date = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];
        $months = [];
        $temp_data = [];
        $month_data = $this->_adapter_grain_recycle_history->getTotalGrainRecycleHistoryGroupData($start_date, $end_date);
        foreach ($month_data as $month_value) {
            if (!in_array($month_value['period'], $months)) {
                $months[] = $month_value['period'];
            }
            $temp_data[$month_value['period']] = $month_value['number'];
        }
        $data['months'] = $this->_getMonthsRange($months);

        $type_data = [];
        foreach ($data['months'] as $month) {
            if (isset($temp_data[$month])) {
                $type_data[] = intval($temp_data[$month]);
            } else {
                $type_data[] = 0;
            }
        }
        $data['data'][] = [
            'name' => 'Grain Recycle Count',
            'data' => $type_data,
        ];
        $json_array = [
            'data' => $data
        ];

        echo json_encode($json_array);
    }

    private function _getMonthsRange(array $months)
    {

        if (count($months) > 2) {
            sort($months);
            $min_month = $months[0];
            $max_month_timestamp = $months[count($months) - 1];
            for ($i = 1; ; $i++) {
                $next_month = date('Y-m', strtotime($min_month . "+ {$i} month"));
                if ((strtotime($next_month) <= $max_month_timestamp) && !in_array($next_month, $months)) {
                    $months[] = $next_month;
                } else {
                    break;
                }
            }
            sort($months);
        }

        return $months;
    }
}
