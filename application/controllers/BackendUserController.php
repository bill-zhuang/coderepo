<?php

class BackendUserController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapterBackendUser;
    /**
     * @var Application_Model_DBTable_BackendRole
     */
    private $_adapterBackendRole;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterBackendUser = new Application_Model_DBTable_BackendUser();
        $this->_adapterBackendRole = new Application_Model_DBTable_BackendRole();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    public function addBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $name = trim($params['backend_user_name']);
                if (!$this->_adapterBackendUser->isUserNameExist($name, Bill_Constant::INVALID_PRIMARY_ID)) {
                    $security = new Bill_Security();
                    $salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
                    $googleAuthenticator = Bill_GoogleAuthenticator::createUserSecretAndQRUrl($name);
                    $data = [
                        'name' => $name,
                        'password' => md5(Bill_Constant::DEFAULT_PASSWORD . $salt),
                        'salt' => $salt,
                        'brid' => intval($params['backend_user_brid']),
                        'google_secret' => $googleAuthenticator['secret'],
                        'google_qr_url' => $googleAuthenticator['qrCodeUrl'],
                        'remark' => trim($params['backend_user_remark']),
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $affectedRows = $this->_adapterBackendUser->insert($data);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                        ],
                    ];
                } else {
                    $jsonArray = [
                        'data' => [
                            'code' => Bill_Constant::INIT_AFFECTED_ROWS,
                            'message' => Bill_JsMessage::ACCOUNT_EXIST,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addBackendUser');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function modifyBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $buid = intval($params['backend_user_buid']);
                if ($buid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $name = trim($params['backend_user_name']);
                    if (!$this->_adapterBackendUser->isUserNameExist($name, $buid)) {
                        $data = [
                            'name' => $name,
                            'brid' => intval($params['backend_user_brid']),
                            'remark' => trim($params['backend_user_remark']),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = $this->_adapterBackendUser->getAdapter()->quoteInto('buid=?', $buid);
                        $affectedRows = $this->_adapterBackendUser->update($data, $where);
                        $jsonArray = [
                            'data' => [
                                'code' => $affectedRows,
                                'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                        ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                            ]
                        ];
                    } else {
                        $jsonArray = [
                            'data' => [
                                'code' => Bill_Constant::INIT_AFFECTED_ROWS,
                                'message' => Bill_JsMessage::ACCOUNT_EXIST,
                            ],
                        ];
                    }
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendUser');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $buid = isset($params['buid']) ? intval($params['buid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($buid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $updateData = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = [
                        $this->_adapterBackendUser->getAdapter()->quoteInto('buid=?', $buid),
                        $this->_adapterBackendUser->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affectedRows = $this->_adapterBackendUser->update($updateData, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteBackendUser');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }

    public function recoverBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $buid = isset($params['buid']) ? intval($params['buid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($buid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $updateData = [
                        'status' => Bill_Constant::VALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = [
                        $this->_adapterBackendUser->getAdapter()->quoteInto('buid=?', $buid),
                        $this->_adapterBackendUser->getAdapter()->quoteInto('status=?', Bill_Constant::INVALID_STATUS),
                    ];
                    $affectedRows = $this->_adapterBackendUser->update($updateData, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::RECOVER_ACCOUNT_SUCCESS : Bill_JsMessage::RECOVER_ACCOUNT_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From recoverBackendUser');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getBackendUserAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $buid = (isset($params['buid'])) ? intval($params['buid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterBackendUser->getByPrimaryKey($buid);
            if (!empty($data)) {
                $jsonArray = [
                    'data' => [
                        'buid' => $data['buid'],
                        'name' => $data['name'],
                        'brid' => $data['brid'],
                        'remark' => $data['remark'],
                        'roles' => $this->_adapterBackendRole->getAllRoles(),
                    ],
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
        $tabType = isset($params['tab_type']) ? intval($params['tab_type']) : 1;

        $conditions = [
            'status =?' => $tabType,
            'name!=?' => Bill_Constant::ADMIN_NAME,
        ];
        if ($keyword !== '') {
            $conditions['name like ?'] = Bill_Util::getLikeString($keyword);
        }
        $orderBy = 'buid ASC';
        $total = $this->_adapterBackendUser->getSearchCount($conditions);
        $data = $this->_adapterBackendUser->getSearchData($conditions, $currentPage, $pageLength, $orderBy);
        $roles = $this->_adapterBackendRole->getAllRoles();
        $output = [];
        foreach ($data as $value) {
            $output[] = [
                'buid' => $value['buid'],
                'name' => $value['name'],
                'role' => isset($roles[$value['brid']]) ? $roles[$value['brid']] : '-',
                'remark' => $value['remark'],
                'googleQrUrl' => $value['google_qr_url'],
            ];
        }

        $jsonData = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $pageLength),
                'pageIndex' => $currentPage,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $pageLength,
                'currentItemCount' => count($output),
                'items' => $output,
            ],
        ];
        return $jsonData;
    }
    
}
