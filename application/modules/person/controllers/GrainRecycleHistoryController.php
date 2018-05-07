<?php

class person_GrainRecycleHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_GrainRecycleHistory
     */
    private $_adapterGrainRecycleHistory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterGrainRecycleHistory = new Application_Model_DBTable_GrainRecycleHistory();
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

    public function addGrainRecycleHistoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $occurDate = isset($params['grain_recycle_history_happen_date']) ? trim($params['grain_recycle_history_happen_date']) : '';
            $occurCount = isset($params['grain_recycle_history_count']) ? intval($params['grain_recycle_history_count']) : 0;
            if (Bill_Util::validDate($occurDate) && $occurCount > 0) {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'happen_date' => $occurDate,
                    'count' => $occurCount,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $date,
                    'update_time' => $date
                ];
                $affectedRows = $this->_adapterGrainRecycleHistory->insert($data);
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                    ],
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    public function modifyGrainRecycleHistoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $grhid = isset($params['grain_recycle_history_grhid'])
                ? intval($params['grain_recycle_history_grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $occurCount = isset($params['grain_recycle_history_count']) ?  intval($params['grain_recycle_history_count']) : 0;
            $occurDate = isset($params['grain_recycle_history_happen_date']) ? trim($params['grain_recycle_history_happen_date']) : '';
            if ($grhid > Bill_Constant::INVALID_PRIMARY_ID && $occurCount > 0 && Bill_Util::validDate($occurDate)) {
                $updateData = [
                    'happen_date' => $occurDate,
                    'count' => $occurCount,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapterGrainRecycleHistory->getAdapter()->quoteInto('grhid=?', $grhid);
                $affectedRows = $this->_adapterGrainRecycleHistory->update($updateData, $where);
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                    ]
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    public function deleteGrainRecycleHistoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $grhid = isset($params['grhid']) ? intval($params['grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($grhid > Bill_Constant::INVALID_PRIMARY_ID) {
                $updateData = [
                    'status' => Bill_Constant::INVALID_STATUS,
                    'update_time' => date('Y-m-d H:i:s'),
                ];
                $where = [
                    $this->_adapterGrainRecycleHistory->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    $this->_adapterGrainRecycleHistory->getAdapter()->quoteInto('grhid=?', $grhid),
                ];
                $affectedRows = $this->_adapterGrainRecycleHistory->update($updateData, $where);
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                    ]
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    public function getGrainRecycleHistoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $grhid = (isset($params['grhid'])) ? intval($params['grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $historyData = $this->_adapterGrainRecycleHistory->getByPrimaryKey($grhid);
            if (!empty($historyData)) {
                $jsonArray = [
                    'data' => $historyData,
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        $orderBy = ['happen_date DESC', 'grhid DESC'];
        $total = $this->_adapterGrainRecycleHistory->getSearchCount($conditions);
        $data = $this->_adapterGrainRecycleHistory->getSearchData($conditions, $currentPage, $pageLength, $orderBy);

        $jsonData = [
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
        return $jsonData;
    }
}
