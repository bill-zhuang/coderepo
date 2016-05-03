<?php

class person_GrainRecycleHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_GrainRecycleHistory
     */
    private $_adapter_grain_recycle_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_grain_recycle_history = new Application_Model_DBTable_GrainRecycleHistory();
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

    public function addGrainRecycleHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $occur_date = isset($params['grain_recycle_history_happen_date']) ? trim($params['grain_recycle_history_happen_date']) : '';
            $occur_count = isset($params['grain_recycle_history_count']) ? intval($params['grain_recycle_history_count']) : 0;
            if (Bill_Util::validDate($occur_date) && $occur_count > 0) {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'happen_date' => $occur_date,
                    'count' => $occur_count,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $date,
                    'update_time' => $date
                ];
                $affected_rows = $this->_adapter_grain_recycle_history->insert($data);
                $json_array = [
                    'data' => [
                        'code' => $affected_rows,
                        'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                    ],
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

    public function modifyGrainRecycleHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $grhid = isset($params['grain_recycle_history_grhid'])
                ? intval($params['grain_recycle_history_grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $occur_count = isset($params['grain_recycle_history_count']) ?  intval($params['grain_recycle_history_count']) : 0;
            $occur_date = isset($params['grain_recycle_history_happen_date']) ? trim($params['grain_recycle_history_happen_date']) : '';
            if ($grhid > Bill_Constant::INVALID_PRIMARY_ID && $occur_count > 0 && Bill_Util::validDate($occur_date)) {
                $update_data = [
                    'happen_date' => $occur_date,
                    'count' => $occur_count,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('grhid=?', $grhid);
                $affected_rows = $this->_adapter_grain_recycle_history->update($update_data, $where);
                $json_array = [
                    'data' => [
                        'code' => $affected_rows,
                        'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                    ]
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

    public function deleteGrainRecycleHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $grhid = isset($params['grhid']) ? intval($params['grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($grhid > Bill_Constant::INVALID_PRIMARY_ID) {
                $update_data = [
                    'status' => Bill_Constant::INVALID_STATUS,
                    'update_time' => date('Y-m-d H:i:s'),
                ];
                $where = [
                    $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('grhid=?', $grhid),
                ];
                $affected_rows = $this->_adapter_grain_recycle_history->update($update_data, $where);
                $json_array = [
                    'data' => [
                        'code' => $affected_rows,
                        'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                    ]
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

    public function getGrainRecycleHistoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $grhid = (isset($params['grhid'])) ? intval($params['grhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $history_data = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryByID($grhid);
            if (!empty($history_data)) {
                $json_array = [
                    'data' => $history_data,
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
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        $order_by = 'grhid DESC';
        $total = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryCount($conditions);
        $data = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryData($conditions, $current_page, $page_length, $order_by);

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
