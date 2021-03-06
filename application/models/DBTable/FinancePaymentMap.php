<?php

class Application_Model_DBTable_FinancePaymentMap extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_payment_map');
    }

    public function getFinanceCategoryIDs($fpid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fcid')
            ->where('fpid=?', $fpid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return array_column($data, 'fcid');
    }

    public function getFpidByFcid($fcid, $orderBy, $startPage, $pageLength)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fpid')
            ->where('fcid=?', $fcid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order($orderBy)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return array_column($data, 'fpid');
    }

    public function isPaymentExistUnderFcid($fcid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fpmid')
            ->where('fcid=?', $fcid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        return isset($data['fpmid']) ? true : false;
    }
}