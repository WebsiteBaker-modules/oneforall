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


// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Scheduling
// Enable / disable items automatically against a start and end time
if ($set_scheduling && file_exists($inc_path.'/scheduling.php')) {
	include('scheduling.php');
}

// Get item
$query_item = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE item_id = '$item_id'");
$fetch_item = $query_item->fetchRow();

// Get item scheduling
$scheduling = @unserialize($fetch_item['scheduling']);
// Ignore start and end time if scheduling is disabled
$scheduling = $set_scheduling === false ? false : $scheduling;

// Sanitize
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
	$fetch_item['scheduling']     = $_SESSION[$mod_name]['item']['scheduling'];
	$fetch_item['description']    = htmlspecialchars($_SESSION[$mod_name]['item']['description']);
	unset($_SESSION[$mod_name]['item']);
}

// Get page setting if we should show / hide item image section
$setting_img_section = $database->get_one("SELECT img_section FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '$section_id'");




// LOAD JQUERY STUFF
// *****************

// Load jquery ui js
// The jquery ui css is loaded by @import() in the backend.css stylesheet
?>
<script>window.jQuery.ui || document.write('<script src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/jquery/ui/jquery-ui.min.js"><\/script>')</script>

<?php
// Load jquery ui datepicker language file
$datepicker_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : 'en';
$datepicker_lang_path = '/include/jquery/i18n/jquery.ui.datepicker-'.$datepicker_lang.'.js';
// By default datepicker will use english if there is no language file
if (file_exists(WB_PATH.$datepicker_lang_path)) {
	echo '<script src="'.WB_URL.$datepicker_lang_path.'" type="text/javascript"></script>';
}

// Load jquery timepicker js
// The jquery timepicker css is loaded by @import() in the backend.css stylesheet
echo '<script src="'.WB_URL.'/modules/'.$mod_name.'/js/timepicker/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>';

// Load jquery ui timepicker language file
$timepicker_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : '';
$timepicker_lang_path = '/modules/'.$mod_name.'/js/timepicker/lang/jquery-ui-timepicker-'.$timepicker_lang.'.js';
// By default timepicker will use english if there is no other language file
if (file_exists(WB_PATH.$timepicker_lang_path)) {
	echo '<script src="'.WB_URL.$timepicker_lang_path.'" type="text/javascript"></script>';
}
?>
<script type="text/javascript">
// Define an object with some properties
var mod_<?php echo $mod_name; ?> = {
	mod_name: '<?php echo $mod_name; ?>',
	txt_dragdrop_message: '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DRAGDROP_MESSAGE']; ?>'
};
</script>

<h2>1. <?php echo $TEXT['ADD'].' / '.$TEXT['MODIFY'].' '.$MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?></h2>

<form name="modify" action="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/save_item.php" method="post" enctype="multipart/form-data" style="margin: 0;">

<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
<input type="hidden" name="link" value="<?php echo $page['link'].$fetch_item['link']; ?>" />

<table id="mod_<?php echo $mod_name; ?>_modify_item_b" cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td><?php echo $MOD_ONEFORALL[$mod_name]['TXT_TITLE']; ?>:</td>
		<td>
			<input type="text" name="title" id="title" style="width: 98%;" maxlength="150" value="<?php echo $fetch_item['title']; ?>" />
		</td>
	</tr>
	<tr>
		<td><?php echo $TEXT['ACTIVE']; ?>:</td>
		<td>
			<?php
			// Users can toggle the item manually if scheduling is not used
			if ($scheduling === false || count(array_filter($scheduling)) < 1) {
			?>
			<input type="radio" name="active" id="active_true" value="1" <?php if ($fetch_item['active'] == 1) { echo " checked='checked'"; } ?> />
			<label for="active_true"><?php echo $TEXT['YES']; ?></label>
			&nbsp;
			<input type="radio" name="active" id="active_false" value="0" <?php if ($fetch_item['active'] == 0) { echo " checked='checked'"; } ?> />
			<label for="active_false"><?php echo $TEXT['NO']; ?></label>
			<?php
			// If scheduling is used, just show an icon to indicate the item status
			} else {
				echo '<span class="mod_'.$mod_name.'_scheduling'.$fetch_item['active'].'_b" title="'.$MOD_ONEFORALL[$mod_name]['TXT_SCHEDULING'].'"></span>';
			}
			// Scheduling: Enable / disable items against a start and end time
			if ($set_scheduling) {
			?>
			<script>
				$(function() {
					$("#scheduling_start").datetimepicker({
						separator: " <?php echo $MOD_ONEFORALL[$mod_name]['TXT_DATETIME_SEPARATOR']; ?> ",
						showOn: "button",
						buttonImage: "images/calendar.png",
						buttonImageOnly: true,
						buttonText: "<?php echo $MOD_ONEFORALL[$mod_name]['TXT_ENABLE']; ?>"
					});
					$("#scheduling_end").datetimepicker({
						separator: " <?php echo $MOD_ONEFORALL[$mod_name]['TXT_DATETIME_SEPARATOR']; ?> ",
						showOn: "button",
						buttonImage: "images/calendar.png",
						buttonImageOnly: true,
						buttonText: "<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DISABLE']; ?>"
					});
				});
			</script>
			<span class="txt_scheduling"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_SCHEDULING']; ?>:</span> 
			<input type="text" class="datetimepicker" name="scheduling_start" id="scheduling_start" maxlength="30" value="<?php echo $scheduling['start']; ?>">
			<input type="text" class="datetimepicker" name="scheduling_end" id="scheduling_end" maxlength="30" value="<?php echo $scheduling['end']; ?>">

			<?php } ?>
		</td>
	</tr>
<?php




// ITEM MOVER / DUPLICATOR
// ***********************

// Show/hide item mover/duplicator (see config.php)

// Do not show item mover/duplicator at all if user has added a new item
if (isset($_GET['from']) AND $_GET['from'] == 'add_item') {
	$show_item_mover      = false;
	$show_item_duplicator = false;
}

// Move or duplicate
if ($show_item_mover) {
?>
	<tr>
		<td><?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM_TO_PAGE']; ?> ... </td>
		<td>
	<?php
	// OneForAll page list
	$query_pages = "SELECT p.page_id, p.menu_title, p.visibility, p.admin_groups, p.admin_users, p.viewing_groups, p.viewing_users, s.section_id FROM `".TABLE_PREFIX."pages` p INNER JOIN `".TABLE_PREFIX."sections` s ON p.page_id = s.page_id WHERE s.module = '".$mod_name."' AND p.visibility != 'deleted' ORDER BY p.level, p.position ASC";
	$get_pages = $database->query($query_pages);

	if ($get_pages->numRows() > 0) {
		// Generate sections select
		echo '<select name="new_section_id" style="width: 240px">'."\n";
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
			echo $fetch_item['section_id'] == $page['section_id'] ? " selected='selected'" : '';
			echo $can_modify == false ? " disabled='disabled' style='color: #aaa;'" : '';
			echo ">{$page['menu_title']}</option>\n";
			// Prepare prechecked radio buttons
			$action_move      = '';
			$action_duplicate = '';
			if (isset($fetch_item['action']) && $fetch_item['action'] == 'duplicate') {
				$action_duplicate = " checked='checked'";
			} else {
				$action_move = " checked='checked'";
			}
		
		} ?>
		</select>
		<input name="action" type="radio" id="action_move" value="move"<?php echo $action_move; ?> style="margin-left: 12px;" /><label for="action_move"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_MOVE']; ?></label> 
		<input name="action" type="radio" id="action_duplicate" value="duplicate"<?php echo $action_duplicate; ?> style="margin-left: 18px;" /><label for="action_duplicate"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_DUPLICATE']; ?></label>
<?php	
	}
	else {	
		echo $TEXT['NONE_FOUND'];
	} ?>
		</td>
	</tr>
<?php
}


// Duplicate only
else if ($show_item_duplicator) {
	$action_duplicate = '';
	if (isset($fetch_item['action']) && $fetch_item['action'] == 'duplicate') {
		$action_duplicate = " checked='checked'";
	}
?>
	<tr>
		<td><?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?> ... </td>
		<td>
			<input name="new_section_id" type="hidden" value="<?php echo $section_id; ?>" />
			<input name="action" type="checkbox" id="action_duplicate" value="duplicate"<?php echo $action_duplicate; ?> />
			<label for="action_duplicate"> <?php echo $MOD_ONEFORALL[$mod_name]['TXT_DUPLICATE']; ?></label>
		</td>
	</tr>
<?php
}




// META DESCRIPTION
// ****************

// Textarea for meta description added to the head of the detail page
if ($view_detail_pages && $field_meta_desc) {
?>
	<tr>
		<td class="align_top"><?php echo $TEXT['DESCRIPTION']; ?>:<br />(Meta description)</td>		
		<td>
			<textarea name="description" id="description" rows="3"><?php echo $fetch_item['description']; ?></textarea>
		</td>
	</tr>
<?php
}




// GENERATE THE ITEM CUSTOM FIELDS
// *******************************

// GET ALL FIELDS FROM DB

// Exclude field type code if not allowed in config.php
$where_clause = $field_type_code ? '' : " AND type != 'code'";

// Query fields
$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type != 'disabled'$where_clause ORDER BY position, field_id ASC");

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
			case 'code':
				field_code($field_id, $name, $label, $value, $wysiwyg_full_width);
				break;
			case 'wb_link':
				unset($options);
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
			case 'multiselect':
				echo field_multiselect($field_id, $name, $extra, $label, $value);
				break;
			case 'checkbox':
				echo field_checkbox($field_id, $name, $extra, $label, $value);
				break;
			case 'switch':
				echo field_switch($field_id, $name, $extra, $label, $value);
				break;
			case 'radio':
				echo field_radio($field_id, $name, $extra, $label, $value);
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

	<tr height="40" class="mod_<?php echo $mod_name; ?>_submit_b">
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
	<a name="images"></a>
	<h2>2. <?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM'].' '.$MOD_ONEFORALL[$mod_name]['TXT_IMAGES']; ?></h2>
	<table id="mod_<?php echo $mod_name; ?>_images_b" cellpadding="2" cellspacing="0" border="0" width="98%" align="center">
		<thead>
			<tr>
				<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_PREVIEW']; ?></th>
				<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FILE_NAME']; ?></th>
				<th>HTML title Attribute<br />* HTML alt Attribute</th>
				<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_CAPTION']; ?></th>
				<th colspan="2"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_POSITION']; ?></th>
				<th><?php echo $TEXT['ACTIVE']; ?></th>
				<th><?php echo $TEXT['DELETE']; ?></th>
			</tr>
		</thead>
		<tbody>

		<?php
		// Get all images of this item
		$no_image = true;
		$main_img = '<b>'.$MOD_ONEFORALL[$mod_name]['TXT_MAIN_IMAGE'].'</b><br />';

		// Prepare image / thumb url and thumb path
		$img_url    = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/';
		$thumb_url  = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/';
		$thumb_path = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/';

		// Get image top position for this item
		$top_img = $database->get_one("SELECT MAX(position) AS top_position FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '$item_id'");

		// Get image data from db
		$query_image = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '$item_id' ORDER BY position ASC");
		if ($query_image->numRows() > 0) {
			$no_image = false;
			while ($image = $query_image->fetchRow()) {
				$image      = array_map('stripslashes', $image);
				$img_id     = $image['img_id'];
				$image_file = $image['filename'];
				$image['delete_image'] = 0;

				// Check if png image has a jpg thumb (version < 0.9 used jpg thumbs only)
				$thumb_file = $image_file;
				if (!file_exists($thumb_path.$thumb_file)) {
					$thumb_file = str_replace('.png', '.jpg', $thumb_file);
				}

				// Use session image data if user has been sent back to complete form		
				if (isset($fetch_item['images'])) {
					$image['title']        = $fetch_item['images'][$img_id]['title'];
					$image['alt']          = $fetch_item['images'][$img_id]['alt'];
					$image['caption']      = $fetch_item['images'][$img_id]['caption'];
					$image['active']       = $fetch_item['images'][$img_id]['active'];
					$image['delete_image'] = $fetch_item['images'][$img_id]['delete_image'];
				}

				// Prepare html output 
				$image = array_map('htmlspecialchars', $image);
				?>

			<tr id="id_<?php echo $img_id; ?>">
			  <td>
				<div class="tooltip">
					<a href="<?php echo $img_url.$image_file; ?>" target="_blank">
						<img src="<?php echo $thumb_url.$thumb_file; ?>" alt="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE'].' '.$image_file; ?>">
					</a>
					<div class="arrow_box">
						<img src="<?php echo $img_url.$image_file; ?>" alt="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE'].' '.$image_file; ?>" title="<?php echo $image_file; ?>">
					</div>
				</div>
			  </td>
			  <td>
			  <?php echo $main_img; ?>
			  <a href="<?php echo $img_url.$image_file; ?>" target="_blank"><?php echo $image_file; ?></a>
			  </td>
			  <td>
				<input type="text" name="images[<?php echo $img_id; ?>][title]" style="width: 150px;" maxlength="255" value="<?php echo $image['title']; ?>" />
				<input type="text" name="images[<?php echo $img_id; ?>][alt]" style="width: 150px;" maxlength="255" value="<?php echo $image['alt']; ?>" />
			  </td>
			  <td>
			    <textarea name="images[<?php echo $img_id; ?>][caption]" rows="3" style="width: 200px;"><?php echo $image['caption']; ?></textarea>
			  </td>
			  <td>
			  <?php if ($image['position'] != 1) { ?>
			    <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_img_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item_id; ?>&amp;img_id=<?php echo $img_id; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
			      <img src="<?php echo THEME_URL; ?>/images/up_16.png" border="0" alt="/\" />
			    </a>
			  <?php } ?>
			  </td>
			  <td>
			  <?php if ($image['position'] != $top_img) { ?>
			    <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_img_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item_id; ?>&amp;img_id=<?php echo $img_id; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
			      <img src="<?php echo THEME_URL; ?>/images/down_16.png" border="0" alt="\/" />
			    </a>
			  <?php } ?>
			  </td>
			  <td>
			  	<input type="checkbox" name="images[<?php echo $img_id; ?>][active]" value="1"<?php if ($image['active'] == 1) {echo ' checked="checked"';} ?> />
			  </td>
			  <td>
			  	<input type="checkbox" name="images[<?php echo $img_id; ?>][delete_image]" value="<?php echo $image_file; ?>"<?php if ($image['delete_image'] == $image_file) {echo ' checked="checked"';} ?> />
			  </td>
			</tr>
			<?php
			$main_img = '';
			}
		}

		// Display message if no directories nor images found
		if ($no_image) {
			echo '<tr height="30" id="mod_'.$mod_name.'_no_image_b"><td colspan="8">'."\n";
			echo '<span style="color: red; padding-left: 50px;">'.$TEXT['NONE_FOUND'].'</span>'."\n";
			echo '</td></tr>'."\n";
		}
		?>
		</tbody>
	</table>
	<br /><br />

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr height="40" class="mod_<?php echo $mod_name; ?>_submit_b">
			<td align="left" style="padding-left: 12px;">
				<input name="save_and_return_to_images" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px;" />
				<input name="save" type="submit" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SAVE_AND_BACK_TO_LISTING']; ?>" style="width: 240px; margin-left: 20px;" />
			</td>
			<td align="right" style="padding-right: 12px;">
			<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; float: right;" />
			</td>
		</tr>
	</table>
	<br /><br /><br />
	<?php



	// PLUPLOAD
	// ********

	// Multi runtime file uploader featuring drag&drop, resize images on clientside, ...
	// http://www.plupload.com
	?>
	<a name="images"></a>
	<h2>3. <?php echo $TEXT['UPLOAD_FILES']; ?></h2>
	<div id="mod_<?php echo $mod_name; ?>_resize_settings_b">
		<input type="checkbox" name="imgresize" id="imgresize" value="yes"<?php echo $img_resize['imgresize'] == 'yes' ? ' checked="checked"' : ''; ?> />
		<label for="imgresize"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE'].' '.$TEXT['RESIZE']; ?>:</label>
		<?php echo $MOD_ONEFORALL[$mod_name]['TXT_MAX_WIDTH']; ?><input type="text" size="5" name="maxwidth" value="<?php echo $img_resize['maxwidth']; ?>" />px
		<span></span>
		<?php echo $MOD_ONEFORALL[$mod_name]['TXT_MAX_HEIGHT']; ?><input type="text" size="5" name="maxheight" value="<?php echo $img_resize['maxheight']; ?>" />px
		<span></span>
		<?php echo $MOD_ONEFORALL[$mod_name]['TXT_JPG_QUALITY']; ?>: 
		<input type="text" size="3" name="quality" value="<?php echo $img_resize['quality']; ?>" />
	</div>
	<div id="uploader">
		<p style="color: red;">Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
	</div>

	<script type="text/javascript">
	// Initialize the widget when the DOM is ready
	$(function() {
		$('#uploader').plupload({

			// General settings
			runtimes: 'html5,flash,silverlight,html4',
			url: '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/ajax/upload.php',

			// Maximum file size
			max_file_size: '<?php echo $max_file_size; ?>mb',
			chunk_size: '1mb',

			// Resize images on clientside if we can
			/*
			resize: {
				width: 200,
				height: 200,
				quality: 90,
				crop: true // crop to exact dimensions
			},
			*/

			// Specify what files to browse for
			filters: [
				{title: 'Image files', extensions: 'jpg,jpeg,png'}
			],

			// Rename files by clicking on their titles
			rename: true,

			// Sort files
			sortable: true,

			// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
			dragdrop: true,

			// Views to activate
			views: {
				list: true,
				thumbs: true, // Show thumbs
				active: 'thumbs'
			},

			// Flash settings
			flash_swf_url: '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/plupload/js/Moxie.swf',

			// Silverlight settings
			silverlight_xap_url: '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/plupload/js/Moxie.xap'
		});



		// Get uploader
		var uploader = $('#uploader').plupload('getUploader');

		// Update resize settings before uploading the file
		// and post them together with the image 
		uploader.bind('BeforeUpload', function(up, files) {

			// Get the current settings
			uploader.settings.multipart_params = {
				mod_name:   '<?php echo $mod_name; ?>',
				section_id: <?php echo $section_id; ?>,
				item_id:    <?php echo $item_id; ?>,
				imgresize:  $('#imgresize').prop('checked') ? 1 : 0,
				maxwidth:   $("input[name='maxwidth']").val(),
				maxheight:  $("input[name='maxheight']").val(),
				quality:    $("input[name='quality']").val()
			};
	    });


		// Get server response
		uploader.bind('FileUploaded', function(up, file, res) {

			// Success
			var res_obj = JSON.parse(res.response);
			if (res_obj.result === null) {

				// Replace the 'non found' notice by the first uploaded image
				var notice = $('#mod_<?php echo $mod_name; ?>_no_image_b');
				if (notice.length) {
					notice.remove();
				}

				// Get the new image id and the filename
				var img_id   = res_obj.img_id;
				var filename = res_obj.filename;
				// Uncomment for debugging
				// console.log('File ' + filename + ' uploaded successfully. New img id = ' + img_id);

				// On success add thumb to the image table
				$('#mod_<?php echo $mod_name; ?>_images_b tbody').append('<tr id="id_' + img_id + '"><td style="width: 94px;"><div class="tooltip"><a href="<?php echo WB_URL; ?>/media/<?php echo $mod_name; ?>/images/item<?php echo $item_id; ?>/' + filename + '" target="_blank"><img src="<?php echo WB_URL; ?>/media/<?php echo $mod_name; ?>/thumbs/item<?php echo $item_id; ?>/' + filename + '" alt=""></a><div class="arrow_box"><img src="<?php echo WB_URL; ?>/media/<?php echo $mod_name; ?>/images/item<?php echo $item_id; ?>/' + filename + '" alt="" title="' + filename + '"></div></div></td><td style="width: 351px;"><a href="<?php echo WB_URL; ?>/media/<?php echo $mod_name; ?>/images/item<?php echo $item_id; ?>/' + filename + '" target="_blank">' + filename + '</a></td><td style="width: 156px;"><input type="text" name="images[' + img_id + '][title]" style="width: 150px;" maxlength="255" value=""><input type="text" name="images[' + img_id + '][alt]" style="width: 150px;" maxlength="255" value=""></td><td style="width: 206px;"><textarea name="images[' + img_id + '][caption]" rows="3" style="width: 200px;"></textarea></td><td></td><td></td><td style="width: 32px;"><input type="checkbox" name="images[' + img_id + '][active]" value="1" checked="checked"></td><td></td></tr>');
			}

			// Error
			else {
				// Uncomment for debugging
				// console.log(res.response);
				// console.log(res_obj.error.filename + ': ' + res_obj.error.message);

				// Add an error message to the image table
				$('#mod_<?php echo $mod_name; ?>_images_b tbody').append('<tr class="plupload_error_msg"><td class="ui-state-error" style="width: 94px;"><span class="ui-icon ui-icon-alert"></span></td><td colspan="6"><strong>' + res_obj.error.filename + '</strong>: ' + res_obj.error.message + '</td><td class="ui-state-error"><span class="ui-icon ui-icon-circle-close" title="<?php echo $TEXT['CLOSE']; ?>"></span></td></tr>');
			}
		});


		// Complete
		$('#uploader').on('complete', function(up, files) {

			// After upload is completed, clear the plupload queue
			$('#uploader').plupload('clearQueue');

			// Function to remove the plupload error messages manually by clicking the row
			$('.plupload_error_msg .ui-icon-circle-close').click(function() {
				$(this).closest('tr').remove();
			});
		});
	});
	</script>

	<script src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/plupload/js/plupload.full.min.js" type="text/javascript"></script>
	<script src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/plupload/js/jquery.ui.plupload/jquery.ui.plupload.min.js" type="text/javascript"></script>
	<?php
	// Load plupload language file
	$plupload_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : 'en';
	$plupload_lang_path = '/modules/'.$mod_name.'/js/plupload/js/i18n/'.$plupload_lang.'.js';
	// By default plupload will use english if there is no suitable language file
	if (file_exists(WB_PATH.$plupload_lang_path)) {
		echo '<script src="'.WB_URL.$plupload_lang_path.'" type="text/javascript"></script>';
	}
	?>
	<br /><br />
</div>
<?php


// Print admin footer
$admin->print_footer();

?>