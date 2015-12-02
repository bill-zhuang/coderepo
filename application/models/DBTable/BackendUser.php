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
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getBackendUserData(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $data = $select->limit($count, $offset)->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function getBackendUserByID($buid)
    {
        return $this->select()->reset()
            ->where('buid=?', $buid)
            ->query()->fetch();
    }

    public function getUserInfo($user_name)
    {
        return $this->select()->reset()
            ->where('name=?', $user_name)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
    }

    public function isUserNameExist($name, $buid)
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('name = ?', $name)
            ->where('buid!=?', $buid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
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
}