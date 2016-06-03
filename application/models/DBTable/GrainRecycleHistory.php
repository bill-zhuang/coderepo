<?php

class Application_Model_DBTable_GrainRecycleHistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('grain_recycle_history');
    }

    public function getTotalGrainRecycleHistoryGroupDataByYearMonth($selectDate)
    {
        return $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->where('date_format(happen_date, "%Y-%m")=?', $selectDate)
            ->query()->fetchAll();
    }

    public function getTotalGrainRecycleHistoryGroupData($startDate, $endDate)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('date_format(happen_date, "%Y-%m") as period', 'sum(count) as number'))
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

    public function getTotalGrainRecycleHistoryDataByDay($startDate, $endDate)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($startDate !== '') {
            $select->where('happen_date>=?', $startDate);
        }
        if ($endDate !== '') {
            $select->where('happen_date<=?', $endDate);
        }

        return $select
            ->query()->fetchAll();
    }
}