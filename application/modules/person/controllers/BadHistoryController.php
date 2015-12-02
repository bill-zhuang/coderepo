<?php

class person_BadHistoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BadHistory
     */
    private $_adapter_bad_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_bad_history = new Application_Model_DBTable_BadHistory();
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

    public function addBadHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $occur_date = isset($params['bad_history_date']) ? $params['bad_history_date'] : '';
            $occur_count = isset($params['bad_history_count']) ? intval($params['bad_history_count']) : 0;
            if (Bill_Util::validDate($occur_date) && $occur_count > 0)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'happen_date' => $occur_date,
                    'count' => $occur_count,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $date,
                    'update_time' => $date
                ];
                $affected_rows = $this->_adapter_bad_history->insert($data);
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            }
        }

        if (!isset($json_array['data']))
        {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }

    public function getBadHistoryAction()
    {
        if ($this->getRequest()->isGet())
        {
            $params = $this->getRequest()->getQuery('params', []);
            $bhid = (isset($params['bhid'])) ? intval($params['bhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $history_data = $this->_adapter_bad_history->getBadHistoryDayByID($bhid);
            if (!empty($history_data))
            {
                $json_array = [
                    'data' => $history_data,
                ];
            }
        }

        if (!isset($json_array['data']))
        {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }

    public function modifyBadHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $bhid = isset($params['bad_history_id']) ? intval($params['bad_history_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $count = isset($params['bad_history_count']) ?  intval($params['bad_history_count']) : 0;
            if ($bhid > Bill_Constant::INVALID_PRIMARY_ID && $count > 0)
            {
                $update_data = [
                    'count' => $count,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_bad_history->getAdapter()->quoteInto('bhid=?', $bhid);
                $affected_rows = $this->_adapter_bad_history->update($update_data, $where);
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows,
                    ]
                ];
            }
        }

        if (!isset($json_array['data']))
        {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }

    public function deleteBadHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $bhid = isset($params['bhid']) ? intval($params['bhid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($bhid > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $update_data = [
                    'status' => Bill_Constant::INVALID_STATUS,
                    'update_time' => date('Y-m-d H:i:s')
                ];
                $where = [
                    $this->_adapter_bad_history->getAdapter()->quoteInto('bhid=?', $bhid),
                    $this->_adapter_bad_history->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                ];
                $affected_rows = $this->_adapter_bad_history->update($update_data, $where);
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows,
                    ]
                ];
            }
        }

        if (!isset($json_array['data']))
        {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';
        $order_by = 'create_time desc';

        $data = $this->_adapter_bad_history->getBadHistoryData($page_length, $start, $order_by);
        $total = $this->_adapter_bad_history->getTotalBadHistoryNumber();

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
