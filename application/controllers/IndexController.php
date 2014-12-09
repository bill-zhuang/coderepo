<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layoutmain');
    }

    public function indexAction()
    {
        // action body
        
    }

    public function downloadbaidumusicAction()
    {
        $real_downloadlink = '';
        $download_link = trim($this->_getParam('downlink', ''));
    	if($download_link !== '')
    	{
    	    $match_count = preg_match(Bill_Regex::BAIDU_MUSIC_DOWNLOAD_LINK, $download_link, $match_id);
    	    if($match_count)
    	    {
    	        $real_downloadlink = 'http://music.baidu.com/data/music/file?link=&song_id=' . $match_id[1];
    	        $bill_curl = new Bill_Curl($real_downloadlink);
    	        $header_info = $bill_curl->getResponseHeaders();
    	        if ($header_info['http_code'] == 302)
    	        {
    	            $real_downloadlink = $header_info['redirect_url'] . '&song_id=' . $match_id[1];
    	        }
    	    }
    	}
    	
	    echo json_encode($real_downloadlink);
	    exit;
    }
}

