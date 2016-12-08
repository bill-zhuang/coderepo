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
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_auth = new Application_Model_Auth();
    }
    public function indexAction()
    {
        // action body
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout->setLayout('layout-login');
        $this->getResponse()->setHeader('Content-Type', 'text/html');
        if (Application_Model_Auth::isValid()) {
            $this->redirect('/main/index');
        }
    }
    
    public function loginAction()
    {
        $jsonArray = [];
        $params = $this->getRequest()->getPost('params', []);
        $userName = isset($params['username']) ? addslashes($params['username']) : '';
        $userPassword = isset($params['password']) ? addslashes($params['password']) : '';
        $database = new Application_Model_DBTable_BackendUser();
        $this->_auth->logIn($userName, $userPassword, 'backend_user', $database->getAdapter());

        if (Application_Model_Auth::isValid()) {
            $redirectUrl = isset($params['location']) ? urldecode($params['location']) : '';
            if ($redirectUrl === '' || $redirectUrl === '/') {
                $redirectUrl = '/main/index';
            }
            $jsonArray['data'] = [
                'redirectUrl' => $redirectUrl,
            ];
        } else {
            $jsonArray['error'] = [
                'message' => Bill_JsMessage::ACCOUNT_PASSWORD_ERROR,
            ];
        }

        echo json_encode($jsonArray);
    }
    
    
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        
        Application_Model_Auth::logOut();
        $this->redirect('/login/index');
    }
}

