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


if (!defined('SYSTEM_RUN')) {require( (dirname(dirname((__DIR__)))).'/config.php');}

// Get id
if (!isset($_GET['item_id']) OR !is_numeric($_GET['item_id'])) {
    header("Location: ".ADMIN_URL."/pages/index.php");
    exit();
} else {
    $item_id = $_GET['item_id'];
}

// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
// Get module name and config
require($inc_path.'/info.php');

// Include WB admin wrapper script and WB functions
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');
if (!function_exists('make_dir')){require(WB_PATH.'/framework/functions.php');}

// Get item details
$query_details = $database->query('SELECT * FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` WHERE `item_id` = '.(int)$item_id.' ');
if ($query_details->numRows() > 0) {
    $get_details = $query_details->fetchRow(MYSQLI_ASSOC);
} else {
    $admin->print_error($TEXT['NOT_FOUND'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Unlink item access file only if the link has initially been stored
if (!empty($get_details['link'])) {
    $item_link = WB_PATH.PAGES_DIRECTORY.$page['link'].$get_details['link'].PAGE_EXTENSION;
    if (is_writable($item_link)) {
        unlink($item_link);
    }
}

// Delete any images if they exists
$image = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id;
$thumb = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id;
if (is_dir($image)) { rm_full_dir($image); }
if (is_dir($thumb)) { rm_full_dir($thumb); }

// Delete item, item fields and item images
$database->query('DELETE FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` WHERE `item_id` = '.(int)$item_id.' LIMIT 1');
$database->query('DELETE FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` WHERE `item_id` = '.(int)$item_id.' ');
$database->query('DELETE FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` WHERE `item_id` = '.(int)$item_id.' ');

// Clean up ordering
require(WB_PATH.'/framework/class.order.php');
$order = new order(TABLE_PREFIX.'mod_'.$mod_name.'_items', 'position', 'item_id', 'section_id');
$order->clean($section_id);

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
    $admin->print_error($database->get_error(), WB_URL.'/modules/modify_post.php?page_id='.$page_id.'&item_id='.$item_id);
} else {
    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>