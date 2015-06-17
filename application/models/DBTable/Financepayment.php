<?php

class Application_Model_DBTable_FinancePayment extends Application_Model_DBTableFactory
{
    private $_adapter_backend_log;

    public function __construct()
    {
        parent::__construct('finance_payment');
        $this->_adapter_backend_log = new Application_Model_DBTable_BackendLog();
    }

    public function insert(array $data)
    {
        $this->_adapter_backend_log->writeLog('insert', $this->_name, $data);
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $this->_adapter_backend_log->writeLog('update', $this->_name, $data, $where);
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_adapter_backend_log->writeLog('delete', $this->_name, [], $where);
        return parent::delete($where);
    }

    public function getFinancePaymentCount(array $conditions)
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

    public function getFinancePaymentData(array $conditions, $count, $offset, $order_by)
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

    public function getFinancePaymentByID($fp_id)
    {
        return $this->select()->reset()
            ->where('fp_id=?', $fp_id)
            ->query()->fetch();
    }

    public function getTotalPaymentHistoryGroupData()
    {
        return $this->select()->reset()
            ->from($this->_name, ['date_format(fp_payment_date, "%Y-%m") as period', 'sum(fp_payment) as payment'])
            ->where('fp_status=?', 1)
            ->group('date_format(fp_payment_date, "%Y%m")')
            ->order('fp_payment_date asc')
            ->query()->fetchAll();
    }

    public function getTotalPaymentHistoryDataByDay($start_date)
    {
        return $this->select()->reset()
            ->from($this->_name, ['fp_payment_date as period', 'sum(fp_payment) as payment'])
            ->where('fp_status=?', 1)
            ->where('fp_payment_date>=?', $start_date)
            ->group('fp_payment_date')
            ->order('fp_payment_date asc')
            ->query()->fetchAll();
    }

    public function getTotalPaymentHistoryDataByCategory($start_date)
    {
        return $this->select()->reset()
            ->from($this->_name, ['fc_id', 'sum(fp_payment) as payment'])
            ->where('fp_status=?', 1)
            ->where('fp_payment_date>=?', $start_date)
            ->group('fc_id')
            ->order('payment desc')
            ->query()->fetchAll();
    }

    public function getAllPaymentDataForTransfer()
    {
        return $this->select()->reset()
            ->from($this->_name, ['fp_id', 'fc_id'])
            ->where('fp_status=?', 1)
            ->query()->fetchAll();
    }
}