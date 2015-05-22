<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    const INIT_START_PAGE = 1;
    const INIT_PAGE_LENGTH = 25;
    const INIT_TOTAL_PAGE = 1;

    const VALID_STATUS = 1;
    const INVALID_STATUS = 0;

    const INIT_AFFECTED_ROWS = 0;

    const INVALID_PRIMARY_ID = 0;

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

