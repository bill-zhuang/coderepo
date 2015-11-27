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
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_dream_history = new Application_Model_DBTable_DreamHistory();
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

    public function addDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $occur_date = isset($params['dream_history_date']) ? $params['dream_history_date'] : '';
            $occur_count = isset($params['dream_history_count']) ? intval($params['dream_history_count']) : 0;
            if (Bill_Util::validDate($occur_date) && $occur_count > 0)
            {
                $date = date('Y-m-d H:i:s');
                $data = [
                    'dh_happen_date' => $occur_date,
                    'dh_count' => $occur_count,
                    'dh_status' => Bill_Constant::VALID_STATUS,
                    'dh_create_time' => $date,
                    'dh_update_time' => $date
                ];
                $affected_rows = $this->_adapter_dream_history->insert($data);
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

    public function getDreamHistoryAction()
    {
        if ($this->getRequest()->isGet())
        {
            $params = $this->getRequest()->getQuery('params', []);
            $dh_id = (isset($params['dh_id'])) ? intval($params['dh_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $history_data = $this->_adapter_dream_history->getDreamHistoryDayByID($dh_id);
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

    public function modifyDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $dh_id = isset($params['dream_history_id']) ? intval($params['dream_history_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $dh_count = isset($params['dream_history_count']) ?  intval($params['dream_history_count']) : 0;
            $dh_date = isset($params['dream_history_date']) ? trim($params['dream_history_date']) : '';
            if ($dh_id > Bill_Constant::INVALID_PRIMARY_ID && $dh_count > 0 && Bill_Util::validDate($dh_date))
            {
                $update_data = [
                    'dh_happen_date' => $dh_date,
                    'dh_count' => $dh_count,
                    'dh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = $this->_adapter_dream_history->getAdapter()->quoteInto('dh_id=?', $dh_id);
                $affected_rows = $this->_adapter_dream_history->update($update_data, $where);
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

    public function deleteDreamHistoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost())
        {
            $params = $this->getRequest()->getPost('params', []);
            $dh_id = isset($params['dh_id']) ? intval($params['dh_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($dh_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $update_data = [
                    'dh_status' => Bill_Constant::INVALID_STATUS,
                    'dh_update_time' => date('Y-m-d H:i:s')
                ];
                $where = [
                    $this->_adapter_dream_history->getAdapter()->quoteInto('dh_id=?', $dh_id),
                    $this->_adapter_dream_history->getAdapter()->quoteInto('dh_status=?', Bill_Constant::VALID_STATUS),
                ];
                $affected_rows = $this->_adapter_dream_history->update($update_data, $where);
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

    public function fakeAction()
    {
        $init_date = date('Y-m-d');
        $data = [
            'dh_happen_date' => '',
            'dh_count' => 1,
            'dh_status' => Bill_Constant::VALID_STATUS,
            'dh_create_time' => '',
            'dh_update_time' => ''
        ];
        for ($i = 0; $i < 100; $i++)
        {
            $init_date = date('Y-m-d', strtotime($init_date . ' + 3 days'));
            $data['dh_happen_date'] = $init_date;
            $data['dh_create_time'] = $init_date;
            $data['dh_update_time'] = $init_date;

            $this->_adapter_dream_history->insert($data);
        }
        exit;
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';
        $order_by = 'dh_create_time desc';

        $data = $this->_adapter_dream_history->getDreamHistoryData($page_length, $start, $order_by);
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
