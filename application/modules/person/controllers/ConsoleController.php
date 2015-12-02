<?php

class person_ConsoleController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        //go int public/ directory & run command: php index.php -i
        $opts = new Zend_Console_Getopt('i');
        $args = $opts->getRemainingArgs();
        echo 'console success';
    }

    public function transferPaymentCategoryAction()
    {
        $adapter_payment = new Application_Model_DBTable_FinancePayment();
        $adapter_payment_map = new Application_Model_DBTable_FinancePaymentMap();

        $payment_data = $adapter_payment->getAllPaymentDataForTransfer();
        foreach ($payment_data as $payment_value)
        {
            $map_data = [
                'fpid' => $payment_value['fpid'],
                'fcid' => $payment_value['fcid'],
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => $payment_value['create_time'],
                'update_time' => $payment_value['update_time']
            ];
            $adapter_payment_map->insert($map_data);
        }
    }

    public function updateLogAction()
    {
        $regex = '/^(\w+)\s+(\w+)\s+([^\(]+)/';
        $adapter_backend_log = new Application_Model_DBTable_BackendLog();
        $log_data = $adapter_backend_log->getAllBlidAndContent();
        foreach ($log_data as $log_value)
        {
            $match = preg_match($regex, $log_value['content'], $matches);
            if ($match)
            {
                $type = '';
                $table = '';
                switch($matches[1])
                {
                    case 'insert':
                    case 'delete':
                        $type = $matches[1];
                        $table = $matches[3];
                        break;
                    case 'update':
                        $type = $matches[1];
                        $table = $matches[2];
                        break;
                    default:
                        break;
                }

                if ($type !== '' && $table !== '')
                {
                    $update_data = [
                        'type' => $type,
                        'table' => $table,
                        'update_time' => $log_value['update_time'],
                    ];
                    $where = $adapter_backend_log->getAdapter()->quoteInto('blid=?', $log_value['blid']);
                    $adapter_backend_log->update($update_data, $where);
                }
            }
        }
    }

    public function createUserAction()
    {
        $opts = new Zend_Console_Getopt('c');
        $args = $opts->getRemainingArgs();
        $user_name = isset($args[0]) ? $args[0] : '';
        if (preg_match('/^[\da-zA-Z]+[\da-zA-Z_]*$/', $user_name))
        {
            $adapter_backend_user = new Application_Model_DBTable_BackendUser();
            if (!$adapter_backend_user->isUserNameExist($user_name, Bill_Constant::INVALID_PRIMARY_ID))
            {
                $security = new Bill_Security();
                $salt = $security->generateRandomString(Bill_Constant::SALT_STRING_LENGTH);
                $insert_data = [
                    'name' => $user_name,
                    'password' => md5(Bill_Constant::DEFAULT_PASSWORD . $salt),
                    'salt' => $salt,
                    'role' => Bill_Constant::DEFAULT_ROLE,
                    'status' => Bill_Constant::VALID_STATUS,
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s'),
                ];
                $adapter_backend_user->insert($insert_data);
                echo 'User create successfully.';
            }
            else
            {
                echo 'User name already exist, change another one.';
            }
        }
        else
        {
            echo 'Account name only accept letter a-z & A-Z & _, and _ not allowed at first letter.';
        }
    }
}