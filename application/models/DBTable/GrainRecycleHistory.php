<?php

class Application_Model_DBTable_GrainRecycleHistory extends Application_Model_DBTableFactory
{
    private $_adapter_backend_log;

    public function __construct()
    {
        parent::__construct('grain_recycle_history');
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

    public function getGrainRecycleHistoryCount(array $conditions)
    {
        $select = $this->select()->reset()->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function getGrainRecycleHistoryData(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $data = $select
            ->limit($count, $offset)
            ->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function getGrainRecycleHistoryByID($grhid)
    {
        return $this->select()->reset()
            ->where('grhid=?', $grhid)
            ->query()->fetch();
    }

    public function getTotalGrainRecycleHistoryGroupDataByYearMonth($select_date)
    {
        return $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('status=?', 1)
            ->where('date_format(happen_date, "%Y-%m")=?', $select_date)
            ->query()->fetchAll();
    }

    public function getTotalGrainRecycleHistoryGroupData()
    {
        return $this->select()->reset()
            ->from($this->_name, array('date_format(happen_date, "%Y-%m") as period', 'sum(count) as number'))
            ->where('status=?', 1)
            ->group('date_format(happen_date, "%Y%m")')
            ->query()->fetchAll();
    }

    public function getTotalGrainRecycleHistoryDataByDay($start_date, $end_date)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('status=?', 1);
        if ($start_date !== '')
        {
            $select->where('happen_date>=?', $start_date);
        }
        if ($end_date !== '')
        {
            $select->where('happen_date<=?', $end_date);
        }

        return $select
            ->query()->fetchAll();
    }
}