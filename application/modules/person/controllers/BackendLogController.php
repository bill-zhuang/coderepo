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
        $current_page = intval($this->_getParam('current_page', Bootstrap::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bootstrap::INIT_PAGE_LENGTH));
        $start = ($current_page - Bootstrap::INIT_START_PAGE) * $page_length;
        $keyword = trim($this->_getParam('keyword', ''));

        $conditions = [
            'status' => [
                'compare_type' => '= ?',
                'value' => Bootstrap::VALID_STATUS
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

        $js_data = [
            'current_page' => $current_page,
            'page_length' => $page_length,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bootstrap::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
            'keyword' => $keyword,
        ];
        $view_data = [
            'data' => $data,
            'js_data' => $js_data,
        ];
        $this->view->assign($view_data);
    }
}
