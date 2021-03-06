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

// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
// Get module name
require($inc_path.'/info.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');

// Get new order
$order = new order(TABLE_PREFIX.'mod_'.$mod_name.'_items', 'position', 'item_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
#$database->query("INSERT INTO `".TABLE_PREFIX."mod_".$mod_name."_items` (section_id,page_id,position,active) VALUES ('$section_id','$page_id','$position','1')");
$Sql =  'INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` SET '
                   .  '`section_id`= '.$section_id.' , '
                   .  '`page_id`= '.$page_id.', '
                   .  '`position`= '.$position.', '
                   .  '`active`= 1, '
                   .  '`link` = \'\', '
                   .  '`description` = \'\' ';
$database->query($Sql);

// Get the id
$item_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Say that a new record has been added, then redirect to modify page
if ($database->is_error()) {
    $admin->print_error($database->get_error(), WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id);
} else {
    $admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id.'&from=add_item');
}

// Print admin footer
$admin->print_footer();

?>