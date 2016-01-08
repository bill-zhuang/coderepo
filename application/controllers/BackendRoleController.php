<?php

class BackendRoleController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_BackendRole
     */
    private $_adapter_backend_role;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
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
                            'affectedRows' => $affected_rows
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
        exit;
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
                            'affectedRows' => $affected_rows,
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
        exit;
    }
    
    public function deleteBackendRoleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_backend_role->getAdapter()->beginTransaction();
                $brid = isset($params['brid']) ? intval($params['brid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($brid > Bill_Constant::INVALID_PRIMARY_ID) {
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
                            'affectedRows' => $affected_rows,
                        ]
                    ];
                }
            } catch (Exception $e) {
                $this->_adapter_backend_role->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteBackendRole');
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
        exit;
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        $order_by = 'brid ASC';
        $total = $this->_adapter_backend_role->getBackendRoleCount($conditions);
        $data = $this->_adapter_backend_role->getBackendRoleData($conditions, $current_page, $page_length, $order_by);

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
