<?php

class person_ConsoleController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
        //go int public/ directory & run command: php index.php -i
        $opts = new Zend_Console_Getopt('i');
        $args = $opts->getRemainingArgs();
        echo 'console success';
        exit;
    }
}