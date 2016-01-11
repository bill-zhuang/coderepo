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
        $adapter_acl = new Application_Model_DBTable_BackendAcl();
        $adapter_role_acl = new Application_Model_DBTable_BackendRoleAcl();

        if ($current_role == null) {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('index');
        } else if (Application_Model_Auth::getIdentity()->name == 'admin') {
            return;
        }else {
            $baid = $adapter_acl->getAclID($request_module, $request_controller, $request_action);
            if ($adapter_role_acl->isAccessGranted(Application_Model_Auth::getIdentity()->brid, $baid)) {
                return;
            } else {
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('error');
            }
        }
    }
}