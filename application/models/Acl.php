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

        $current_role = Application_Model_Auth::isValid();
        $adapter_role_acl = new Application_Model_DBTable_BackendRoleAcl();

        if ($current_role == null) {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('index');
        } else if (Application_Model_Auth::getIdentity()->name == Bill_Constant::ADMIN_NAME) {
            return;
        } else {
            $baid = $this->_getAclID($request_module, $request_controller, $request_action);
            if ($baid > Bill_Constant::INVALID_PRIMARY_ID
                && $adapter_role_acl->isAccessGranted(Application_Model_Auth::getIdentity()->brid, $baid)) {
                return;
            } else {
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('error');
            }
        }
    }

    private function _getAclID($module, $controller, $action)
    {
        if (Zend_Registry::isRegistered('acl_map')) {
            $acl_map = Zend_Registry::get('acl_map');
        } else {
            $adapter_acl = new Application_Model_DBTable_BackendAcl();
            $acl_map = $adapter_acl->getAclMap();
            Zend_Registry::set('acl_map', $acl_map);
        }
        $acl_map_key = $module . '_' . $controller . '_' . $action;
        return isset($acl_map[$acl_map_key]) ? $acl_map[$acl_map_key] : Bill_Constant::INVALID_PRIMARY_ID;
    }
}