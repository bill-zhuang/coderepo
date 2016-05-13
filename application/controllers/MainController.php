<?php

class MainController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_Auth
     */
    private $_auth;
    /**
     * @var Application_Model_DBTable_BackendUser
     */
	private $_adapterBackendUser;
    
    public function init ()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_auth = new Application_Model_Auth();
        $this->_adapterBackendUser = new Application_Model_DBTable_BackendUser();
    }
    public function indexAction()
    {
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }
    
    public function modifyPasswordAction()
    {
        if ($this->getRequest()->isPost()) {
            $jsonArray = [];
            $userName = Application_Model_Auth::getIdentity()->name;
            $salt = Application_Model_Auth::getIdentity()->salt;
            $params = $this->getRequest()->getPost('params', []);
            $oldPassword = isset($params['old_password']) ? addslashes($params['old_password']) : '';
            $newPassword = isset($params['new_password']) ? addslashes($params['new_password']) : '';
            $userInfo = $this->_adapterBackendUser->getUserInfo($userName);
            if (isset($userInfo['password'])) {
                if ($userInfo['password'] !== md5($oldPassword . $salt)) {
                    $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, '原密码错误!');
                } else {
                    $security = new Bill_Security();
                    $newSalt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
                    $where = $this->_adapterBackendUser->getAdapter()->quoteInto('name = ?', $userName);
                    $updateData = [
                        'password' => md5($newPassword . $newSalt),
                        'salt' => $newSalt,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $affectedRows = $this->_adapterBackendUser->update($updateData, $where);
                    if ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS) {
                        $this->_auth->logIn($userName, $newPassword, 'backend_user',
                            $this->_adapterBackendUser->getAdapter());
                        $jsonArray['data'] = [
                            'code' => $affectedRows,
                            'message' => ($affectedRows > Bill_Constant::INIT_AFFECTED_ROWS)
                                    ? Bill_JsMessage::MODIFY_SUCCESS : Bill_JsMessage::MODIFY_FAIL,
                        ];
                    } else {
                        $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, '修改失败!');
                    }
                }
            }

            if (!isset($jsonArray['data']) && !isset($jsonArray['error'])) {
                $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO);
            }

            echo json_encode($jsonArray);
        } else {
            $this->_helper->layout()->enableLayout();
            $this->_helper->viewRenderer->setNoRender(false);
        }
    }

}

