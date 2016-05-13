<?php
define("G_SESSIONTIMEOUT",3600);
class Application_Model_Auth
{
    protected $authAdapter;

    protected function setAuth($tableName, $columnName, $columnPassword, $databaseAdapter)
    {
        $this->authAdapter = new Zend_Auth_Adapter_DbTable(
            $databaseAdapter,
            $tableName,
            $columnName,
            $columnPassword,
            'MD5(CONCAT(?, salt)) AND status=1'
        );
    }

    public function logIn($username, $password, $tableName = null, $databaseAdapter = null)
    {
        if ($tableName == null) {
            $tableName = 'backend_user';
        }
        if ($databaseAdapter == null) {
            $database = new Application_Model_DBTable_BackendUser();
            $databaseAdapter = $database->getAdapter();
        }
        $this->setauth($tableName, 'name', 'password', $databaseAdapter);
        $this->authAdapter->setIdentity($username);
        $this->authAdapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));
        $userInfo = new Zend_Session_Namespace('user_info');
        $userInfo->setExpirationSeconds(G_SESSIONTIMEOUT);
        $result = $auth->authenticate($this->authAdapter);

        if($result->isValid()) {
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

        if($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            return $identity;
        } else {
            return null;
        }
    }

    public static function isValid()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('user_info'));

        if($auth->hasIdentity()) {
            $userInfo = new Zend_Session_Namespace('user_info');
            $userInfo->setExpirationSeconds(G_SESSIONTIMEOUT);
            return true;
        } else {
            return false;
        }
    }
}