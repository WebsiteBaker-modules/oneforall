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
require_once($inc_path.'/config.php');

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
	color: orange;
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
$error    = 0;
$continue = true;
echo '<h1>Upgrading Module '.$mod_name.' (OneForAll)</h1>';
echo '<ol>';



// Check module name for not allowed characters
if ($continue) {

	echo '<li><span class="title">Check module name</span><br>';

	if (!preg_match('/^[a-zA-Z0-9_ -]{3,20}$/', $module_name)) {
		echo '<span class="bad">Allowed characters for the module name are a-z, A-Z, 0-9, - (hyphen), _ (underscore) and spaces.<br>Min 3, max 20 characters.<br>Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false; $error++;
	} else {
		echo '<span class="good">Module name &quot;'.$module_name.'&quot; is ok.</span>';
	}
	echo '</li>';
}



// Check if the module is registered in the database
if ($continue) {

	echo '<li><span class="title">Check if the module is registered in the database</span><br>';

	$query_tables = $database->query("SHOW TABLES LIKE '".TABLE_PREFIX."mod_".$mod_name."%'");
	if ($query_tables->numRows() >= 5) {
		echo '<span class="good">The module &quot;'.$module_name.'&quot; is registered in the database.</span>';
	} else {
		echo '<span class="bad">There is no module like &quot;'.$module_name.'&quot; registered in the database.<br>Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false; $error++;
	}
	echo '</li>';
}



// On manual installation rename module directory

// Old and new directory pathes
$old_dir = $inc_path;
$new_dir = WB_PATH.'/modules/'.$module_directory;

// Rename module directory only when manual installation
if ($continue && !isset($action)) {

	echo '<li><span class="title">On manual installation rename module directory</span><br>';

	// Check if the name of the module directory does not exist yet
	if ($old_dir != $new_dir && is_dir($new_dir)) {
		echo '<span class="bad">A module directory with the name &quot;'.$module_directory.'&quot; is already in use.<br>Please change the name of your module in the <code>info.php</code> file.</span>';
		$continue = false; $error++;
	} else {
		// Rename directory
		if (!rename($old_dir, $new_dir)) {
			echo '<span class="bad">'.$MESSAGE['MEDIA']['CANNOT_RENAME'].'</span>';
			$continue = false; $error++;
		} else {
			echo '<span class="good">Renamed module directory to &quot;'.$module_directory.'&quot; successfully.</span>';
		}
	}
	echo '</li>';
}




// *****************************************
// CONVERT SOME FILES TO THE NEW MODULE NAME
// *****************************************

if ($continue && $mod_name != 'oneforall') {

	// For css and js add 'mod_' to the module name
	$needle  = 'mod_oneforall';
	$replace = 'mod_'.$mod_name;


	// Convert the frontend stylesheet to the new module name
	echo '<li><span class="title">Convert the frontend stylesheet to the new module name</span><br>';

	$search_file   = 'frontend.css';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);

	// Check if the file is converted yet
	$num = substr_count($file_contents, $replace);
	if ($num > 0) {
		echo '<span class="ok">Found <strong>'.$num.' occurrences</strong> of &quot;'.$replace.'&quot;.<br>It seems that the file has already been converted</span><br><strong>or</strong><br><span class="bad">id and class names in your frontend stylesheet might be inconsistent.<br>Please check the file <code>'.$search_file.'</code> manually.</span>';
	} else {
		$file_contents = str_replace($needle, $replace, $file_contents, $count);
		// Write replaced string back to file
		if (file_put_contents($file_path, $file_contents) === false) {
			echo '<span class="bad">Failed to modify the frontend stylesheet <code>'.$search_file.'</code>.<br>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;'.$replace.'&quot;.</span>';
			$error++;
		} else {
			echo '<span class="good">Modified the frontend stylesheet <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
		}
	}
	echo '</li>';
	unset($file_contents);



	// Convert the backend stylesheet to the new module name
	echo '<li><span class="title">Convert the backend stylesheet to the new module name</span><br>';

	$search_file   = 'backend.css';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);

	// Check if the file is converted yet
	$num = substr_count($file_contents, $replace);
	if ($num > 0) {
		echo '<span class="ok">Found <strong>'.$num.' occurrences</strong> of &quot;'.$replace.'&quot;.<br>It seems that the file has already been converted</span><br><strong>or</strong><br><span class="bad"> id and class names in your backend stylesheet might be inconsistent.<br>Please check the file <code>'.$search_file.'</code> manually.</span>';
	} else {
		$file_contents = str_replace($needle, $replace, $file_contents, $count);
		// Write replaced string back to file
		if (file_put_contents($file_path, $file_contents) === false) {
			echo '<span class="bad">Failed to modify the backend stylesheet <code>'.$search_file.'</code>.<br>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;'.$replace.'&quot;.</span>';
			$error++;
		} else {
			echo '<span class="good">Modified the backend stylesheet <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
		}
	}
	echo '</li>';
	unset($file_contents);



	// Convert the backend javascript file to the new module name
	echo '<li><span class="title">Convert the backend javascript file to the new module name</span><br>';

	$search_file   = 'backend.js';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);

	// Check if the file is converted yet
	$num = substr_count($file_contents, $replace);
	if ($num > 0) {
		echo '<span class="ok">Found <strong>'.$num.' occurrences</strong> of &quot;'.$replace.'&quot;.<br>It seems that the file has already been converted</span><br><strong>or</strong><br><span class="bad"> id and class names in your backend javascript file might be inconsistent.<br>Please check the file <code>'.$search_file.'</code> manually.</span>';
	} else {
		$file_contents = str_replace($needle, $replace, $file_contents, $count);
		// Write replaced string back to file
		if (file_put_contents($file_path, $file_contents) === false) {
			echo '<span class="bad">Failed to modify the backend javascript file <code>'.$search_file.'</code>.<br>Please modify it manually by replacing the placeholders <code>'.$needle.'</code> by the new module name &quot;'.$replace.'&quot;.</span>';
			$error++;
		} else {
			echo '<span class="good">Modified the backend javascript file <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
		}
	}
	echo '</li>';
	unset($file_contents);



	// Convert the search file to the new module name
	echo '<li><span class="title">Convert the search file to the new module name</span><br>';

	// php file just uses the plain module name
	$needle        = 'oneforall';
	$replace       = $mod_name;
	$search_file   = 'search.php';
	$file_path     = $new_dir.'/'.$search_file;
	$file_contents = file_get_contents($file_path);

	// Check if the file is converted yet
	$num = substr_count($file_contents, $replace);
	if ($num > 0) {
		echo '<span class="ok">Found <strong>'.$num.' occurrences</strong> of &quot;'.$replace.'&quot;.<br>It seems that the file has already been converted</span><br><strong>or</strong><br><span class="bad"> your search file is inconsistent.<br>Please check the file <code>'.$search_file.'</code> manually.</span>';
	} else {
		$file_contents = str_replace($needle, $replace, $file_contents, $count);
		// Write replaced string back to file
		if (file_put_contents($file_path, $file_contents) === false) {
			echo '<span class="bad">Failed to modify the search file <code>'.$search_file.'</code>.<br>Please modify it manually by replacing the 2 placeholders <code>'.$needle.'</code> by the new module name &quot;'.$replace.'&quot;.</span>';
			$error++;
		} else {
			echo '<span class="good">Modified the search file <code>'.$search_file.'</code> successfully. Replaced &quot;'.$needle.'&quot; '.$count.' times.</span>';
		}
	}
	echo '</li>';
	unset($file_contents);
}




// *******************************************
// ONEFORALL UPGRADE STARTING FROM VERSION 0.5
// *******************************************

// Modify media field path
if ($continue) {
	echo '<li><span class="title">Modify media field path</span><br>';
	$query_fields = $database->query("SELECT field_id FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type = 'media'");
	if ($query_fields->numRows() > 0) {
		while ($field = $query_fields->fetchRow()) {
			$field_id = $field['field_id'];
			$sql = "UPDATE `".TABLE_PREFIX."mod_".$mod_name."_item_fields`
					SET value = REPLACE(value, '".MEDIA_DIRECTORY."', '')
					WHERE value LIKE '%media%'
					AND field_id = '".$field_id."'";
			if ($database->query($sql)) {
				echo '<span class="good">Changed media field id='.$field_id.' paths successfully to paths not containing the media directory &quot;'.MEDIA_DIRECTORY.'&quot;.</span>';
			} else {
				echo '<span class="bad">'.$database->get_error().'</span>';
				$error++;
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
	$parameter  = "TEXT NOT NULL AFTER `link`";

	echo '<li><span class="title">Add field &quot;'.$field_name.'&quot; to table &quot;'.$table_name.'&quot;</span><br>';

	// Check if field exists
	if (!$database->field_exists($table_name, $field_name)) {

		// Add it to the database
		$database->field_add($table_name, $field_name, $parameter);

		// Print error else success message
		if ($database->is_error()) {
			echo '<span class="bad">'.$database->get_error().'</span>';
			$error++;
		} else {
			echo '<span class="good">Added field &quot;'.$field_name.'&quot; successfully to table &quot;'.$table_name.'&quot;.</span>';
		}
	} else {
		// Field already exists
		echo '<span class="ok">Field &quot;'.$field_name.'&quot; already exists.</span>';
	}
	echo '</li>';
}




// *********************************************
// ONEFORALL UPGRADE STARTING FROM VERSION 1.0.0
// *********************************************

// Add field 'scheduling' to table 'items', if not exists
if ($continue) {

	$table_name = TABLE_PREFIX.'mod_'.$mod_name.'_items';
	$field_name = 'scheduling';
	$parameter  = "VARCHAR(255) NOT NULL DEFAULT '' AFTER `active`";

	echo '<li><span class="title">Add field &quot;'.$field_name.'&quot; to table &quot;'.$table_name.'&quot;</span><br>';

	// Check if field exists
	if (!$database->field_exists($table_name, $field_name)) {

		// Add it to the database
		$database->field_add($table_name, $field_name, $parameter);

		// Print error else success message
		if ($database->is_error()) {
			echo '<span class="bad">'.$database->get_error().'</span>';
			$error++;
		} else {
			echo '<span class="good">Added field &quot;'.$field_name.'&quot; successfully to table &quot;'.$table_name.'&quot;.</span>';
		}
	} else {
		// Field already exists
		echo '<span class="ok">Field &quot;'.$field_name.'&quot; already exists.</span>';
	}
	echo '</li>';
}



// Display only one oneforall item on detail pages even when we have multiple oneforall sections on a page
// Update all module access files and add the constant ITEM_SID (item section id)
if ($continue) {

	echo '<li><span class="title">Update all module access files and add the constant ITEM_SID (item section id)</span><br>';
	
	// Print warning if item detail pages are disabled in config.php
	if (!$view_detail_pages) {
		echo '<span class="warn"><strong>WARNING:</strong><br>The generation of item detail pages is disabled in config.php. Existing item access files will be deleted but not recreated.</span><br>If you need item detail pages edit the variable <code>$view_detail_pages</code> in the config.php file and run this upgrade script again.<br><br>';
	}

	// Get all items of this module
	$sql = "SELECT i.item_id, i.section_id, i.link AS item_link,
			p.page_id, p.link AS page_link
			FROM `".TABLE_PREFIX."mod_".$mod_name."_items` i
			INNER JOIN `".TABLE_PREFIX."sections` s
			ON i.section_id = s.section_id
			INNER JOIN `".TABLE_PREFIX."pages` p
			ON s.page_id = p.page_id
			WHERE s.module = '".$mod_name."'";
	$query_items = $database->query($sql);
	$num_items   = $query_items->numRows();
	$count_files = 0;

	if ($num_items > 0) {
		while ($item = $query_items->fetchRow()) {

			// Item data from db
			$item_id    = $item['item_id'];
			$section_id = $item['section_id'];
			$item_link  = $item['item_link'];
			$page_id    = $item['page_id'];
			$page_link  = $item['page_link'];

			// Generate access file only if the item link is existing
			if (!empty($item_link)) {

				// Access file name and full path to the access file
				$access_file = $item_link.PAGE_EXTENSION;
				$full_path   = WB_PATH.PAGES_DIRECTORY.$page_link.$access_file;

				// Delete existing access file
				if (is_writable($full_path)) {
					unlink($full_path);
					echo '<span class="good">Deleted old access file &quot;'.$access_file.'&quot; successfully.</span><br>';
				} else {
					echo '<span class="bad">Could not delete old access file &quot;'.$access_file.'&quot;.</span><br>';
				}

				// Only update module access files if item detail pages are enabled in config.php
				if ($view_detail_pages) {

					// The depth of the page directory in the directory hierarchy
					// 'PAGES_DIRECTORY' is at depth 1
					$pages_dir_depth = count(explode('/', $page_link))-1;
					// Work-out how many ../'s we need to get to the index page
					$index_location = '../';
					for ($i = 0; $i < $pages_dir_depth; $i++) {
						$index_location .= '../';
					}

					// Create new access file
					$content = ''.
'<?php
$page_id    = '.$page_id.';
$section_id = '.$section_id.';
$item_id    = '.$item_id.';
$item_sid   = '.$section_id.';
define("ITEM_ID",  $item_id);
define("ITEM_SID", $item_sid);
require("'.$index_location.'config.php");
require(WB_PATH."/index.php");
?>';
					if ($handle = fopen($full_path, 'w')) {
						fwrite($handle, $content);
						fclose($handle);
						change_mode($full_path);
						echo ' <span class="good">Created new access file &quot;'.$access_file.'&quot; successfully.</span><br><br>';
						$count_files++;
					} else {
						echo '<span class="bad">Could not create new access file &quot;'.$access_file.'&quot;.</span><br><br>';
						$error++;
					}
				}
			}
			else {
				echo '<span class="bad">Could not create new access file for <strong>item id '.$item_id.'</strong> since there is no item link saved in the database.</span><br>This might be because the generation of item detail pages had been disabled in config.php earlier.<br>You can fix it by <a href="'.WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id.'" title="Go to the item page and re-save it manually &hellip;" target="_blank">re-saving this item manually</a>.<br><br>';
			}
		}
	}

	// Feedback about number of created access files 
	if ($num_items == $count_files) {
		echo '<br><span class="good"><strong>Created all '.$num_items.' access files successfully.</strong></span>';
	} else {
		echo '<br><span class="ok"><strong>Created '.$count_files.' of '.$num_items.' new access files.</strong></span>';
	}
}




// **************************************
// STOP FOR DEBUGGING - DISPLAY ERROR LOG
// **************************************

if ($error == 0) {
	$go_to = '';
	$class = ' success';
	$title = 'Module '.$module_name.' upgraded successfully';
	$text  = 'Please check the upgrade log carefully.';
} else {
	$go_to = '?advanced';
	$class = ' error';
	$title = 'Error while upgrading module '.$module_name;
	$text  = 'Please check the upgrade log carefully and fix the reported error(s).<br><strong>Then try again using the manual execution of the module upgrade!</strong><br>See &quot;Add-ons&quot; &gt; &quot;Modules&quot; &gt; &quot;Advanced&quot; &gt; &quot;Execute module files manually&quot; &hellip;';
}
?>
</ol>
<div class="conclusion<?php echo $class ?>">
	<p class="conclusion_title"><?php echo $title ?></p>
	<p><?php echo $text ?><br><br><strong>Save a copy for later use!</strong> Then click &hellip;</p>
	<form action="">
		<input type="button" value="OK" onclick="location.href='index.php<?php echo $go_to; ?>'" style="width: 30%;">
	</form>
</div>
<?php

// Print admin footer
$admin->print_footer();
?>

<script language="javascript" type="text/javascript">
	stop();
</script>
