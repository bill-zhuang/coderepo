<?php

class person_DreamHistoryController extends Zend_Controller_Action
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

    public function addDreamHistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['dream_history_date']))
        {
            $occur_date = $_POST['dream_history_date'];
            $occur_count = intval($_POST['dream_history_count']);
            if (Bill_Util::validDate($occur_date) && $occur_count > 0)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'dh_happen_date' => $occur_date,
                    'dh_count' => $occur_count,
                    'dh_status' => Bill_Constant::VALID_STATUS,
                    'dh_create_time' => $date,
                    'dh_update_time' => $date
                ];
                $affect_rows = $this->_adapter_dream_history->insert($data);
            }
        }
        
        echo json_encode($affect_rows);
        exit;
    }

    public function getDreamHistoryAction()
    {
        $data = [];
        if (isset($_GET['dh_id']))
        {
            $dh_id = intval($_GET['dh_id']);
            if ($dh_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_dream_history->getDreamHistoryDayByID($dh_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function modifyDreamHistoryAction()
    {
        $affect_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['dream_history_id']))
        {
            $dh_id = intval($_POST['dream_history_id']);
            $dh_count = intval($_POST['dream_history_count']);
            $dh_date = $_POST['dream_history_date'];
            if ($dh_id > Bill_Constant::INVALID_PRIMARY_ID && $dh_count > 0)
            {
                $update_data = [
                    'dh_happen_date' => $dh_date,
                    'dh_count' => $dh_count,
                    'dh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_dream_history->getAdapter()->quoteInto('dh_id=?', $dh_id);
                $affect_rows = $this->_adapter_dream_history->update($update_data, $where);
            }
        }

        echo json_encode($affect_rows);
        exit;
    }

    public function deleteDreamHistoryAction()
    {
        $affect_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['dh_id']))
        {
            $dh_id = intval($_POST['dh_id']);
            if ($dh_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $update_data = [
                    'dh_status' => Bill_Constant::INVALID_STATUS,
                    'dh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_dream_history->getAdapter()->quoteInto('dh_id=?', $dh_id);
                $affect_rows = $this->_adapter_dream_history->update($update_data, $where);
            }
        }

        echo json_encode($affect_rows);
        exit;
    }

    public function fakeAction()
    {
        $init_date = date('Y-m-d');
        $data = [
            'dh_happen_date' => '',
            'dh_count' => 1,
            'dh_status' => Bill_Constant::VALID_STATUS,
            'dh_create_time' => '',
            'dh_update_time' => ''
        ];
        for ($i = 0; $i < 100; $i++)
        {
            $init_date = date('Y-m-d', strtotime($init_date . ' + 3 days'));
            $data['dh_happen_date'] = $init_date;
            $data['dh_create_time'] = $init_date;
            $data['dh_update_time'] = $init_date;

            $this->_adapter_dream_history->insert($data);
        }
        exit;
    }

    private function _index()
    {
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $order_by = 'dh_create_time desc';
        $start = intval(($current_page - Bill_Constant::INIT_START_PAGE) * $page_length);

        $data = $this->_adapter_dream_history->getDreamHistoryData($page_length, $start, $order_by);
        $total = $this->_adapter_dream_history->getTotalDreamHistoryNumber();

        $json_data = [
            'data' => $data,
            'current_page' => $current_page,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bill_Constant::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
        ];
        return $json_data;
    }

}
