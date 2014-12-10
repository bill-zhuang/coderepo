<?php
require_once APPLICATION_PATH . '/models/DBTable/Dreamhistory.php';
class person_DreamhistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_Dreamhistory
     */
    private $_adapter_dream_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layoutmain');
        $this->_adapter_dream_history = new Application_Model_DBTable_Dreamhistory();
    }

    public function indexAction()
    {
        // action body
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', 1));
        $page_length = intval($this->_getParam('page_length', 25));
        $order_by = 'createdtime desc';
        $start = intval(($current_page - 1) * $page_length);

        $data = $this->_adapter_dream_history->getData($page_length, $start, $order_by);
        $total = $this->_adapter_dream_history->getTotalRecordNumber();

        $this->view->data = $data;
        $this->view->current_page = $current_page;
        $this->view->page_length = $page_length;
        $this->view->total_pages = ceil($total / $page_length) ? ceil($total / $page_length) : 1;
        $this->view->total = $total;
        $this->view->keyword = $keyword;
    }

    public function adddreamhistoryAction()
    {
        $affect_rows = 0;
        if (isset($_POST['add_dreamhistory_date']))
        {
            $occur_date = $_POST['add_dreamhistory_date'];
            $occur_count = $_POST['add_dreamhistory_count'];
            $date = date('Y-m-d H:i:s');
            $data = [
                'happeneddate' => $occur_date,
                'count' => $occur_count,
                'status' => 1,
                'createdtime' => $date,
                'updatedtime' => $date
            ];
            $affect_rows = $this->_adapter_dream_history->insert($data);
        }
        
        echo json_encode($affect_rows);
        exit;
    }

    public function fakeAction()
    {
        $init_date = date('Y-m-d');
        $data = [
            'happeneddate' => '',
            'count' => 1,
            'status' => 1,
            'createdtime' => '',
            'updatedtime' => ''
        ];
        for ($i = 0; $i < 100; $i++)
        {
            $init_date = date('Y-m-d', strtotime($init_date . ' + 3 days'));
            $data['happeneddate'] = $init_date;
            $data['createdtime'] = $init_date;
            $data['updatedtime'] = $init_date;

            $this->_adapter_dream_history->insert($data);
        }
        exit;
    }

}
