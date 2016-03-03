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
            if (Application_Model_Auth::getIdentity()->name == Bill_Constant::ADMIN_NAME) {
                return;
            } else {
                $adapter_role_acl = new Application_Model_DBTable_BackendRoleAcl();
                $baid = $this->_getAclID($request_module, $request_controller, $request_action);
                if ($baid > Bill_Constant::INVALID_PRIMARY_ID
                    && $adapter_role_acl->isAccessGranted(Application_Model_Auth::getIdentity()->brid, $baid)) {
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
            }
        } else {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('index');
        }
    }

    private function _getAclID($module, $controller, $action)
    {
        if (!Zend_Registry::isRegistered(Bill_Constant::ACL_MAP_NAME)) {
            $adapter_acl = new Application_Model_DBTable_BackendAcl();
            $adapter_acl->updateGlobalAclMap();
        }
        $acl_map = Zend_Registry::get(Bill_Constant::ACL_MAP_NAME);
        $acl_map_key = Bill_Util::getAclMapKey($module, $controller, $action);
        return isset($acl_map[$acl_map_key]) ? $acl_map[$acl_map_key] : Bill_Constant::INVALID_PRIMARY_ID;
    }
}