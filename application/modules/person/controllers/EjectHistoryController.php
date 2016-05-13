<?php

class person_EjectHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EjectHistory
     */
    private $_adapterEjectHistory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterEjectHistory = new Application_Model_DBTable_EjectHistory();
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

    public function addEjectHistoryAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $happenDate = isset($params['eject_history_happen_date']) ? $params['eject_history_happen_date'] : '';
                $count = isset($params['eject_history_count']) ? intval($params['eject_history_count']) : 0;
                $type = isset($params['eject_history_type']) ? intval($params['eject_history_type']) : Bill_Constant::EJECT_TYPE_DREAM;
                if (Bill_Util::validDate($happenDate) && $count > 0) {
                    if (!$this->_adapterEjectHistory->isHistoryExistByHappenDateTypeEhid($happenDate, $type)) {
                        $data = [
                            'happen_date' => $happenDate,
                            'count' => $count,
                            'type' => $type,
                            'status' => Bill_Constant::VALID_STATUS,
                            'create_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $affectedRows = $this->_adapterEjectHistory->insert($data);
                        $jsonArray = [
                            'data' => [
                                'code' => $affectedRows,
                                'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                            ],
                        ];
                    } else {
                        $jsonArray = [
                            'data' => [
                                'code' => Bill_Constant::INIT_AFFECTED_ROWS,
                                'message' => Bill_JsMessage::ADD_FAIL,
                            ],
                        ];
                    }
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addEjectHistory');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function modifyEjectHistoryAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ehid = isset($params['eject_history_ehid']) ? intval($params['eject_history_ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $happenDate = isset($params['eject_history_happen_date']) ? $params['eject_history_happen_date'] : '';
                $count = isset($params['eject_history_count']) ? intval($params['eject_history_count']) : 0;
                $type = isset($params['eject_history_type']) ? intval($params['eject_history_type']) : Bill_Constant::EJECT_TYPE_DREAM;
                if ($ehid > Bill_Constant::INVALID_PRIMARY_ID && $count > 0) {
                    if (!$this->_adapterEjectHistory->isHistoryExistByHappenDateTypeEhid($happenDate, $type, $ehid)) {
                        $data = [
                            'happen_date' => $happenDate,
                            'count' => $count,
                            'type' => $type,
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = $this->_adapterEjectHistory->getAdapter()->quoteInto('ehid=?', $ehid);
                        $affectedRows = $this->_adapterEjectHistory->update($data, $where);
                        $jsonArray = [
                            'data' => [
                                'code' => $affectedRows,
                                'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                            ]
                        ];
                    } else {
                        $jsonArray = [
                            'data' => [
                                'code' => Bill_Constant::INIT_AFFECTED_ROWS,
                                'message' => Bill_JsMessage::MODIFY_FAIL,
                            ]
                        ];
                    }
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyEjectHistory');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteEjectHistoryAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ehid = isset($params['ehid']) ? intval($params['ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($ehid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $where = [
                        $this->_adapterEjectHistory->getAdapter()->quoteInto('ehid=?', $ehid),
                        $this->_adapterEjectHistory->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affectedRows = $this->_adapterEjectHistory->delete($where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteEjectHistory');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getEjectHistoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $ehid = (isset($params['ehid'])) ? intval($params['ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterEjectHistory->getEjectHistoryByID($ehid);
            if (!empty($data)) {
                $jsonArray = [
                    'data' => $data,
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
        $types = [
            1 => 'Dream',
            2 => 'Bad',
        ];
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $tabType = isset($params['tab_type']) ? intval($params['tab_type']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($tabType !== 0) {
            $conditions['type=?'] = $tabType;
        }
        $orderBy = 'happen_date DESC';
        $total = $this->_adapterEjectHistory->getEjectHistoryCount($conditions);
        $data = $this->_adapterEjectHistory->getEjectHistoryData($conditions, $currentPage, $pageLength, $orderBy);
        foreach ($data as &$value) {
            $value['type'] = isset($types[$value['type']]) ? $types[$value['type']] : 'Unknown';
        }

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
