<?php

class person_EjectHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EjectHistory
     */
    private $_adapter_eject_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_eject_history= new Application_Model_DBTable_EjectHistory();
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
                $happen_date = isset($params['eject_history_happen_date']) ? $params['eject_history_happen_date'] : '';
                $count = isset($params['eject_history_count']) ? intval($params['eject_history_count']) : 0;
                $type = isset($params['eject_history_type']) ? intval($params['eject_history_type']) : Bill_Constant::EJECT_TYPE_DREAM;
                if (Bill_Util::validDate($happen_date) && $count > 0) {
                    if (!$this->_adapter_eject_history->isHistoryExistByHappenDateTypeEhid($happen_date, $type)) {
                        $data = [
                            'happen_date' => $happen_date,
                            'count' => $count,
                            'type' => $type,
                            'status' => Bill_Constant::VALID_STATUS,
                            'create_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $affected_rows = $this->_adapter_eject_history->insert($data);
                        $json_array = [
                            'data' => [
                                'code' => $affected_rows,
                                'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                            ],
                        ];
                    } else {
                        $json_array = [
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

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function modifyEjectHistoryAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ehid = isset($params['eject_history_ehid']) ? intval($params['eject_history_ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $happen_date = isset($params['eject_history_happen_date']) ? $params['eject_history_happen_date'] : '';
                $count = isset($params['eject_history_count']) ? intval($params['eject_history_count']) : 0;
                $type = isset($params['eject_history_type']) ? intval($params['eject_history_type']) : Bill_Constant::EJECT_TYPE_DREAM;
                if ($ehid > Bill_Constant::INVALID_PRIMARY_ID && $count > 0) {
                    if (!$this->_adapter_eject_history->isHistoryExistByHappenDateTypeEhid($happen_date, $type, $ehid)) {
                        $data = [
                            'happen_date' => $happen_date,
                            'count' => $count,
                            'type' => $type,
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = $this->_adapter_eject_history->getAdapter()->quoteInto('ehid=?', $ehid);
                        $affected_rows = $this->_adapter_eject_history->update($data, $where);
                        $json_array = [
                            'data' => [
                                'code' => $affected_rows,
                                'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                            ]
                        ];
                    } else {
                        $json_array = [
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

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function deleteEjectHistoryAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ehid = isset($params['ehid']) ? intval($params['ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($ehid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $where = [
                        $this->_adapter_eject_history->getAdapter()->quoteInto('ehid=?', $ehid),
                        $this->_adapter_eject_history->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows = $this->_adapter_eject_history->delete($where);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteEjectHistory');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }
    
    public function getEjectHistoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $ehid = (isset($params['ehid'])) ? intval($params['ehid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_eject_history->getEjectHistoryByID($ehid);
            if (!empty($data)) {
                $json_array = [
                    'data' => $data,
                ];
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }

    private function _index()
    {
        $types = [
            1 => 'Dream',
            2 => 'Bad',
        ];
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $tab_type = isset($params['tab_type']) ? intval($params['tab_type']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($tab_type !== 0) {
            $conditions['type=?'] = $tab_type;
        }
        $order_by = 'happen_date DESC';
        $total = $this->_adapter_eject_history->getEjectHistoryCount($conditions);
        $data = $this->_adapter_eject_history->getEjectHistoryData($conditions, $current_page, $page_length, $order_by);
        foreach ($data as &$value) {
            $value['type'] = isset($types[$value['type']]) ? $types[$value['type']] : 'Unknown';
        }

        $json_data = [
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
        return $json_data;
    }
    
}
