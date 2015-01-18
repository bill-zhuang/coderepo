<?php

class Application_Model_DbTable_Financepayment extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_payment');
    }

    public function getFinancepaymentCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getFinancepaymentData(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $data = $select->limit($count, $offset)->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function getFinancepaymentByID($fp_id)
    {
        return $this->select()->reset()
            ->where('fp_id=?', $fp_id)
            ->query()->fetch();
    }
}