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
        $this->_helper->layout()->setLayout('layout');
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
        if ($this->getRequest()->isPost())
        {
            try 
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $payments = isset($params['finance_payment_payment']) ? array_filter(explode(',', $params['finance_payment_payment'])) : [];
                $payment_date = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $category_ids = isset($params['finance_payment_fc_id']) ? $params['finance_payment_fc_id'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';
                $add_time = date('Y-m-d H:i:s');

                $data = [
                    'fp_payment_date' => $payment_date,
                    'fp_detail' => $intro,
                    'fp_status' => Bill_Constant::VALID_STATUS,
                    'fp_create_time' => $add_time,
                    'fp_update_time' => $add_time
                ];
                if (Bill_Util::validDate($payment_date))
                {
                    foreach ($payments as $payment)
                    {
                        $payment = floatval($payment);
                        if ($payment > 0)
                        {
                            $data['fp_payment'] = $payment;
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
            }
            catch (Exception $e)
            {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From addFinancePayment');
            }
        }

        if (!isset($json_array['data']))
        {
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
        if ($this->getRequest()->isPost())
        {
            try
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fp_id = isset($params['finance_payment_fp_id']) ? intval($params['finance_payment_fp_id']) : Bill_Constant::INVALID_PRIMARY_ID;
                $payment = isset($params['finance_payment_payment']) ? floatval($params['finance_payment_payment']) : 0;
                $payment_date = isset($params['finance_payment_payment_date']) ? trim($params['finance_payment_payment_date']) : '';
                $category_ids = isset($params['finance_payment_fc_id']) ? $params['finance_payment_fc_id'] : [];
                $intro = isset($params['finance_payment_intro']) ? trim($params['finance_payment_intro']) : '';

                if (Bill_Util::validDate($payment_date) && $payment > 0)
                {
                    $data = [
                        'fp_payment' => $payment,
                        'fp_payment_date' => $payment_date,
                        'fp_detail' => $intro,
                        'fp_update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_id=?', $fp_id);
                    $affected_rows = $this->_adapter_finance_payment->update($data, $where);
                    $affected_rows += $this->_updateFinancePaymentMap($fp_id, $category_ids);
                }
                $this->_adapter_finance_payment->getAdapter()->commit();
                $json_array = [
                    'data' => [
                        'affectedRows' => $affected_rows
                    ],
                ];
            }
            catch (Exception $e)
            {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From modifyFinancePayment');
            }
        }

        if (!isset($json_array['data']))
        {
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
        if ($this->getRequest()->isPost())
        {
            try
            {
                $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;
                $params = $this->getRequest()->getPost('params', []);
                $this->_adapter_finance_payment->getAdapter()->beginTransaction();
                $fp_id = isset($params['fp_id']) ? intval($params['fp_id']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($fp_id > Bill_Constant::INVALID_PRIMARY_ID)
                {
                    $payment_update_data = [
                        'fp_status' => Bill_Constant::INVALID_STATUS,
                        'fp_update_time' => date('Y-m-d H:i:s')
                    ];
                    $payment_where = [
                        $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_id=?', $fp_id),
                        $this->_adapter_finance_payment->getAdapter()->quoteInto('fp_status=?', Bill_Constant::VALID_STATUS)
                    ];
                    $affected_rows = $this->_adapter_finance_payment->update($payment_update_data, $payment_where);
                    //update map table
                    $map_update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $map_where = [
                        $this->_adapter_finance_payment_map->getAdapter()->quoteInto('fp_id=?', $fp_id),
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
            }
            catch (Exception $e)
            {
                $this->_adapter_finance_payment->getAdapter()->rollBack();
                Bill_Util::handleException($e, 'Error From deleteFinancePayment');
            }
        }

        if (!isset($json_array['data']))
        {
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
        if ($this->getRequest()->isGet())
        {
            $params = $this->getRequest()->getQuery('params', []);
            $fp_id = isset($params['fp_id']) ? intval($params['fp_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            if ($fp_id > Bill_Constant::INVALID_PRIMARY_ID)
            {
                $data = $this->_adapter_finance_payment->getFinancePaymentByID($fp_id);
                $data['fc_ids'] = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($fp_id);
                if (!empty($data))
                {
                    $json_array = [
                        'data' => $data,
                    ];
                }
            }
        }

        if (!isset($json_array['data']))
        {
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
            'fp_status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' != $payment_date)
        {
            $conditions['fp_payment_date =?'] = $payment_date;
        }
        if (0 !== $finance_category_id)
        {
            $adapter_payment_map = new Application_Model_DBTable_FinancePaymentMap();
            $fpids = $adapter_payment_map->getFpidByFcid($finance_category_id, 'create_time desc', $page_length, $start);
            if (!empty($fpids))
            {
                $conditions['fp_id in (?)'] = $fpids;
            }
            else
            {
                $conditions['1 =?'] = 0;
            }
        }
        $order_by = 'fp_payment_date desc';
        $total = $this->_adapter_finance_payment->getFinancePaymentCount($conditions);
        $data = $this->_adapter_finance_payment->getFinancePaymentData($conditions, $page_length, $start, $order_by);
        foreach ($data as &$value)
        {
            $fc_ids = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($value['fp_id']);
            if (!empty($fc_ids))
            {
                $value['category'] =
                    implode(',', $this->_adapter_finance_category->getFinanceCategoryNames($fc_ids));
            }
            else
            {
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

    private function _addFinancePaymentMap($fp_id, array $fc_ids)
    {
        $add_time = date('Y-m-d H:i:s');
        $map_data = [
            'fp_id' => 0,
            'fc_id' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => $add_time,
            'update_time' => $add_time
        ];

        $map_data['fp_id'] = $fp_id;
        foreach ($fc_ids as $category_id)
        {
            $map_data['fc_id'] = $category_id;
            $this->_adapter_finance_payment_map->insert($map_data);
        }
    }
    
    private function _updateFinancePaymentMap($fp_id, array $fc_ids)
    {
        $affected_rows = Bill_Constant::INIT_AFFECTED_ROWS;

        $update_data = [
            'status' => Bill_Constant::INVALID_STATUS,
            'update_time' => date('Y-m-d H:i:s')
        ];
        $insert_data = [
            'fp_id' => $fp_id,
            'fc_id' => 0,
            'status' => Bill_Constant::VALID_STATUS,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        $origin_fc_ids = $this->_adapter_finance_payment_map->getFinanceCategoryIDs($fp_id);
        $update_fc_ids = array_diff($origin_fc_ids, $fc_ids);
        $insert_fc_ids = array_diff($fc_ids, $origin_fc_ids);
        foreach ($update_fc_ids as $fc_id)
        {
            $where = [
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("fp_id=?", $fp_id),
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("status=?", Bill_Constant::VALID_STATUS),
                $this->_adapter_finance_payment_map->getAdapter()->quoteInto("fc_id=?", $fc_id),
            ];
            $affected_rows += $this->_adapter_finance_payment_map->update($update_data, $where);
        }

        foreach ($insert_fc_ids as $fc_id)
        {
            $insert_data['fc_id'] = $fc_id;
            $affected_rows += $this->_adapter_finance_payment_map->insert($insert_data);
        }

        return $affected_rows;
    }

}
