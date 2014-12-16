<?php

class person_DreamhistoryController extends Zend_Controller_Action
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
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', 1));
        $page_length = intval($this->_getParam('page_length', 25));
        $order_by = 'dh_create_time desc';
        $start = intval(($current_page - 1) * $page_length);

        $data = $this->_adapter_dream_history->getDreamHistoryData($page_length, $start, $order_by);
        $total = $this->_adapter_dream_history->getTotalDreamHistoryNumber();

        $view_data = [
            'data' => $data,
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : 1,
            'total' => $total,
            'start' => $start,
            'keyword' => $keyword
        ];
        $this->view->assign($view_data);
    }

    public function adddreamhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['add_dreamhistory_date']))
        {
            $occur_date = $_POST['add_dreamhistory_date'];
            $occur_count = intval($_POST['add_dreamhistory_count']);
            if (Bill_Util::validDate($occur_date) && $occur_count > 0)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'dh_happen_date' => $occur_date,
                    'dh_count' => $occur_count,
                    'dh_status' => 1,
                    'dh_create_time' => $date,
                    'dh_update_time' => $date
                ];
                $affect_rows = $this->_adapter_dream_history->insert($data);
            }
        }
        
        echo json_encode($affect_rows);
        exit;
    }

    public function getdreamhistoryAction()
    {
        $data = [];
        if (isset($_POST['dh_id']))
        {
            $dh_id = intval($_POST['dh_id']);
            if ($dh_id > 0)
            {
                $data = $this->_adapter_dream_history->getDreamHistoryDayByID($dh_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function modifydreamhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['modify_dreamhistory_id']))
        {
            $dh_id = intval($_POST['modify_dreamhistory_id']);
            $dh_count = intval($_POST['modify_dreamhistory_count']);
            if ($dh_id > 0 && $dh_count > 0)
            {
                $update_data = [
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

    public function deletedreamhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['dh_id']))
        {
            $dh_id = intval($_POST['dh_id']);
            if ($dh_id > 0)
            {
                $update_data = [
                    'dh_status' => 0,
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
            'dh_status' => 1,
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

}
