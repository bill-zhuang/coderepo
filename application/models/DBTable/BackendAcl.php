<?php

class Application_Model_DBTable_BackendAcl extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_acl');
    }

    public function getBackendAclCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function getBackendAclData(array $conditions, $startPage, $pageLength, $order_by, $group_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order($order_by)
            ->group($group_by)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return $data;
    }

    public function getBackendAclByID($baid)
    {
        return $this->select()->reset()
            ->where('baid=?', $baid)
            ->query()->fetch();
    }

    public function isAclExist($module, $controller, $action)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('module=?', $module)
            ->where('controller=?', $controller)
            ->where('action=?', $action)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return intval($data[0]['total']) === 0 ? false : true;
    }

    public function getAclID($module, $controller, $action)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('module=?', $module)
            ->where('controller=?', $controller)
            ->where('action=?', $action)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetch();
        return isset($data['baid']) ? $data['baid'] : Bill_Constant::INVALID_PRIMARY_ID;
    }

    public function getInvalidActionAclIDs($module, $controller, array $valid_actions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('module=?', $module)
            ->where('controller=?', $controller)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if (!empty($valid_actions)) {
            $select->where('action not in (?)', $valid_actions);
        }
        $data = $select
            ->query()->fetchAll();
        $baids = [];
        foreach ($data as $value) {
            $baids[] = $value['baid'];
        }

        return $baids;
    }

    public function getInvalidControllerAclIDs($module, array $valid_controllers)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('module=?', $module)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if (!empty($valid_controllers)) {
            $select->where('controller not in (?)', $valid_controllers);
        }
        $data = $select
            ->query()->fetchAll();
        $baids = [];
        foreach ($data as $value) {
            $baids[] = $value['baid'];
        }

        return $baids;
    }

    public function getAclList()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['module', 'controller', 'action', 'baid'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->group(['module', 'controller', 'action'])
            ->query()->fetchAll();
        $acl = [];
        foreach ($data as $value) {
            $module = $value['module'];
            $controller = $value['controller'];
            $action = $value['action'];
            if (isset($acl[$module])) {
                if (!isset($acl[$module][$controller])) {
                    $acl[$module][$controller] = [];
                }
                $acl[$module][$controller][] = [
                    'action' => $action,
                    'id' => $value['baid'],
                ];
            } else {
                $acl[$module] = [
                    $controller => [
                        [
                            'action' => $action,
                            'id' => $value['baid'],
                        ]
                    ]
                ];
            }
        }

        return $acl;
    }

    public function getAclMap()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['module', 'controller', 'action', 'baid'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->group(['module', 'controller', 'action'])
            ->query()->fetchAll();
        $map = [];
        foreach ($data as $value) {
            $acl_map_key = Bill_Util::getAclMapKey($value['module'], $value['controller'], $value['action']);
            $map[$acl_map_key] = $value['baid'];
        }

        return $map;
    }
}