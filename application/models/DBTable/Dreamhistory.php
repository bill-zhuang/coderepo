<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_DreamHistory extends Application_Model_DBTableFactory
{
    private $_adapter_backend_log;

    public function __construct()
    {
        parent::__construct('dream_history');
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

    public function getDreamHistoryData($limit, $offset, $order_by)
    {
        return $this->select()->reset()
            ->where('dh_status=?', 1)
            ->limit($limit, $offset)
            ->order($order_by)
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryNumber()
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('dh_status=?', 1)
            ->query()->fetchAll();

        return $count[0]['total'];
    }

    public function getTotalDreamHistoryGroupData()
    {
        return $this->select()->reset()
            ->from($this->_name, array('date_format(dh_happen_date, "%Y-%m") as period', 'count(dh_count) as number'))
            ->where('dh_status=?', 1)
            ->group('date_format(dh_happen_date, "%Y%m")')
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryGroupDataByYearMonth($select_date)
    {
        return $this->select()->reset()
            ->from($this->_name, array('dh_happen_date as period', 'dh_count as number'))
            ->where('dh_status=?', 1)->where('date_format(dh_happen_date, "%Y-%m")=?', $select_date)
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryDataByDay()
    {
        return $this->select()->reset()
            ->from($this->_name, array('dh_happen_date as period', 'dh_count as number'))
            ->where('dh_status=?', 1)
            ->query()->fetchAll();
    }

    public function getDreamHistoryDayByID($dh_id)
    {
        return $this->select()->reset()
            ->where('dh_id=?', $dh_id)
            ->query()->fetch();
    }
} 