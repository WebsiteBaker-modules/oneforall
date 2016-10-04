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

// Make use of the skinable backend themes of WB > 2.7
// Check if THEME_URL is supported otherwise use ADMIN_URL
if (!defined('THEME_URL')) {
	define('THEME_URL', ADMIN_URL);
}

//Look for language File
if (LANGUAGE_LOADED) {
    require_once($inc_path.'/languages/EN.php');
    if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
        require_once($inc_path.'/languages/'.LANGUAGE.'.php');
    }
}

// Include WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
if (file_exists(WB_PATH.'/framework/module.functions.php') && file_exists(WB_PATH.'/modules/edit_module_files.php')) {
	include_once(WB_PATH.'/framework/module.functions.php');
}

// Delete empty Database records
$database->query("DELETE FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE page_id = '$page_id' and section_id = '$section_id' and title = ''");
$database->query("ALTER TABLE `".TABLE_PREFIX."mod_".$mod_name."_items` auto_increment = 1");

// Display settings to admin only
$display_settings = 'inline';
if ($settings_admin_only && $_SESSION['USER_ID'] != 1) {
	$display_settings = 'none';
}

// Load jQuery ui if not loaded yet
?>
<script type="text/javascript">
jQuery().sortable || document.write('<script src="<?php echo WB_URL; ?>/include/jquery/jquery-ui-min.js"><\/script>');
</script>

<div id="mod_oneforall_modify_b">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td colspan="2" rowspan="2" align="left"><h2 class="mod_oneforall_section_header_b"><?php echo $TEXT['PAGE_TITLE'].": ".get_page_title($page_id)." <span>".$TEXT['SECTION'].": ".$section_id; ?></span></h2></td>
		<td align="right" valign="bottom">
			<input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELDS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_fields.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" />
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_page_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" />
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" width="66%">
			<input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_ADD_ITEM']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/add_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 80%;" />
		</td>
		<td align="right">
			<?php
			if (function_exists('edit_module_css')) {
				if ($display_settings == "inline") {
					edit_module_css($mod_name);
				}
			} else {
				echo "<input type='button' name='edit_module_file' class='mod_oneforall_edit_css' value='{$TEXT['CAP_EDIT_CSS']}' onclick=\"javascript: alert('To take advantage of this feature please upgrade to WB 2.7 or higher.')\" />";
			} ?>
		</td>
	</tr>
</table>

<br />
<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?>
<img src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/images/ajax_loader.gif" id="mod_oneforall_ajax_loader_b" alt="Ajax loader" ></h2>
<table id="mod_oneforall_sortable_b" class="<?php echo $mod_name; ?>" cellpadding="2" cellspacing="0" border="0" width="100%">


<?php
// Get group names
$query_fields = $database->query("SELECT field_id, extra, label FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type = 'group' LIMIT 1");
if ($query_fields->numRows() > 0) {
	$field    = $query_fields->fetchRow();
	$field_id = $field['field_id'];
	$label    = $field['label'];
	$a_groups = explode(',', stripslashes($field['extra']));
	array_unshift($a_groups, '&#8211;');
} else {
	$field_id = false;
}


// Define the up and down arrows depending on ordering
$position_order = $order_by_position_asc ? 'ASC' : 'DESC';
$arrow1       = 'up';
$arrow2       = 'down';
$arrow1_title = $TEXT['MOVE_UP'];
$arrow2_title = $TEXT['MOVE_DOWN'];
if ($position_order == 'DESC') {
	$arrow1       = 'down';
	$arrow2       = 'up';
	$arrow1_title = $TEXT['MOVE_DOWN'];
	$arrow2_title = $TEXT['MOVE_UP'];
}

// Get item data
$position_order = $order_by_position_asc ? 'ASC' : 'DESC';
$query_items    = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id' AND title != '' ORDER BY position ".$position_order);

if ($query_items->numRows() > 0) {
	$num_items = $query_items->numRows();

	// Loop through existing items
	while ($post = $query_items->fetchRow()) {

		// Get item group id
		if ($field_id) {
			$group_id = $database->get_one("SELECT value FROM `".TABLE_PREFIX."mod_".$mod_name."_item_fields` WHERE item_id = '".$post['item_id']."' AND field_id = '".$field_id."'");
			$group_id   = $group_id === null ? 0 : $group_id;
			$group_name = $label.': '.$a_groups[$group_id];
		} else {
			$group_name = '';
		}

		?>
		<tr height="20" id="id_<?php echo $post['item_id']; ?>">
			<td width="20" style="padding-left: 5px;">
				<a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/modify_16.png" border="0" alt="<?php echo $TEXT['MODIFY']; ?>" />
				</a>
			</td>
			<td>
				<a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>">
					<?php echo stripslashes($post['title']); ?>
				</a>
			</td>
			<td width="12%" nowrap="nowrap">
				<?php echo $group_name; ?>
			</td>
			<td width="60" align="center">
				<?php $active_title = $post['active'] == 1 ? $TEXT['YES'] : $TEXT['NO']; ?>
				<img src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/images/active_<?php echo $post['active']; ?>.gif" border="0" alt="" title="<?php echo $TEXT['ACTIVE'].': '.$active_title; ?>" />
			</td>
			<td width="20">
			<?php if ($post['position'] != 1) { ?>
				<a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_up.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>" title="<?php echo $arrow1_title; ?>">
					<img src="<?php echo THEME_URL; ?>/images/<?php echo $arrow1; ?>_16.png" border="0" alt="^" />
				</a>
			<?php } ?>
			</td>
			<td width="20">
			<?php if ($post['position'] != $num_items) { ?>
				<a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_down.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>" title="<?php echo $arrow2_title; ?>">
					<img src="<?php echo THEME_URL; ?>/images/<?php echo $arrow2; ?>_16.png" border="0" alt="v" />
				</a>
			<?php } ?>
			</td>
			<td width="20">
				<a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/delete_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="X" />
				</a>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	<?php
} else {
	echo $TEXT['NONE_FOUND'];
}
?>
</div> <!-- enddiv #mod_oneforall_modify_b -->