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
        $this->_helper->layout->setLayout('layout');
        /* Initialize action controller here */
        $this->_auth = new Application_Model_Auth();
        $this->_adapter_backend_user = new Application_Model_DBTable_BackendUser();
    }
    public function indexAction()
    {
        
    }
    
    public function modifypasswordAction()
    {
        if ($_POST)
        {
            $user_name = Application_Model_Auth::getIdentity()->bu_name;
            $old_password = addslashes($_POST['old_password']);
            $new_password = addslashes($_POST['new_password']);
            $user_info = $this->_adapter_backend_user->getUserInfo($user_name);
            if (isset($user_info['bu_password']))
            {
                if ($user_info['bu_password'] !== md5($old_password))
                {
                    $this->view->content = '原密码错误！';
                }
                else 
                {
                    $where = $this->_adapter_backend_user->getAdapter()->quoteInto('bu_name = ?', $user_name);
                    $update_data = [
                        'bu_password' => md5($new_password),
                        'bu_update_time' => date('Y-m-d H:i:s')
                    ];
                    $affect_rows = $this->_adapter_backend_user->update($update_data, $where);
                    if ($affect_rows > 0)
                    {
                        $this->_auth->logIn($user_name, $new_password, 'backend_user',
                            $this->_adapter_backend_user->getAdapter());
                        $this->view->content = '修改成功！';
                    }
                    else 
                    {
                        $this->view->content = '修改失败！';
                    }
                }
                
                $this->view->reurl = '/main/index';
                $this->render('result', null, true);
            }
        }
    }
    
}

