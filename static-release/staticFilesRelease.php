<?php

class StaticFiles
{
    private $_resourcePaths;
    private $_htmlPaths;
    private $_releaseDir;
    private $_staticReflection;

    public function __construct()
    {
        $this->_resourcePaths = [
            __DIR__ . '/../public/js',
            __DIR__ . '/../public/css',
        ];
        $this->_htmlPaths = [
            __DIR__ . '/../application/layout',
            __DIR__ . '/../application/modules',
            __DIR__ . '/../application/views',
        ];
        $this->_releaseDir = __DIR__ . '/../public/static';
        $this->_staticReflection = [];
        if (!file_exists($this->_releaseDir)) {
            mkdir($this->_releaseDir, '0777', true);
        }
    }

    private function _generateReleaseStaticFiles($resourcePaths)
    {
        foreach ($resourcePaths as $scanPath) {
            $objects = scandir($scanPath);
            if ($objects !== false) {
                foreach ($objects as $object) {
                    if ($object != '.' && $object != '..') {
                        $filePath = $scanPath . '/' . $object;
                        if (filetype($filePath) == "dir") {
                            $this->_generateReleaseStaticFiles(array($filePath));
                        } else {
                            $fileInfo = pathinfo($filePath);
                            $releaseFilename = $fileInfo['filename'] . '-' . md5_file($filePath) . '.' . $fileInfo['extension'];
                            $copyDest = $this->_releaseDir . '/' . $releaseFilename;
                            //if (!file_exists($copyDest)) {
                                $copySuccess = copy($filePath, $copyDest);
                                if ($copySuccess) {
                                    $replacement = __DIR__ . '/../public';
                                    $this->_staticReflection[str_replace($replacement, '', $filePath)]
                                        = str_replace($replacement, '', $copyDest);
                                }
                            //}
                        }
                    }
                }
            }
        }
    }

    private function _replaceHtmlFiles($htmlPaths)
    {
        if (!empty($this->_staticReflection)) {
            foreach ($htmlPaths as $htmlPath) {
                $objects = scandir($htmlPath);
                if ($objects !== false) {
                    foreach ($objects as $object) {
                        if ($object != '.' && $object != '..') {
                            $filePath = $htmlPath . '/' . $object;
                            if (filetype($filePath) == "dir") {
                                $this->_replaceHtmlFiles(array($filePath));
                            } else {
                                if (pathinfo($filePath, PATHINFO_EXTENSION) == 'phtml') {
                                    $htmlContent = file_get_contents($filePath);
                                    foreach ($this->_staticReflection as $searchContent => $replaceContent) {
                                        $htmlContent = str_replace($searchContent, $replaceContent, $htmlContent);
                                    }
                                    file_put_contents($filePath, $htmlContent);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //todo trigger git push hook in production server
    public function release()
    {
        $this->_generateReleaseStaticFiles($this->_resourcePaths);
        $this->_replaceHtmlFiles($this->_htmlPaths);
    }
}

$staticFiles = new StaticFiles();
$staticFiles->release();