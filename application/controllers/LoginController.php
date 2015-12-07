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
        $this->_helper->layout->setLayout('layoutindex');
        $role = Application_Model_Auth::isValid();
        if ($role != null) {
            $this->redirect('/main/index');
        }
    }
    
    public function loginAction()
    {
        $user_name = addslashes($_POST['username']);
        $user_password = addslashes($_POST['password']);
        $database = new Application_Model_DBTable_BackendUser();
        $this->_auth->logIn($user_name, $user_password, 'backend_user', $database->getAdapter());

        if (Application_Model_Auth::isValid() != null) {
            $this->redirect('/main/index');
        } else {
            $this->view->ret = 0;
        }
        
        $this->view->name = $user_name;
    }
    
    
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        
        Application_Model_Auth::logOut();
        $this->redirect('/login/index');
    }
}

