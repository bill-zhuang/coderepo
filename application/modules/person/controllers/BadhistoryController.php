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

        $data = $this->_adapter_bad_history->getData($page_length, $start, $order_by);
        $total = $this->_adapter_bad_history->getTotalRecordNumber();

        $this->view->data = $data;
        $this->view->current_page = $current_page;
        $this->view->page_length = $page_length;
        $this->view->total_pages = ceil($total / $page_length) ? ceil($total / $page_length) : 1;
        $this->view->total = $total;
        $this->view->keyword = $keyword;
    }

    public function addbadhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['add_badhistory_date']))
        {
            $occur_date = $_POST['add_badhistory_date'];
            $occur_count = $_POST['add_badhistory_count'];
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
