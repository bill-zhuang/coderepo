#!/usr/local/bin/php
<?php

function dict($args)
{
    if (count($args) == 1) {
        return 'no word input';
    }
    $url = 'http://dict.youdao.com/w/' . $args[1];
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);
    $ret = curl_exec($handle);
    if ($ret === false) {
        return 'network error.';
    }
    $regex = '/<div class="trans\-container">[^<]+<ul>(.*?)<\/ul>.*?<\/div>/s';
    preg_match_all($regex, $ret, $matches);
    if (count($matches) == 0) {
        return 'no translation found.';
    }
    if (!isset($matches[1][0])) {
        return 'no translation found.';
    }
    preg_match_all('/<li>(.*?)<\/li>/', $matches[1][0], $detail);
    if (count($detail) == 0) {
        return 'no translation found.';
    }
    return $detail[1];
}


if (PHP_SAPI == 'cli') {
    $dictRet = dict($argv);
    if (!is_array($dictRet)) {
        echo $dictRet . PHP_EOL;
    } else {
        echo $argv[1] . PHP_EOL;
        foreach ($dictRet as $trans) {
            echo "    " . $trans . PHP_EOL;
        }
    }
}