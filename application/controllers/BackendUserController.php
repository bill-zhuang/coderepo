<?php

class BackendUserController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendUser
     */
    private $_adapter_backend_user;
    /**
     * @var Application_Model_DBTable_BackendRole
     */
    private $_adapter_backend_role;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
        $this->_adapter_backend_user= new Application_Model_DBTable_BackendUser();
        $this->_adapter_backend_role= new Application_Model_DBTable_BackendRole();
    }

    public function indexAction()
    {
        // action body
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
        exit;
    }

    public function addBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $name = trim($params['backend_user_name']);
                if (!$this->_adapter_backend_user->isUserNameExist($name, Bill_Constant::INVALID_PRIMARY_ID)) {
                    $security = new Bill_Security();
                    $salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
                    $data = [
                        'name' => $name,
                        'password' => md5(Bill_Constant::DEFAULT_PASSWORD . $salt),
                        'salt' => $salt,
                        'brid' => intval($params['backend_user_brid']),
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $affected_rows = $this->_adapter_backend_user->insert($data);
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows
                        ],
                    ];
                } else {
                    $json_array = [
                        'data' => [
                            'affectedRows' => Bill_Constant::INIT_AFFECTED_ROWS,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addBackendUser');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
        exit;
    }
    
    public function modifyBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $buid = intval($params['backend_user_buid']);
                if ($buid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $name = trim($params['backend_user_name']);
                    if (!$this->_adapter_backend_user->isUserNameExist($name, $buid)) {
                        $data = [
                            'name' => $name,
                            'brid' => intval($params['backend_user_brid']),
                            'update_time' => date('Y-m-d H:i:s'),
                        ];
                        $where = $this->_adapter_backend_user->getAdapter()->quoteInto('buid=?', $buid);
                        $affected_rows = $this->_adapter_backend_user->update($data, $where);
                        $json_array = [
                            'data' => [
                                'affectedRows' => $affected_rows,
                            ]
                        ];
                    } else {
                        $json_array = [
                            'data' => [
                                'affectedRows' => Bill_Constant::INIT_AFFECTED_ROWS,
                            ],
                        ];
                    }
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyBackendUser');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
        exit;
    }
    
    public function deleteBackendUserAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $buid = isset($params['buid']) ? intval($params['buid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($buid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = [
                        $this->_adapter_backend_user->getAdapter()->quoteInto('buid=?', $buid),
                        $this->_adapter_backend_user->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows = $this->_adapter_backend_user->update($update_data, $where);
                    $json_array = [
                        'data' => [
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteBackendUser');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
        exit;
    }
    
    public function getBackendUserAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $buid = (isset($params['buid'])) ? intval($params['buid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_backend_user->getBackendUserByID($buid);
            $data['roles'] = $this->_adapter_backend_role->getAllRoles();
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
        exit;
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS,
            'name!=?' => 'admin',
        ];
        $order_by = 'buid ASC';
        $total = $this->_adapter_backend_user->getBackendUserCount($conditions);
        $data = $this->_adapter_backend_user->getBackendUserData($conditions, $current_page, $page_length, $order_by);
        $roles = $this->_adapter_backend_role->getAllRoles();
        foreach ($data as &$value) {
            if (isset($roles[$value['brid']])) {
                $value['role'] = $roles[$value['brid']];
            } else {
                $value['role'] = '-';
            }
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
