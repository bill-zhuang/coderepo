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
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getFinancePaymentMapData(array $conditions, $startPage, $pageLength, $order_by)
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

    public function getFinancePaymentMapByID($fpmid)
    {
        return $this->select()->reset()
            ->where('fpmid=?', $fpmid)
            ->query()->fetch();
    }

    public function getFinanceCategoryIDs($fpid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fcid')
            ->where('fpid=?', $fpid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $fcids = [];
        foreach ($data as $value) {
            $fcids[] = $value['fcid'];
        }

        return $fcids;
    }

    public function getFpidByFcid($fcid, $order_by, $count, $offset)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fpid')
            ->where('fcid=?', $fcid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order($order_by)
            ->limit($count, $offset)
            ->query()->fetchAll();
        $fpids = [];
        foreach ($data as $value) {
            $fpids[] = $value['fpid'];
        }

        return $fpids;
    }
}