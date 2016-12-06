<?php

class Application_Model_DBTable_EtfFund extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('etf_fund');
    }

    public function getFundList()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['name', 'fuid'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $data;
    }
}