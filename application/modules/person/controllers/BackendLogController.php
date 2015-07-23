<?php

class person_BackendLogController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendLog
     */
    private $_adapter_backend_log;
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapter_backend_user;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_backend_log= new Application_Model_DBTable_BackendLog();
        $this->_adapter_backend_user= new Application_Model_DBTable_BackendUser();
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

    private function _index()
    {
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $start = ($current_page - Bill_Constant::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));

        $conditions = [
            'status' => [
                'compare_type' => '= ?',
                'value' => Bill_Constant::VALID_STATUS
            ]
        ];
        if ('' !== $keyword)
        {
            $conditions['content'] = [
                'compare_type' => 'like ?',
                'value' => '%' . $keyword . '%'
            ];
        }
        $order_by = 'blid DESC';
        $total = $this->_adapter_backend_log->getBackendLogCount($conditions);
        $data = $this->_adapter_backend_log->getBackendLogData($conditions, $page_length, $start, $order_by);
        foreach ($data as $key => $value)
        {
            $data[$key]['name'] = $this->_adapter_backend_user->getUserName($value['buid']);
        }

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
