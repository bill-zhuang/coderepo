<?php

class person_DreamHistoryChartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_DreamHistory
     */
    private $_adapter_dream_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_dream_history = new Application_Model_DBTable_DreamHistory();
    }

    public function indexAction()
    {
        //action body
    }

    public function ajaxDreamHistoryPeriodAction()
    {
        $params = $this->_getParam('params', []);
        $start_date = isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date'])
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 year'));
        $end_date = isset($params['day_end_date']) ? trim($params['day_end_date']) : '';
        $data = $this->_getAllDreamHistoryDataByDay($start_date, $end_date);
        $json_array = [
            'data' => $data
        ];
        
        echo json_encode($json_array);
        exit;
    }

    public function ajaxDreamHistoryMonthAction()
    {
        $params = trim($this->_getParam('params', []));
        $start_date = isset($params['month_start_date']) ? trim($params['month_start_date']) : date('Y-m', strtotime('-1 year')) . '-01';
        $end_date = isset($params['month_end_date']) ? trim($params['month_end_date']) : '';
        $data = $this->_getAllDreamHistoryDataByMonth($start_date, $end_date);
        $json_array = [
            'data' => $data
        ];

        echo json_encode($json_array);
        exit;
    }

    //not used
    public function getDreamHistoryMonthDetailAction()
    {
        $chart_data = [
            'period' => [],
            'number' => [],
        ];
        if (isset($_POST['select_date'])) {
            $select_date = $_POST['select_date'];
            $group_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupDataByYearMonth($select_date);
            foreach ($group_data as $group_value) {
                $chart_data['period'][] = $group_value['period'];
                $chart_data['number'][] = $group_value['number'];
            }
        }

        echo json_encode($chart_data);
        exit;
    }

    private function _getAllDreamHistoryDataByDay($start_date, $end_date)
    {
        $all_chart_data = [
            'period' => [],
            'number' => [],
        ];
        $all_data = $this->_adapter_dream_history->getTotalDreamHistoryDataByDay($start_date, $end_date);
        foreach ($all_data as $key => $all_value) {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['number'][] = $all_value['number'];
            $all_chart_data['interval'][] = ($key == 0) ? 0 :
                intval((strtotime($all_value['period']) - strtotime($all_data[$key - 1]['period'])) / Bill_Constant::DAY_SECONDS);
        }

        return $all_chart_data;
    }

    private function _getAllDreamHistoryDataByMonth($start_date, $end_date)
    {
        $data = [
            'period' => [],
            'number' => [],
        ];
        $month_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupData($start_date, $end_date);
        foreach ($month_data as $month_value) {
            $data['period'][] = $month_value['period'];
            $data['number'][] = $month_value['number'];
        }

        return $data;
    }
}
