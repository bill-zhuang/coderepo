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

    protected function _initDB()
    {
        $dbList = [
            'localdb' => Bill_Constant::LOCAL_DB,
            'alphadb' => Bill_Constant::ALPHA_DB,
            'releasedb' => Bill_Constant::RELEASE_DB,
        ];
        foreach ($dbList as $dbName => $dbConstant) {
            try {
                $config = Zend_Registry::get('config');
                $dbAdapter = Zend_Db::factory($config->$dbName->adapter, $config->$dbName->toArray());
                if ($config->$dbName->profiler) {
                    $dbAdapter->setProfiler($this->_getDbProfileFirebug());
                }
                Zend_Registry::set($dbConstant, $dbAdapter);
            } catch (Exception $e) {
                echo $e->getMessage();
                die('Init ' . $dbName . ' failed.');
            }
        }
    }

    protected function _initGetopt()
    {
        if(Bill_Util::isCommandLineInterface() || defined('STDIN')) {
            $opts = new Zend_Console_Getopt(
                array(
                    'index|i' => 'index',
                    'createUser|c' => 'create user',
                    'transferDreamBadData|t' => 'transfer dream & bad data to eject',
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
            if (isset($opts->transferDreamBadData)) {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'transfer-dream-bad-data-to-eject',
                    )
                );
            }

            if(isset($route)) {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $router->addRoute('', $route);
            }
        }
    }

    /*protected function _initSentry()
    {
        $client = new Raven_Client('https://4e711ef3119f491ba0dd9a27e813eb0c:4bf3e159d9b744809cc4de2d20bb3728@sentry.io/218067');
        $error_handler = new Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
    }*/

    protected function _initError()
    {
        set_error_handler('errorHandler');
        register_shutdown_function('shutdownFunction');
    }

    private function _getDbProfileFirebug()
    {
        $profiler = new Zend_Db_Profiler_Firebug('All Database Queries:');
        $profiler->setEnabled(true);
        return $profiler;
    }
}

function errorHandler($errorNumber, $errorMessage, $filename, $lineNumber, $vars)
{
    if ($errorNumber == E_NOTICE || $errorNumber == E_WARNING || $errorNumber == E_STRICT) {
        return;
    }
    $title = 'Error from errorHandler';
    $content = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n"
        . 'Error number: ' . $errorNumber . "\n"
        . 'Error message: ' . $errorMessage . "\n"
        . 'Filename: ' . $filename . "\n"
        . 'Line number: ' . $lineNumber . "\n"
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