<?php

class Application_Model_DBTable_LagouJob extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('lagou_job');
    }

    public function getJoidsByCondition(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'joid');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->query()->fetchAll();
        $joids = [];
        foreach ($data as $value) {
            $joids[] = $value['joid'];
        }
        return $joids;
    }

    public function getNameByJoid($joid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('joid=?', $joid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        return isset($data['name']) ? $data['name'] : '';
    }

    public function getJobListByCaid($caid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['joid', 'name'])
            ->where('caid=?', $caid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $data;
    }
}