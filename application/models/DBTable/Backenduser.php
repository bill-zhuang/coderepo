<?php

class Application_Model_DBTable_BackendUser extends Application_Model_DBTableFactory
{
    private $_adapter_backend_log;
    
    public function __construct()
    {
        parent::__construct('backend_user');
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
            ->where('bu_name like ?', $name)
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