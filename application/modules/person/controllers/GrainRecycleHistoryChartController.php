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
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_grain_recycle_history = new Application_Model_DBTable_GrainRecycleHistory();
    }

    public function indexAction()
    {
        // action body
    }

    public function ajaxGrainRecycleHistoryPeriodAction()
    {
        list($start_date, $end_date) = $this->_getSearchParams();
        $data = $this->_getAllGrainRecycleHistoryDataByDay($start_date, $end_date);

        echo json_encode($data);
        exit;
    }

    public function ajaxGrainRecycleHistoryMonthAction()
    {
        $data = $this->_getAllDreamHistoryDataByMonth();

        echo json_encode($data);
        exit;
    }

    public function getGrainRecycleHistoryMonthDetailAction()
    {
        $chart_data = [
            'period' => [],
            'number' => [],
        ];
        if (isset($_POST['select_date']))
        {
            $select_date = $_POST['select_date'];
            $group_data = $this->_adapter_grain_recycle_history->getTotalGrainRecycleHistoryGroupDataByYearMonth($select_date);
            foreach ($group_data as $group_value)
            {
                $chart_data['period'][] = $group_value['period'];
                $chart_data['number'][] = $group_value['number'];
            }
        }

        echo json_encode($chart_data);
        exit;
    }

    private function _getAllGrainRecycleHistoryDataByDay($start_date, $end_date)
    {
        $all_chart_data = [
            'period' => [],
            'number' => [],
        ];
        $day_interval = intval((strtotime($end_date) - strtotime($start_date)) / 86400);
        $all_data = $this->_adapter_grain_recycle_history->getTotalGrainRecycleHistoryDataByDay($start_date, $end_date);
        foreach ($all_data as $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['number'][] = $all_value['number'];
        }

        if (count($all_chart_data['period']) != $day_interval)
        {
            $sort_chart_data = [];
            for($i = 1; $i <= $day_interval; $i++)
            {
                $period_date = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' day'));
                $sort_chart_data['period'][] = $period_date;
                if (!in_array($period_date, $all_chart_data['period']))
                {
                    $sort_chart_data['number'][] = 0;
                }
                else
                {
                    $period_key = array_search($period_date, $all_chart_data['period']);
                    $sort_chart_data['number'][] = $all_chart_data['number'][$period_key];
                }
            }
            $all_chart_data = $sort_chart_data;
        }

        return $all_chart_data;
    }

    private function _getAllDreamHistoryDataByMonth()
    {
        $data = [
            'period' => [],
            'number' => [],
        ];
        $month_data = $this->_adapter_grain_recycle_history->getTotalGrainRecycleHistoryGroupData();
        foreach ($month_data as $month_value)
        {
            $data['period'][] = $month_value['period'];
            $data['number'][] = $month_value['number'];
        }

        return $data;
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
