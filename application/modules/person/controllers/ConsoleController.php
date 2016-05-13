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
        $userName = Bill_Constant::ADMIN_NAME;
        $adapterBackendRole = new Application_Model_DBTable_BackendRole();
        $adapterBackendUser = new Application_Model_DBTable_BackendUser();
        if (!$adapterBackendUser->isUserNameExist($userName, Bill_Constant::INVALID_PRIMARY_ID)) {
            $insertData = [
                'role' => 'admin',
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
            $brid = $adapterBackendRole->insert($insertData);
            $security = new Bill_Security();
            $salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
            $insertData = [
                'name' => $userName,
                'password' => md5(Bill_Constant::DEFAULT_PASSWORD . $salt),
                'salt' => $salt,
                'brid' => $brid,
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
            $adapterBackendUser->insert($insertData);
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
        $adapterEjectHistory = new Application_Model_DBTable_EjectHistory();
        $data = $adapterEjectHistory->getAdapter()->query($sql)->fetchAll();
        foreach ($data as $item) {
            try {
                $adapterEjectHistory->insert($item);
            } catch (Exception $e) {
                ;
            }
        }
    }
}