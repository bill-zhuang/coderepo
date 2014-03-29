<?php

class Bill_Zip
{
	private $zip_archive;

	public function __construct()
	{
		$this->zip_archive = new ZipArchive();
	}

	public function unZipFile($zip_path, $unzip_path)
	{
		$open_zip_info = $this->_openZip($zip_path);

		if($open_zip_info === true)
		{
			if($this->zip_archive->extractTo($unzip_path) === true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			$this->zip_archive->close();

			return $open_zip_info;
		}
	}

	private function _openZip($zip_path)
	{
		if(!file_exists($zip_path))
		{
			return 'file not exist.';
		}
		else
		{
			if(strtolower(Bill_File::getFileExtension($zip_path)) != 'zip')
			{
				return 'not zip file.';
			}
			else
			{
				if($this->zip_archive->open($zip_path) === true)
				{
					return true;
				}
				else
				{
					return 'open zip file failed.';
				}
			}
		}
	}

}