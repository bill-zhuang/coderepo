<?php
require_once APPLICATION_PATH . '/models/DBTable/Badhistory.php';
class person_BadhistoryController extends Zend_Controller_Action
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
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', 1));
        $page_length = intval($this->_getParam('page_length', 25));
        $order_by = 'bh_create_time desc';
        $start = intval(($current_page - 1) * $page_length);

        $data = $this->_adapter_bad_history->getBadHistoryData($page_length, $start, $order_by);
        $total = $this->_adapter_bad_history->getTotalBadHistoryNumber();

        $this->view->data = $data;
        $this->view->current_page = $current_page;
        $this->view->page_length = $page_length;
        $this->view->total_pages = ceil($total / $page_length) ? ceil($total / $page_length) : 1;
        $this->view->total = $total;
        $this->view->start = $start;
        $this->view->keyword = $keyword;
    }

    public function addbadhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['add_badhistory_date']))
        {
            $occur_date = $_POST['add_badhistory_date'];
            $occur_count = intval($_POST['add_badhistory_count']);
            if (Bill_Util::validDate($occur_date) && $occur_count > 1)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'bh_happen_date' => $occur_date,
                    'bh_count' => $occur_count,
                    'bh_status' => 1,
                    'bh_create_time' => $date,
                    'bh_update_time' => $date
                ];
                $affect_rows = $this->_adapter_bad_history->insert($data);
            }
        }
        
        echo json_encode($affect_rows);
        exit;
    }

    public function getbadhistoryAction()
    {
        $data = [];
        if (isset($_POST['bh_id']))
        {
            $bh_id = intval($_POST['bh_id']);
            if ($bh_id > 0)
            {
                $data = $this->_adapter_bad_history->getBadHistoryDayByID($bh_id);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function modifybadhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['modify_badhistory_id']))
        {
            $bh_id = intval($_POST['modify_badhistory_id']);
            $bh_count = intval($_POST['modify_badhistory_count']);
            if ($bh_id > 0 && $bh_count > 0)
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

    public function deletebadhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['bh_id']))
        {
            $bh_id = intval($_POST['bh_id']);
            if ($bh_id > 0)
            {
                $update_data = [
                    'bh_status' => 0,
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
            'bh_status' => 1,
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

}
