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
    public function sendRequest($url, array $postData, $method = 'GET')
    {
        $postData = http_build_query($postData);

        $options = array('http' => array('method' => $method, 'header' => 'Content-type:application/x-www-form-urlencoded', 'content' => $postData, /* ��ʱʱ��(��λ:s)*/
            'timeout' => 15 * 60,),);

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    /**
     * Download file through file_get_contents.
     * @param string $url
     * @param string $fileName
     * @param string/null $dir
     */
    public function fileGetContentDownload($url, $fileName, $dir = null)
    {
        $fullPath = ($dir == null) ? $fileName : $dir . $fileName;

        $file_content = file_get_contents($url);
        file_put_contents($fullPath, $file_content);
    }

    /**
     * Download file through wget.
     * @param string $url
     * @param string $fileName
     * @param string/null $dir
     */
    public function wgetDownload($url, $fileName, $dir = null)
    {
        $fullPath = ($dir == null) ? $fileName : $dir . $fileName;

        $cmd = "e:/wget1.11.4.exe " . $url . " -O " . $fullPath;
        exec($cmd, $array, $ret);

        return $ret;
    }

    /**
     * download file through curl.
     * @param string $url
     * @param string $fileName
     * @param string $dir
     */
    public function curlSingleDownload($url, $fileName, $dir = null)
    {
        if ($dir != null) {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        $fullPath = ($dir == null) ? $fileName : $dir . $fileName;

        if (file_exists($fullPath)) {
            return;
            //unlink($fullPath);
        }

        $fp = fopen($fullPath, 'w+');

        if ($fp) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);
        } else {
            echo "Download {$fileName} file from {$url} failed.<br>";
            return;
        }

        fclose($fp);
    }

    /**
     * Download file through multi-thread by curl.
     * @param array $filenameUrl
     * <p>key-filename, value-url.</p>
     * @param string $dir
     * <p>directory to save the files.</p>
     * @param integer $downloadNum [optinal]
     * <p>download files at one time, default 100.</p>*/
    public function curlMultipleDownload(array $filenameUrl, $dir, $downloadNum = 100)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $mh = curl_multi_init();
        $urlhundred = array_chunk($filenameUrl, $downloadNum, true);

        foreach ($urlhundred as $nameurls) {
            foreach ($nameurls as $filename => $url) {
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

            foreach ($nameurls as $filename => $url) {
                curl_multi_remove_handle($mh, $conn[$filename]);
                curl_close($conn[$filename]);
                fclose($fp[$filename]);
            }
        }

        curl_multi_close($mh);
    }
}