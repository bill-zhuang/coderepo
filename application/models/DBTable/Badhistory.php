<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_Badhistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('bad_history');
    }

    public function getBadHistoryData($limit, $offset, $order_by)
    {
        return $this->select()->reset()
            ->where('bh_status=?', 1)
            ->limit($limit, $offset)
            ->order($order_by)
            ->query()->fetchAll();
    }

    public function getTotalBadHistoryNumber()
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('bh_status=?', 1)
            ->query()->fetchAll();

        return $count[0]['total'];
    }

    public function getTotalBadHistoryDataByDay()
    {
        return $this->select()->reset()
            ->from($this->_name, array('bh_happen_date as period', 'bh_count as number'))
            ->where('bh_status=?', 1)
            ->query()->fetchAll();
    }
} 