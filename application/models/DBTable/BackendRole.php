<?php

class Application_Model_DBTable_BackendRole extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_role');
    }

    public function getBackendRoleCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function getBackendRoleData(array $conditions, $startPage, $pageLength, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order($order_by)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return $data;
    }

    public function getBackendRoleByID($brid)
    {
        return $this->select()->reset()
            ->where('brid=?', $brid)
            ->query()->fetch();
    }

    public function isRoleExist($role, $brid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('role=?', $role)
            ->where('brid!=?', $brid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return intval($data[0]['total']) === 0 ? false : true;
    }

    public function getAllRoles()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['brid', 'role'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $roles = [];
        foreach ($data as $value) {
            $roles[$value['brid']] = $value['role'];
        }

        return $roles;
    }
}