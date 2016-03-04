<?php

class LoginController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_Auth
     */
    private $_auth;
    
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_auth = new Application_Model_Auth();
    }
    public function indexAction()
    {
        // action body
        $this->_helper->layout->setLayout('layout-login');
        if (Application_Model_Auth::isValid()) {
            $this->redirect('/main/index');
        }
    }
    
    public function loginAction()
    {
        $json_array = [];
        $params = $this->getRequest()->getPost('params', []);
        $user_name = isset($params['username']) ? addslashes($params['username']) : '';
        $user_password = isset($params['password']) ? addslashes($params['password']) : '';
        $database = new Application_Model_DBTable_BackendUser();
        $this->_auth->logIn($user_name, $user_password, 'backend_user', $database->getAdapter());

        if (Application_Model_Auth::isValid()) {
            $json_array['data'] = [
                'redirectUrl' => '/main/index',
            ];
        } else {
            $json_array['error'] = [
                'message' => '用户名或密码错误!',
            ];
        }

        echo json_encode($json_array);
        exit;
    }
    
    
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        
        Application_Model_Auth::logOut();
        $this->redirect('/login/index');
    }
}

