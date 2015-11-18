<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
    }

    public function indexAction()
    {
        // action body
        
    }

    public function getBaiduMusicLinkAction()
    {
        $real_download_link = '';
        $download_link = trim($this->_getParam('download_link', ''));
    	if($download_link !== '')
    	{
    	    $match_count = preg_match(Bill_Regex::BAIDU_MUSIC_DOWNLOAD_LINK, $download_link, $match_id);
    	    if($match_count)
    	    {
                $real_download_link = 'http://music.baidu.com/data/music/file?link=&song_id=' . $match_id[1];
    	        $bill_curl = new Bill_Curl($real_download_link);
    	        $header_info = $bill_curl->getResponseHeaders();
    	        if ($header_info['http_code'] == 302)
    	        {
                    $real_download_link = $header_info['redirect_url'] . '&song_id=' . $match_id[1];
    	        }
    	    }
    	}
    	
	    echo json_encode($real_download_link);
	    exit;
    }
}

