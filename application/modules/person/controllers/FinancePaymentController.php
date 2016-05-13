<?php

class person_FinancePaymentController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapterFinanceCategory;
    /**
     * @var Application_Model_DBTable_FinancePaymentMap
     */
    private $_adapterFinancePaymentMap;
    /**
     * @var Application_Model_DBTable_FinancePayment
     */
    private $_adapterFinancePayment;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterFinanceCategory = new Application_Model_DBTable_FinanceCategory();
        $this->_adapterFinancePayment = new Application_Model_DBTable_FinancePayment();
        $this->_adapterFinancePaymentMap = new Application_Model_DBTable_FinancePaymentMap();
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

    public function addFinancePaymentAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapterFinancePayment->getAdapter()->beginTransaction();
                $payments = isset($params['finance_payment_payment']) ? array_filter(explode(',', $params['finance_payment_payment'])) : [];
                $paymentDate = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $categoryIds = isset($params['finance_payment_fcid']) ? $params['finance_payment_fcid'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';
                $addTime = date('Y-m-d H:i:s');

                $data = [
                    'payment_date' => $paymentDate,
                    'detail' => $intro,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => $addTime,
                    'update_time' => $addTime
                ];
                if (Bill_Util::validDate($paymentDate)) {
                    foreach ($payments as $payment) {
                        $payment = floatval($payment);
                        if ($payment > 0) {
                            $data['payment'] = $payment;
                            $fpId = $this->_adapterFinancePayment->insert($data);
                            $this->_addFinancePaymentMap($fpId, $categoryIds);
                            $affectedRows += $fpId;
                        }
                    }
                }
                $this->_adapterFinancePayment->getAdapter()->commit();
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapterFinancePayment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From addFinancePayment');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function modifyFinancePaymentAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapterFinancePayment->getAdapter()->beginTransaction();
                $fpid = isset($params['finance_payment_fpid']) ? intval($params['finance_payment_fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $payment = isset($params['finance_payment_payment']) ? floatval($params['finance_payment_payment']) : 0;
                $paymentDate = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $categoryIds = isset($params['finance_payment_fcid']) ? $params['finance_payment_fcid'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';

                if (Bill_Util::validDate($paymentDate) && $payment > 0) {
                    $data = [
                        'payment' => $payment,
                        'payment_date' => $paymentDate,
                        'detail' => $intro,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = $this->_adapterFinancePayment->getAdapter()->quoteInto('fpid=?', $fpid);
                    $affectedRows = $this->_adapterFinancePayment->update($data, $where);
                    $affectedRows += $this->_updateFinancePaymentMap($fpid, $categoryIds);
                }
                $this->_adapterFinancePayment->getAdapter()->commit();
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapterFinancePayment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From modifyFinancePayment');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteFinancePaymentAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            try {
                $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapterFinancePayment->getAdapter()->beginTransaction();
                $fpid = isset($params['fpid']) ? intval($params['fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($fpid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $paymentUpdateData = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $paymentWhere = [
                        $this->_adapterFinancePayment->getAdapter()->quoteInto('fpid=?', $fpid),
                        $this->_adapterFinancePayment->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS)
                    ];
                    $affectedRows = $this->_adapterFinancePayment->update($paymentUpdateData, $paymentWhere);
                    //update map table
                    $mapUpdateData = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $mapWhere = [
                        $this->_adapterFinancePaymentMap->getAdapter()->quoteInto('fpid=?', $fpid),
                        $this->_adapterFinancePaymentMap->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affectedRows += $this->_adapterFinancePaymentMap->update($mapUpdateData, $mapWhere);
                }
                $this->_adapterFinancePayment->getAdapter()->commit();
                $jsonArray = [
                    'data' => [
                        'code' => $affectedRows,
                        'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                    ],
                ];
            } catch (Exception $e) {
                $this->_adapterFinancePayment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteFinancePayment');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function getFinancePaymentAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $fpid = isset($params['fpid']) ? intval($params['fpid']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($fpid > Bill_Constant::INVALID_PRIMARY_ID) {
                $data = $this->_adapterFinancePayment->getFinancePaymentByID($fpid);
                $data['fcids'] = $this->_adapterFinancePaymentMap->getFinanceCategoryIDs($fpid);
                if (!empty($data)) {
                    $jsonArray = [
                        'data' => $data,
                    ];
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

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $paymentDate = isset($params['payment_date']) ? trim($params['payment_date']) : '';
        $financeCategoryId = isset($params['category_parent_id']) ? intval($params['category_parent_id']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' != $paymentDate) {
            $conditions['payment_date =?'] = $paymentDate;
        }
        if (0 !== $financeCategoryId) {
            $adapterPaymentMap = new Application_Model_DBTable_FinancePaymentMap();
            $fpids = $adapterPaymentMap->getFpidByFcid($financeCategoryId, 'create_time desc', $currentPage, $pageLength);
            if (!empty($fpids)) {
                $conditions['fpid in (?)'] = $fpids;
            } else {
                $conditions['1 =?'] = 0;
            }
        }
        $orderBy = 'payment_date desc';
        $total = $this->_adapterFinancePayment->getFinancePaymentCount($conditions);
        $data = $this->_adapterFinancePayment->getFinancePaymentData($conditions, $currentPage, $pageLength, $orderBy);
        foreach ($data as &$value) {
            $fcids = $this->_adapterFinancePaymentMap->getFinanceCategoryIDs($value['fpid']);
            if (!empty($fcids)) {
                $value['category'] =
                    implode(',', $this->_adapterFinanceCategory->getFinanceCategoryNames($fcids));
            } else {
                $value['category'] = '';
            }
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

    private function _addFinancePaymentMap($fpid, array $fcids)
    {
        $addTime = date('Y-m-d H:i:s');
        $mapData = [
            'fpid' => 0,
            'fcid' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => $addTime,
            'update_time' => $addTime
        ];

        $mapData['fpid'] = $fpid;
        foreach ($fcids as $categoryId) {
            $mapData['fcid'] = $categoryId;
            $this->_adapterFinancePaymentMap->insert($mapData);
        }
    }
    
    private function _updateFinancePaymentMap($fpid, array $fcids)
    {
        $affectedRows = Bill_Constant::INIT_AFFECTED_ROWS;

        $updateData = [
            'status' => Bill_Constant::INVALID_STATUS,
            'update_time' => date('Y-m-d H:i:s')
        ];
        $insertData = [
            'fpid' => $fpid,
            'fcid' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        $originFcids = $this->_adapterFinancePaymentMap->getFinanceCategoryIDs($fpid);
        $updateFcids = array_diff($originFcids, $fcids);
        $insertFcids = array_diff($fcids, $originFcids);
        foreach ($updateFcids as $fcid) {
            $where = [
                $this->_adapterFinancePaymentMap->getAdapter()->quoteInto("fpid=?", $fpid),
                $this->_adapterFinancePaymentMap->getAdapter()->quoteInto("status=?", Bill_Constant::VALID_STATUS),
                $this->_adapterFinancePaymentMap->getAdapter()->quoteInto("fcid=?", $fcid),
            ];
            $affectedRows += $this->_adapterFinancePaymentMap->update($updateData, $where);
        }

        foreach ($insertFcids as $fcid) {
            $insertData['fcid'] = $fcid;
            $affectedRows += $this->_adapterFinancePaymentMap->insert($insertData);
        }

        return $affectedRows;
    }

}
