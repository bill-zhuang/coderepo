<?php

class Application_Model_DBTable_BackendUser extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_user');
    }

    public function getBackendUserCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getBackendUserData(array $conditions, $startPage, $pageLength, $orderBy)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order($orderBy)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return $data;
    }

    public function getBackendUserByID($buid)
    {
        return $this->select()->reset()
            ->where('buid=?', $buid)
            ->query()->fetch();
    }

    public function getUserInfo($userName)
    {
        return $this->select()->reset()
            ->where('name=?', $userName)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
    }

    public function isUserNameExist($name, $buid)
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('name = ?', $name)
            ->where('buid!=?', $buid)
            ->query()->fetchAll();

        return $count[0]['total'] == 0 ? false : true;
    }

    public function getUserName($buid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('buid=?', $buid)
            ->query()->fetch();
        return isset($data['name']) ? $data['name'] : '';
    }

    public function getRoleCount($brid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('brid = ?', $brid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return intval($data[0]['total']);
    }
}