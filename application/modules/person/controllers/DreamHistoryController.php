<?php

class person_DreamHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_DreamHistory
     */
    private $_adapter_dream_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_dream_history = new Application_Model_DBTable_DreamHistory();
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

    public function addDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $occur_date = isset($params['dream_history_date']) ? $params['dream_history_date'] : '';
            $occur_count = isset($params['dream_history_count']) ? intval($params['dream_history_count']) : 0;
            if (Bill_Util::validDate($occur_date) && $occur_count > 0) {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'happen_date' => $occur_date,
                    'count' => $occur_count,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $date,
                    'update_time' => $date
                ];
                $affected_rows = $this->_adapter_dream_history->insert($data);
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

    public function getDreamHistoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $dhid = (isset($params['dhid'])) ? intval($params['dhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $history_data = $this->_adapter_dream_history->getDreamHistoryDayByID($dhid);
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

    public function modifyDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $dhid = isset($params['dream_history_id']) ? intval($params['dream_history_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $count = isset($params['dream_history_count']) ?  intval($params['dream_history_count']) : 0;
            $date = isset($params['dream_history_date']) ? trim($params['dream_history_date']) : '';
            if ($dhid > Bill_Constant::INVALID_PRIMARY_ID && $count > 0 && Bill_Util::validDate($date)) {
                $update_data = [
                    'happen_date' => $date,
                    'count' => $count,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_dream_history->getAdapter()->quoteInto('dhid=?', $dhid);
                $affected_rows = $this->_adapter_dream_history->update($update_data, $where);
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

    public function deleteDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $dhid = isset($params['dhid']) ? intval($params['dhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($dhid > Bill_Constant::INVALID_PRIMARY_ID) {
                $update_data = [
                    'status' => Bill_Constant::INVALID_STATUS,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = [
                    $this->_adapter_dream_history->getAdapter()->quoteInto('dhid=?', $dhid),
                    $this->_adapter_dream_history->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                ];
                $affected_rows = $this->_adapter_dream_history->update($update_data, $where);
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

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';
        $order_by = 'create_time desc';

        $data = $this->_adapter_dream_history->getDreamHistoryData($current_page, $page_length, $order_by);
        $total = $this->_adapter_dream_history->getTotalDreamHistoryNumber();

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
