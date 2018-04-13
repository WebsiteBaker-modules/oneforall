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


// Prevent this file from being accessed directly
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}

// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
// Get module name and config
require($inc_path.'/info.php');
require($inc_path.'/config.php');

// Include WB functions
if (!function_exists('make_dir')){require(WB_PATH.'/framework/functions.php');}

// No need to delete item access directory or files
// since all module pages have to be deleted manually before uninstall

// Delete module image directory
$directory = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name;
if (is_dir($directory)) {
    rm_full_dir($directory);
}

// Drop module tables
$database->query('DROP TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_fields`');
$database->query('DROP TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_images`');
$database->query('DROP TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_items`');
$database->query('DROP TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields`');
$database->query('DROP TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_page_settings`');

