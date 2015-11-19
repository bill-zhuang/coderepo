<?php

class Application_Model_DBTable_FinancePaymentMap extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_payment_map');
    }

    public function getFinancePaymentMapCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getFinancePaymentMapData(array $conditions, $count, $offset, $order_by)
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

    public function getFinancePaymentMapByID($fpmid)
    {
        return $this->select()->reset()
            ->where('fpmid=?', $fpmid)
            ->query()->fetch();
    }

    public function getFinanceCategoryIDs($fpid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_id')
            ->where('fp_id=?', $fpid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $fc_ids = [];
        foreach ($data as $value)
        {
            $fc_ids[] = $value['fc_id'];
        }

        return $fc_ids;
    }

    public function getFpidByFcid($fcid, $order_by, $count, $offset)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fp_id')
            ->where('fc_id=?', $fcid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order($order_by)
            ->limit($count, $offset)
            ->query()->fetchAll();
        $fpids = [];
        foreach ($data as $value)
        {
            $fpids[] = $value['fp_id'];
        }

        return $fpids;
    }
}