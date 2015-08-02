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
        // action body
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
        exit;
    }

    public function ajaxDreamHistoryPeriodAction()
    {
        list($start_date, $end_date) = $this->_getSearchParams();
        echo json_encode($this->_getAllDreamHistoryDataByDay($start_date, $end_date));
        exit;
    }

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

    private function _index()
    {
        $chart_data = [
            'period' => [],
            'number' => [],
        ];
        $month_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupData();
        foreach ($month_data as $month_value)
        {
            $chart_data['period'][] = $month_value['period'];
            $chart_data['number'][] = $month_value['number'];
        }

        list($start_date, $end_date) = $this->_getSearchParams();
        $all_chart_data = $this->_getAllDreamHistoryDataByDay($start_date, $end_date);

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'chart_data' => $chart_data,
            'all_chart_data' => $all_chart_data,
        ];
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
                intval((strtotime($all_value['period']) - strtotime($all_data[$key - 1]['period'])) / 86400);
        }

        return $all_chart_data;
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
