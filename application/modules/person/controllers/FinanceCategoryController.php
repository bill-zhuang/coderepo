<?php

class person_FinanceCategoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_FinanceCategory
     */
    private $_adapterFinanceCategory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterFinanceCategory = new Application_Model_DBTable_FinanceCategory();
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

    public function addFinanceCategoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $name = isset($params['finance_category_name']) ? trim($params['finance_category_name']) : '';
                $parentId = isset($params['finance_category_parent_id']) ? intval($params['finance_category_parent_id']) : 0;
                $weight = isset($params['finance_category_weight'])
                    ? intval($params['finance_category_weight']) : Bill_Constant::DEFAULT_WEIGHT;
                $addTime = date('Y-m-d H:i:s');

                if (!$this->_adapterFinanceCategory->isFinanceCategoryExist($name, 0)) {
                    $data = [
                        'name' => $name,
                        'parent_id' => $parentId,
                        'weight' => $weight,
                        'status' => Bill_Constant::VALID_STATUS,
                        'create_time' => $addTime,
                        'update_time' => $addTime
                    ];
                    $affectedRows = $this->_adapterFinanceCategory->insert($data);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::ADD_SUCCESS : Bill_JsMessage::ADD_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From addFinanceCategory');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function modifyFinanceCategoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $fcid = isset($params['finance_category_fcid']) ? intval($params['finance_category_fcid']) : 0;
                $name = isset($params['finance_category_name']) ? trim($params['finance_category_name']) : '';
                $parentId = isset($params['finance_category_parent_id']) ? intval($params['finance_category_parent_id']) : 0;
                $weight = isset($params['finance_category_weight'])
                    ? intval($params['finance_category_weight']) : Bill_Constant::DEFAULT_WEIGHT;

                if (!$this->_adapterFinanceCategory->isFinanceCategoryExist($name, $fcid)) {
                    $data = [
                        'name' => $name,
                        'parent_id' => $parentId,
                        'weight' => $weight,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = $this->_adapterFinanceCategory->getAdapter()->quoteInto('fcid=?', $fcid);
                    $affectedRows = $this->_adapterFinanceCategory->update($data, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ],
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyFinanceCategory');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteFinanceCategoryAction()
    {
        $jsonArray = [];
        if ($this->getRequest()->isPost()) {
            $adapterFinancePaymentMap = new Application_Model_DBTable_FinancePaymentMap();
            try {
                $params = $this->getRequest()->getPost('params', []);
                $fcid = isset($params['fcid']) ? intval($params['fcid']) : Bill_Constant::INVALID_PRIMARY_ID;
                $isPaymentExistUnderCategory = $adapterFinancePaymentMap->isPaymentExistUnderFcid($fcid);
                if (!$isPaymentExistUnderCategory) {
                    $updateData = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $where = [
                        $this->_adapterFinanceCategory->getAdapter()->quoteInto('(fcid=? or parent_id=?)', $fcid),
                        $this->_adapterFinanceCategory->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affectedRows = $this->_adapterFinanceCategory->update($updateData, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                } else {
                    $jsonArray = [
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

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getFinanceCategoryAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $fcid = (isset($params['fcid'])) ? intval($params['fcid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterFinanceCategory->getByPrimaryKey($fcid);
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

    public function getFinanceSubcategoryAction()
    {
        $jsonArray = [];
        if (isset($_GET['params'])) {
            $params = $this->_getParam('params', []);
            $parentId = isset($params['parent_id']) ? intval($params['parent_id']) : Bill_Constant::INVALID_PRIMARY_ID;
            $subcategoryData = $this->_adapterFinanceCategory->getFinanceSubcategory($parentId);
            if (!empty($subcategoryData)) {
                $jsonArray = [
                    'data' => [
                        'currentItemCount' => count($subcategoryData),
                        'items' => $subcategoryData,
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

    public function getFinanceMainCategoryAction()
    {
        $data = $this->_adapterFinanceCategory->getAllParentCategory();
        $jsonData = [
            'data' => [
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        echo json_encode($jsonData);
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ('' !== $keyword) {
            $conditions['name like ?'] = Bill_Util::getLikeString($keyword);
        }
        $orderBy = 'weight desc';
        $total = $this->_adapterFinanceCategory->getSearchCount($conditions);
        $data = $this->_adapterFinanceCategory->getSearchData($conditions, $currentPage, $pageLength, $orderBy);
        foreach ($data as &$value) {
            $value['parent'] = $value['parent_id'] == 0 ?
                '无' : $this->_adapterFinanceCategory->getParentCategoryName($value['parent_id']);
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
