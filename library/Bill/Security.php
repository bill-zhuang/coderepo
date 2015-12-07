<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 15-11-19
 * Time: 上午9:53
 * Reference: Yii 2.x Security Class
 */

class Bill_Security
{
    public function generateRandomString($length = 32)
    {
        $bytes = $this->generateRandomKey($length);
        // '=' character(s) returned by base64_encode() are always discarded because
        // they are guaranteed to be after position $length in the base64_encode() output.
        return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
    }

    public function generateRandomKey($length = 32)
    {
        if (!extension_loaded('mcrypt')) {
            throw new Exception('The mcrypt PHP extension is not installed.');
        }
        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        if ($bytes === false) {
            throw new Exception('Unable to generate random bytes.');
        }
        return $bytes;
    }
}