<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-10
 * Time: 下午5:41
 */

class Application_Model_DBTable_Dreamhistory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('dream_history');
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
} 