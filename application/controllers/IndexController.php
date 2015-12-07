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
        $json_array = [];
        $params = $this->_getParam('params', []);
        if (isset($params['downloadLink'])) {
            $download_link = trim($params['downloadLink']);
            if($download_link !== '') {
                $match_count = preg_match(Bill_Regex::BAIDU_MUSIC_DOWNLOAD_LINK, $download_link, $match_id);
                if($match_count) {
                    $real_download_link = 'http://music.baidu.com/data/music/file?link=&song_id=' . $match_id[1];
                    $header_info = Bill_Curl::getResponseHeaders($real_download_link);
                    if ($header_info['http_code'] == 302) {
                        $json_array['data'] = [
                            'downloadUrl' => $header_info['redirect_url'] . '&song_id=' . $match_id[1]
                        ];
                    } else {
                        $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Fail to get baidu download music url.');
                    }
                } else {
                    $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink is invalid.');
                }
            } else {
                $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink not empty.');
            }
        } else {
            $json_array['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink not set.');
        }

	    echo json_encode($json_array);
	    exit;
    }
}

