<?php
/**
 * User: Bill
 * Date: 17-4-2
 * Time: ÉÏÎç10:27
 */

class Bill_GoogleAuthenticator
{
    private static $_gaInstance;

    public static function createUserSecretAndQRUrl($userName)
    {
        $ga = self::_getGAInstance();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($userName . '/Coderepo', $secret);
        $ret = [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ];

        return $ret;
    }

    public static function checkCode($secret, $code)
    {
        $ga = self::_getGAInstance();
        return $ga->verifyCode($secret, $code, 2);    // 2 = 2 * 30sec clock tolerance
    }

    public static function getCode($secret)
    {
        $ga = self::_getGAInstance();
        return $ga->getCode($secret);
    }

    private static function _getGAInstance()
    {
        if (self::$_gaInstance === null) {
            self::$_gaInstance = new PHPGangsta_GoogleAuthenticator();
        }

        return self::$_gaInstance;
    }
}