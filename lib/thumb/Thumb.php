<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


// Debug level 1 is less noisy and 3 is the most noisy
if (!defined('MEMORY_LIMIT'))
    define('MEMORY_LIMIT', '30M');       // Set PHP memory limit

//Image size and defaults
if (!defined('MAX_WIDTH'))
    define('MAX_WIDTH', 1500);  // Maximum image width
if (!defined('MAX_HEIGHT'))
    define('MAX_HEIGHT', 1500); // Maximum image height
if (!defined('PNG_IS_TRANSPARENT'))
    define('PNG_IS_TRANSPARENT', FALSE);  //42 Define if a png image should have a transparent background color. Use False value if you want to display a custom coloured canvas_colour 
if (!defined('DEFAULT_Q'))
    define('DEFAULT_Q', 90);  // Default image quality. Allows overrid in Thumb-config.php
if (!defined('DEFAULT_ZC'))
    define('DEFAULT_ZC', 1);  // Default zoom/crop setting. Allows overrid in Thumb-config.php
if (!defined('DEFAULT_F'))
    define('DEFAULT_F', '');  // Default image filters. Allows overrid in Thumb-config.php
if (!defined('DEFAULT_S'))
    define('DEFAULT_S', 0);  // Default sharpen value. Allows overrid in Thumb-config.php
if (!defined('DEFAULT_CC'))
    define('DEFAULT_CC', 'ffffff');       // Default canvas colour. Allows overrid in Thumb-config.php

if (!defined('CACHE_DIR_THUMB'))
    define('CACHE_DIR_THUMB', 'cache/thumb'); 

class Thumb {

    protected $_localImage = "";
    protected $_localDefaultImage = "";
    protected $cacheDirectory = '';
    protected $cacheDirectory_root = '';
    protected $cropTop = false;
    protected $options = array();


    public function __construct(array $options = array()) {

	// control image
	if (!empty($options["image"])) {
	    $this->_localImage = $options["image"];
	    $this->_localDefaultImage = $options["image"];
	} else {
	    if (!empty($options["defaultImage"])) {
		$this->_localImage = $options["defaultImage"];
		$this->_localDefaultImage = $options["defaultImage"];
	    } else {
		$this->error("undefined default image");
	    }
	}

	// direcotry cache
	$this->cacheDirectory_root = !empty($options["cacheDirectory"]) ? $options["cacheDirectory"] : CACHE_DIR_THUMB;
	$this->cacheDirectory = !empty($options["namespace"]) ? $this->cacheDirectory_root . $options["namespace"] : $this->cacheDirectory_root;

	// controle directory cache
	if (!file_exists($this->cacheDirectory)) {
	    mkdir($this->cacheDirectory, 0770, true);
	}

	// crop top
	if (!empty($options["cropTop"])) {
	    $this->cropTop = $options["cropTop"];
	}

	$this->options = $options;
    }


    public function getThumbImage() {
	return $this->processImageAndWriteToCache($this->_localImage);
    }


    /**
     * 
     * @param type $namespace
     */
    protected function cleanCache($namespace) {
	if (file_exists($namespace)) {
	    rmdir($namespace);
	}
    }


    /**
     * 
     * @param type $localImage
     * @return null
     */
    protected function processImageAndWriteToCache($localImage) {

	$sData = getimagesize($localImage);
	$origType = $sData[2];
	$mimeType = $sData['mime'];
	$cachefile = "";
	$imgType = "";

	$new_width = (int) abs($this->param('w', 100));
	$new_height = (int) abs($this->param('h', 100));

	if (preg_match('/^image\/(?:jpg|jpeg)$/i', $mimeType)) {

	    if (preg_match("/jpg$/", $localImage)) {
		$imgType = 'jpg';
		$cachefile = sprintf("%s%s-%sx%s.%s", $this->cacheDirectory, substr($localImage, 0, -4), $new_width, $new_height, $imgType);
	    }

	    if (preg_match("/jpeg$/", $localImage)) {
		$imgType = 'jpeg';
		$cachefile = sprintf("%s%s-%sx%s.%s", $this->cacheDirectory, substr($localImage, 0, -5), $new_width, $new_height, $imgType);
	    }
	    
	} else if (preg_match('/^image\/png$/i', $mimeType)) {
	    $imgType = 'png';
	    $cachefile = sprintf("%s%s-%sx%s.%s", $this->cacheDirectory, substr($localImage, 0, -4), $new_width, $new_height, $imgType);
	} else if (preg_match('/^image\/gif$/i', $mimeType)) {
	    $imgType = 'gif';
	    $cachefile = sprintf("%s%s-%sx%s.%s", $this->cacheDirectory, substr($localImage, 0, -4), $new_width, $new_height, $imgType);
	} else {
	    return $this->error("The image is not a valid gif, jpg or png.");
	}

	// cache file
	if ($result = $this->serveCacheFile($cachefile)) {
	    return $result;
	} else {

	    if (!preg_match('/^image\/(?:gif|jpg|jpeg|png)$/i', $mimeType)) {
		return $this->error("The image being resized is not a valid gif, jpg or png.");
	    }

	    if (!function_exists('imagecreatetruecolor')) {
		return $this->error('GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library');
	    }

	    if (function_exists('imagefilter') && defined('IMG_FILTER_NEGATE')) {
		$imageFilters = array(
		    1 => array(IMG_FILTER_NEGATE, 0),
		    2 => array(IMG_FILTER_GRAYSCALE, 0),
		    3 => array(IMG_FILTER_BRIGHTNESS, 1),
		    4 => array(IMG_FILTER_CONTRAST, 1),
		    5 => array(IMG_FILTER_COLORIZE, 4),
		    6 => array(IMG_FILTER_EDGEDETECT, 0),
		    7 => array(IMG_FILTER_EMBOSS, 0),
		    8 => array(IMG_FILTER_GAUSSIAN_BLUR, 0),
		    9 => array(IMG_FILTER_SELECTIVE_BLUR, 0),
		    10 => array(IMG_FILTER_MEAN_REMOVAL, 0),
		    11 => array(IMG_FILTER_SMOOTH, 0),
		);
	    }

	    $zoom_crop = (int) $this->param('zc', DEFAULT_ZC);
	    $quality = (int) abs($this->param('q', DEFAULT_Q));
	    $align = $this->cropTop ? 't' : $this->param('a', 'c');
	    $filters = $this->param('f', DEFAULT_F);
	    $sharpen = (bool) $this->param('s', DEFAULT_S);
	    $canvas_color = $this->param('cc', DEFAULT_CC);
	    $canvas_trans = (bool) $this->param('ct', '1');


	    // set memory limit to be able to have enough space to resize larger images
	    $this->setMemoryLimit();

	    // open the existing image
	    $image = $this->openImage($mimeType, $localImage);

	    // Get original width and height
	    $width = imagesx($image);
	    $height = imagesy($image);
	    $origin_x = 0;
	    $origin_y = 0;

	    // generate new w/h if not provided
	    if ($new_width && !$new_height) {
		$new_height = floor($height * ($new_width / $width));
	    } else if ($new_height && !$new_width) {
		$new_width = floor($width * ($new_height / $height));
	    }

	    // scale down and add borders
	    if ($zoom_crop == 3) {
		$final_height = $height * ($new_width / $width);

		if ($final_height > $new_height) {
		    $new_width = $width * ($new_height / $height);
		} else {
		    $new_height = $final_height;
		}
	    }

	    // create a new true color image
	    $canvas = imagecreatetruecolor($new_width, $new_height);
	    imagealphablending($canvas, false);

	    if (strlen($canvas_color) == 3) { //if is 3-char notation, edit string into 6-char notation
		$canvas_color = str_repeat(substr($canvas_color, 0, 1), 2) . str_repeat(substr($canvas_color, 1, 1), 2) . str_repeat(substr($canvas_color, 2, 1), 2);
	    } else if (strlen($canvas_color) != 6) {
		$canvas_color = DEFAULT_CC; // on error return default canvas color
	    }

	    $canvas_color_R = hexdec(substr($canvas_color, 0, 2));
	    $canvas_color_G = hexdec(substr($canvas_color, 2, 2));
	    $canvas_color_B = hexdec(substr($canvas_color, 4, 2));

	    // Create a new transparent color for image
	    // If is a png and PNG_IS_TRANSPARENT is false then remove the alpha transparency 
	    // (and if is set a canvas color show it in the background)
	    if (preg_match('/^image\/png$/i', $mimeType) && !PNG_IS_TRANSPARENT && $canvas_trans) {
		$color = imagecolorallocatealpha($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 127);
	    } else {
		$color = imagecolorallocatealpha($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 0);
	    }


	    // Completely fill the background of the new image with allocated color.
	    imagefill($canvas, 0, 0, $color);

	    // scale down and add borders
	    if ($zoom_crop == 2) {

		$final_height = $height * ($new_width / $width);

		if ($final_height > $new_height) {
		    $origin_x = $new_width / 2;
		    $new_width = $width * ($new_height / $height);
		    $origin_x = round($origin_x - ($new_width / 2));
		} else {

		    $origin_y = $new_height / 2;
		    $new_height = $final_height;
		    $origin_y = round($origin_y - ($new_height / 2));
		}
	    }

	    // Restore transparency blending
	    imagesavealpha($canvas, true);

	    if ($zoom_crop > 0) {

		$src_x = $src_y = 0;
		$src_w = $width;
		$src_h = $height;

		$cmp_x = $width / $new_width;
		$cmp_y = $height / $new_height;

		// calculate x or y coordinate and width or height of source
		if ($cmp_x > $cmp_y) {

		    $src_w = round($width / $cmp_x * $cmp_y);
		    $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
		} else if ($cmp_y > $cmp_x) {

		    $src_h = round($height / $cmp_y * $cmp_x);
		    $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
		}

		// positional cropping!
		if ($align) {
		    if (strpos($align, 't') !== false) {
			$src_y = 0;
		    }
		    if (strpos($align, 'b') !== false) {
			$src_y = $height - $src_h;
		    }
		    if (strpos($align, 'l') !== false) {
			$src_x = 0;
		    }
		    if (strpos($align, 'r') !== false) {
			$src_x = $width - $src_w;
		    }
		}

		imagecopyresampled($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
	    } else {

		// copy and resize part of an image with resampling
		imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	    }

	    if ($filters != '' && function_exists('imagefilter') && defined('IMG_FILTER_NEGATE')) {
		// apply filters to image
		$filterList = explode('|', $filters);
		foreach ($filterList as $fl) {

		    $filterSettings = explode(',', $fl);
		    if (isset($imageFilters[$filterSettings[0]])) {

			for ($i = 0; $i < 4; $i++) {
			    if (!isset($filterSettings[$i])) {
				$filterSettings[$i] = null;
			    } else {
				$filterSettings[$i] = (int) $filterSettings[$i];
			    }
			}

			switch ($imageFilters[$filterSettings[0]][1]) {
			    case 1:
				imagefilter($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1]);
				break;

			    case 2:
				imagefilter($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2]);
				break;

			    case 3:
				imagefilter($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3]);
				break;

			    case 4:
				imagefilter($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3], $filterSettings[4]);
				break;

			    default:
				imagefilter($canvas, $imageFilters[$filterSettings[0]][0]);
				break;
			}
		    }
		}
	    }

	    // sharpen image
	    if ($sharpen && function_exists('imageconvolution')) {

		$sharpenMatrix = array(
		    array(-1, -1, -1),
		    array(-1, 16, -1),
		    array(-1, -1, -1),
		);

		$divisor = 8;
		$offset = 0;

		imageconvolution($canvas, $sharpenMatrix, $divisor, $offset);
	    }
	    //Straight from Wordpress core code. Reduces filesize by up to 70% for PNG's
	    if ((IMAGETYPE_PNG == $origType || IMAGETYPE_GIF == $origType) && function_exists('imageistruecolor') && !imageistruecolor($image) && imagecolortransparent($image) > 0) {
		imagetruecolortopalette($canvas, false, imagecolorstotal($image));
	    }

	    switch ($imgType) {
		case "jpg":
		case "jpeg":
		    imagejpeg($canvas, $cachefile, $quality);
		    break;
		case "png":
		    imagepng($canvas, $cachefile, floor($quality * 0.09));
		    break;
		case "gif":
		    imagegif($canvas, $cachefile);
		    break;
	    }

	    imagedestroy($canvas);
	    imagedestroy($image);

	    if ($result = $this->serveCacheFile($cachefile)) {
		return $result;
	    } else {
		return null;
	    }
	}
    }


    /**
     * 
     * @param type $file
     * @return boolean
     */
    protected function serveCacheFile($file) {
	$file_real = realpath($file);
	if (file_exists($file_real)) {
	    return $file_real;
	} else {
	    return false;
	}
    }


    /**
     * 
     * @param type $property
     * @param type $default
     * @return type
     */
    protected function param($property, $default = '') {
	if (isset($this->options[$property])) {
	    return $this->options[$property];
	} else {
	    return $default;
	}
    }


    /**
     * 
     * @param type $mimeType
     * @param type $src
     * @return type
     */
    protected function openImage($mimeType, $src) {
	switch ($mimeType) {
	    case 'image/jpeg':
		$image = imagecreatefromjpeg($src);
		break;

	    case 'image/png':
		$image = imagecreatefrompng($src);
		break;

	    case 'image/gif':
		$image = imagecreatefromgif($src);
		break;
	    default:
		$this->error("Unrecognised mimeType");
	}

	return $image;
    }


    /**
     * 
     * @param type $file
     * @return string
     */
    protected function getMimeType($file) {
	$info = getimagesize($file);
	if (is_array($info) && $info['mime']) {
	    return $info['mime'];
	}
	return '';
    }


    /**
     * 
     */
    protected function setMemoryLimit() {
	$inimem = ini_get('memory_limit');
	$inibytes = Thumb::returnBytes($inimem);
	$ourbytes = Thumb::returnBytes(MEMORY_LIMIT);
	if ($inibytes < $ourbytes) {
	    ini_set('memory_limit', MEMORY_LIMIT);
	}
    }


    /**
     * 
     * @param type $size_str
     * @return type
     */
    protected static function returnBytes($size_str) {
	switch (substr($size_str, -1)) {
	    case 'M': case 'm': return (int) $size_str * 1048576;
	    case 'K': case 'k': return (int) $size_str * 1024;
	    case 'G': case 'g': return (int) $size_str * 1073741824;
	    default: return $size_str;
	}
    }


    /**
     * 
     * @param type $err
     * @throws Exception
     */
    protected function error($err) {
	throw new Exception($err);
    }

}