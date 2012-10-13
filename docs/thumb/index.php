<?php require_once 'highLight/highLight.php'; ?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Thumb - PHP Image Resizer</title>
	<link rel="stylesheet" href="highLight/highLight.css">
	<link rel="stylesheet" href="../bootstrap.twitter/css/bootstrap.min.css">
	<link rel="stylesheet" href="style/style.css">
	<script src="../bootstrap.twitter/js/bootstrap.min.js"></script>
    </head>
    <body>
	<div class="main-wrapper">
	    <div class="title">
		<h1>Thumb (Fork of TimThumbs)</h1><h4>by Gérard TOKO</h4>
		<hr>
	    </div>
	    <br>
	    <div class="description">		
		<p><strong>Thumb</strong> is PHP Image Resizer and Cache manager thumbails</p>
		<p>Require PHP 5.3 </p>
		<p><a href="https://github.com/gerardtoko/thumb"  TARGET="_blank">On Github</a></p>
		<hr>
	    </div>

	    <div class="docs">

		<h5>Installation</h5>
		<?php echo HighLight::dump('docs/install.txt') ?>
		<h5>Demo Thumb</h5>

		<?php echo HighLight::dump('docs/thumb.demo.1.php') ?>
		<?php echo HighLight::dump('docs/thumb.demo.2.php') ?>
		<h5>Cache Manager Thumb</h5>
		<?php echo HighLight::dump('docs/thumb.demo.3.php') ?>
		<br>
		<h5>CONSTANT</h5>
		<table class="table table-striped table-bordered">
		    <colgroup>
			<col width="15%">
			<col width="15%">
			<col width="10%">
			<col width="60%">			
		    </colgroup>
		    <thead>
			<tr class="headings">
			    <th>Name</th>
			    <th>Default Option</th>
			    <th>Type</th>
			    <th>Description</th>
			</tr>
		    </thead>
		    <tbody>
			<tr>
			    <td>MEMORY_LIMIT</td>
			    <td>30M</td>
			    <td>String</td>
			    <td>Set PHP memory limit</td>
			</tr>
			<tr>
			    <td>MAX_WIDTH</td>
			    <td>1500</td>
			    <td>Int</td>
			    <td>Maximum image width</td>
			</tr>
			<tr>
			    <td>MAX_HEIGHT</td>
			    <td>1500</td>
			    <td>Int</td>
			    <td>Maximum image height</td>
			</tr>
			<tr>
			    <td>PNG_IS_TRANSPARENT</td>
			    <td>FALSE</td>
			    <td>Bool</td>
			    <td>Define if a png image should have a transparent background color. Use False value if you want to display a custom coloured canvas_colour </td>
			</tr>
			<tr>
			    <td>DEFAULT_Q</td>
			    <td>90</td>
			    <td>Int</td>
			    <td>image quality (0...100)</td>
			</tr>
			
			<tr>
			    <td>CACHE_DIR_THUMB</td>
			    <td>cache/thumb</td>
			    <td>String</td>
			    <td>Directory cache</td>
			</tr>

		    </tbody>
		</table>
		<br>
		<h5>OPTIONS (array)</h5>
		<table class="table table-striped table-bordered">
		    <colgroup>
			<col width="15%">
			<col width="15%">
			<col width="10%">
			<col width="60%">			
		    </colgroup>
		    <thead>
			<tr class="headings">
			    <th>Name</th>
			    <th>Default Option</th>
			    <th>Type</th>
			    <th>Description</th>
			</tr>
		    </thead>
		    <tbody>
			<tr>
			    <td>image</td>
			    <td></td>
			    <td>String</td>
			    <td>Url of Image (support png, jpg and gif</td>
			</tr>
			<tr>
			    <td>defaultImage</td>
			    <td>1500</td>
			    <td>String</td>
			    <td>Default Url image</td>
			</tr>
			<tr>
			    <td>cacheDirectory</td>
			    <td>1500</td>
			    <td>String</td>
			    <td>Cache directory</td>
			</tr>
			<tr>
			    <td>namespace</td>
			    <td>cacheDirectory</td>
			    <td>String</td>
			    <td>Sub directory in the cache directory</td>
			</tr>
			<tr>
			    <td>cropTop</td>
			    <td>false</td>
			    <td>String</td>
			    <td>Zoom file</td>
			</tr>
		    </tbody>
		</table>
		
		<br>
		<h5>FUNCTIONS</h5>
		<p><strong>__construct( array $options )</strong></p>
		<p><strong>getThumbImage()</strong></p>
		<p><strong>cleanCache($cache, $namespace)</strong></p>
	    </div>
	</div>
	<br>
    </body>
</html>
