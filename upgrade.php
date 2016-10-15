<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2016, Christoph Marti

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


//Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Database
global $database;

// Include path
$inc_path = dirname(__FILE__);
// Get module name and config
require_once($inc_path.'/info.php');

// Setup styles to help id errors
echo '
<style type="text/css">
h1 {
	color: #333;
	margin: 60px 0 20px 0;
}
li {
	margin-bottom: 14px;
}
.good {
	color: green;
}
.bad {
	color: red;
}
.ok {
	color: blue;
}
.warn {
	color: yellow;
}
.conclusion {
	margin-top: 60px;
	padding: 15px 10px;
	text-align: center;
	color: blue;
	border: solid 1px blue;
	background-color: #DCEAFE;
}
.error {
	color: red;
	border: solid 1px red;
	background-color: #FFDCD9;
}
.success {
	color: green;
	border: solid 1px green;
	background-color: #D4FFD1;
}
.conclusion_title {
	font-weight: bold;
	font-size: 14px;
	margin-bottom: 5px;
}
</style>
';




// ***********************
// RENAME MODULE IF NEEDED
// ***********************


// Start
$continue = true;
echo '<h1>Upgrading Module '.$mod_name.' (OneForAll)</h1>';
echo '<ol>';



// Check module name for not allowed characters
if ($continue) {

	echo '<li><span class="title">Check module name</span><br />';

	if (!preg_match('/^[a-zA-Z0-9_ -]{3,20}$/', $module_name)) {
		echo '<span class="bad">Allowed characters for the module name are a-z, A-Z, 0-9, - (hyphen), _ (underscore) and spaces.<br />Min 3, max 20 characters.<br />Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false;
	} else {
		echo '<span class="good">Module name &quot;'.$module_name.'&quot; is ok.</span>';
	}
	echo '</li>';
}



// Check if the module is registered in the database
if ($continue) {

	echo '<li><span class="title">Check if the module is registered in the database</span><br />';

	$query_tables = $database->query("SHOW TABLES LIKE '".TABLE_PREFIX."mod_".$mod_name."%'");
	if ($query_tables->numRows() == 5) {
		echo '<span class="good">The module &quot;'.$module_name.'&quot; is registered in the database.</span>';
	} else {
		echo '<span class="bad">There is no module like &quot;'.$module_name.'&quot; registered in the database.<br />Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false;
	}
	echo '</li>';
}



// On manual installation rename module directory

// Old and new directory pathes
$old_dir = $inc_path;
$new_dir = WB_PATH.'/modules/'.$module_directory;

// Rename module directory only when manual installation
if ($continue && !isset($action)) {

	echo '<li><span class="title">On manual installation rename module directory</span><br />';

	// Check if the name of the module directory does not exist yet
	if ($old_dir != $new_dir && is_dir($new_dir)) {
		echo '<span class="bad">A module directory with the name &quot;'.$module_directory.'&quot; is already in use.<br />Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false;
	} else {
		// Rename directory
		if (!rename($old_dir, $new_dir)) {
			echo '<span class="bad">'.$MESSAGE['MEDIA']['CANNOT_RENAME'].'</span>';
			$continue = false;
		} else {
			echo '<span class="good">Renamed module directory to &quot;'.$module_directory.'&quot; successfully.</span>';
		}
	}
	echo '</li>';
}



// Adopt some files to the new module name
if ($continue && $mod_name != 'oneforall') {

	// Adopt the frontend stylesheet to the new module name
	echo '<li><span class="title">Adopt the frontend stylesheet to the new module name</span><br />';

	$search_file   = 'frontend.css';
	$needle        = 'mod_oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, 'mod_'.$mod_name, $file_contents, $count);
	// Write replaced string back to file
	if (file_put_contents($file_path, $file_contents) === false) {
		echo '<span class="bad">Failed to modify the frontend stylesheet <code>'.$search_file.'</code>.<br />Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;mod_'.$mod_name.'&quot;.</span>';
		$continue = false;
	} else {
		echo '<span class="good">Modified the frontend stylesheet <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
	}
	echo '</li>';
	unset($file_contents);


	// Adopt the search file to the new module name
	echo '<li><span class="title">Adopt the search file to the new module name</span><br />';

	$search_file   = 'search.php';
	$needle        = 'oneforall';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);
	$file_contents = str_replace($needle, $mod_name, $file_contents, $count);
	// Write replaced string back to file
	if (file_put_contents($file_path, $file_contents) === false) {
		echo '<span class="bad">Failed to modify the search file <code>'.$search_file.'</code>.<br />Please modify it manually by replacing the 2 placeholders <code>'.$needle.'</code> by the new module name &quot;'.$mod_name.'&quot;.</span>';
		$continue = false;
	} else {
		echo '<span class="good">Modified the search file <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
	}
	echo '</li>';
	unset($file_contents);
}




// *******************************************
// ONEFORALL UPGRADE STARTING FROM VERSION 0.5
// *******************************************

// Modify media field path
if ($continue) {
	echo '<li><span class="title">Modify media field path</span><br />';
	$query_fields = $database->query("SELECT field_id FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type = 'media'");
	if ($query_fields->numRows() > 0) {
		while ($field = $query_fields->fetchRow()) {
			$field_id = $field['field_id'];
			$sql = "UPDATE `".TABLE_PREFIX."mod_".$mod_name."_item_fields
					SET value = REPLACE(value, '".MEDIA_DIRECTORY."', '')
					WHERE value LIKE '%media%'
					AND field_id = '".$field_id."'";
			if ($database->query($sql)) {
				echo '<span class="good">Changed media field id='.$field_id.' paths successfully to paths not containing the media directory &quot;'.MEDIA_DIRECTORY.'&quot;.</span>';
			} else {
				echo '<span class="bad">'.$database->get_error().'</span>';
				$continue = false;
			}
		}
	} else {
		echo '<span class="ok">No media fields found. Update not needed.</span>';
	}
	echo '</li>';
}


// Add field 'description' to table 'items', if not exists
if ($continue) {

	$table_name = TABLE_PREFIX.'mod_'.$mod_name.'_items';
	$field_name = 'description';
	$parameter  = "TEXT NOT NULL DEFAULT '' AFTER `link`";
	
	echo '<li><span class="title">Add field &quot;'.$field_name.'&quot; to table &quot;'.$table_name.'&quot;</span><br />';

	// Check if field exists
	if (!$database->field_exists($table_name, $field_name)) {

		// Add it to the database
		$database->field_add($table_name, $field_name, $parameter);

		// Print error else success message
		if ($database->is_error()) {
			echo '<span class="bad">'.$database->get_error().'</span>';
			$continue = false;
		} else {
			echo '<span class="good">Added field &quot;'.$field_name.'&quot; successfully to table &quot;'.$table_name.'&quot;.</span>';
		}
	} else {
		// Field already exists
		echo '<span class="ok">Field "'.$field_name.'" already exists.</span>';
	}
	echo '</li>';
}







// **************************************
// STOP FOR DEBUGGING - DISPLAY ERROR LOG
// **************************************

if ($continue) {
	$class = ' success';
	$title = 'Module '.$module_name.' upgraded successfully';
	$text  = 'Please check the upgrade log carefully.';
} else {
	$class = ' error';
	$title = 'Error while upgrading module '.$module_name;
	$text  = 'Please check the upgrade log carefully and fix the reported error. Then try again!';
}
?>
</ol>
<div class="conclusion<?php echo $class ?>">
	<p class="conclusion_title"><?php echo $title ?></p>
	<p><?php echo $text ?> <strong>Save a copy for later use!</strong> Then click &hellip;</p>
	<form action="">
		<input type="button" value="OK" onclick="location.href='index.php'" style="width: 30%;">
	</form>
</div>
<?php

// Print admin footer
$admin->print_footer();
?>

<script language="javascript" type="text/javascript">
	stop();
</script>
