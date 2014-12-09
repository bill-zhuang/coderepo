<?php
class DemoController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layoutmain');
    }

    public function indexAction()
    {
        // action body
        $keyword = trim($this->_getParam('keyword', ''));
        $current_page = intval($this->_getParam('current_page', 1));
        $page_length = intval($this->_getParam('page_length', 25));
        $data = [
            ['Misc', 'NetFront 3.4', 'Embedded devices', '-', 'A'],
            ['Gecko', 'Epiphany 2.20', 'Gnome', '1.8', 'A'],
            ['Webkit', 'iPod Touch / iPhone', 'iPod', '420.1', 'A'],
            ['KHTML', 'Konqureror 3.3', 'KDE 3.3', '3.3', 'A'],
            ['KHTML', 'Konqureror 3.5', 'KDE 3.5', '3.5', 'A'],
            ['Presto', 'Nokia N800', 'N800', '-', 'A'],
            ['Gecko', 'Camino 1.0', 'OSX.2+', '1.8', 'A'],
            ['Webkit', 'Safari 1.2', 'OSX.3', '125.5', 'A'],
            ['Webkit', 'Safari 1.3', 'OSX.3', '312.8', 'A'],
            ['Gecko', 'Camino 1.5', 'OSX.3+', '1.8', 'A']
        ];
        $total = count($data);

        $this->view->data = $data;
        $this->view->current_page = $current_page;
        $this->view->page_length = $page_length;
        $this->view->total_pages = ceil($total / $page_length) ? ceil($total / $page_length) : 1;
        $this->view->total = $total;
        $this->view->keyword = $keyword;
    }

}
