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


// *********************************************************************
//
// THIS CODE ENABLES / DISABLES ITEMS AUTOMATICALLY AGAINST A START AND END TIME
// 
// Please note:
// Enable scheduling in the config.php file
// Set your timezone difference in the config.php file if the wb constant DEFAULT_TIMEZONE returns unexpected values
//
// *********************************************************************


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit('Cannot access this file directly'); 
}



// GET THE START AND END TIMES AND ENABLE / DISABLE ALL ITEMS

// Timezone
$timezone = DEFAULT_TIMEZONE;
// Provided manually at the config.php
if (!empty($scheduling_timezone) && is_numeric($scheduling_timezone) && $scheduling_timezone > -13 && $scheduling_timezone < 15) {
	$timezone = $scheduling_timezone * 3600;
}

// Get the value of the scheduled start and end time
$sql = "SELECT item_id, scheduling
	FROM `".TABLE_PREFIX."mod_".$mod_name."_items`
	WHERE scheduling != ''
		AND (`scheduling` NOT LIKE '%\"start\";s:0:%' OR `scheduling` NOT LIKE '%\"end\";s:0:%')";
$query = $database->query($sql);

// Loop through all items
if ($query->numRows() > 0) {
	while ($row = $query->fetchRow(MYSQLI_ASSOC)) {
		$row = array_map('stripslashes', $row);

		// Item id (do not use $item_id otherwise it overwrites still needed values)
		$iid = (int)$row['item_id'];

		// Value is serialized, we have to unserialize it
		$unserialized = @unserialize($row['scheduling']);
		if ($unserialized !== false || $row['scheduling'] == 'b:0;') {
			$scheduling = $unserialized;
		}

		// Current time
		$now    = time() + $timezone;
		$active = null;

		// Start and end time given
		if (!empty($scheduling['ts_start']) && !empty($scheduling['ts_end'])) {
			$active = ($now >= $scheduling['ts_start'] && $now < $scheduling['ts_end']) ? 1 : 0;
		}
		// Only start time given
		elseif (!empty($scheduling['ts_start'])) {
			$active = $now >= $scheduling['ts_start'] ? 1 : 0;
		}
		// Only end time given
		elseif (!empty($scheduling['ts_end'])) {
			$active = $now >= $scheduling['ts_end']   ? 0 : 1;
		}

		// Update db field active
		if ($active !== null) {
			$database->query("UPDATE
				`".TABLE_PREFIX."mod_".$mod_name."_items`
				SET active = '$active'
				WHERE item_id = '$iid';");
		}

		// Debug
		if ($scheduling_debug) {
			$f = DEFAULT_DATE_FORMAT.' '.DEFAULT_TIME_FORMAT; // "F j, Y, g:i a"

			// Time
			if (!isset($hide_time)) {
				echo '<b>DEBUG ITEM SCHEDULING</b><br>';
				echo 'GMT is '.gmdate($f).'<br>';
				echo 'Your timezone adjustment is '.$timezone.'s<br>';
				echo 'Your current time is '.gmdate($f, $now).'<br><br>';
				$hide_time = true;
			}

			// Item scheduling settings and status
			echo '<b>DEBUG ITEM '.$iid.'</b><br>';
			$start = empty($scheduling['ts_start']) ? '---- (no setting)' : gmdate($f, $scheduling['ts_start']);
			echo 'Enable item at '.$start.'<br>';
			$end   = empty($scheduling['ts_end'])   ? '---- (no setting)' : gmdate($f, $scheduling['ts_end']);
			echo 'Disable item at '.$end.'<br>';
			$ts_start = null;
			$ts_end   = null;

			$active = $database->get_one("SELECT active FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE item_id = '$iid';");
			$status = $active ? 'enabled' : 'disabled';
			$color  = $active ? 'green'   : 'red';
			echo 'Item '.$iid.' is <span style="color: '.$color.';">'.$status.'</span>.<br><br>';
		}
	}
}	
	
return;
