<?php

class crawler_ToutiaoArticleController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_Toutiao
     */
    private $_adapterToutiao;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterToutiao = new Application_Model_DBTable_Toutiao();
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

    public function modifyToutiaoArticleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ttid = intval($params['toutiao_article_ttid']);
                if ($ttid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $data = [
                        'date' => date('Y-m-d'),
                        'title' => trim($params['toutiao_article_title']),
                        'url' => trim($params['toutiao_article_url']),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = $this->_adapterToutiao->getAdapter()->quoteInto('ttid=?', $ttid);
                    $affectedRows = $this->_adapterToutiao->update($data, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From modifyToutiaoArticle');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }
        
        echo json_encode($jsonArray);
    }
    
    public function deleteToutiaoArticleAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getRequest()->getPost('params', []);
                $ttid = isset($params['ttid']) ? intval($params['ttid']) : Bill_Constant::INVALID_PRIMARY_ID;
                if ($ttid > Bill_Constant::INVALID_PRIMARY_ID) {
                    $updateData = [
                        'status' => Bill_Constant::INVALID_STATUS,
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $where = [
                        $this->_adapterToutiao->getAdapter()->quoteInto('ttid=?', $ttid),
                        $this->_adapterToutiao->getAdapter()->quoteInto('status=?', Bill_Constant::VALID_STATUS),
                    ];
                    $affectedRows = $this->_adapterToutiao->update($updateData, $where);
                    $jsonArray = [
                        'data' => [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::DELETE_SUCCESS : Bill_JsMessage::DELETE_FAIL,
                        ]
                    ];
                }
            } catch (Exception $e) {
                Bill_Util::handleException($e, 'Error From deleteToutiaoArticle');
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
    
    public function getToutiaoArticleAction()
    {
        if ($this->getRequest()->isGet()) {
            $params = $this->getRequest()->getQuery('params', []);
            $ttid = (isset($params['ttid'])) ? intval($params['ttid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $data = $this->_adapterToutiao->getByPrimaryKey($ttid);
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

    private function _index()
    {
        $params = $this->_getParam('params', []);
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $keyword = isset($params['keyword']) ? trim($params['keyword']) : '';
        $date = isset($params['date']) ? trim($params['date']) : '';

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($keyword !== '') {
            $conditions['title like ?'] = Bill_Util::getLikeString($keyword);
        }
        if ('' != $date) {
            $conditions['date =?'] = $date;
        }
        $order_by = ['date DESC', 'ttid ASC'];
        $total = $this->_adapterToutiao->getSearchCount($conditions);
        $data = $this->_adapterToutiao->getSearchData($conditions, $currentPage, $pageLength, $order_by);

        $json_data = [
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
        return $json_data;
    }
    
}
