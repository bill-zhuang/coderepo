<?php

class BackendRoleController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendRole
     */
    private $_adapterBackendRole;
    /**
     * @var Application_Model_DBTable_BackendAcl
     */
    private $_adapterBackendAcl;
    /**
     * @var Application_Model_DBTable_BackendRoleAcl
     */
    private $_adapterBackendRoleAcl;
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapterBackendUser;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterBackendRole = new Application_Model_DBTable_BackendRole();
        $this->_adapterBackendAcl = new Application_Model_DBTable_BackendAcl();
        $this->_adapterBackendRoleAcl = new Application_Model_DBTable_BackendRoleAcl();
        $this->_adapterBackendUser= new Application_Model_DBTable_BackendUser();
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
                $roleName = trim($params['backend_role_role']);
                if ($roleName !== ''
                    && !$this->_adapterBackendRole->isRoleExist($roleName, Bill_Constant::INVALID_PRIMARY_ID)) {
                    $data = [
                        'role' => $roleName,
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $affectedRows = $this->_adapterBackendRole->insert($data);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addBackendRole');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function modifyBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $brid = intval($params['backend_role_brid']);
                $roleName = trim($params['backend_role_role']);
                if ($brid > Bill_Constant::INVALID_PRIMARY_ID && $roleName !== ''
                    && !$this->_adapterBackendRole->isRoleExist($roleName, $brid)) {
                    $data = [
                        'role' => $roleName,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = $this->_adapterBackendRole->getAdapter()->quoteInto('brid=?', $brid);
                    $affectedRows = $this->_adapterBackendRole->update($data, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendRole');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapterBackendRole->getAdapter()->beginTransaction();
                $brid = isset($params['brid']) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($brid > Bill_Constant::INVALID_PRIMARY_ID) {
                    if ($this->_adapterBackendUser->getRoleCount($brid) == 0) {
                        $updateData = [
                            'status' => Bill_Constant::INVALID_STATUS,
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = [
                            $this->_adapterBackendRole->getAdapter()->quoteInto('brid=?', $brid),
                            $this->_adapterBackendRole->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                        ];
                        $affectedRows = $this->_adapterBackendRole->update($updateData, $where);
                        $this->_adapterBackendRole->getAdapter()->commit();
                        $jsonArray = [
                            'data' => [
                                'code' => $affectedRows,
                                'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                            ]
                        ];
                    } else {
                        $jsonArray = [
                            'error' => [
                                'message' => '该角色下还有用户，删除失败',
                            ],
                        ];
                    }
                }
            } catch (Exception $e) {
                $this->_adapterBackendRole->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteBackendRole');
            }
        }

        if (!isset($jsonArray['data']) && !isset($jsonArray['error'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getBackendRoleAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $brid = (isset($params['brid'])) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterBackendRole->getByPrimaryKey($brid);
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

    public function getBackendRoleAclAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $brid = (isset($params['brid'])) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            //
            $aclList = $this->_adapterBackendAcl->getAclList();
            $jsonArray = [
                'data' => [
                    'brid' => $brid,
                    'aclList' => $aclList,
                    'roleAcl' => $this->_adapterBackendRoleAcl->getUserAclByBrid($brid),
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

    public function modifyBackendRoleAclAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost('params', []);
            $brid = (isset($params['backend_role_acl_brid']))
                ? intval($params['backend_role_acl_brid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $submitBaids = (isset($params['backend_role_acl_baid'])) ? array_filter($params['backend_role_acl_baid']) : [];
            if ($brid > Bill_Constant::INVALID_PRIMARY_ID && !empty($submitBaids)) {
                //
                $existBaids = $this->_adapterBackendRoleAcl->getUserAclByBrid($brid);
                $addBaids = array_diff($submitBaids, $existBaids);
                $removeBaids = array_diff($existBaids, $submitBaids);
                //transaction
                try {
                    $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
                    $this->_adapterBackendRoleAcl->getAdapter()->beginTransaction();
                    if (!empty($removeBaids)) {
                        $where = [
                            $this->_adapterBackendRoleAcl->getAdapter()->quoteInto('brid=?', $brid),
                            $this->_adapterBackendRoleAcl->getAdapter()->quoteInto('baid in (?)', $removeBaids),
                        ];
                        $affectedRows += $this->_adapterBackendRoleAcl->delete($where);
                    }
                    if (!empty($addBaids)) {
                        $initData = [
                            'brid' => $brid,
                            'baid' => Bill_Constant::INVALID_PRIMARY_ID,
                            'status' => Bill_Constant::VALID_STATUS,
                            'create_time' => date('Y-m-d H:i:s'),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        foreach ($addBaids as $baid) {
                            $initData['baid'] = $baid;
                            $affectedRows += $this->_adapterBackendRoleAcl->insert($initData);
                        }
                    }
                    $this->_adapterBackendRoleAcl->getAdapter()->commit();
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ],
                    ];
                } catch (Exception $e) {
                    $this->_adapterBackendRoleAcl->getAdapter()->rollBack();
                    Bill_Util::handleException($e, 'Error from modifyBackendRoleAcl');
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

    public function getAllRolesAction()
    {
        $jsonArray = [
            'data' => $this->_adapterBackendRole->getAllRoles(),
        ];
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
            $conditions['role like ?'] = Bill_Util::getLikeString($keyword);
        }
        $orderBy = 'brid ASC';
        $total = $this->_adapterBackendRole->getSearchCount($conditions);
        $data = $this->_adapterBackendRole->getSearchData($conditions, $currentPage, $pageLength, $orderBy);
        foreach ($data as &$value) {
            $value['count'] = $this->_adapterBackendUser->getRoleCount($value['brid']);
        }

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
    
}
