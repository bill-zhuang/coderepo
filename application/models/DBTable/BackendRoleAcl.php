<?php

class Application_Model_DBTable_BackendRoleAcl extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_role_acl');
    }

    public function getUserAclByBrid($brid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('brid=?', $brid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return array_column($data, 'baid');
    }

    public function isAccessGranted($brid, $baid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'braid')
            ->where('brid=?', $brid)
            ->where('baid=?', $baid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        return isset($data['braid']) ? true : false;
    }
}