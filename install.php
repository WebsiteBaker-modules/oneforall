<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2017, Christoph Marti

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


// Include path
$inc_path = dirname(__FILE__);
// Include module info to get module name
require_once($inc_path.'/info.php');


// Check module name for not allowed characters
if (!preg_match('/^[a-zA-Z0-9_ -]{3,20}$/', $module_name)) {
	$admin->print_error('Allowed characters for the module name are a-z, A-Z, 0-9, - (hyphen), _ (underscore) and spaces.<br>Min 3, max 20 characters.', ADMIN_URL.'/modules/index.php?advanced');
}


// Check if there is not yet a media directory using the module name
if (is_dir(WB_URL.MEDIA_DIRECTORY.'/'.$module_name)) {
	$admin->print_error('<p style="font-weight: bold;">A media directory with the name &quot;'.$module_name.'&quot; is already in use.</p><p>Please change the name of your module in the <code>info.php</code> file.</p>', ADMIN_URL.'/modules/index.php?advanced');
}


// Old and new directory pathes
$old_dir = dirname(__FILE__);
$new_dir = WB_PATH.'/modules/'.$module_directory;

// Rename module directory only when manual installation
if (!isset($action)) {

	// Check if the name of the module directory does not exist yet
	if ($old_dir != $new_dir && is_dir($new_dir)) {
		$admin->print_error('<p style="font-weight: bold;">A module directory with the name &quot;'.$module_directory.'&quot; is already in use.</p><p>Please change the name of your module in the <code>info.php</code> file.</p>', ADMIN_URL.'/modules/index.php?advanced');
	}

	// Rename directory
	if (!rename($old_dir, $new_dir)) {
		$admin->print_error($MESSAGE['MEDIA']['CANNOT_RENAME'], ADMIN_URL.'/modules/index.php?advanced');
	}
}


// If module has been renamed some files have to be converted
if ($mod_name != 'oneforall') {

	// Convert the frontend stylesheet to the new module name
	$search_file   = 'frontend.css';
	$needle        = 'mod_oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, 'mod_'.$mod_name, $file_contents, $count);
	// Write replaced string back to file
	if (file_put_contents($file_path, $file_contents) === false) {
		$admin->print_error('<p style="font-weight: bold;">Failed to modify the frontend stylesheet <code>'.$search_file.'</code>.</p><p>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;mod_'.$mod_name.'&quot;.</p>', ADMIN_URL.'/modules/index.php?advanced');
	}
	unset($file_contents);

	// Convert the backend stylesheet to the new module name
	$search_file   = 'backend.css';
	$needle        = 'mod_oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, 'mod_'.$mod_name, $file_contents, $count);
	// Write replaced string back to file
	if (file_put_contents($file_path, $file_contents) === false) {
		$admin->print_error('<p style="font-weight: bold;">Failed to modify the backend stylesheet <code>'.$search_file.'</code>.</p><p>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;mod_'.$mod_name.'&quot;.</p>', ADMIN_URL.'/modules/index.php?advanced');
	}
	unset($file_contents);

	// Convert the backend javascript file to the new module name
	$search_file   = 'backend.js';
	$needle        = 'mod_oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, 'mod_'.$mod_name, $file_contents, $count);
	// Write replaced string back to file
	if (file_put_contents($file_path, $file_contents) === false) {
		$admin->print_error('<p style="font-weight: bold;">Failed to modify the backend javascript file <code>'.$search_file.'</code>.</p><p>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;mod_'.$mod_name.'&quot;.</p>', ADMIN_URL.'/modules/index.php?advanced');
	}
	unset($file_contents);

	// Convert the search file to the new module name
	$search_file   = 'search.php';
	$needle        = 'oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, $mod_name, $file_contents, $count);
	// Write replaced string back to file
	if ($count == 2) {
		if (file_put_contents($file_path, $file_contents) === false) {
			$admin->print_error('<p style="font-weight: bold;">Failed to modify the search file <code>'.$search_file.'</code>.</p><p>Please modify it manually by replacing the 2 placeholders <code>'.$needle.'</code> by the new module name &quot;'.$mod_name.'&quot;.</p>', ADMIN_URL.'/modules/index.php?advanced');
	}
	// No placeholders found - no need to change anything
	} else {
		unset($file_contents);
	}
}


// Create module database tables
if (defined('WB_URL')) {

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_".$mod_name."_fields`");
	$mod_oneforall = "CREATE TABLE `".TABLE_PREFIX."mod_".$mod_name."_fields` ( "
			. "`field_id` INT(11) NOT NULL AUTO_INCREMENT,"
			. "`position` INT(11) NOT NULL DEFAULT '0',"
			. "`type` VARCHAR(50) NOT NULL DEFAULT '',"
			. "`extra` TEXT NOT NULL,"
			. "`name` VARCHAR(50) NOT NULL DEFAULT '',"
			. "`label` VARCHAR(50) NOT NULL DEFAULT '',"
			. "`template` TEXT NOT NULL,"
			. "PRIMARY KEY (`field_id`),"
			. "UNIQUE KEY `NAME` (`name`)"
			. " )";
	$database->query($mod_oneforall);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_".$mod_name."_images`");
	$mod_oneforall = "CREATE TABLE `".TABLE_PREFIX."mod_".$mod_name."_images` ( "
			. "`img_id` INT(11) NOT NULL AUTO_INCREMENT,"
			. "`item_id` INT(11) NOT NULL DEFAULT '0',"
			. "`filename` VARCHAR(150) NOT NULL DEFAULT '',"
			. "`active` ENUM('1','0') NOT NULL DEFAULT '1',"
			. "`position` INT(11) NOT NULL DEFAULT '0',"
			. "`alt` VARCHAR(255) NOT NULL DEFAULT '',"
			. "`title` VARCHAR(255) NOT NULL DEFAULT '',"
			. "`caption` TEXT NOT NULL,"
			. "PRIMARY KEY (`img_id`)"
			. " )";
	$database->query($mod_oneforall);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_".$mod_name."_items`");
	$mod_oneforall = "CREATE TABLE `".TABLE_PREFIX."mod_".$mod_name."_items` ( "
			. "`item_id` INT(11) NOT NULL AUTO_INCREMENT,"
			. "`section_id` INT(11) NOT NULL DEFAULT '0',"
			. "`page_id` INT(11) NOT NULL DEFAULT '0',"
			. "`group_id` INT(11) NOT NULL DEFAULT '0',"
			. "`active` INT(11) NOT NULL DEFAULT '0',"
			. "`scheduling` VARCHAR(255) NOT NULL DEFAULT '',"
			. "`position` INT(11) NOT NULL DEFAULT '0',"
			. "`title` VARCHAR(255) NOT NULL DEFAULT '',"
			. "`link` TEXT NOT NULL,"
			. "`description` TEXT NOT NULL,"
			. "`main_image` VARCHAR(50) NOT NULL DEFAULT '',"
			. "`modified_when` INT(11) NOT NULL DEFAULT '0',"
			. "`modified_by` INT(11) NOT NULL DEFAULT '0',"
			. "PRIMARY KEY (`item_id`)"
			. " )";
	$database->query($mod_oneforall);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_".$mod_name."_item_fields`");
	$mod_oneforall = "CREATE TABLE `".TABLE_PREFIX."mod_".$mod_name."_item_fields` ( "
			. "`item_id` INT(11) NOT NULL DEFAULT '0',"
			. "`field_id` INT(11) NOT NULL DEFAULT '0',"
			. "`value` TEXT NOT NULL,"
			. "UNIQUE KEY `FIELD` (`item_id`,`field_id`)"
			. " )";
	$database->query($mod_oneforall);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_".$mod_name."_page_settings`");
	$mod_oneforall = "CREATE TABLE `".TABLE_PREFIX."mod_".$mod_name."_page_settings` ( "
			. "`section_id` INT(11) NOT NULL DEFAULT '0',"
			. "`page_id` INT(11) NOT NULL DEFAULT '0',"
			. "`header` TEXT NOT NULL,"
			. "`item_loop` TEXT NOT NULL,"
			. "`footer` TEXT NOT NULL,"
			. "`item_header` TEXT NOT NULL,"
			. "`item_footer` TEXT NOT NULL,"
			. "`items_per_page` INT(11) NOT NULL DEFAULT '0',"
			. "`resize` INT(11) NOT NULL DEFAULT '100',"
			. "`lightbox2` VARCHAR(10) NOT NULL DEFAULT 'detail',"
			. "`img_section` enum('0','1') NOT NULL DEFAULT '0',"
			. "PRIMARY KEY (`section_id`)"
			. " )";
	$database->query($mod_oneforall);
}



// Update db addons table

// Delete not existing module oneforall from database
if (!is_dir(WB_PATH.'/modules/oneforall')) {
	$sql = "DELETE FROM `".TABLE_PREFIX."addons` WHERE `type` = 'module' and `directory` = 'oneforall'";
	$database->query($sql);
}
// Include WB functions...
require_once(WB_PATH.'/framework/functions.php');
// then load the renamed module into the addons table
load_module(WB_PATH.'/modules/'.$mod_name);

?>
