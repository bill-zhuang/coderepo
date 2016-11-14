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

    public function getSearchData(array $conditions, $startPage, $pageLength, $orderBy, $groupBy = null)
    {
        if (!isset($conditions['finance_payment_map.fcid=?'])) {
            return parent::getSearchData($conditions, $startPage, $pageLength, $orderBy, $groupBy);
        } else {
            $select = $this->select()->reset()
                ->setIntegrityCheck(false)
                ->from($this->_name)
                ->joinInner($this->_join_table, $this->_join_condition, []);
            foreach ($conditions as $cond => $value) {
                $select->where($cond, $value);
            }
            $select
                ->limitPage($startPage, $pageLength)
                ->order($orderBy);
            if ($groupBy !== null) {
                $select->group($groupBy);
            }
            $data = $select
                ->query()->fetchAll();
            return $data;
        }
    }

    public function getSearchCount(array $conditions, $groupBy)
    {
        if (!isset($conditions['finance_payment_map.fcid=?'])) {
            return parent::getSearchCount($conditions);
        } else {
            $select = $this->select()->reset()
                ->setIntegrityCheck(false)
                ->from($this->_name, 'count(*) as total')
                ->joinInner($this->_join_table, $this->_join_condition, []);
            foreach ($conditions as $cond => $value) {
                $select->where($cond, $value);
            }
            $count = $select
                ->group($groupBy)
                ->query()->fetchAll();
            return $count[0]['total'];
        }
    }

    public function getTotalPaymentHistoryGroupData($startDate, $endDate)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['date_format(payment_date, "%Y-%m") as period', 'sum(payment) as payment'])
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if ($startDate !== '') {
            $select->where('payment_date>=?', $startDate);
        }
        if ($endDate !== '') {
            $select->where('payment_date<=?', $endDate);
        }
        return $select
            ->group('date_format(payment_date, "%Y%m")')
            ->order('payment_date asc')
            ->query()->fetchAll();
    }

    public function getTotalPaymentHistoryDataByDay($startDate, $endDate, $fcid, $ignoreMoney = 0)
    {
        if ($fcid == Bill_Constant::INVALID_PRIMARY_ID) {
            $select = $this->select()->reset()
                ->from($this->_name, ['payment_date as period', 'sum(payment) as payment'])
                ->where('status=?', Bill_Constant::VALID_STATUS)
                ->where('payment_date>=?', $startDate)
                ->where('payment_date<=?', $endDate);
            if ($ignoreMoney > 0) {
                $select->where('payment<?', $ignoreMoney);
            }
            return $select
                ->group('payment_date')
                ->order('payment_date asc')
                ->query()->fetchAll();
        } else {
            $select = $this->select()->reset()
                ->setIntegrityCheck(false)
                ->from($this->_name, ['payment_date as period', 'sum(payment) as payment'])
                ->joinInner($this->_join_table, $this->_join_condition, [])
                ->where($this->_name . '.status=?', Bill_Constant::VALID_STATUS)
                ->where($this->_name . '.payment_date>=?', $startDate)
                ->where($this->_name . '.payment_date<=?', $endDate)
                ->where($this->_join_table . '.fcid=?', $fcid);
            if ($ignoreMoney > 0) {
                $select->where('payment<?', $ignoreMoney);
            }
            return $select
                ->group($this->_name . '.payment_date')
                ->order($this->_name . '.payment_date asc')
                ->query()->fetchAll();
        }
    }

    public function getTotalPaymentHistoryDataByCategory($startDate)
    {
        return $this->select()->reset()
            ->setIntegrityCheck(false)
            ->from($this->_name, ['sum(payment) as payment'])
            ->joinInner($this->_join_table, $this->_join_condition, 'fcid')
            ->where($this->_name . '.status=?', Bill_Constant::VALID_STATUS)
            ->where($this->_name . '.payment_date>=?', $startDate)
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

    public function getSumPaymentByDate($startDate)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'sum(payment) as total')
            ->where('payment_date>=?', $startDate)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return floatval($data[0]['total']);
    }
}