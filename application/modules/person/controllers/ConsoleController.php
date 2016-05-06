<?php

class person_ConsoleController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //only command line interface available
        if(!Bill_Util::isCommandLineInterface()) {
            exit;
        }
    }

    public function indexAction()
    {
        //go int public/ directory & run command: php index.php -i
        $opts = new Zend_Console_Getopt('i');
        $args = $opts->getRemainingArgs();
        echo 'console success';
    }

    public function createUserAction()
    {
        $user_name = Bill_Constant::ADMIN_NAME;
        $adapter_backend_role = new Application_Model_DBTable_BackendRole();
        $adapter_backend_user = new Application_Model_DBTable_BackendUser();
        if (!$adapter_backend_user->isUserNameExist($user_name, Bill_Constant::INVALID_PRIMARY_ID)) {
            $insert_data = [
                'role' => 'admin',
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
            $brid = $adapter_backend_role->insert($insert_data);
            $security = new Bill_Security();
            $salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
            $insert_data = [
                'name' => $user_name,
                'password' => md5(Bill_Constant::DEFAULT_PASSWORD . $salt),
                'salt' => $salt,
                'brid' => $brid,
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
            $adapter_backend_user->insert($insert_data);
            echo 'User create successfully.';
        } else {
            echo 'User name already exist, change another one.';
        }
    }

    public function transferDreamBadDataToEjectAction()
    {
        $sql =  'SELECT 1 AS type, d.happen_date, d.count, d.`status`, d.create_time, d.update_time FROM dream_history AS d'
            . ' UNION ALL'
            . ' SELECT 2 AS type, b.happen_date, b.count, b.`status`, b.create_time, b.update_time FROM bad_history AS b'
            . ' ORDER BY happen_date ASC';
        $adapter_eject_history = new Application_Model_DBTable_EjectHistory();
        $data = $adapter_eject_history->getAdapter()->query($sql)->fetchAll();
        foreach ($data as $item) {
            try {
                $adapter_eject_history->insert($item);
            } catch (Exception $e) {
                ;
            }
        }
    }
}