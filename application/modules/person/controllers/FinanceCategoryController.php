<?php

class person_FinanceCategoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapter_finance_category;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_finance_category = new Application_Model_DBTable_FinanceCategory();
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

    public function addFinanceCategoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $name = isset($params['finance_category_name']) ? trim($params['finance_category_name']) : '';
                $parent_id = isset($params['finance_category_parent_id']) ? intval($params['finance_category_parent_id']) : 0;
                $weight = isset($params['finance_category_weight'])
                    ? intval($params['finance_category_weight']) : Bill_Constant::DEFAULT_WEIGHT;
                $add_time = date('Y-m-d H:i:s');

                if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, 0)) {
                    $data = [
                        'name' => $name,
                        'parent_id' => $parent_id,
                        'weight' => $weight,
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => $add_time,
                        'update_time' => $add_time
                    ];
                    $affected_rows = $this->_adapter_finance_category->insert($data);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addFinanceCategory');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function modifyFinanceCategoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $fcid = isset($params['finance_category_fcid']) ? intval($params['finance_category_fcid']) : 0;
                $name = isset($params['finance_category_name']) ? trim($params['finance_category_name']) : '';
                $parent_id = isset($params['finance_category_parent_id']) ? intval($params['finance_category_parent_id']) : 0;
                $weight = isset($params['finance_category_weight'])
                    ? intval($params['finance_category_weight']) : Bill_Constant::DEFAULT_WEIGHT;

                if (!$this->_adapter_finance_category->isFinanceCategoryExist($name, $fcid)) {
                    $data = [
                        'name' => $name,
                        'parent_id' => $parent_id,
                        'weight' => $weight,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = $this->_adapter_finance_category->getAdapter()->quoteInto('fcid=?', $fcid);
                    $affected_rows = $this->_adapter_finance_category->update($data, $where);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyFinanceCategory');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($json_array);
    }
    
    public function deleteFinanceCategoryAction()
    {
        $json_array = [];
        if ($this->getRequest()->isPost()) {
            $adapter_finance_payment_map = new Application_Model_DBTable_FinancePaymentMap();
            try {
                $params = $this->getRequest()->getPost('params', []);
                $fcid = isset($params['fcid']) ? intval($params['fcid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $isPaymentExistUnderCategory = $adapter_finance_payment_map->isPaymentExistUnderFcid($fcid);
                if (!$isPaymentExistUnderCategory) {
                    $update_data = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = [
                        $this->_adapter_finance_category->getAdapter()->quoteInto('(fcid=? or parent_id=?)', $fcid),
                        $this->_adapter_finance_category->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affected_rows = $this->_adapter_finance_category->update($update_data, $where);
                    $json_array = [
                        'data' => [
                            'code' => $affected_rows,
                            'message' => ($affected_rows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                } else {
                    $json_array = [
                        'data' => [
                            'code' => 0,
                            'message' => Bill_JsMessage::PAYMENT_EXIST_UNDER_CATEGORY,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteFinanceCategory');
            }
        }

        if (!isset($json_array['data'])) {
            $json_array = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($json_array);
    }
    
    public function getFinanceCategoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $fcid = (isset($params['fcid'])) ? intval($params['fcid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapter_finance_category->getFinanceCategoryByID($fcid);
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

    public function getFinanceSubcategoryAction()
    {
        $json_array = [];
        if (isset($_GET['parent_id'])) {
            $params = $this->_getParam('params', []);
            $parent_id = isset($params['parent_id']) ? intval($params['parent_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $subcategory_data = $this->_adapter_finance_category->getFinanceSubcategory($parent_id);
            if (!empty($data)) {
                $json_array = [
                    'data' => [
                        'currentItemCount' => count($subcategory_data),
                        'items' => $subcategory_data,
                    ],
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

    public function getFinanceMainCategoryAction()
    {
        $data = $this->_adapter_finance_category->getAllParentCategory();
        $json_data = [
            'data' => [
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        echo json_encode($json_data);
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($current_page, $page_length, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' !== $keyword) {
            $conditions['name like ?'] = Bill_Util::getLikeString($keyword);
        }
        $order_by = 'weight desc';
        $total = $this->_adapter_finance_category->getFinanceCategoryCount($conditions);
        $data = $this->_adapter_finance_category->getFinanceCategoryData($conditions, $current_page, $page_length, $order_by);
        foreach ($data as &$value) {
            $value['parent'] = $value['fc_parent_id'] == 0 ?
                '无' : $this->_adapter_finance_category->getParentCategoryName($value['fc_parent_id']);
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
