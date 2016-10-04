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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit('Cannot access this file directly'); 
}

// Include path
$inc_path = dirname(__FILE__);

// Get module name and config
require_once($inc_path.'/info.php');
require_once($inc_path.'/config.php');

// Look for language file
if (LANGUAGE_LOADED) {
    include($inc_path.'/languages/EN.php');
    if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
        include($inc_path.'/languages/'.LANGUAGE.'.php');
    }
}

// Check if there is a start point defined
if (isset($_GET['p']) AND is_numeric($_GET['p']) AND $_GET['p'] >= 0) {
	$position = $_GET['p'];
} else {
	$position = 0;
}

// Get user's username, display name, email, and id - needed for insertion into item info
$users = array();
$query_users = $database->query("SELECT user_id,username,display_name,email FROM ".TABLE_PREFIX."users");
if ($query_users->numRows() > 0) {
	while ($user = $query_users->fetchRow()) {
		// Insert user info into users array
		$user_id = $user['user_id'];
		$users[$user_id]['username'] = $user['username'];
		$users[$user_id]['display_name'] = $user['display_name'];
		$users[$user_id]['email'] = $user['email'];
	}
}

// Add a wrapper for OneForAll to help with layout
echo "\n<div id='mod_".$mod_name."_wrapper_f'>\n";
$end_of_wrapper = "\n</div> <!-- End of ".$mod_name." wrapper -->\n";



// SHOW OVERVIEW PAGE
// ******************

if (!defined('ITEM_ID') OR !is_numeric(ITEM_ID)) {
	include('view_overview.php');
	echo $end_of_wrapper;  // End of oneforall wrapper
}



// SHOW ITEM DETAIL PAGE
// *********************

elseif (defined('ITEM_ID') AND is_numeric(ITEM_ID)) {
	include('view_item.php');
	echo $end_of_wrapper;  // End of oneforall wrapper
}


?>