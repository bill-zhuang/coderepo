<?php

class Application_Model_DBTable_LagouCategory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('lagou_category');
    }

    public function getAllMainCategory()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['caid', 'name'])
            ->where('pid=?', 0)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $data;
    }

    public function getAllSubCategoryByPid($pid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['caid', 'name'])
            ->where('pid=?', $pid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $data;
    }

    public function getAllSubCaids($pid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'caid')
            ->where('pid=?', $pid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $caids = [];
        foreach ($data as $value) {
            $caids[] = $value['caid'];
        }

        return $caids;
    }

    public function getCategoryName($caid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('caid=?', $caid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        return isset($data['name']) ? $data['name'] : '';
    }

    public function getNamePid($caid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['name', 'pid'])
            ->where('caid=?', $caid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        if (isset($data['name'])) {
            return [
                $data['name'],
                $data['pid'],
            ];
        } else {
            return [
                'name' => '',
                'pid' => 0,
            ];
        }
    }
}