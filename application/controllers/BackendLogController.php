<?php

class BackendLogController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendLog
     */
    private $_adapterBackendLog;
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapterBackendUser;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterBackendLog = new Application_Model_DBTable_BackendLog();
        $this->_adapterBackendUser = new Application_Model_DBTable_BackendUser();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status=?' => Bill_Constant::VALID_STATUS
        ];
        if ('' !== $keyword) {
            $conditions['content like ?'] = Bill_Util::getLikeString($keyword);
        }
        $orderBy = 'blid DESC';
        $total = $this->_adapterBackendLog->getBackendLogCount($conditions);
        $data = $this->_adapterBackendLog->getBackendLogData($conditions, $currentPage, $pageLength, $orderBy);
        $cacheUserName = [];
        foreach ($data as &$value) {
            if (!isset($cacheUserName[$value['buid']])) {
                $cacheUserName[$value['buid']] = $this->_adapterBackendUser->getUserName($value['buid']);
            }
            $value['name'] = $cacheUserName[$value['buid']];
        }

        $jsonArray = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $pageLength),
                'pageIndex' => $currentPage,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $pageLength,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];

        return $jsonArray;
    }
}
