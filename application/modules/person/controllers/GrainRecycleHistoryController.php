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
                Bill_Util::sendMail('Error From addGrainRecycleHistory', $e->getMessage() . Bill_Html::br() . $e->getTraceAsString());
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
                Bill_Util::sendMail('Error From modifyGrainRecycleHistory', $e->getMessage() . Bill_Html::br() . $e->getTraceAsString());
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
                Bill_Util::sendMail('Error From deleteGrainRecycleHistory', $e->getMessage() . Bill_Html::br() . $e->getTraceAsString());
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
        $current_page = intval($this->_getParam('current_page', Bill_Constant::INIT_START_PAGE));
        $page_length = intval($this->_getParam('page_length', Bill_Constant::INIT_PAGE_LENGTH));
        $start = ($current_page - Bill_Constant::INIT_START_PAGE) * $page_length;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        $order_by = 'grhid DESC';
        $total = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryCount($conditions);
        $data = $this->_adapter_grain_recycle_history->getGrainRecycleHistoryData($conditions, $page_length, $start, $order_by);

        $json_data = [
            'data' => $data,
            'current_page' => $current_page,
            'total_pages' => ceil($total / $page_length) ? ceil($total / $page_length) : Bill_Constant::INIT_TOTAL_PAGE,
            'total' => $total,
            'start' => $start,
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
