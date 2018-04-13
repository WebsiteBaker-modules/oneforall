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

// Look for language file
if (is_readable(__DIR__.'/languages/EN.php')) {require(__DIR__.'/languages/EN.php');}
if (is_readable(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php')) {require(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php');}
if (is_readable(__DIR__.'/languages/'.LANGUAGE.'.php')) {require(__DIR__.'/languages/'.LANGUAGE.'.php');}

// Check if there is a start point defined
if (isset($_GET['p']) AND is_numeric($_GET['p']) AND $_GET['p'] >= 0) {
    $position = $_GET['p'];
} else {
    $position = 0;
}

// Get user's username, display name, email, and id - needed for insertion into item info
$users = array();
$query_users = $database->query('SELECT `user_id`,`username`,`display_name`,`email` FROM `'.TABLE_PREFIX.'users`');
if ($query_users->numRows() > 0) {
    while ($user = $query_users->fetchRow(MYSQLI_ASSOC)) {
        // Insert user info into users array
        $user_id = $user['user_id'];
        $users[$user_id]['username'] = $user['username'];
        $users[$user_id]['display_name'] = $user['display_name'];
        $users[$user_id]['email'] = $user['email'];
    }
}



// ITEM SCHEDULING
// ***************

// Enable / disable items automatically against a start and end time
if ($set_scheduling && file_exists($inc_path.'/scheduling.php')) {
    include('scheduling.php');
}


// SHOW OVERVIEW PAGE
// ******************

// Add a module wrapper to help with layout
$wrapper_start = "\n".'<div id="mod_'.$mod_name.'_wrapper_'.$section_id.'_f">'."\n";
$wrapper_end   = "\n".'</div> <!-- End of #mod_'.$mod_name.'_wrapper_'.$section_id.'_f -->'."\n";

if (!defined('ITEM_ID') OR !is_numeric(ITEM_ID)) {
    echo $wrapper_start;
    include('view_overview.php');
    echo $wrapper_end;
}



// SHOW ITEM DETAIL PAGE
// *********************

if (defined('ITEM_ID') && is_numeric(ITEM_ID)) {
    echo $wrapper_start;
    include('view_item.php');
    echo $wrapper_end;
}

?>