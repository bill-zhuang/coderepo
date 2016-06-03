<?php

class Application_Model_DBTable_BackendAcl extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_acl');
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

    public function getInvalidActionsAclIDs($module, $controller, array $validActions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('module=?', $module)
            ->where('controller=?', $controller)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if (!empty($validActions)) {
            $select->where('action not in (?)', $validActions);
        }
        $data = $select
            ->query()->fetchAll();
        $baids = [];
        foreach ($data as $value) {
            $baids[] = $value['baid'];
        }

        return $baids;
    }

    public function getInvalidControllersAclIDs($module, array $validControllers)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'baid')
            ->where('module=?', $module)
            ->where('status=?', Bill_Constant::VALID_STATUS);
        if (!empty($validControllers)) {
            $select->where('controller not in (?)', $validControllers);
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
            $aclMapKey = Bill_Util::getAclMapKey($value['module'], $value['controller'], $value['action']);
            $map[$aclMapKey] = $value['baid'];
        }

        return $map;
    }
}