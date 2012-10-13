<?php

// lib load
require_once "lib/thumb/Thumb.php";

// clean all cache (default cache CONSTANT)
$thumb = Thumb::cleanCache();

// clean all cache in "thumb" 
$thumb = Thumb::cleanCache("thumb");

// clean cache in the namespace "products"  (cache directory "thumb")
$thumb = Thumb::cleanCache("thumb", "products");