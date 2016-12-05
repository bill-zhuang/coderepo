<?php

class Application_Model_DBTable_LagouJobAnalysis extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('lagou_job_analysis');
    }

    public function getJobAnalysisData(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, ['joid', 'lg_ctid', 'date', 'num']);
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order(['joid asc', 'lg_ctid asc', 'date asc'])
            ->query()->fetchAll();

        return $data;
    }
}