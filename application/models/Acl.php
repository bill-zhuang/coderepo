<?php
class Application_Model_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //return;
        $requestModule = $request->getModuleName();
        $requestController = $request->getControllerName();
        $requestAction = $request->getActionName();
        if ($requestModule == 'default' && $requestController == 'login') {
            return;
        }
        if ($requestModule == 'person' && $requestController == 'console') {
            //console not auth
            return;
        }

        if (Application_Model_Auth::isValid()) {
            if (Application_Model_Auth::getIdentity()->name != Bill_Constant::ADMIN_NAME) {
                $adapterRoleAcl = new Application_Model_DBTable_BackendRoleAcl();
                $baid = $this->_getAclID($requestModule, $requestController, $requestAction);
                if ($baid > Bill_Constant::INVALID_PRIMARY_ID
                    && $adapterRoleAcl->isAccessGranted(Application_Model_Auth::getIdentity()->brid, $baid)
                ) {
                    return;
                } else {
                    if ($this->getRequest()->isXmlHttpRequest()) {
                        $jsonArray = [
                            'error' => [
                                'message' => '无权限访问',
                            ],
                        ];
                        echo json_encode($jsonArray);
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

        $sqlInfo = Bill_Util::getSQLInfo();
        if (isset($sqlInfo['queryCost']) && $sqlInfo['queryCost'] > Bill_Constant::SQL_QUERY_COST_TRIGGER) {
            //TODO slow query trigger
        }
    }

    private function _getAclID($module, $controller, $action)
    {
        $adapterAcl = new Application_Model_DBTable_BackendAcl();
        $aclMap = $adapterAcl->getAclMap();
        $aclMap_key = Bill_Util::getAclMapKey($module, $controller, $action);
        return isset($aclMap[$aclMap_key]) ? $aclMap[$aclMap_key] : Bill_Constant::INVALID_PRIMARY_ID;
    }
}