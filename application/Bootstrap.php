<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initGetopt()
    {
        if(is_null($_SERVER['HTTP_HOST']))
        {
            $opts = new Zend_Console_Getopt(
                array(
                    'index|i' => 'index',
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

            if(isset($route))
            {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $router->addRoute('', $route);
            }
        }
    }
}

