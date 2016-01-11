<?php

class Application_Model_DBTable_BackendRoleAcl extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_role_acl');
    }

    public function getBackendRoleAclCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function getBackendRoleAclData(array $conditions, $startPage, $pageLength, $order_by)
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

    public function getBackendRoleAclByID($braid)
    {
        return $this->select()->reset()
            ->where('braid=?', $braid)
            ->query()->fetch();
    }

    public function getUserAclByBrid($brid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('brid=?', $brid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $baids = [];
        foreach ($data as $value) {
            $baids[] = $value['baid'];
        }

        return $baids;
    }
}