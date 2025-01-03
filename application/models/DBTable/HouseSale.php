<?php

class Application_Model_DBTable_HouseSale extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('house_sale');
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

    public function getSaleDataByMonth($startDate, $endDate)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['date_format(date, "%Y-%m") as period', 'sum(sales) as sales'])
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($startDate !== '') {
            $select->where('date>=?', $startDate);
        }
        if ($endDate !== '') {
            $select->where('date<=?', $endDate);
        }
        return $select
            ->group('date_format(date, "%Y-%m")')
            ->order('date asc')
            ->query()->fetchAll();
    }
}