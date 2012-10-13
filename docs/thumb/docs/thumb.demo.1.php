<?php

// lib load
require_once "lib/thumb/Thumb.php";

// image
$image = "media/products/tshirt.png";

// options require
$options = array(
    "image" => $image,  
    "w" => 500, 
    "h" => 300
);


$thumb = new Thumb($options);
$thumb->getThumbImage();

// return cache/thumb/tshirt-500x300.png
