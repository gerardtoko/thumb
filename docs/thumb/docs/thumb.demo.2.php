<?php

// lib load
require_once "lib/thumb/Thumb.php";

// image
$image = "media/products/tshirt.png";
$image_default = "media/products/default.png";

// options
$options = array(
    "image" => $image,
    "defaultImage" => $image_default,
    "cacheDirectory" => "thumb", // change the directory cache
    "namespace" => "products",
    "cropTop" => TRUE,
    "w" => 500, 
    "h" => 300
);


$thumb = new Thumb($options);
$thumb->getThumbImage();

// return thumb/products/tshirt-500x300.png