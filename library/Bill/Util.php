<?php

class Bill_Util
{
    public static function encodeChineseCharacterInUrl($url)
    {
        return preg_replace_callback(Bill_Regex::CHINESE_CHARACTER, function ($matches)
            {
                return urlencode($matches[0]);
            }, trim($url));
    }

    public static function extractImageBase64Content($content)
    {
        $preg_img = '/<img.*?src="([^"]+)"/';
        $preg_image_base64 = '/^data.*?64,/';
        $is_match = preg_match_all($preg_img, $content, $matches);
        if ($is_match > 0)
        {
            $image_base64_contents = array();
            foreach ($matches[1] as $value)
            {
                if (substr($value, 0, 4) != 'http')
                {
                    $image_base64_contents[] = $value;
                }
            }
            if (!empty($image_base64_contents))
            {
                foreach ($image_base64_contents as $image_base64_content)
                {
                    //return base64_decode(preg_replace($preg_image_base64, '', $image_base64_content));
                    //save image or upload image here
                }
            }
        }

        return $content;
    }

    public static function validDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    public static function isProductionEnv()
    {
        return ($_SERVER['HTTP_HOST'] == Bill_Constant::PRODUCTION_HOST) ? true : false;
    }

    public static function isAlphaEnv()
    {
        return ($_SERVER['HTTP_HOST'] == Bill_Constant::ALPHA_HOST) ? true : false;
    }

    public static function handleException($exception, $from)
    {
        $title = trim($from);
        if ($exception instanceof Exception)
        {
            $content = $exception->getMessage() . Bill_Html::br() . $exception->getTraceAsString();
        }
        else
        {
            $content = trim($exception);
        }
        self::sendMail($title, $content);
    }

    public static function sendMail($title, $content)
    {
        //todo
    }

    public static function createDirectory($dir)
    {
        if (!file_exists($dir))
        {
            mkdir($dir, '0777', true);
        }
    }
} 