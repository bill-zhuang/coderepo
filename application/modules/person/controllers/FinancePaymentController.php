<?php

class person_FinancePaymentController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapter_finance_category;
    /**
     * @var Application_Model_DBTable_FinancePaymentMap
     */
    private $_adapter_finance_payment_map;
    /**
     * @var Application_Model_DBTable_FinancePayment
     */
    private $_adapter_finance_payment;

    public function init()
    {
        /* Initialize action controller here */
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
        $this->_adapter_finance_payment = new Application_Model_DBTable_FinancePayment();
        $this->_adapter_finance_payment_map = new Application_Model_DBTable_FinancePaymentMap();
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

    public function addFinancePaymentAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $payments = isset($params['finance_payment_payment']) ? array_filter(explode(',', $params['finance_payment_payment'])) : [];
                $payment_date = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $category_ids = isset($params['finance_payment_fcid']) ? $params['finance_payment_fcid'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';
                $add_time = date('Y-m-d H:i:s');

                $data = [
                    'payment_date' => $payment_date,
                    'detail' => $intro,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $add_time,
                    'update_time' => $add_time
                ];
                if (Bill_Util::validDate($payment_date)) {
                    foreach ($payments as $payment) {
                        $payment = floatval($payment);
                        if ($payment > 0) {
                            $data['payment'] = $payment;
                            $fp_id = $this->_adapter_finance_payment->insert($data);
                            $this->_addFinancePaymentMap($fp_id, $category_ids);
                            $affected_rows += $fp_id;
                        }
                    }
                }
                $this->_adapter_finance_payment->getAdapter()->commit();
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From addFinancePayment');
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
    
    public function modifyFinancePaymentAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fpid = isset($params['finance_payment_fpid']) ? intval($params['finance_payment_fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $payment = isset($params['finance_payment_payment']) ? floatval($params['finance_payment_payment']) : 0;
                $payment_date = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $category_ids = isset($params['finance_payment_fcid']) ? $params['finance_payment_fcid'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';

                if (Bill_Util::validDate($payment_date) && $payment > 0) {
                    $data = [
                        'payment' => $payment,
                        'payment_date' => $payment_date,
                        'detail' => $intro,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = $this->_adapter_finance_payment->getAdapter()->quoteInto('fpid=?', $fpid);
                    $affected_rows = $this->_adapter_finance_payment->update($data, $where);
                    $affected_rows += $this->_updateFinancePaymentMap($fpid, $category_ids);
                }
                $this->_adapter_finance_payment->getAdapter()->commit();
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From modifyFinancePayment');
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
    
    public function deleteFinancePaymentAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fpid = isset($params['fpid']) ? intval($params['fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($fpid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $payment_update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $payment_where = [
                        $this->_adapter_finance_payment->getAdapter()->quoteInto('fpid=?', $fpid),
                        $this->_adapter_finance_payment->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS)
                    ];
                    $affected_rows = $this->_adapter_finance_payment->update($payment_update_data, $payment_where);
                    //update map table
                    $map_update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $map_where = [
                        $this->_adapter_finance_payment_map->getAdapter()->quoteInto('fpid=?', $fpid),
                        $this->_adapter_finance_payment_map->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows += $this->_adapter_finance_payment_map->update($map_update_data, $map_where);
                }
                $this->_adapter_finance_payment->getAdapter()->commit();
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteFinancePayment');
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
    
    public function getFinancePaymentAction()
    {
        $json_array = [];
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $fpid = isset($params['fpid']) ? intval($params['fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($fpid > Bill_Constant::INVALID_PRIMARY_ID) {
                $data = $this->_adapter_finance_payment->getFinancePaymentByID($fpid);
                $data['fcids'] = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($fpid);
                if (!empty($data)) {
                    $json_array = [
                        'data' => $data,
                    ];
                }
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
        $payment_date = isset($params['payment_date']) ? trim($params['payment_date']) : '';
        $finance_category_id = isset($params['category_parent_id']) ? intval($params['category_parent_id']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' != $payment_date) {
            $conditions['payment_date =?'] = $payment_date;
        }
        if (0 !== $finance_category_id) {
            $adapter_payment_map = new Application_Model_DBTable_FinancePaymentMap();
            $fpids = $adapter_payment_map->getFpidByFcid($finance_category_id, 'create_time desc', $current_page, $page_length);
            if (!empty($fpids)) {
                $conditions['fpid in (?)'] = $fpids;
            } else {
                $conditions['1 =?'] = 0;
            }
        }
        $order_by = 'payment_date desc';
        $total = $this->_adapter_finance_payment->getFinancePaymentCount($conditions);
        $data = $this->_adapter_finance_payment->getFinancePaymentData($conditions, $current_page, $page_length, $order_by);
        foreach ($data as &$value) {
            $fcids = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($value['fpid']);
            if (!empty($fcids)) {
                $value['category'] =
                    implode(',', $this->_adapter_finance_category->getFinanceCategoryNames($fcids));
            } else {
                $value['category'] = '';
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

    private function _addFinancePaymentMap($fpid, array $fcids)
    {
        $add_time = date('Y-m-d H:i:s');
        $map_data = [
            'fpid' => 0,
            'fcid' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => $add_time,
            'update_time' => $add_time
        ];

        $map_data['fpid'] = $fpid;
        foreach ($fcids as $category_id) {
            $map_data['fcid'] = $category_id;
            $this->_adapter_finance_payment_map->insert($map_data);
        }
    }
    
    private function _updateFinancePaymentMap($fpid, array $fcids)
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;

        $update_data = [
            'status' => Bill_Constant::INVALID_STATUS,
            'update_time' => date('Y-m-d H:i:s')
        ];
        $insert_data = [
            'fpid' => $fpid,
            'fcid' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        $origin_fcids = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($fpid);
        $update_fcids = array_diff($origin_fcids, $fcids);
        $insert_fcids = array_diff($fcids, $origin_fcids);
        foreach ($update_fcids as $fcid) {
            $where = [
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("fpid=?", $fpid),
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("status=?", Bill_Constant::VALID_STATUS),
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("fcid=?", $fcid),
            ];
            $affected_rows += $this->_adapter_finance_payment_map->update($update_data, $where);
        }

        foreach ($insert_fcids as $fcid) {
            $insert_data['fcid'] = $fcid;
            $affected_rows += $this->_adapter_finance_payment_map->insert($insert_data);
        }

        return $affected_rows;
    }

}
