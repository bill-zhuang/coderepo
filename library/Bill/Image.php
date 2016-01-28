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
                $scale_width = intval($this->_width * $ratio);
                $scale_height = intval($this->_height * $ratio);
                $this->resize($scale_width, $scale_height);
            }
        }

        return $this;
    }

    public function resizeWidth($resize_width)
    {
        $resize_width = intval($resize_width);
        if ($resize_width > 0) {
            if ($resize_width != $this->_width) {
                $ratio = floatval($resize_width / $this->_width);
                $resize_height = intval($this->_height * $ratio);
                $this->resize($resize_width, $resize_height);
            }
        }

        return $this;
    }

    public function resizeHeight($resize_height)
    {
        $resize_height = intval($resize_height);
        if ($resize_height > 0) {
            if ($resize_height != $this->_height) {
                $ratio = floatval($resize_height / $this->_height);
                $resize_width = intval($this->_width * $ratio);
                $this->resize($resize_width, $resize_height);
            }
        }

        return $this;
    }

    public function resize($resize_width, $resize_height)
    {
        if ($resize_width > 0 && $resize_height > 0) {
            if ($resize_width != $this->_width || $resize_height != $this->_height) {
                $resize_image = imagecreatetruecolor($resize_width, $resize_height);
                imagecopyresampled($resize_image, $this->_image, 0, 0, 0, 0,
                    $resize_width, $resize_height, $this->getImageWidth(), $this->getImageHeight());
                $this->_width = $resize_width;
                $this->_height = $resize_height;
                $this->_image = $resize_image;
            }
        }

        return $this;
    }

    public function save($save_path, $extension = null, $quality = null)
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
                imagejpeg($this->_image, $save_path, $quality);
                break;
            case 'png':
                imagepng($this->_image, $save_path, $quality);
                break;
            case 'gif':
                imagegif($this->_image, $save_path);
                break;
            case 'bmp':
                imagewbmp($this->_path, $save_path);
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
     * @param string $blur_type string gaussian|selective
     * @return $this
     */
    public function blur($blur_type = 'gaussian')
    {
        $blur_type = strtolower($blur_type);
        switch ($blur_type) {
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

    public function rotate($angle, $bg_color_hex = '#000000')
    {
        $angle = intval($angle);
        if ($angle >= 0 && $angle <= 360) {
            $bg_color = 0;
            if (strlen($bg_color_hex) == 7) {
                list($red, $green, $blue) = sscanf($bg_color_hex, '#%02x%02x%02x');
                $bg_color = imagecolorallocate($this->_image, $red, $green, $blue);
            }
            $rotate_image = imagerotate($this->_image, $angle, $bg_color);
            imagesavealpha($rotate_image, true);
            imagealphablending($rotate_image, true);
            $this->_image = $rotate_image;
            $this->_width = imagesx($rotate_image);
            $this->_height = imagesy($rotate_image);
        }

        return $this;
    }

    /**
     * @param $direction string x|y
     * @return $this
     */
    public function flip($direction)
    {
        $flip_image = imagecreatetruecolor($this->_width, $this->_height);
        imagealphablending($flip_image, false);
        imagesavealpha($flip_image, true);
        //
        $direction = strtolower($direction);
        switch ($direction) {
            case 'x':
                for ($x = 0; $x < $this->_width; $x++) {
                    imagecopy($flip_image, $this->_image, $x, 0, $this->_width - $x - 1, 0, 1, $this->_height);
                }
                $this->_image = $flip_image;
                break;
            case 'y':
                for ($y = 0; $y < $this->_height; $y++) {
                    imagecopy($flip_image, $this->_image, 0, $y, 0, $this->_height - $y - 1, $this->_width, 1);
                }
                $this->_image = $flip_image;
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * combine two images together, both two images width & height should same
     * @param $background_path string background image path
     * @param $foreground_path string foreground image path
     * @return string result image path
     */
    public static function cover($background_path, $foreground_path)
    {
        if (file_exists($background_path) && file_exists($foreground_path)) {
            $output_path = Bill_File::getTempDir() . uniqid() . 'cover.png';
            $background_size = getimagesize($background_path);
            $foreground_size = getimagesize($foreground_path);
            if ($background_size[0] == $foreground_size[0] && $background_size[1] == $foreground_size[1]) {
                $back_image = imagecreatefrompng($background_path);
                $fore_image = imagecreatefrompng($foreground_path);

                imagesavealpha($fore_image, true);
                imagealphablending($fore_image, true);
                imagecopy($back_image, $fore_image, 0, 0, 0, 0, $foreground_size[0], $foreground_size[1]);
                imagepng($back_image, $output_path);
                if (file_exists($output_path)) {
                    return $output_path;
                }
            }
        }

        return '';
    }

    /**
     * @param array $paths join images path
     * @param $direction string acceptable argument: x|y, x: join height, y: join width
     * @param $direction_length integer join image width/height
     * @return string join image path
     * @throws Exception
     */
    public function joinMultipleImages(array $paths, $direction, $direction_length)
    {
        $join_path = Bill_File::getTempDir() . uniqid() . 'join.png';
        if (!empty($paths)) {
            if ($direction == 'x' || $direction == 'y') {
                if ($direction_length > 0) {
                    list($join_width, $join_height) = ($direction == 'x') ? [$direction_length, 0] : [0, $direction_length];
                    $widths = [];
                    $heights = [];
                    foreach ($paths as $path) {
                        $this->_loadImage($path);
                        if ($direction == 'x') {
                            $this->resizeWidth($direction_length)->save($path);
                        } else {
                            $this->resizeHeight($direction_length)->save($path);
                        }
                        $join_height += $this->getImageHeight();
                        $widths[] = $this->getImageWidth();
                        $heights[] = $this->getImageHeight();
                    }
                    $join_image = imagecreatetruecolor($join_width, $join_height);
                    $start_x = 0;
                    $start_y = 0;
                    foreach ($paths as $path_key => $path) {
                        $im = imagecreatefrompng($path);
                        imagecopyresampled($join_image, $im, $start_x, $start_y, 0, 0,
                            $widths[$path_key], $heights[$path_key], $widths[$path_key], $heights[$path_key]);
                        //increase width/height
                        $start_x += ($direction == 'x') ? 0 : $widths[$path_key];
                        $start_y += ($direction == 'x') ? $heights[$path_key] : 0;
                        //remove file
                        imagedestroy($im);
                        //@unlink($path);
                    }
                    imagepng($join_image, $join_path);
                } else {
                    throw new Exception('Join image direction length can\'t lower than 0.');
                }
            } else {
                throw new Exception('Direction acceptable argument: x or y only.');
            }
        } else {
            throw new Exception('Join image path empty!');
        }

        return $join_path;
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