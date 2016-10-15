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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit('Cannot access this file directly');
}


// Include path
$inc_path = dirname(__FILE__);
// Get module name
require_once($inc_path.'/info.php');

// Include WB functions
require_once(WB_PATH.'/framework/functions.php');

// Delete item access file, images and thumbs associated with the section
$query_items = $database->query("SELECT i.item_id, i.link AS item_link, p.link AS page_link FROM `".TABLE_PREFIX."mod_".$mod_name."_items` i INNER JOIN `".TABLE_PREFIX."pages` p ON i.page_id = p.page_id WHERE i.section_id = '$section_id'");

if ($query_items->numRows() > 0) {
	while ($item = $query_items->fetchRow()) {
		// Delete item access files
		$access_file = WB_PATH.PAGES_DIRECTORY.$item['page_link'].$item['item_link'].PAGE_EXTENSION;
		if (is_writable($access_file)) {
			unlink($access_file);
		}
		// Delete any images if they exists
		$image_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item['item_id'];
		$thumb_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item['item_id'];
		if (is_dir($image_dir)) {
			rm_full_dir($image_dir);
		}
		if (is_dir($thumb_dir)) {
			rm_full_dir($thumb_dir);
		}
		// Delete images in db
		$database->query("DELETE FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '".$item['item_id']."'");
		// Delete item fields in db
		$database->query("DELETE FROM `".TABLE_PREFIX."mod_".$mod_name."_item_fields` WHERE item_id = '".$item['item_id']."'");
	}
}

// Delete items and page settings in db
$database->query("DELETE FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id'");
$database->query("DELETE FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '$section_id'");

?>