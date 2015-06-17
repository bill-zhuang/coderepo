<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initGetopt()
    {
        if(php_sapi_name() === 'cli' || defined('STDIN'))
        {
            $opts = new Zend_Console_Getopt(
                array(
                    'index|i' => 'index',
                    'transferPaymentCategory|t' => 'transfer payment category'
                )
            );
            if(isset($opts->index))
            {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'index',
                    )
                );
            }
            if (isset($opts->transfer))
            {
                $route = new Zend_Controller_Router_Route_Hostname(
                    '',
                    array(
                        'module' => 'person',
                        'controller' => 'console',
                        'action' => 'transfer-payment-category',
                    )
                );
            }

            if(isset($route))
            {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $router->addRoute('', $route);
            }
        }
    }

}

