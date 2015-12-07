<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        try {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', null, true);
            Zend_Registry::set('config', $config);
        } catch (Exception $e) {
            echo $e->getMessage();
            die('Init config failed.');
        }
    }

    protected function _initLocalDB()
    {
        try {
            $config = Zend_Registry::get('config');
            $db_adapter = Zend_Db::factory($config->localdb->adapter, $config->localdb->toArray());
            Zend_Registry::set('localdb', $db_adapter);
        } catch (Exception $e) {
            echo $e->getMessage();
            die('Init local db failed.');
        }
    }

    protected function _initAlphaDB()
    {
        //init alpha db like local
    }

    protected function _initReleaseDB()
    {
        //init release db like local
    }

    protected function _initGetopt()
    {
        if(php_sapi_name() === 'cli' || defined('STDIN')) {
            $opts = new Zend_Console_Getopt(
                array(
                    'index|i' => 'index',
                    'transferPaymentCategory|t' => 'transfer payment category',
                    'updateLog|u' => 'update log',
                    'createUser|c' => 'create user',
                )
            );
            if(isset($opts->index)) {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'index',
                    )
                );
            }
            if (isset($opts->transferPaymentCategory)) {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'transfer-payment-category',
                    )
                );
            }
            if (isset($opts->updateLog)) {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'update-log',
                    )
                );
            }
            if (isset($opts->createUser)) {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'create-user',
                    )
                );
            }

            if(isset($route)) {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $router->addRoute('', $route);
            }
        }
    }

    protected function _initError()
    {
        set_error_handler('errorHandler');
        register_shutdown_function('shutdownFunction');
    }
}

function errorHandler($error_number, $error_message, $filename, $line_number, $vars)
{
    if ($error_number == E_NOTICE || $error_number == E_WARNING || $error_number == E_STRICT) {
        return;
    }
    $title = 'Error from errorHandler';
    $content = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n"
        . 'Error number: ' . $error_number . "\n"
        . 'Error message: ' . $error_message . "\n"
        . 'Filename: ' . $filename . "\n"
        . 'Line number: ' . $line_number . "\n"
        . 'Vars: ' . $vars;
    Bill_Mail::send($title, $content);
}

function shutDownFunction()
{
    $error = error_get_last();
    if (!empty($error)) {
        errorHandler($error['type'], $error['message'], $error['file'], $error['line'], '');
    }
}