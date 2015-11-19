<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_BadHistory extends Application_Model_DBTableFactory
{
    private $_adapter_backend_log;

    public function __construct()
    {
        parent::__construct('bad_history');
        $this->_adapter_backend_log = new Application_Model_DBTable_BackendLog();
    }

    public function insert(array $data)
    {
        $this->_adapter_backend_log->writeLog('insert', $this->_name, $data);
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $this->_adapter_backend_log->writeLog('update', $this->_name, $data, $where);
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_adapter_backend_log->writeLog('delete', $this->_name, [], $where);
        return parent::delete($where);
    }

    public function getBadHistoryData($limit, $offset, $order_by)
    {
        return $this->select()->reset()
            ->where('bh_status=?', Bill_Constant::VALID_STATUS)
            ->limit($limit, $offset)
            ->order($order_by)
            ->query()->fetchAll();
    }

    public function getTotalBadHistoryNumber()
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('bh_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $count[0]['total'];
    }

    public function getTotalBadHistoryDataByDay()
    {
        return $this->select()->reset()
            ->from($this->_name, array('bh_happen_date as period', 'bh_count as number'))
            ->where('bh_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
    }

    public function getBadHistoryDayByID($bh_id)
    {
        return $this->select()->reset()
            ->where('bh_id=?', $bh_id)
            ->query()->fetch();
    }
} 