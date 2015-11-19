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

    public function getBackendUserByID($bu_id)
    {
        return $this->select()->reset()
            ->where('bu_id=?', $bu_id)
            ->query()->fetch();
    }

    public function getUserInfo($user_name)
    {
        return $this->select()->reset()
            ->where('bu_name=?', $user_name)
            ->where('bu_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
    }

    public function isUserNameExist($name, $buid)
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('bu_name = ?', $name)
            ->where('bu_id!=?', $buid)
            ->where('bu_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();

        return $count[0]['total'] == 0 ? false : true;
    }

    public function getUserName($buid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'bu_name')
            ->where('bu_id=?', $buid)
            ->query()->fetch();
        return isset($data['bu_name']) ? $data['bu_name'] : '';
    }
}