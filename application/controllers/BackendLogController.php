<?php

class BackendLogController extends Zend_Controller_Action
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
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_backend_log= new Application_Model_DBTable_BackendLog();
        $this->_adapter_backend_user= new Application_Model_DBTable_BackendUser();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status=?' => Bill_Constant::VALID_STATUS
        ];
        if ('' !== $keyword) {
            $conditions['content like ?'] = Bill_Util::getLikeString($keyword);
        }
        $order_by = 'blid DESC';
        $total = $this->_adapter_backend_log->getBackendLogCount($conditions);
        $data = $this->_adapter_backend_log->getBackendLogData($conditions, $current_page, $page_length, $order_by);
        $cacheUserName = [];
        foreach ($data as &$value) {
            if (!isset($cacheUserName[$value['buid']])) {
                $cacheUserName[$value['buid']] = $this->_adapter_backend_user->getUserName($value['buid']);
            }
            $value['name'] = $cacheUserName[$value['buid']];
        }

        $json_array = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $page_length),
                'pageIndex' => $current_page,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $page_length,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];

        return $json_array;
    }
}
