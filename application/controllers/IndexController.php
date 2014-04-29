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
    	if($_GET && $_GET['downlink'])
    	{
    	    $match_count = preg_match(Bill_Regex::BAIDU_MUSIC_DOWNLOAD_LINK, $_GET['downlink'], $match_id);
    	    if($match_count)
    	    {
    	        $real_downloadlink = 'http://music.baidu.com/data/music/file?link=&song_id=' . $match_id[1];
    	        echo json_encode($real_downloadlink);
    	        exit;
    	    }
    	}
    	
	    echo json_encode('');
	    exit;
    }
    
    public function encodeChineseCharacterInUrl($url)
    {
        return preg_replace_callback(Bill_Regex::CHINESE_CHARACTER, function($matches){return urlencode($matches[0]); }, trim($url));
    }
}

