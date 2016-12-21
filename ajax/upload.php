<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2016, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/

/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */



// Validate post var
if (!isset($_POST['name']) || empty($_POST['name']) || 
	!isset($_POST['chunk']) || !is_numeric($_POST['chunk']) || 
	!isset($_POST['chunks']) || !is_numeric($_POST['chunks']) || 
	!isset($_POST['mod_name']) || !is_string($_POST['mod_name']) || 
	!isset($_POST['section_id']) || !is_numeric($_POST['section_id']) || 
	!isset($_POST['item_id']) || !is_numeric($_POST['item_id']) || 
	!isset($_POST['imgresize']) || 
	!isset($_POST['maxwidth']) || 
	!isset($_POST['maxheight']) || 
	!isset($_POST['quality'])) {
	die();
}

// Chunking might be enabled
$chunk      = isset($_POST['chunk'])  ? intval($_POST['chunk'])  : 0;
$chunks     = isset($_POST['chunks']) ? intval($_POST['chunks']) : 0;

// Other vars
$section_id = intval($_POST['section_id']);
$item_id    = intval($_POST['item_id']);

$imgresize  = intval($_POST['imgresize']);
$maxheight  = intval($_POST['maxwidth']);
$maxwidth   = intval($_POST['maxheight']);
$quality    = intval($_POST['quality']);



// INCLUDES

require('../../../config.php');
$inc_path = dirname(dirname(__FILE__));
require($inc_path.'/info.php');
require($inc_path.'/resize_img.php');
require($inc_path.'/pngthumb.php');
require(WB_PATH.'/framework/functions.php');

// Look for language file
if (LANGUAGE_LOADED) {
	include($inc_path.'/languages/EN.php');
	if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
		include($inc_path.'/languages/'.LANGUAGE.'.php');
	}
}

// Load admin class
require_once(WB_PATH.'/framework/class.admin.php');
$admin = new admin('Modules', 'module_view', false, false);



// SOME SECURITY CHECKS

// Check if module name in file info.php and post array match
if ($mod_name != $_POST['mod_name']) {
	die();
}

// Check if module is registered in the database
$addon_id = $database->get_one("SELECT addon_id FROM `".TABLE_PREFIX."addons` WHERE type = 'module' AND directory = '".$mod_name."'");
if (!is_numeric($addon_id)) {
	die();
}

// Check if user has permissions to access the module
if (!($admin->is_authenticated() && $admin->get_permission($mod_name, 'module'))) {
	die();
}



// UPLOAD SETTINGS

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Target directory
$cleanup_target_dir = true;      // Remove old files
$max_file_age       = 5 * 3600;  // Temp file age in seconds



// SEND HEADERS

// Make sure file is not cached (as it happens for example on iOS devices)
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

/* 
// Support CORS
header('Access-Control-Allow-Origin: *');
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	exit; // finish preflight CORS requests here
}
*/



// IMAGE AND THUMBNAIL

// Set array of all image and thumb directories
$directories = array(
	'',
	'/images',
	'/thumbs',
	'/images/item'.$item_id,
	'/thumbs/item'.$item_id
);

// Try to make the needed directories for image and thumb
$img_section = $database->get_one("SELECT img_section FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '".$section_id."'");

if ($img_section == 0) {
	foreach ($directories as $i => $directory) {
		$directory_path = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.$directory;
		make_dir($directory_path);

		// Save the directory pathes for later use
		if ($i == 3) $img_dir_path   = $directory_path;
		if ($i == 4) $thumb_dir_path = $directory_path;

		// Add index.php files if it is not existing yet
		if (!is_file($directory_path.'/index.php')) {
			$content = ''."<?php

header('Location: /index.php');

?>";
			$handle = fopen($directory_path.'/index.php', 'w');
			fwrite($handle, $content);
			fclose($handle);
			change_mode($directory_path.'/index.php', 'file');
		}
	}
}

// Get a file name
if (isset($_REQUEST['name'])) {
	$file = $_REQUEST['name'];
} elseif (!empty($_FILES)) {
	$file = $_FILES['file']['name'];
} else {
	$file = uniqid('file_');
}

// Check filename
$path_parts = pathinfo($file);
$filename   = $path_parts['basename'];
$fileext    = $path_parts['extension'];
$filename   = str_replace('.'.$fileext, '', $filename);  // Filename without extension
$filename   = str_replace(' ', '_', $filename);          // Replace spaces by underscores
$fileext    = strtolower($fileext);

// Make sure the image is a jpg or png file
if (!($fileext == 'jpg' || $fileext == 'jpeg' || $fileext == 'png')) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "'.html_entity_decode($MESSAGE['GENERIC_FILE_TYPES']).' .jpg, .jpeg, .png", "filename": "'.$file.'"}, "id" : "id"}');
}
// Check for invalid chars in filename
if (!preg_match('#^[a-zA-Z0-9._-]*$#', $filename)) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 201, "message": "'.html_entity_decode($MOD_ONEFORALL[$mod_name]['ERR_INVALID_FILE_NAME']).'.", "filename": "'.$file.'"}, "id" : "id"}');
}

// Path to the new image
$img_path = $img_dir_path.'/'.$filename.'.'.$fileext;

// Check if filename already exists
if (file_exists($img_path)) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 202, "message": "'.html_entity_decode($MESSAGE['MEDIA']['FILE_EXISTS']).'.", "filename": "'.$file.'"}, "id" : "id"}');
}


// Remove old temp files	
if ($cleanup_target_dir) {
	if (!is_dir($img_dir_path) || !$dir = opendir($img_dir_path)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmp_file_path = $img_dir_path.'/'.$file;

		// If temp file is current file proceed to the next
		if ($tmp_file_path == "{$img_path}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmp_file_path) < time() - $max_file_age)) {
			@unlink($tmp_file_path);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$img_path}.part", $chunks ? 'ab' : 'wb')) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES['file']['tmp_name'], 'rb')) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen('php://input', 'rb')) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	// Strip the temp .part suffix off 
	rename("{$img_path}.part", $img_path);
}



// CREATE A THUMB

// Get thumb size for this page
$resize = $database->get_one("SELECT resize FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '".$section_id."'");

if ($resize > 0) {

	// Thumbnail path
	$thumb_path = $thumb_dir_path.'/'.$filename.'.'.$fileext;

	// Check thumbnail type
	if ($fileext == 'png') {
		make_thumb_png($img_path, $thumb_path, $resize);
	} else {
		make_thumb($img_path, $thumb_path, $resize);
	}
	change_mode($thumb_path);
}



// RESIZE IMAGE

// Check if we need to resize the image
if ($imgresize == 1 && file_exists($img_path)) {

	// Check image type
	if ($fileext == 'png') {
		resizePNG($img_path, $img_path, $maxwidth, $maxheight);
	} else {
		resizeJPEG($img_path, $maxwidth, $maxheight, $quality);
	}
	change_mode($img_path);
}



// ADD IMAGE TO THE DATABASE

// Get image top position for this item
$top_position = $database->get_one("SELECT MAX(position) AS top_position FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '$item_id'");
// Increment position (db function returns NULL if this item has no image yet)
$top_position = intval($top_position) + 1;

// Insert filename into database
$filename = $filename.'.'.$fileext;
$database->query("INSERT INTO `".TABLE_PREFIX."mod_".$mod_name."_images` (item_id, filename, active, position) VALUES ('$item_id', '$filename', 1, '$top_position')");

// Get new image id
$img_id = $database->getLastInsertId();

// Check if there was a db error
if ($database->is_error()) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 300, "message": "Database error: '.$database->get_error().'"}, "id" : "id"}');
}



// SERVER RESPONSE

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id", "img_id" : "'.$img_id.'", "filename" : "'.$filename.'"}');



