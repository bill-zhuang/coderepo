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

        $this->view->chart_data = json_encode($chart_data);
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
            try{
                $group_data = $this->_adapter_dream_history->getTotalDreamHistoryGroupDataByYearMonth($select_date);
                foreach ($group_data as $group_value)
                {
                    $chart_data['period'][] = $group_value['period'];
                    $chart_data['number'][] = $group_value['number'];
                }
            }catch(Exception $e)
            {
                echo $e->getMessage();
            }

        }

        echo json_encode($chart_data);
        exit;
    }
}
