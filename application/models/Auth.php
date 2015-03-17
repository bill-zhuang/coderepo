<?php
define("G_SESSIONTIMEOUT",3600);
class Application_Model_Auth
{
    protected $authAdapter;

    protected function setAuth($table_name, $column_name, $column_password, $database_adapter)
    {
        $this->authAdapter = new Zend_Auth_Adapter_DbTable($database_adapter, $table_name, $column_name, $column_password,
                'MD5(?) and bu_status=1');
    }

    public function logIn($username, $password, $table_name = null, $database_adapter = null)
    {
        if ($table_name == null)
        {
            $table_name = 'backend_user';
        }
        if ($database_adapter == null)
        {
            $database = new Application_Model_DbTable_Backenduser();
            $database_adapter = $database->getAdapter();
        }
        $this->setauth($table_name, 'bu_name', 'bu_password', $database_adapter);
        $this->authAdapter->setIdentity($username);
        $this->authAdapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));
        $user_info = new Zend_Session_Namespace('user_info');
        $user_info->setExpirationSeconds(G_SESSIONTIMEOUT);
        $result = $auth->authenticate($this->authAdapter);

        if($result->isValid())
        {
            $storage = $auth->getStorage();
            $storage->write($this->authAdapter->getResultRowObject());
        }
    }

    public static function logOut()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));
        $auth->clearIdentity();
    }

    public static function getIdentity()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));

        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            return $identity;
        }
        else
        {
            return null;
        }
    }

    public static function isValid()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));

        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $role = $identity->bu_role; //bu_role field in table backend_user
            $user_info = new Zend_Session_Namespace('user_info');
            $user_info->setExpirationSeconds(G_SESSIONTIMEOUT);
            return $role;
        }
        else
        {
            return null;
        }
    }

    public static function getClientIp()
    {
        if ($_SERVER['REMOTE_ADDR'])
        {
            $cip = $_SERVER['REMOTE_ADDR'];
        }
        elseif (getenv("REMOTE_ADDR"))
        {
            $cip = getenv("REMOTE_ADDR");
        }
        elseif (getenv("HTTP_CLIENT_IP"))
        {
            $cip = getenv("HTTP_CLIENT_IP");
        }
        else
        {
            $cip = "unknown";
        }
        return $cip;
    }
}