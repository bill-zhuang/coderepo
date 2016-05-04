<?php
class Application_Model_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //return;
        $request_module = $request->getModuleName();
        $request_controller = $request->getControllerName();
        $request_action = $request->getActionName();
        if ($request_module == 'default' && $request_controller == 'login') {
            return;
        }
        if ($request_module == 'person' && $request_controller == 'console') {
            //console not auth
            return;
        }

        if (Application_Model_Auth::isValid()) {
            if (Application_Model_Auth::getIdentity()->name != Bill_Constant::ADMIN_NAME) {
                $adapter_role_acl = new Application_Model_DBTable_BackendRoleAcl();
                $baid = $this->_getAclID($request_module, $request_controller, $request_action);
                if ($baid > Bill_Constant::INVALID_PRIMARY_ID
                    && $adapter_role_acl->isAccessGranted(Application_Model_Auth::getIdentity()->brid, $baid)
                ) {
                    return;
                } else {
                    if ($this->getRequest()->isXmlHttpRequest()) {
                        $json_array = [
                            'error' => [
                                'message' => '无权限访问',
                            ],
                        ];
                        echo json_encode($json_array);
                        exit;
                    } else {
                        $request->setModuleName('default');
                        $request->setControllerName('error');
                        $request->setActionName('error');
                    }
                }
            } else {
                return;
            }
        } else {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('index');
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (isset($_REQUEST['sql']) && boolval($_REQUEST['sql'])) {
            Bill_Util::printSQL();
        }

        $sql_info = Bill_Util::getSQLInfo();
        if (isset($sql_info['queryCost']) && $sql_info['queryCost'] > Bill_Constant::SQL_QUERY_COST_TRIGGER) {
            //TODO slow query trigger
        }
    }

    private function _getAclID($module, $controller, $action)
    {
        $adapter_acl = new Application_Model_DBTable_BackendAcl();
        $acl_map = $adapter_acl->getAclMap();
        $acl_map_key = Bill_Util::getAclMapKey($module, $controller, $action);
        return isset($acl_map[$acl_map_key]) ? $acl_map[$acl_map_key] : Bill_Constant::INVALID_PRIMARY_ID;
    }
}