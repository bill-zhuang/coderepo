<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_BadHistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('bad_history');
    }

    public function getBadHistoryData($startPage, $pageLength, $order_by)
    {
        return $this->select()->reset()
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order($order_by)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
    }

    public function getTotalBadHistoryNumber()
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $count[0]['total'];
    }

    public function getTotalBadHistoryDataByDay()
    {
        return $this->select()->reset()
            ->from($this->_name, array('happen_date as period', 'count as number'))
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
    }

    public function getBadHistoryDayByID($bhid)
    {
        return $this->select()->reset()
            ->where('bhid=?', $bhid)
            ->query()->fetch();
    }
} 