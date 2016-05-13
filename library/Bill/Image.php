<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 16-1-25
 * Time: 下午3:00
 */

class Bill_Image
{
    private $_path;
    private $_image;
    private $_width;
    private $_height;

    public function __construct($path)
    {
        if (!extension_loaded('gd')) {
            throw new Exception('GD extension required!');
        }
        $this->_loadImage($path);
    }

    public function scale($ratio)
    {
        $ratio = floatval($ratio);
        if ($ratio > 0) {
            if ($ratio != 1.0) {
                $scaleWidth = intval($this->_width * $ratio);
                $scaleHeight = intval($this->_height * $ratio);
                $this->resize($scaleWidth, $scaleHeight);
            }
        }

        return $this;
    }

    public function resizeWidth($resizeWidth)
    {
        $resizeWidth = intval($resizeWidth);
        if ($resizeWidth > 0) {
            if ($resizeWidth != $this->_width) {
                $ratio = floatval($resizeWidth / $this->_width);
                $resizeHeight = intval($this->_height * $ratio);
                $this->resize($resizeWidth, $resizeHeight);
            }
        }

        return $this;
    }

    public function resizeHeight($resizeHeight)
    {
        $resizeHeight = intval($resizeHeight);
        if ($resizeHeight > 0) {
            if ($resizeHeight != $this->_height) {
                $ratio = floatval($resizeHeight / $this->_height);
                $resizeWidth = intval($this->_width * $ratio);
                $this->resize($resizeWidth, $resizeHeight);
            }
        }

        return $this;
    }

    public function resize($resizeWidth, $resizeHeight)
    {
        if ($resizeWidth > 0 && $resizeHeight > 0) {
            if ($resizeWidth != $this->_width || $resizeHeight != $this->_height) {
                $resizeImage = imagecreatetruecolor($resizeWidth, $resizeHeight);
                imagecopyresampled($resizeImage, $this->_image, 0, 0, 0, 0,
                    $resizeWidth, $resizeHeight, $this->getImageWidth(), $this->getImageHeight());
                $this->_width = $resizeWidth;
                $this->_height = $resizeHeight;
                $this->_image = $resizeImage;
            }
        }

        return $this;
    }

    public function save($savePath, $extension = null, $quality = null)
    {
        if ($quality !== null) {
            $quality = intval($quality);
            $quality = ($quality >= 0 && $quality <= 100) ? $quality : 90;
        }
        if ($extension !== null) {
            $extension = strtolower($extension);
        } else {
            $extension = strtolower(pathinfo($this->_path, PATHINFO_EXTENSION));
        }
        switch($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->_image, $savePath, $quality);
                break;
            case 'png':
                imagepng($this->_image, $savePath, $quality);
                break;
            case 'gif':
                imagegif($this->_image, $savePath);
                break;
            case 'bmp':
                imagewbmp($this->_path, $savePath);
                break;
            default:
                throw new Exception('Unsupported image extension.');
                break;
        }

        return $this;
    }

    public function grayscale()
    {
        imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    public function edgeDetect()
    {
        imagefilter($this->_image, IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * @param string $blurType string gaussian|selective
     * @return $this
     */
    public function blur($blurType = 'gaussian')
    {
        $blurType = strtolower($blurType);
        switch ($blurType) {
            case 'gaussian':
                imagefilter($this->_image, IMG_FILTER_GAUSSIAN_BLUR);
                break;
            case 'selective':
                imagefilter($this->_image, IMG_FILTER_SELECTIVE_BLUR);
                break;
            default:
                break;
        }
        return $this;
    }

    public function rotate($angle, $bgColorHex = '#000000')
    {
        $angle = intval($angle);
        if ($angle >= 0 && $angle <= 360) {
            $bgColor = 0;
            if (strlen($bgColorHex) == 7) {
                list($red, $green, $blue) = sscanf($bgColorHex, '#%02x%02x%02x');
                $bgColor = imagecolorallocate($this->_image, $red, $green, $blue);
            }
            $rotateImage = imagerotate($this->_image, $angle, $bgColor);
            imagesavealpha($rotateImage, true);
            imagealphablending($rotateImage, true);
            $this->_image = $rotateImage;
            $this->_width = imagesx($rotateImage);
            $this->_height = imagesy($rotateImage);
        }

        return $this;
    }

    /**
     * @param $direction string x|y
     * @return $this
     */
    public function flip($direction)
    {
        $flipImage = imagecreatetruecolor($this->_width, $this->_height);
        imagealphablending($flipImage, false);
        imagesavealpha($flipImage, true);
        //
        $direction = strtolower($direction);
        switch ($direction) {
            case 'x':
                for ($x = 0; $x < $this->_width; $x++) {
                    imagecopy($flipImage, $this->_image, $x, 0, $this->_width - $x - 1, 0, 1, $this->_height);
                }
                $this->_image = $flipImage;
                break;
            case 'y':
                for ($y = 0; $y < $this->_height; $y++) {
                    imagecopy($flipImage, $this->_image, 0, $y, 0, $this->_height - $y - 1, $this->_width, 1);
                }
                $this->_image = $flipImage;
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * combine two images together, both two images width & height should same
     * @param $backgroundPath string background image path
     * @param $foregroundPath string foreground image path
     * @return string result image path
     */
    public static function cover($backgroundPath, $foregroundPath)
    {
        if (file_exists($backgroundPath) && file_exists($foregroundPath)) {
            $outputPath = Bill_File::getTempDir() . uniqid() . 'cover.png';
            $backgroundSize = getimagesize($backgroundPath);
            $foregroundSize = getimagesize($foregroundPath);
            if ($backgroundSize[0] == $foregroundSize[0] && $backgroundSize[1] == $foregroundSize[1]) {
                $backImage = imagecreatefrompng($backgroundPath);
                $foreImage = imagecreatefrompng($foregroundPath);

                imagesavealpha($foreImage, true);
                imagealphablending($foreImage, true);
                imagecopy($backImage, $foreImage, 0, 0, 0, 0, $foregroundSize[0], $foregroundSize[1]);
                imagepng($backImage, $outputPath);
                if (file_exists($outputPath)) {
                    return $outputPath;
                }
            }
        }

        return '';
    }

    /**
     * @param array $paths join images path
     * @param $direction string acceptable argument: x|y, x: join height, y: join width
     * @param $directionLength integer join image width/height
     * @return string join image path
     * @throws Exception
     */
    public function joinMultipleImages(array $paths, $direction, $directionLength)
    {
        $joinPath = Bill_File::getTempDir() . uniqid() . 'join.png';
        if (!empty($paths)) {
            if ($direction == 'x' || $direction == 'y') {
                if ($directionLength > 0) {
                    list($joinWidth, $joinHeight) = ($direction == 'x') ? [$directionLength, 0] : [0, $directionLength];
                    $widths = [];
                    $heights = [];
                    foreach ($paths as $path) {
                        $this->_loadImage($path);
                        if ($direction == 'x') {
                            $this->resizeWidth($directionLength)->save($path);
                        } else {
                            $this->resizeHeight($directionLength)->save($path);
                        }
                        $joinHeight += $this->getImageHeight();
                        $widths[] = $this->getImageWidth();
                        $heights[] = $this->getImageHeight();
                    }
                    $joinImage = imagecreatetruecolor($joinWidth, $joinHeight);
                    $startX = 0;
                    $startY = 0;
                    foreach ($paths as $pathKey => $path) {
                        $im = imagecreatefrompng($path);
                        imagecopyresampled($joinImage, $im, $startX, $startY, 0, 0,
                            $widths[$pathKey], $heights[$pathKey], $widths[$pathKey], $heights[$pathKey]);
                        //increase width/height
                        $startX += ($direction == 'x') ? 0 : $widths[$pathKey];
                        $startY += ($direction == 'x') ? $heights[$pathKey] : 0;
                        //remove file
                        imagedestroy($im);
                        //@unlink($path);
                    }
                    imagepng($joinImage, $joinPath);
                } else {
                    throw new Exception('Join image direction length can\'t lower than 0.');
                }
            } else {
                throw new Exception('Direction acceptable argument: x or y only.');
            }
        } else {
            throw new Exception('Join image path empty!');
        }

        return $joinPath;
    }

    public function getImageWidth()
    {
        return $this->_width;
    }

    public function getImageHeight()
    {
        return $this->_height;
    }

    private function _loadImageMetaData()
    {
        $info = getimagesize($this->_path);
        switch ($info['mime']) {
            case 'image/gif':
                $this->_image = imagecreatefromgif($this->_path);
                break;
            case 'image/jpeg':
                $this->_image = imagecreatefromjpeg($this->_path);
                break;
            case 'image/png':
                $this->_image = imagecreatefrompng($this->_path);
                break;
            case 'image/bmp':
                $this->_image = imagecreatefromwbmp($this->_path);
                break;
            default:
                throw new Exception('Invalid image: ' . $this->_path);
                break;
        }
        $this->_width = $info[0];
        $this->_height = $info[1];
    }

    private function _loadImage($path)
    {
        $this->_path = $path;
        $this->_loadImageMetaData($path);
        return $this;
    }
}