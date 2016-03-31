<?php

class BackendAclController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendAcl
     */
    private $_adapter_backend_acl;
    /**
     * @var Application_Model_DBTable_BackendRoleAcl
     */
    private $_adapter_backend_role_acl;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_backend_acl= new Application_Model_DBTable_BackendAcl();
        $this->_adapter_backend_role_acl = new Application_Model_DBTable_BackendRoleAcl();
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
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        $default_controller_dir = APPLICATION_PATH . '/controllers/';
        $module_dir = APPLICATION_PATH . '/modules/';
        //default
        $this->_loadAcl2DB('default', $default_controller_dir);
        //modules
        if (is_dir($module_dir)) {
            $modules = scandir($module_dir);
            foreach ($modules as $module) {
                if ($module != '.' && $module != '..' && is_dir($module_dir . $module . DIRECTORY_SEPARATOR)) {
                    $controller_dir_path = $module_dir . $module . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR;
                    if (is_dir($controller_dir_path)) {
                        $affected_rows += $this->_loadAcl2DB(strtolower($module), $controller_dir_path);
                    }
                }
            }
            $json_array = [
                'data' => [
                    'affectedRows' => $affected_rows,
                ],
            ];
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
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
                    $where = $this->_adapter_backend_acl->getAdapter()->quoteInto('baid=?', $baid);
                    $affected_rows = $this->_adapter_backend_acl->update($data, $where);
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendAcl');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function deleteBackendAclAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $baid = isset($params['baid']) ? intval($params['baid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($baid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $where = [
                        $this->_adapter_backend_acl->getAdapter()->quoteInto('baid=?', $baid),
                        $this->_adapter_backend_acl->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows = $this->_adapter_backend_acl->delete($where);
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteBackendAcl');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }
    
    public function getBackendAclAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $baid = (isset($params['baid'])) ? intval($params['baid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_backend_acl->getBackendAclByID($baid);
            if (!empty($data)) {
                $json_array = [
                    'data' => $data,
                ];
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($keyword !== '') {
            $conditions['module LIKE ? OR controller LIKE ? OR action LIKE ?'] = Bill_Util::getLikeString($keyword);
        }
        $order_by = null;
        $group_by = ['module', 'controller', 'action'];
        $total = $this->_adapter_backend_acl->getBackendAclCount($conditions);
        $data = $this->_adapter_backend_acl->getBackendAclData($conditions, $current_page, $page_length, $order_by, $group_by);

        $json_data = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $page_length),
                'pageIndex' => $current_page,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $page_length,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        return $json_data;
    }

    private function _loadAcl2DB($module_name, $controller_path)
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
        $preg_controller = '/.*?Controller.php$/';
        $preg_controller_postfix = '/Controller.php$/';
        $preg_action = '/public\s+function\s+(.*?)Action\(\)/';
        $preg_action_postfix = '/Action$/';
        $data = [
            'name' => '',
            'module' => '',
            'controller' => '',
            'action' => '',
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if (is_dir($controller_path)) {
            $controllers = scandir($controller_path);
            foreach ($controllers as $controller) {
                if ($controller != '.' && $controller != '..') {
                    if (preg_match($preg_controller, $controller) !== 0) {
                        $controller_name = preg_replace($preg_controller_postfix, '', $controller);
                        $controller_name = strtolower(implode('-', $this->_splitCamel($controller_name)));
                        $controller_content = file_get_contents($controller_path . $controller);
                        $is_match = preg_match_all($preg_action, $controller_content, $action_matches);
                        $data['module'] = $module_name;
                        $data['controller'] = $controller_name;
                        if ($is_match) {
                            $valid_actions = [];
                            foreach ($action_matches[1] as $action) {
                                $action_name = preg_replace($preg_action_postfix, '', $action);
                                $action_name = strtolower(implode('-', $this->_splitCamel($action_name)));
                                $data['action'] = $action_name;
                                $valid_actions[] = $action_name;
                                if (!$this->_adapter_backend_acl->isAclExist($data['module'], $data['controller'], $data['action'])) {
                                    $data['name'] = $data['module'] . '/' . $data['controller'] . '/' . $data['action'];
                                    $affected_rows += $this->_adapter_backend_acl->insert($data);
                                }
                            }
                            //delete unused action
                            if (!empty($valid_actions)) {
                                $this->_removeInvalidAcl($data['module'], $data['controller'], $valid_actions);
                            }
                        } else {
                            $this->_removeInvalidAcl($data['module'], $data['controller'], array());
                        }
                    }
                }
            }
        }

        return $affected_rows;
    }

    private function _removeInvalidAcl($module, $controller, array $valid_actions)
    {
        $invalid_acl_ids = $this->_adapter_backend_acl->getInvalidAclIDs($module, $controller, $valid_actions);
        if (!empty($invalid_acl_ids)) {
            $delete_where = [
                $this->_adapter_backend_acl->getAdapter()->quoteInto('baid in (?)', $invalid_acl_ids),
            ];
            try {
                $this->_adapter_backend_acl->getAdapter()->beginTransaction();
                $this->_adapter_backend_acl->delete($delete_where);
                $this->_adapter_backend_role_acl->delete($delete_where);
                $this->_adapter_backend_acl->getAdapter()->commit();
            } catch (Exception $e) {
                $this->_adapter_backend_acl->getAdapter()->rollBack();
            }
        }
    }

    private function _splitCamel($controller)
    {
        $preg_controller = '/([A-Z][a-z\d]*)/';
        $is_match = preg_match_all($preg_controller, ucfirst($controller), $matches);
        if ($is_match) {
            return $matches[1];
        } else {
            return [];
        }
    }
}
