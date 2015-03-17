<?php

class Application_Model_DbTable_Backenduser extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_user');
    }

    public function getBackenduserCount(array $conditions)
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

    public function getBackenduserData(array $conditions, $count, $offset, $order_by)
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

    public function getBackenduserByID($bu_id)
    {
        return $this->select()->reset()
            ->where('bu_id=?', $bu_id)
            ->query()->fetch();
    }

    public function getUserInfo($user_name)
    {
        return $this->select()->reset()
            ->where('bu_name=?', $user_name)
            ->where('bu_status=?', 1)
            ->query()->fetch();
    }

    public function isUserNameExist($name, $buid)
    {
        $count = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('bu_name like ?', $name)
            ->where('bu_id!=?', $buid)
            ->where('bu_status=?', 1)
            ->query()->fetchAll();

        return $count[0]['total'] == 0 ? false : true;
    }
}