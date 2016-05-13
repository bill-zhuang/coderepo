<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function getBaiduMusicLinkAction()
    {
        $jsonArray = [];
        $params = $this->_getParam('params', []);
        if (isset($params['downloadLink'])) {
            $downloadLink = trim($params['downloadLink']);
            if($downloadLink !== '') {
                $matchCount = preg_match(Bill_Regex::BAIDU_MUSIC_DOWNLOAD_LINK, $downloadLink, $matchId);
                if($matchCount) {
                    $realDownloadLink = 'http://music.baidu.com/data/music/file?link=&song_id=' . $matchId[1];
                    $headerInfo = Bill_Curl::getResponseHeaders($realDownloadLink);
                    if ($headerInfo['http_code'] == 302) {
                        $jsonArray['data'] = [
                            'downloadUrl' => $headerInfo['redirect_url'] . '&song_id=' . $matchId[1]
                        ];
                    } else {
                        $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Fail to get baidu download music url.');
                    }
                } else {
                    $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink is invalid.');
                }
            } else {
                $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink not empty.');
            }
        } else {
            $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray(200, 'Request param downloadLink not set.');
        }

	    echo json_encode($jsonArray);
    }
}

