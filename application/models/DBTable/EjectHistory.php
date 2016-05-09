<?php

class Application_Model_DBTable_EjectHistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('eject_history');
    }

    public function getEjectHistoryCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function getEjectHistoryData(array $conditions, $startPage, $pageLength, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order($order_by)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return $data;
    }

    public function getEjectHistoryByID($ehid)
    {
        return $this->select()->reset()
            ->where('ehid=?', $ehid)
            ->query()->fetch();
    }

    public function isHistoryExistByHappenDateTypeEhid($happenDate, $type, $ehid = Bill_Constant::INVALID_PRIMARY_ID)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('happen_date=?', $happenDate)
            ->where('type=?', $type)
            ->where('ehid !=?', $ehid)
            ->query()->fetchAll();
        return ($data[0]['total'] > 0) ? true : false;
    }

    public function getTotalEjectHistoryDataByDay($start_date, $end_date, $type)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('type=?', $type)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($start_date !== '') {
            $select->where('happen_date>=?', $start_date);
        }
        if ($end_date !== '') {
            $select->where('happen_date<=?', $end_date);
        }

        return $select
            ->order('period asc')
            ->query()->fetchAll();
    }

    public function getTotalEjectHistoryGroupData($start_date, $end_date, $type)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('date_format(happen_date, "%Y-%m") as period', 'sum(count) as number'))
            ->where('type=?', $type)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($start_date !== '') {
            $select->where('happen_date>=?', $start_date);
        }
        if ($end_date !== '') {
            $select->where('happen_date<=?', $end_date);
        }
        return $select
            ->group('date_format(happen_date, "%Y%m")')
            ->query()->fetchAll();
    }
}