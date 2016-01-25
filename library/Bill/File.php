<?php

class Bill_File
{
    public static function getFileExtension($filename)
    {
        $path_info = pathinfo($filename);

        return $path_info['extension'];
    }

    public static function moveUploadFile($upload_id, $destination_directory)
    {
        if ($_FILES && array_key_exists($upload_id, $_FILES) && $_FILES[$upload_id]['size'] != 0) {
            $upload_path = $_FILES[$upload_id]['tmp_name'];
            //!!!!!!filename with non-ascii character will failed use move_uploaded_file function, here rename.
            //use $dest_file_name = mb_convert_encoding($_FILES[$upload_id]['name'], 'utf-8');
            //or use unix timestamp to rename filename.
            $dest_filename = time() . '.' . self::getFileExtension($_FILES[$upload_id]['name']);

            $move_ok = move_uploaded_file($upload_path, $destination_directory . $dest_filename);

            if ($move_ok) {
                return $dest_filename;
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

    public static function deleteDirectory($dir_path)
    {
        if (file_exists($dir_path)) {
            $files = array_diff(scandir($dir_path), array('.', '..')); //scandir can retrive hidden files

            foreach ($files as $file) {
                $path = $dir_path . DIRECTORY_SEPARATOR . $file;
                (is_dir($path)) ? self::deleteDirectory($path) : unlink($path);
            }

            return rmdir($dir_path); //rmdir success when dir is empty.
        }
    }

}