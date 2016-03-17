<?php

class Application_Model_DBTable_FinancePayment extends Application_Model_DBTableFactory
{
    private $_join_table;
    private $_join_condition;

    public function __construct()
    {
        parent::__construct('finance_payment');

        $this->_join_table = 'finance_payment_map';
        $this->_join_condition = 'finance_payment.fpid=finance_payment_map.fpid';
    }

    public function getFinancePaymentCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getFinancePaymentData(array $conditions, $startPage, $pageLength, $order_by)
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

    public function getFinancePaymentByID($fpid)
    {
        return $this->select()->reset()
            ->where('fpid=?', $fpid)
            ->query()->fetch();
    }

    public function getTotalPaymentHistoryGroupData($start_date, $end_date)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['date_format(payment_date, "%Y-%m") as period', 'sum(payment) as payment'])
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($start_date !== '') {
            $select->where('payment_date>=?', $start_date);
        }
        if ($end_date !== '') {
            $select->where('payment_date<=?', $end_date);
        }
        return $select
            ->group('date_format(payment_date, "%Y%m")')
            ->order('payment_date asc')
            ->query()->fetchAll();
    }

    public function getTotalPaymentHistoryDataByDay($start_date, $end_date, $fcid)
    {
        if ($fcid == Bill_Constant::INVALID_PRIMARY_ID) {
            return $this->select()->reset()
                ->from($this->_name, ['payment_date as period', 'sum(payment) as payment'])
                ->where('status=?', Bill_Constant::VALID_STATUS)
                ->where('payment_date>=?', $start_date)
                ->where('payment_date<=?', $end_date)
                ->group('payment_date')
                ->order('payment_date asc')
                ->query()->fetchAll();
        } else {
            return $this->select()->reset()
                ->setIntegrityCheck(false)
                ->from($this->_name, ['payment_date as period', 'sum(payment) as payment'])
                ->joinInner($this->_join_table, $this->_join_condition, [])
                ->where($this->_name . '.status=?', Bill_Constant::VALID_STATUS)
                ->where($this->_name . '.payment_date>=?', $start_date)
                ->where($this->_name . '.payment_date<=?', $end_date)
                ->where($this->_join_table . '.fcid=?', $fcid)
                ->group($this->_name . '.payment_date')
                ->order($this->_name . '.payment_date asc')
                ->query()->fetchAll();
        }
    }

    public function getTotalPaymentHistoryDataByCategory($start_date)
    {
        return $this->select()->reset()
            ->setIntegrityCheck(false)
            ->from($this->_name, ['sum(payment) as payment'])
            ->joinInner($this->_join_table, $this->_join_condition, 'fcid')
            ->where($this->_name . '.status=?', Bill_Constant::VALID_STATUS)
            ->where($this->_name . '.payment_date>=?', $start_date)
            ->where($this->_join_table . '.status=?', Bill_Constant::VALID_STATUS)
            ->group($this->_join_table . '.fcid')
            ->order('payment desc')
            ->query()->fetchAll();
    }

    public function getAllPaymentDataForTransfer()
    {
        return $this->select()->reset()
            ->from($this->_name, ['fpid', 'fcid', 'create_time', 'update_time'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
    }

    public function getSumPaymentByDate($start_date)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'sum(payment) as total')
            ->where('payment_date>=?', $start_date)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return floatval($data[0]['total']);
    }
}