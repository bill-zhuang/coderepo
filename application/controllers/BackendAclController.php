<?php

class BackendAclController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendAcl
     */
    private $_adapterBackendAcl;
    /**
     * @var Application_Model_DBTable_BackendRoleAcl
     */
    private $_adapterBackendRoleAcl;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterBackendAcl = new Application_Model_DBTable_BackendAcl();
        $this->_adapterBackendRoleAcl = new Application_Model_DBTable_BackendRoleAcl();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    public function loadBackendAclAction()
    {
        $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
        $defaultControllerDir = APPLICATION_PATH . '/controllers/';
        $moduleDir = APPLICATION_PATH . '/modules/';
        //default
        $this->_loadAcl2DB('default', $defaultControllerDir);
        //modules
        if (is_dir($moduleDir)) {
            $modules = scandir($moduleDir);
            foreach ($modules as $module) {
                if ($module != '.' && $module != '..' && is_dir($moduleDir . $module . DIRECTORY_SEPARATOR)) {
                    $controllerDirPath = $moduleDir . $module . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR;
                    if (is_dir($controllerDirPath)) {
                        $affectedRows += $this->_loadAcl2DB(strtolower($module), $controllerDirPath);
                    }
                }
            }
            $jsonArray = [
                'data' => [
                    'code' => $affectedRows,
                    'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                            ? Bill_JsMessage::LOAD_ACL_SUCCESS : Bill_JsMessage::LOAD_ACL_NO_ACL_LOADED,
                ],
            ];
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function modifyBackendAclAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $baid = intval($params['backend_acl_baid']);
                if ($baid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $data = [
                        'name' => trim($params['backend_acl_name']),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = $this->_adapterBackendAcl->getAdapter()->quoteInto('baid=?', $baid);
                    $affectedRows = $this->_adapterBackendAcl->update($data, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendAcl');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteBackendAclAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $baid = isset($params['baid']) ? intval($params['baid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($baid > Bill_Constant::INVALID_PRIMARY_ID) {
                $where = [
                    $this->_adapterBackendAcl->getAdapter()->quoteInto('baid=?', $baid),
                    $this->_adapterBackendAcl->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                ];
                try {
                    $this->_adapterBackendAcl->getAdapter()->beginTransaction();
                    $affectedRows = $this->_adapterBackendAcl->delete($where);
                    $this->_adapterBackendRoleAcl->delete($where);
                    $this->_adapterBackendAcl->getAdapter()->commit();
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                } catch (Exception $e) {
                    $this->_adapterBackendAcl->getAdapter()->rollBack();
                    Bill_Util::handleException($e, 'Error From deleteBackendAcl');
                }
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getBackendAclAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $baid = (isset($params['baid'])) ? intval($params['baid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterBackendAcl->getBackendAclByID($baid);
            if (!empty($data)) {
                $jsonArray = [
                    'data' => $data,
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($keyword !== '') {
            $conditions['module LIKE ? OR controller LIKE ? OR action LIKE ?'] = Bill_Util::getLikeString($keyword);
        }
        $orderBy = null;
        $groupBy = ['module', 'controller', 'action'];
        $total = $this->_adapterBackendAcl->getBackendAclCount($conditions);
        $data = $this->_adapterBackendAcl->getBackendAclData($conditions, $currentPage, $pageLength, $orderBy, $groupBy);

        $jsonData = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $pageLength),
                'pageIndex' => $currentPage,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $pageLength,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        return $jsonData;
    }

    private function _loadAcl2DB($moduleName, $controllerPath)
    {
        $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
        $pregController = '/.*?Controller.php$/';
        $pregController_postfix = '/Controller.php$/';
        $pregAction = '/public\s+function\s+(.*?)Action\(\)/';
        $pregAction_postfix = '/Action$/';
        $data = [
            'name' => '',
            'module' => '',
            'controller' => '',
            'action' => '',
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if (is_dir($controllerPath)) {
            $validControllers = [];
            $controllers = scandir($controllerPath);
            foreach ($controllers as $controller) {
                if ($controller != '.' && $controller != '..') {
                    if (preg_match($pregController, $controller) !== 0) {
                        $controllerName = preg_replace($pregController_postfix, '', $controller);
                        $controllerName = strtolower(implode('-', $this->_splitCamel($controllerName)));
                        $controllerContent = file_get_contents($controllerPath . $controller);
                        $isMatch = preg_match_all($pregAction, $controllerContent, $actionMatches);
                        $data['module'] = $moduleName;
                        $data['controller'] = $controllerName;
                        $validControllers[] = $controllerName;
                        $validActions = [];
                        if ($isMatch) {
                            foreach ($actionMatches[1] as $action) {
                                $actionName = preg_replace($pregAction_postfix, '', $action);
                                $actionName = strtolower(implode('-', $this->_splitCamel($actionName)));
                                $data['action'] = $actionName;
                                $validActions[] = $actionName;
                                if (!$this->_adapterBackendAcl->isAclExist($data['module'], $data['controller'], $data['action'])) {
                                    $data['name'] = $data['module'] . '/' . $data['controller'] . '/' . $data['action'];
                                    $affectedRows += $this->_adapterBackendAcl->insert($data);
                                }
                            }
                        }
                        //delete unused action
                        $this->_removeInvalidAcl($data['module'], $data['controller'], $validActions);
                    }
                }
            }
            //delete unused controller
            $this->_removeInvalidAcl($moduleName, $validControllers, array());
        }

        return $affectedRows;
    }

    private function _removeInvalidAcl($module, $controller, array $validActions)
    {
        if (!is_array($controller)) {
            $invalidAclIds = $this->_adapterBackendAcl->getInvalidActionsAclIDs($module, $controller, $validActions);
        } else {
            $invalidAclIds = $this->_adapterBackendAcl->getInvalidControllersAclIDs($module, $controller);
        }
        if (!empty($invalidAclIds)) {
            $deleteWhere = [
                $this->_adapterBackendAcl->getAdapter()->quoteInto('baid in (?)', $invalidAclIds),
            ];
            try {
                $this->_adapterBackendAcl->getAdapter()->beginTransaction();
                $this->_adapterBackendAcl->delete($deleteWhere);
                $this->_adapterBackendRoleAcl->delete($deleteWhere);
                $this->_adapterBackendAcl->getAdapter()->commit();
            } catch (Exception $e) {
                $this->_adapterBackendAcl->getAdapter()->rollBack();
            }
        }
    }

    private function _splitCamel($controller)
    {
        $pregController = '/([A-Z][a-z\d]*)/';
        $isMatch = preg_match_all($pregController, ucfirst($controller), $matches);
        if ($isMatch) {
            return $matches[1];
        } else {
            return [];
        }
    }
}
