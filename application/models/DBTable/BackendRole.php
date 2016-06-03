<?php

class Application_Model_DBTable_BackendRole extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_role');
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