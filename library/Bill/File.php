<?php

class Bill_File
{
    public static function getFileExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function moveUploadFile($uploadId, $destinationDirectory)
    {
        if ($_FILES && array_key_exists($uploadId, $_FILES) && $_FILES[$uploadId]['size'] != 0) {
            $uploadPath = $_FILES[$uploadId]['tmp_name'];
            //!!!!!!filename with non-ascii character will failed use move_uploaded_file function, here rename.
            //use $destFileName = mb_convert_encoding($_FILES[$uploadId]['name'], 'utf-8');
            //or use unix timestamp to rename filename.
            $destFilename = time() . '.' . self::getFileExtension($_FILES[$uploadId]['name']);

            $moveOk = move_uploaded_file($uploadPath, $destinationDirectory . $destFilename);

            if ($moveOk) {
                return $destFilename;
            }
        }

        return false;
    }

    public static function inArrayMulti($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle)
                || (is_array($item) && self::inArrayMulti($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public static function createDirectory($dir, $mode = 0777, $recursive = true, $content = null)
    {
        if (!file_exists($dir)) {
            mkdir($dir, $mode, $recursive, $content);
        }
    }

    public static function deleteDirectory($dirPath)
    {
        if (file_exists($dirPath)) {
            $files = array_diff(scandir($dirPath), array('.', '..')); //scandir can retrive hidden files

            foreach ($files as $file) {
                $path = $dirPath . DIRECTORY_SEPARATOR . $file;
                (is_dir($path)) ? self::deleteDirectory($path) : unlink($path);
            }

            return rmdir($dirPath); //rmdir success when dir is empty.
        }
    }

    public static function getTempDir()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'C:' . DIRECTORY_SEPARATOR . 'Windows' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        } else {
            return DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        }
    }
}