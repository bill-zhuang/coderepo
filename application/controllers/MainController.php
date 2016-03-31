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
	private $_adapter_backend_user;
    
    public function init ()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_auth = new Application_Model_Auth();
        $this->_adapter_backend_user = new Application_Model_DBTable_BackendUser();
    }
    public function indexAction()
    {
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }
    
    public function modifyPasswordAction()
    {
        if ($this->getRequest()->isPost()) {
            $json_array = [];
            $user_name = Application_Model_Auth::getIdentity()->name;
            $salt = Application_Model_Auth::getIdentity()->salt;
            $params = $this->getRequest()->getPost('params', []);
            $old_password = isset($params['old_password']) ? addslashes($params['old_password']) : '';
            $new_password = isset($params['new_password']) ? addslashes($params['new_password']) : '';
            $user_info = $this->_adapter_backend_user->getUserInfo($user_name);
            if (isset($user_info['password'])) {
                if ($user_info['password'] !== md5($old_password . $salt)) {
                    $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, '原密码错误!');
                } else {
                    $security = new Bill_Security();
                    $new_salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
                    $where = $this->_adapter_backend_user->getAdapter()->quoteInto('name = ?', $user_name);
                    $update_data = [
                        'password' => md5($new_password . $new_salt),
                        'salt' => $new_salt,
                        'update_time' => date('Y-m-d H:i:s')
                    ];
                    $affect_rows = $this->_adapter_backend_user->update($update_data, $where);
                    if ($affect_rows > Bill_Constant::INIT_AFFECTED_ROWS) {
                        $this->_auth->logIn($user_name, $new_password, 'backend_user',
                            $this->_adapter_backend_user->getAdapter());
                        $json_array['data'] = [
                            'affectedRows' => $affect_rows,
                        ];
                    } else {
                        $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, '修改失败!');
                    }
                }
            }

            if (!isset($json_array['data']) && !isset($json_array['error'])) {
                $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO);
            }

            echo json_encode($json_array);
        } else {
            $this->_helper->layout()->enableLayout();
            $this->_helper->viewRenderer->setNoRender(false);
        }
    }

}

