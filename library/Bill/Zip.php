<?php

class Bill_Zip
{
    /**
     * @var ZipArchive
     */
    private $_zipArchive;

    public function __construct()
    {
        $this->_zipArchive = new ZipArchive();
    }

    public function unzip($zipPath, $unzipPath)
    {
        if (file_exists($zipPath))
        {
            if ($this->_zipArchive->open($zipPath) === true)
            {
                if (!file_exists($unzipPath))
                {
                    mkdir($unzipPath, '0777');
                }
                return $this->_zipArchive->extractTo($unzipPath);
            }
            else
            {
                $this->_zipArchive->close();
            }
        }

        return false;
    }

    public function unrar($rarPath, $unrarPath)
    {
        if (function_exists('rar_open'))
        {
            $rar_file = rar_open($rarPath);
            $list = rar_list($rar_file);
            foreach ($list as $file)
            {
                $entry = rar_entry_get($rar_file, $file->getName());
                $entry->extract($unrarPath);
            }
            return true;
        }
        else
        {
            echo 'rar extension not exist.';
            return false;
        }
    }
}