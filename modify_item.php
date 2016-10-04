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

require('../../config.php');

// Make use of the skinable backend themes of WB > 2.7
// Check if THEME_URL is supported otherwise use ADMIN_URL
if (!defined('THEME_URL')) {
	define('THEME_URL', ADMIN_URL);
}

// Include path
$inc_path = dirname(__FILE__);
// Get module name and config
require_once($inc_path.'/info.php');
require_once($inc_path.'/config.php');
// Include functions file
require_once($inc_path.'/functions.php');

// Look for language File
if (LANGUAGE_LOADED) {
	require_once($inc_path.'/languages/EN.php');
	if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
		require_once($inc_path.'/languages/'.LANGUAGE.'.php');
	}
}

// Get id
if (!isset($_GET['item_id']) OR !is_numeric($_GET['item_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$item_id = $_GET['item_id'];
}
// Get from
if (isset($_GET['from']) AND $_GET['from'] == 'add_item') {
	$show_item_mover = false;
} else {
	$show_item_mover = true;
}


// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Get item
$query_item = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE item_id = '$item_id'");
$fetch_item = $query_item->fetchRow();
$fetch_item = array_map('stripslashes', $fetch_item);
$fetch_item = array_map('htmlspecialchars', $fetch_item);

// Prepopulate the text fields with previously entered item data when it has been submitted incorrectely
if (isset($_SESSION[$mod_name]['item']) && is_array($_SESSION[$mod_name]['item'])) {
	array_walk_recursive($_SESSION[$mod_name]['item']['fields'], function (&$value) {
    	$value = htmlspecialchars($value);
	});
	$fetch_item['images']         = $_SESSION[$mod_name]['item']['images'];
	$img_resize['imgresize']      = $_SESSION[$mod_name]['item']['imgresize'];
	$img_resize['quality']        = htmlspecialchars($_SESSION[$mod_name]['item']['quality']);
	$img_resize['maxheight']      = htmlspecialchars($_SESSION[$mod_name]['item']['maxheight']);
	$img_resize['maxwidth']       = htmlspecialchars($_SESSION[$mod_name]['item']['maxwidth']);
	$fetch_item['active']         = htmlspecialchars($_SESSION[$mod_name]['item']['active']);
	$fetch_item['new_section_id'] = $_SESSION[$mod_name]['item']['new_section_id'];
	$fetch_item['action']         = $_SESSION[$mod_name]['item']['action'];	
	unset($_SESSION[$mod_name]['item']);
}

// Get page setting if we should show / hide item image section
$setting_img_section = $database->get_one("SELECT img_section FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '$section_id'");


?>

<h2>1. <?php echo $TEXT['ADD'].'/'.$TEXT['MODIFY'].' '.$MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?></h2>

<form name="modify" action="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/save_item.php" method="post" enctype="multipart/form-data" style="margin: 0;">

<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
<input type="hidden" name="link" value="<?php echo $page['link'].$fetch_item['link']; ?>" />

<table id="modify_item" cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td width="20%" align="right"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_TITLE']; ?>:</td>
		<td>
			<input type="text" name="title" id="title" style="width: 98%;" maxlength="150" value="<?php echo $fetch_item['title']; ?>" />
		</td>
	</tr>
	<tr>
		<td width="20%" align="right"><?php echo $TEXT['ACTIVE']; ?>:</td>
		<td>
			<input type="radio" name="active" id="active_true" value="1" <?php if ($fetch_item['active'] == 1) { echo " checked='checked'"; } ?> />
			<label for="active_true"><?php echo $TEXT['YES']; ?></label>
			&nbsp;
			<input type="radio" name="active" id="active_false" value="0" <?php if ($fetch_item['active'] == 0) { echo " checked='checked'"; } ?> />
			<label for="active_false"><?php echo $TEXT['NO']; ?></label>
		</td>
	</tr>
<?php


// Only show item mover for existing items
if ($show_item_mover) {
?>
	<tr>
		<td width="20%" align="right"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM_TO_PAGE']; ?>... </td>
		<td>
	<?php
	// OneForAll page list
	$query_pages = "SELECT p.page_id, p.page_title, p.visibility, p.admin_groups, p.admin_users, p.viewing_groups, p.viewing_users, s.section_id FROM ".TABLE_PREFIX."pages p INNER JOIN ".TABLE_PREFIX."sections s ON p.page_id = s.page_id WHERE s.module = '".$mod_name."' AND p.visibility != 'deleted' ORDER BY p.level, p.position ASC";
	$get_pages = $database->query($query_pages);
	
	if ($get_pages->numRows() > 0) {
		// Generate sections select
		echo "<select name='new_section_id' style='width: 240px'>\n";
		while($page = $get_pages->fetchRow()) {
			$page = array_map('stripslashes', $page);
			// Only display if visible
			if ($admin->page_is_visible($page) == false)
				continue;
			// Get user perms
			$admin_groups = explode(',', str_replace('_', '', $page['admin_groups']));
			$admin_users  = explode(',', str_replace('_', '', $page['admin_users']));
			// Check user perms
			$in_group = false;
			foreach ($admin->get_groups_id() as $cur_gid){
				if (in_array($cur_gid, $admin_groups)) {
					$in_group = true;
				}
			}
			if (($in_group) OR is_numeric(array_search($admin->get_user_id(), $admin_users))) {
				$can_modify = true;
			} else {
				$can_modify = false;
			}
			// Options
			echo "<option value='{$page['section_id']}'";
			echo $fetch_item['section_id'] == $page['section_id'] ? " selected='selected'" : "";
			echo $can_modify == false ? " disabled='disabled' style='color: #aaa;'" : "";
			echo ">{$page['page_title']}</option>\n";
			// Prepare prechecked radio buttons
			$action_move      = '';
			$action_duplicate = '';
			if (isset($fetch_item['action']) && $fetch_item['action'] == "duplicate") {
				$action_duplicate = " checked='checked'";
			} else {
				$action_move = " checked='checked'";
			}
		
		} ?>
		</select>
		<input name="action" type="radio" id="action_move" value="move"<?php echo $action_move; ?> /><label for="action_move">...<?php echo $MOD_ONEFORALL[$mod_name]['TXT_MOVE']; ?></label>&nbsp; 
		<input name="action" type="radio" id="action_duplicate" value="duplicate"<?php echo $action_duplicate; ?> /><label for="action_duplicate">...<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DUPLICATE']; ?></label>
<?php	
	}
	else {	
		echo $TEXT['NONE_FOUND'];
	} ?>
		</td>
	</tr>
<?php
}



// GENERATE THE ITEM CUSTOM FIELDS
// *******************************

// Load jquery ui js
// The jquery ui css is loaded by @import() in the backend.css stylesheet
echo '<script src="'.WB_URL.'/include/jquery/jquery-ui-min.js" type="text/javascript"></script>';

// Load jquery ui datepicker language file
$datepicker_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : 'en';
$datepicker_lang_path = '/include/jquery/i18n/jquery.ui.datepicker-'.$datepicker_lang.'.js';
// By default datepaicker will use english if there is no language file
if (file_exists(WB_PATH.$datepicker_lang_path)) {
	echo '<script src="'.WB_URL.$datepicker_lang_path.'" type="text/javascript"></script>';
}

// Load jquery timepicker js
// The jquery timepicker css is loaded by @import() in the backend.css stylesheet
echo '<script src="'.WB_URL.'/modules/'.$mod_name.'/js/timepicker/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>';

// Load jquery ui timepicker language file
$timepicker_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : '';
$timepicker_lang_path = '/modules/'.$mod_name.'/js/timepicker/lang/jquery-ui-timepicker-'.$timepicker_lang.'.js';
// By default datepicker will use english if there is no other language file
if (file_exists(WB_PATH.$datepicker_lang_path)) {
	echo '<script src="'.WB_URL.$timepicker_lang_path.'" type="text/javascript"></script>';
}


// Get all fields from db
$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type != 'disabled' ORDER BY position, field_id ASC");
if ($query_fields->numRows() > 0) {
	while ($field = $query_fields->fetchRow()) {

		// Get field infos
		$field    = array_map('stripslashes', $field);
		$field_id = $field['field_id'];
		$type     = $field['type'];
		$extra    = $field['extra']; // Eg. a media subdirectory or select options
		$name     = $field['name'];
		$label    = $field['label'];
		#$template = $field['template'];

		// Get item field value
		if (!empty($fetch_item['fields'][$field_id])) {
			$value = $fetch_item['fields'][$field_id];
		} else {
			$value = $database->get_one("SELECT value FROM `".TABLE_PREFIX."mod_".$mod_name."_item_fields` WHERE item_id = '$item_id' AND field_id = '$field_id'");
			$value = stripslashes($value);
		}

		// If label is blank provide a link to set it
		if (empty($label)) {
			$label = '<a href="'.WB_URL.'/modules/'.$mod_name.'/modify_fields.php?page_id='.$page_id.'" style="color: red;">'.$MOD_ONEFORALL[$mod_name]['ERR_SET_A_LABEL'].'</a>';
		}

		// If value is serialized, unserialize it
		$unserialized = @unserialize($value);
		if ($unserialized !== false || $value == 'b:0;') {
			$value = $unserialized;
		}

		// Generate different field types
		$nl = "\n"; $t1 = "\t"; $t2 = "\t\t"; $t3 = "\t\t\t"; $t4 = "\t\t\t\t"; $t5 = "\t\t\t\t\t";
		switch ($type) {
			case 'text':
				echo field_text($field_id, $name, $label, $value);
				break;
			case 'textarea':
				echo field_textarea($field_id, $name, $label, $value);
				break;
			case 'wysiwyg':
				field_wysiwyg($field_id, $name, $label, $value, $wysiwyg_full_width);
				break;
			case 'wb_link':
				echo field_wb_link($field_id, $name, $page_id, $label, $value);
				break;
			case 'oneforall_link':
				echo field_oneforall_link($field_id, $name, $extra, $label, $value);
				break;
			case 'foldergallery_link':
				echo field_foldergallery_link($field_id, $name, $extra, $label, $value);
				break;
			case 'url':
				echo field_url($field_id, $name, $label, $value);
				break;
			case 'email':
				echo field_email($field_id, $name, $label, $value);
				break;
			case 'media':
				echo field_media($field_id, $name, $extra, $label, $value, $media_extensions);
				break;
			case 'upload':
				echo field_upload($field_id, $name, $extra, $label, $value, $upload_extensions);
				break;
			case 'datepicker':
				echo field_datepicker($field_id, $name, $label, $value);
				break;
			case 'datepicker_start_end':
				echo field_datepicker_start_end($field_id, $name, $label, $value);
				break;
			case 'datetimepicker':
				echo field_datetimepicker($field_id, $name, $label, $value);
				break;
			case 'datetimepicker_start_end':
				echo field_datetimepicker_start_end($field_id, $name, $label, $value);
				break;
			case 'droplet':
				echo field_droplet($field_id, $name, $label, $value);
				break;
			case 'select':
				echo field_select($field_id, $name, $extra, $label, $value);
				break;
			case 'group':
				echo field_group($field_id, $name, $extra, $label, $value);
				break;
			default:
				echo field_default();
		}
	}
}
?>

	<tr height="40" class="mod_oneforall_submit_row_b">
		<td colspan="2">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" style="padding-left: 12px;">
					<input name="save_and_return" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px;" />
					<input name="save" type="submit" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SAVE_AND_BACK_TO_LISTING']; ?>" style="width: 240px; margin-left: 20px;" />
				</td>
				<td align="right" style="padding-right: 12px;">
				<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; float: right;" />
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php



// ITEM IMAGES
// ***********

// Hide item image section if there is no use for it 
$display_img_section = $setting_img_section ? 'none;' : 'block;';

// Title and table header
?>
<div style="display: <?php echo $display_img_section; ?>">
	<br /><br /><br />
	<a name="images"><h2>2. <?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM']." ".$MOD_ONEFORALL[$mod_name]['TXT_IMAGES']; ?></h2></a>
	<table cellpadding="2" cellspacing="0" border="0" width="98%" align="center">
		<tr height="38" valign="bottom" class="mod_oneforall_submit_row_b">
		  <th width="10%" align="left"><span style="margin-left: 5px;"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_PREVIEW']; ?></span></th>
		  <th align="left"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FILE_NAME']; ?></th>
		  <th width="15%" align="left">HTML title Attribute<br />* HTML alt Attribute</th>
		  <th width="15%" align="left"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_CAPTION']; ?></th>
		  <th width="5%" align="left" colspan="2"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_POSITION']; ?></th>
		  <th width="3%" align="left"><?php echo $TEXT['ACTIVE']; ?></th>
		  <th width="3%" align="left"><?php echo $TEXT['DELETE']; ?></th>
		</tr>

		<?php
		// Get all images of this item
		$row      = 'a'; // Row color
		$no_image = true;
		$main_img = '<b>'.$MOD_ONEFORALL[$mod_name]['TXT_MAIN_IMAGE'].'</b><br />';

		// Prepare image and thumb directory pathes and urls
		$img_url   = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/';
		$thumb_url = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/';

		// Get image top position for this item
		$top_img = $database->get_one("SELECT MAX(`position`) AS `top_position` FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE `item_id` = '$item_id'");

		// Get image data from db
		$query_image = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE `item_id` = '$item_id' ORDER BY position ASC");
		if ($query_image->numRows() > 0) {
			$no_image = false;
			while ($image = $query_image->fetchRow()) {
				$image      = array_map('stripslashes', $image);
				$img_id     = $image['img_id'];
				$image_file = $image['filename'];
				$image['delete_image'] = 0;

				// Use session image data if user has been sent back to complete form		
				if (isset($fetch_item['images'])) {
					$image['title']        = $fetch_item['images'][$img_id]['title'];
					$image['alt']          = $fetch_item['images'][$img_id]['alt'];
					$image['caption']      = $fetch_item['images'][$img_id]['caption'];
					$image['active']       = $fetch_item['images'][$img_id]['active'];
					$image['delete_image'] = $fetch_item['images'][$img_id]['delete_image'];
				}

				// Thumbs use .jpg extension only
				$thumb_file = str_replace(".png", ".jpg", $image_file);

				// Prepare html output 
				$image = array_map('htmlspecialchars', $image);
				?>

			<tr class="row_<?php echo $row; ?>">
			  <td><a href="<?php echo $img_url.$image_file; ?>" target="_blank"><img src="<?php echo $thumb_url.$thumb_file; ?>" alt="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE']." ".$image_file; ?>" title="<?php echo $image_file; ?>" height="40" border="0" /></a>
			  </td>
			  <td>
			  <?php echo $main_img; ?>
			  <a href="<?php echo $img_url.$image_file; ?>" target="_blank" style="word-break: break-all;"><?php echo $image_file; ?></a>
			  </td>
			  <td>
				<input type="text" name="images[<?php echo $img_id; ?>][title]" style="width: 150px;" maxlength="255" value="<?php echo $image['title']; ?>" />
				<input type="text" name="images[<?php echo $img_id; ?>][alt]" style="width: 150px;" maxlength="255" value="<?php echo $image['alt']; ?>" />
			  </td>
			  <td>
			    <textarea name="images[<?php echo $img_id; ?>][caption]" rows="3" style="width: 200px;"><?php echo $image['caption']; ?></textarea>
			  </td>
			  <td align="right">
			  <?php if ($image['position'] != 1) { ?>
			    <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_img_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item_id; ?>&amp;img_id=<?php echo $img_id; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
			      <img src="<?php echo THEME_URL; ?>/images/up_16.png" border="0" alt="/\" />
			    </a>
			  <?php } ?>
			  </td>
			  <td align="left">
			  <?php if ($image['position'] != $top_img) { ?>
			    <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_img_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item_id; ?>&amp;img_id=<?php echo $img_id; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
			      <img src="<?php echo THEME_URL; ?>/images/down_16.png" border="0" alt="\/" />
			    </a>
			  <?php } ?>
			  </td>
			  <td nowrap="nowrap" align="center">
			  	<input type="checkbox" name="images[<?php echo $img_id; ?>][active]" value="1"<?php if ($image['active'] == 1) {echo ' checked="checked"';} ?> />
			  </td>
			  <td nowrap="nowrap" align="center">
			  	<input type="checkbox" name="images[<?php echo $img_id; ?>][delete_image]" value="<?php echo $image_file; ?>"<?php if ($image['delete_image'] == $image_file) {echo ' checked="checked"';} ?> />
			  </td>
			</tr>
			<?php
			$row = $row == 'a' ? 'b' : 'a'; // Alternate row color
			$main_img = '';
			}
		}

		// Display message if no directories nor images found
		if ($no_image) {
			echo "<tr height='30'><td colspan='5'>\n";
			echo "<span style='color: red; padding-left: 50px;'>".$TEXT['NONE_FOUND']."</span>";
			echo "</td></tr>";
		}
		?>
	</table>
	<br /><br />


	<?php
	// Image upload
	?>
	<a name="images"><h2>3. <?php echo $TEXT['ADD']." ".$MOD_ONEFORALL[$mod_name]['TXT_IMAGES']; ?></h2></a>
	<table cellpadding="2" cellspacing="0" border="0" width="100%" align="center">	
		<tr align="left" valign="top">
			<td>
			<?php
			// Image resize table
			?>
				<table class="mod_oneforall_img_resize_table_b" cellspacing="4">
					<tr>
						<th colspan="2">
							<input type="checkbox" name="imgresize" id="imgresize" value="yes"<?php echo $img_resize['imgresize'] == 'yes' ? ' checked="checked"' : ""; ?> />
							<label for="imgresize"><strong><?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE']." ".$TEXT['RESIZE']; ?></strong></label>
						</th>
					</tr>				
					<tr>
						<td><?php echo $MOD_ONEFORALL[$mod_name]['TXT_MAX_WIDTH']; ?>:</td>
						<td><input type="text" size="5" name="maxwidth" value="<?php echo $img_resize['maxwidth']; ?>" /></td>
					</tr>			
					<tr>
						<td><?php echo $MOD_ONEFORALL[$mod_name]['TXT_MAX_HEIGHT']; ?>:</td>
						<td><input type="text" size="5" name="maxheight" value="<?php echo $img_resize['maxheight']; ?>" /></td>
					</tr>				
					<tr>
						<td> <?php echo $MOD_ONEFORALL[$mod_name]['TXT_JPG_QUALITY']; ?>:</td>
						<td><input type="text" size="3" name="quality" value="<?php echo $img_resize['quality']; ?>" /></td>
					</tr>
				</table>
			</td>
			<td width="70%">
			<?php
			// Image upload table
			?>
			<table align="left" id="upload" style="margin: 5px;">	
				<tr>
					<td>
						<input type="file" name="image[]">
					</td>
				</tr>	
				<tfoot>
					<tr>
						<td>
							<span onclick="addFile(' [-] <?php echo $TEXT['DELETE']; ?>')" style="cursor: pointer;"> [+]  <?php echo $TEXT['ADD']; ?></span>
							<br /><br />
						</td>
					</tr>
				</tfoot>			
			</table>
			</td>
		</tr>
		<tr height="40" class="mod_oneforall_submit_row_b">
			<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" style="padding-left: 12px;">
						<input name="save_and_return_to_images" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px;" />
						<input name="save" type="submit" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SAVE_AND_BACK_TO_LISTING']; ?>" style="width: 240px; margin-left: 20px;" />
					</td>
					<td align="right" style="padding-right: 12px;">
					<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; float: right;" />
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	<br /><br /><br />
</div>
<?php


// Print admin footer
$admin->print_footer();

?>