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
        if (file_exists($zipPath)) {
            if ($this->_zipArchive->open($zipPath) === true) {
                Bill_File::createDirectory($unzipPath);
                return $this->_zipArchive->extractTo($unzipPath);
            } else {
                $this->_zipArchive->close();
            }
        }

        return false;
    }

    //tarball/dll src: http://pecl.php.net/package/rar
    public function unrar($rarPath, $unrarPath)
    {
        if (function_exists('rar_open')) {
            $rarFile = rar_open($rarPath);
            $list = rar_list($rarFile);
            Bill_File::createDirectory($unrarPath);
            foreach ($list as $file) {
                $entry = rar_entry_get($rarFile, $file->getName());
                $entry->extract($unrarPath);
            }
            return true;
        } else {
            echo 'rar extension not exist.';
            return false;
        }
    }
}