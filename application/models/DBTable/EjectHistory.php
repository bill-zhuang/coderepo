<?php

class Application_Model_DBTable_EjectHistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('eject_history');
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

    public function getTotalEjectHistoryDataByDay($startDate, $endDate, $type)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('type=?', $type)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($startDate !== '') {
            $select->where('happen_date>=?', $startDate);
        }
        if ($endDate !== '') {
            $select->where('happen_date<=?', $endDate);
        }

        return $select
            ->order('period asc')
            ->query()->fetchAll();
    }

    public function getTotalEjectHistoryGroupData($startDate, $endDate, $type)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('date_format(happen_date, "%Y-%m") as period', 'sum(count) as number'))
            ->where('type=?', $type)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($startDate !== '') {
            $select->where('happen_date>=?', $startDate);
        }
        if ($endDate !== '') {
            $select->where('happen_date<=?', $endDate);
        }
        return $select
            ->group('date_format(happen_date, "%Y%m")')
            ->query()->fetchAll();
    }
}