<?php

class Bill_Mail
{
    private static $_username = 'your@mail.com';
    private static $_password = 'your_password';
    private static $_host = 'smtp.mail.com';
    private static $_port = 25;
    private static $_receiver = 'receive@mail.com';

    public static function send($title, $body, $receiver = null, $attachment = null, $charset = 'utf-8')
    {
        if (!Bill_Util::isProductionEnv() && !Bill_Util::isAlphaEnv())
        {
            return true;
        }

        $transport = new Zend_Mail_Transport_Smtp(self::$_host, self::_initConfig());

        $mail = new Zend_Mail($charset);
        $mail->setBodyText($body);
        $mail->setFrom(self::$_username, self::$_username);

        $receivers = self::_initReceivers($receiver);
        for ($i = 0, $len = count($receivers); $i < $len; $i++)
        {
            $mail->addTo($receiver[$i]);
        }

        if ($attachment != null)
        {
            $mail->createAttachment(
                file_get_contents($attachment),
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $attachment
            );
        }

        $env = Bill_Util::isProductionEnv() ? 'product' : 'alpha';
        $title = '(' . Application_Model_Auth::getIdentity()->name . '-' . $env . ')' . $title;
        $mail->setSubject($title);
        $mail->send($transport);

        return true;
    }

    private static function _initConfig()
    {
        return [
            'auth' => 'login',
            'username' => self::$_username,
            'password' => self::$_password,
            //'ssl' => 'ssl',
            'port' => self::$_port,
        ];
    }

    private static function _initReceivers($receiver)
    {
        if (is_array($receiver) && count($receiver) == 0)
        {
            return [self::$_receiver];
        }

        if ($receiver == null)
        {
            return [self::$_receiver];
        }

        return $receiver;
    }
} 