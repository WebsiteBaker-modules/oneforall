<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2017, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/


if (!isset($_POST['action']) || $_POST['action'] != 'update_pos' || !isset($_POST['id']) || !isset($_POST['mod_name'])) {
	die();	
}
else {
	// Include
	require('../../../config.php');
	// Include path
	$inc_path = dirname(dirname(__FILE__));
	// Get module name and config
	require_once($inc_path.'/info.php');

	// Load admin class
	require_once(WB_PATH.'/framework/class.admin.php');
	$admin = new admin('Modules', 'module_view', false, false);

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

	// Set the new image positions
	$positions = $_POST['id'];
	foreach ($positions as $position => $img_id) {
		$database->query("UPDATE `".TABLE_PREFIX."mod_".$mod_name."_images` SET position = ".(int)$position." + 1 WHERE img_id = ".(int)$img_id);
	}
}
?>