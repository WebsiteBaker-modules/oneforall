<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2015, Christoph Marti

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
	require('../../config.php');
	// Include path
	$inc_path = dirname(__FILE__);
	// Get module name and config
	require_once($inc_path.'/info.php');
	require_once($inc_path.'/config.php');

	// Load admin class
	require_once(WB_PATH.'/framework/class.admin.php');
	$admin = new admin('Modules', 'module_view', false, false);

	// Check if module name in file info.php and post array match
	if ($mod_name != $_POST['mod_name']) {
		die();
	}

	// Check if module is registered in the database
	$addon_id = $database->get_one("SELECT `addon_id` FROM `".TABLE_PREFIX."addons` WHERE `type` = 'module' AND `directory` = '".$mod_name."'");
	if (!is_numeric($addon_id)) {
		die();
	}

	// Check if user has permissions to access the module
	if (!($admin->is_authenticated() && $admin->get_permission($mod_name, 'module'))) {
		die();
	}

	// Set the new item positions depending on asc (post array) or desc (post array reverse)
	$positions = $_POST['id'];
	if (!$order_by_position_asc) {
		$positions = array_reverse($positions);
	}
	foreach ($positions as $position => $item_id) {
		$database->query("UPDATE `".TABLE_PREFIX."mod_".$mod_name."_items` SET `position` = ".(int)$position." WHERE `item_id` = ".(int)$item_id);
	}
}
?>