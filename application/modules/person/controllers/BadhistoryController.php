<?php

class person_BadHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BadHistory
     */
    private $_adapter_bad_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_bad_history = new Application_Model_DBTable_BadHistory();
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

    public function addBadHistoryAction()
    {
        $affect_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['bad_history_date']))
        {
            $occur_date = $_POST['bad_history_date'];
            $occur_count = intval($_POST['bad_history_count']);
            if (Bill_Util::validDate($occur_date) && $occur_count > 0)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'bh_happen_date' => $occur_date,
                    'bh_count' => $occur_count,
                    'bh_status' => Bill_Constant::VALID_STATUS,
                    'bh_create_time' => $date,
                    'bh_update_time' => $date
                ];
                $affect_rows = $this->_adapter_bad_history->insert($data);
            }
        }
        
        echo json_encode($affect_rows);
        exit;
    }

    public function getBadHistoryAction()
    {
        $data = [];
        if (isset($_GET['bh_id']))
        {
            $bh_id = intval($_GET['bh_id']);
            if ($bh_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_bad_history->getBadHistoryDayByID($bh_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function modifyBadHistoryAction()
    {
        $affect_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['bad_history_id']))
        {
            $bh_id = intval($_POST['bad_history_id']);
            $bh_count = intval($_POST['bad_history_count']);
            if ($bh_id > Bill_Constant::INVALID_PRIMARY_ID && $bh_count > 0)
            {
                $update_data = [
                    'bh_count' => $bh_count,
                    'bh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_bad_history->getAdapter()->quoteInto('bh_id=?', $bh_id);
                $affect_rows = $this->_adapter_bad_history->update($update_data, $where);
            }
        }

        echo json_encode($affect_rows);
        exit;
    }

    public function deleteBadHistoryAction()
    {
        $affect_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['bh_id']))
        {
            $bh_id = intval($_POST['bh_id']);
            if ($bh_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $update_data = [
                    'bh_status' => Bill_Constant::INVALID_STATUS,
                    'bh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_bad_history->getAdapter()->quoteInto('bh_id=?', $bh_id);
                $affect_rows = $this->_adapter_bad_history->update($update_data, $where);
            }
        }

        echo json_encode($affect_rows);
        exit;
    }

    public function fakeAction()
    {
        $init_date = date('Y-m-d');
        $data = [
            'bh_happen_date' => '',
            'bh_count' => 1,
            'bh_status' => Bill_Constant::VALID_STATUS,
            'bh_create_time' => '',
            'bh_update_time' => ''
        ];
        for ($i = 0; $i < 100; $i++)
        {
            $init_date = date('Y-m-d', strtotime($init_date . ' + 3 days'));
            $data['bh_happen_date'] = $init_date;
            $data['bh_create_time'] = $init_date;
            $data['bh_update_time'] = $init_date;

            $this->_adapter_bad_history->insert($data);
        }
        exit;
    }

    private function _index()
    {
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $order_by = 'bh_create_time desc';
        $start = intval(($current_page - Bill_Constant::INIT_START_PAGE) * $page_length);

        $data = $this->_adapter_bad_history->getBadHistoryData($page_length, $start, $order_by);
        $total = $this->_adapter_bad_history->getTotalBadHistoryNumber();

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
