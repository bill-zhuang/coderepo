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
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_grain_recycle_history = new Application_Model_DBTable_GrainRecycleHistory();
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

    public function addGrainRecycleHistoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['grain_recycle_history_happen_date']))
        {
            try
            {
                $affected_rows = $this->_addGrainRecycleHistory();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From addGrainRecycleHistory');
            }
        }

        echo json_encode($affected_rows);
        exit;
    }

    public function modifyGrainRecycleHistoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['grain_recycle_history_grhid']))
        {
            try
            {
                $affected_rows = $this->_updateGrainRecycleHistory();
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From modifyGrainRecycleHistory');
            }
        }

        echo json_encode($affected_rows);
        exit;
    }

    public function deleteGrainRecycleHistoryAction()
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        if (isset($_POST['grhid']))
        {
            try
            {
                $grhid = intval($_POST['grhid']);
                $update_data = [
                    'status' => Bill_Constant::INVALID_STATUS,
                    'update_time' => date('Y-m-d H:i:s'),
                ];
                $where = [
                    $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('grhid=?', $grhid),
                ];
                $affected_rows = $this->_adapter_grain_recycle_history->update($update_data, $where);
            }
            catch (Exception $e)
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                Bill_Util::handleException($e, 'Error From deleteGrainRecycleHistory');
            }
        }

        echo json_encode($affected_rows);
        exit;
    }

    public function getGrainRecycleHistoryAction()
    {
        $data = [];
        if (isset($_GET['grhid']))
        {
            $grhid = intval($_GET['grhid']);
            if ($grhid > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryByID($grhid);
            }
        }

        echo json_encode($data);
        exit;
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
        $data = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryData($conditions, $page_length, $start, $order_by);

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

    private function _addGrainRecycleHistory()
    {
        $data = [
            'happen_date' => $this->_getHappenDate(),
            'count' => intval($_POST['grain_recycle_history_count']),
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $affected_rows = $this->_adapter_grain_recycle_history->insert($data);

        return $affected_rows;
    }

    private function _updateGrainRecycleHistory()
    {
        $grhid = intval($_POST['grain_recycle_history_grhid']);
        $data = [
            'happen_date' => $this->_getHappenDate(),
            'count' => intval($_POST['grain_recycle_history_count']),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $where = $this->_adapter_grain_recycle_history->getAdapter()->quoteInto('grhid=?', $grhid);
        $affected_rows = $this->_adapter_grain_recycle_history->update($data, $where);

        return $affected_rows;
    }

    private function _getHappenDate()
    {
        $happen_date = trim($_POST['grain_recycle_history_happen_date']);
        if (!Bill_Util::validDate($happen_date))
        {
            $happen_date = date('Y-m-d H:i:s');
        }

        return $happen_date;
    }
}
