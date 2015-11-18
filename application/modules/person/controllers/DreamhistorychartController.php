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
        list($start_date, $end_date) = $this->_getSearchParams();
        $data = $this->_getAllDreamHistoryDataByDay($start_date, $end_date);
        
        echo json_encode($data);
        exit;
    }

    public function ajaxDreamHistoryMonthAction()
    {
        $data = $this->_getAllDreamHistoryDataByMonth();

        echo json_encode($data);
        exit;
    }

    //not used
    public function getDreamHistoryMonthDetailAction()
    {
        $chart_data = [
            'period' => [],
            'number' => [],
        ];
        if (isset($_POST['select_date']))
        {
            $select_date = $_POST['select_date'];
            $group_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupDataByYearMonth($select_date);
            foreach ($group_data as $group_value)
            {
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
        foreach ($all_data as $key => $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['number'][] = $all_value['number'];
            $all_chart_data['interval'][] = ($key == 0) ? 0 :
                intval((strtotime($all_value['period']) - strtotime($all_data[$key - 1]['period'])) / Bill_Constant::DAY_SECONDS);
        }

        return $all_chart_data;
    }

    private function _getAllDreamHistoryDataByMonth()
    {
        $data = [
            'period' => [],
            'number' => [],
        ];
        $month_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupData();
        foreach ($month_data as $month_value)
        {
            $data['period'][] = $month_value['period'];
            $data['number'][] = $month_value['number'];
        }

        return $data;
    }

    private function _getSearchParams()
    {
        $start_date = trim($this->getParam('start_date', date('Y-m-d', strtotime('-1 year'))));
        $end_date = trim($this->getParam('end_date', ''));

        return [
            $start_date,
            $end_date,
        ];
    }
}
