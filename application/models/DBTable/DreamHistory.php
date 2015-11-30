<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_DreamHistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('dream_history');
    }

    public function getDreamHistoryData($limit, $offset, $order_by)
    {
        return $this->select()->reset()
            ->where('dh_status=?', Bill_Constant::VALID_STATUS)
            ->limit($limit, $offset)
            ->order($order_by)
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryNumber()
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('dh_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $count[0]['total'];
    }

    public function getTotalDreamHistoryGroupData($start_date, $end_date)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('date_format(dh_happen_date, "%Y-%m") as period', 'sum(dh_count) as number'))
            ->where('dh_status=?', Bill_Constant::VALID_STATUS);
        if ($start_date !== '')
        {
            $select->where('dh_happen_date>=?', $start_date);
        }
        if ($end_date !== '')
        {
            $select->where('dh_happen_date<=?', $end_date);
        }
        return $select
            ->group('date_format(dh_happen_date, "%Y%m")')
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryGroupDataByYearMonth($select_date)
    {
        return $this->select()->reset()
            ->from($this->_name, array('dh_happen_date as period', 'dh_count as number'))
            ->where('dh_status=?', Bill_Constant::VALID_STATUS)
            ->where('date_format(dh_happen_date, "%Y-%m")=?', $select_date)
            ->query()->fetchAll();
    }

    public function getTotalDreamHistoryDataByDay($start_date, $end_date)
    {
        $select = $this->select()->reset()
            ->from($this->_name, array('dh_happen_date as period', 'dh_count as number'))
            ->where('dh_status=?', Bill_Constant::VALID_STATUS);
        if ($start_date !== '')
        {
            $select->where('dh_happen_date>=?', $start_date);
        }
        if ($end_date !== '')
        {
            $select->where('dh_happen_date<=?', $end_date);
        }

        return $select
            ->query()->fetchAll();
    }

    public function getDreamHistoryDayByID($dh_id)
    {
        return $this->select()->reset()
            ->where('dh_id=?', $dh_id)
            ->query()->fetch();
    }
} 