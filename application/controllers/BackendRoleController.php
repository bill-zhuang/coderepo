<?php

class BackendRoleController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendRole
     */
    private $_adapter_backend_role;
    /**
     * @var Application_Model_DBTable_BackendAcl
     */
    private $_adapter_backend_acl;
    /**
     * @var Application_Model_DBTable_BackendRoleAcl
     */
    private $_adapter_backend_role_acl;
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapter_backend_user;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_backend_role= new Application_Model_DBTable_BackendRole();
        $this->_adapter_backend_acl = new Application_Model_DBTable_BackendAcl();
        $this->_adapter_backend_role_acl = new Application_Model_DBTable_BackendRoleAcl();
        $this->_adapter_backend_user= new Application_Model_DBTable_BackendUser();
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

    public function addBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $role_name = trim($params['backend_role_role']);
                if ($role_name !== ''
                    && !$this->_adapter_backend_role->isRoleExist($role_name, Bill_Constant::INVALID_PRIMARY_ID)) {
                    $data = [
                        'role' => $role_name,
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $affected_rows = $this->_adapter_backend_role->insert($data);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addBackendRole');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function modifyBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $brid = intval($params['backend_role_brid']);
                $role_name = trim($params['backend_role_role']);
                if ($brid > Bill_Constant::INVALID_PRIMARY_ID && $role_name !== ''
                    && !$this->_adapter_backend_role->isRoleExist($role_name, $brid)) {
                    $data = [
                        'role' => $role_name,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = $this->_adapter_backend_role->getAdapter()->quoteInto('brid=?', $brid);
                    $affected_rows = $this->_adapter_backend_role->update($data, $where);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendRole');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function deleteBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_backend_role->getAdapter()->beginTransaction();
                $brid = isset($params['brid']) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($brid > Bill_Constant::INVALID_PRIMARY_ID) {
                    if ($this->_adapter_backend_user->getRoleCount($brid) == 0) {
                        $update_data = [
                            'status' => Bill_Constant::INVALID_STATUS,
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = [
                            $this->_adapter_backend_role->getAdapter()->quoteInto('brid=?', $brid),
                            $this->_adapter_backend_role->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                        ];
                        $affected_rows = $this->_adapter_backend_role->update($update_data, $where);
                        $this->_adapter_backend_role->getAdapter()->commit();
                        $json_array = [
                            'data' => [
                                'code' => $affected_rows,
                                'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                            ]
                        ];
                    } else {
                        $json_array = [
                            'error' => [
                                'message' => '改角色下还有用户，删除失败',
                            ],
                        ];
                    }
                }
            } catch (Exception $e) {
                $this->_adapter_backend_role->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteBackendRole');
            }
        }

        if (!isset($json_array['data']) && !isset($json_array['error'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }
    
    public function getBackendRoleAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $brid = (isset($params['brid'])) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_backend_role->getBackendRoleByID($brid);
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

    public function getBackendRoleAclAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $brid = (isset($params['brid'])) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            //
            $aclList = $this->_adapter_backend_acl->getAclList();
            $json_array = [
                'data' => [
                    'brid' => $brid,
                    'aclList' => $aclList,
                    'roleAcl' => $this->_adapter_backend_role_acl->getUserAclByBrid($brid),
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

    public function modifyBackendRoleAclAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $brid = (isset($params['backend_role_acl_brid']))
                ? intval($params['backend_role_acl_brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $submit_baids = (isset($params['backend_role_acl_baid'])) ? array_filter($params['backend_role_acl_baid']) : [];
            if ($brid > Bill_Constant::INVALID_PRIMARY_ID && !empty($submit_baids)) {
                //
                $exist_baids = $this->_adapter_backend_role_acl->getUserAclByBrid($brid);
                $add_baids = array_diff($submit_baids, $exist_baids);
                $remove_baids = array_diff($exist_baids, $submit_baids);
                //transaction
                try {
                    $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                    $this->_adapter_backend_role_acl->getAdapter()->beginTransaction();
                    if (!empty($remove_baids)) {
                        $where = [
                            $this->_adapter_backend_role_acl->getAdapter()->quoteInto('brid=?', $brid),
                            $this->_adapter_backend_role_acl->getAdapter()->quoteInto('baid in (?)', $remove_baids),
                        ];
                        $affected_rows += $this->_adapter_backend_role_acl->delete($where);
                    }
                    if (!empty($add_baids)) {
                        $init_data = [
                            'brid' => $brid,
                            'baid' => Bill_Constant::INVALID_PRIMARY_ID,
                            'status' => Bill_Constant::VALID_STATUS,
                            'create_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        foreach ($add_baids as $baid) {
                            $init_data['baid'] = $baid;
                            $affected_rows += $this->_adapter_backend_role_acl->insert($init_data);
                        }
                    }
                    $this->_adapter_backend_role_acl->getAdapter()->commit();
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ],
                    ];
                } catch (Exception $e) {
                    $this->_adapter_backend_role_acl->getAdapter()->rollBack();
                    Bill_Util::handleException($e, 'Error from modifyBackendRoleAcl');
                }
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }

    public function getAllRolesAction()
    {
        $json_array = [
            'data' => $this->_adapter_backend_role->getAllRoles(),
        ];
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
            $conditions['name like ?'] = Bill_Util::getLikeString($keyword);
        }
        $order_by = 'brid ASC';
        $total = $this->_adapter_backend_role->getBackendRoleCount($conditions);
        $data = $this->_adapter_backend_role->getBackendRoleData($conditions, $current_page, $page_length, $order_by);
        foreach ($data as &$value) {
            $value['count'] = $this->_adapter_backend_user->getRoleCount($value['brid']);
        }

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
    
}
