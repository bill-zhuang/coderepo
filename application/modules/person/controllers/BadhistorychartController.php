<?php
require_once APPLICATION_PATH . '/models/DBTable/Badhistory.php';
class person_BadhistorychartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_Badhistory
     */
    private $_adapter_bad_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_bad_history = new Application_Model_DBTable_Badhistory();
    }

    public function indexAction()
    {
        // action body
        $all_chart_data = [
            'period' => [],
            'number' => [],
        ];
        $all_data = $this->_adapter_bad_history->getTotalBadHistoryDataByDay();
        foreach ($all_data as $key => $all_value)
        {
            $all_chart_data['period'][] = $all_value['period'];
            $all_chart_data['number'][] = $all_value['number'];
            $all_chart_data['interval'][] = ($key == 0) ? 0 :
                intval((strtotime($all_value['period']) - strtotime($all_data[$key - 1]['period'])) / 86400);
        }

        $this->view->all_chart_data = json_encode($all_chart_data);
    }
}
