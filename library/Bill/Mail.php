<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-1-4
 * Time: 下午1:31
 */

class Bill_Mail
{
    private $_username = 'your@mail.com';
    private $_password = 'your_password';
    private $_host = 'smtp.mail.com';
    private $_port = 25;
    private $_receiver = 'receive@mail.com';
    private $_bill_server_host = 'production.host';

    public function send($title, $body, $receiver = null, $attachment = null, $charset = 'utf-8')
    {
        $server_host = $_SERVER['HTTP_HOST'];
        if ($server_host != $this->_bill_server_host)
        {
            return true;
        }

        $config = [
            'auth'=>'login',
            'username'=> $this->_username,
            'password'=> $this->_password,
            //'ssl' => 'ssl',
            'port' => $this->_port
        ];
        $transport = new Zend_Mail_Transport_Smtp($this->_host, $config);

        $mail = new Zend_Mail($charset);
        $mail->setBodyText($body);
        $mail->setFrom($this->_username, 'Admin');

        if(is_array($receiver))
        {
            if(count($receiver) == 0)
            {
                return false;
            }

            for($i = 0, $len = count($receiver); $i < $len; $i++)
            {
                $mail->addTo($receiver[$i]);
            }
        }
        else
        {
            if($receiver == null)
            {
                $receiver = $this->_receiver;
            }

            $mail->addTo($receiver);
        }

        if($attachment != null)
        {
            $mail->createAttachment(
                file_get_contents($attachment),
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $attachment
            );
        }

        //todo set mail from alpha or product
        $env = 'product';
        $title = '(' . Application_Model_Auth::getIdentity()->name . '-' . $env . ')' . $title;
        $mail->setSubject($title);
        $mail->send($transport);

        return true;
    }
} 