<?php
class Application_Model_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //return;
        $request_module = $request->getModuleName();
        $request_controller = $request->getControllerName();
        //$request_action = $request->getActionName();
        if ($request_module == 'default' && $request_controller == 'login')
        {
            return;
        }
        if ($request_module == 'person' && $request_controller == 'console')
        {
            //console not auth
            return;
        }

        $current_role = Application_Model_Auth::isValid();

        if ($current_role == null)
        {
            $request->setModuleName('default');
            $request->setControllerName('login');
            $request->setActionName('index');
        }
        else
        {
            //todo set access list
            return;
        }
    }
}