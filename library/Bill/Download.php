<?php

class Bill_Download
{
    /**
     * send request through get/post method.
     * @param string $url
     * @param array $postData
     * @param string $method
     * @return string
     */
    public function sendRequest($url, array $postData, $method = Bill_Constant::HTTP_METHOD_GET)
    {
        $postData = http_build_query($postData);

        $options = array(
            'http' => array(
                'method' => $method,
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 15 * 60, //second
            ),
        );

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    /**
     * Download file through file_get_contents.
     * @param string $url
     * @param string $savePath
     */
    public function fileGetContentDownload($url, $savePath)
    {
        $fileContent = file_get_contents($url);
        file_put_contents($savePath, $fileContent);
    }

    /**
     * Download file through wget.
     * @param string $url
     * @param string $savePath
     * @param string $commandPath wget command path
     */
    public function wgetDownload($url, $savePath, $commandPath = 'wget')
    {
        if (file_exists($commandPath)) {
            $cmd = $commandPath . $url . " -O " . $savePath;
            exec($cmd, $array, $ret);
        }
    }

    /**
     * download file through curl.
     * @param string $url
     * @param string $savePath
     */
    public function curlDownload($url, $savePath)
    {
        $fp = fopen($savePath, 'w+');

        if ($fp !== false) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);
        }
    }

    /**
     * Download file through multi-thread by curl.
     * @param array $filenameUrl
     * <p>key-filename, value-url.</p>
     * @param string $dir
     * <p>directory to save the files.</p>
     * @param integer $downloadNum [optional]
     * <p>download files at one time, default 100.</p>*/
    public function curlMultipleDownload(array $filenameUrl, $dir, $downloadNum = 100)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $mh = curl_multi_init();
        $urlChunks = array_chunk($filenameUrl, $downloadNum, true);

        foreach ($urlChunks as $urlSections) {
            $fp = [];
            $conn = [];
            foreach ($urlSections as $filename => $url) {
                if (!is_file($dir . $filename)) {
                    $conn[$filename] = curl_init($url);
                    $fp[$filename] = fopen($dir . $filename, "w+");

                    curl_setopt($conn[$filename], CURLOPT_FILE, $fp[$filename]);
                    curl_setopt($conn[$filename], CURLOPT_HEADER, 0);
                    curl_setopt($conn[$filename], CURLOPT_CONNECTTIMEOUT, 60);
                    curl_multi_add_handle($mh, $conn[$filename]);
                }
            }

            do {
                $n = curl_multi_exec($mh, $active);
            } while ($active);

            foreach ($urlSections as $filename => $url) {
                curl_multi_remove_handle($mh, $conn[$filename]);
                curl_close($conn[$filename]);
                fclose($fp[$filename]);
            }
        }

        curl_multi_close($mh);
    }
}