<?php

class Application_Model_DBTable_EtfFundAnalysis extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('etf_fund_analysis');
    }

    public function getFundAnalysisData(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['date', 'unit_net_value', 'accum_net_value']);
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order(['date asc'])
            ->query()->fetchAll();

        return $data;
    }
}