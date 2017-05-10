<?php

class Application_Model_DBTable_CnnbfdcSale extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('cnnbfdc_sale');
    }

    public function getSaleDataByDay($startDate, $endDate)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['date as period', 'sales'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->where('date>=?', $startDate)
            ->where('date<=?', $endDate);
        return $select
            ->order('date asc')
            ->query()->fetchAll();
    }
}