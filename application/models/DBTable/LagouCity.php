<?php

class Application_Model_DBTable_LagouCity extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('lagou_city');
    }

    public function getCityListByFirstLetter($firstLetter)
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['name', 'lg_ctid'])
            ->where('letter=?', $firstLetter)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $data;
    }
}