<?php
require_once APPLICATION_PATH . '/models/DBTable/Dreamhistory.php';
class person_DreamhistorychartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_Dreamhistory
     */
    private $_adapter_dream_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_dream_history = new Application_Model_DBTable_Dreamhistory();
    }

    public function indexAction()
    {
        // action body
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

        $all_chart_data = $this->_getAllDreamHistoryDataByDay();

        $this->view->chart_data = json_encode($chart_data);
        $this->view->all_chart_data = json_encode($all_chart_data);
    }

    public function getdreamhistorymonthdetailAction()
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

    private function _getAllDreamHistoryDataByDay()
    {
        $all_chart_data = [
            'period' => [],
            'number' => [],
        ];
        $all_data = $this->_adapter_dream_history->getTotalDreamHistoryDataByDay();
        foreach ($all_data as $key => $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['number'][] = $all_value['number'];
            $all_chart_data['interval'][] = ($key == 0) ? 0 :
                intval((strtotime($all_value['period']) - strtotime($all_data[$key - 1]['period'])) / 86400);
        }

        return $all_chart_data;
    }
}
