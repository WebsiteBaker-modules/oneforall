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
if (!function_exists('is_serialized')){require(__DIR__.'/functions.php');}

// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
// Get module name and config
require($inc_path.'/info.php');
require($inc_path.'/config.php');

// Make use of the skinable backend themes of WB > 2.7
// Check if THEME_URL is supported otherwise use ADMIN_URL
if (!defined('THEME_URL')) {
    define('THEME_URL', ADMIN_URL);
}

//Look for language File
if (is_readable(__DIR__.'/languages/EN.php')) {require(__DIR__.'/languages/EN.php');}
if (is_readable(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php')) {require(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php');}
if (is_readable(__DIR__.'/languages/'.LANGUAGE.'.php')) {require(__DIR__.'/languages/'.LANGUAGE.'.php');}

$oLang = Translate::getInstance();
$oLang->enableAddon('modules\\'.basename(__DIR__));
// Include WB functions file
if (!function_exists('make_dir')){require(WB_PATH.'/framework/functions.php');}

// Include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
if (!function_exists('edit_module_css')){include(WB_PATH .'/framework/module.functions.php');}

// Delete empty Database records
$database->query('DELETE FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` WHERE `page_id` = '.(int)$page_id.' and `section_id` = '.(int)$section_id.' and `title` = \'\' ');
$database->query('ALTER TABLE `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` `auto_increment` = 1');

// Scheduling: Enable / disable items automatically against a start and end time
if ($set_scheduling && file_exists($inc_path.'/scheduling.php')) {
    include($inc_path.'/scheduling.php');
}

// Display settings to admin only
$display_settings = 'inline';
if ($settings_admin_only && $_SESSION['USER_ID'] != 1) {
    $display_settings = 'none';
}

// Add space to the text var TXT_SORT_BY2 if it is not empty
if (!empty($MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2'])) {
    $MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2'] = ' '.$MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2'];
}
?>
<script>
// Load jQuery ui if not loaded yet
jQuery().sortable || document.write('<script src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/jquery/ui/jquery-ui.min.js"><\/script>');
// Load table_sort.js if not loaded yet
if (typeof(table_sort_loaded) === 'undefined') {
    document.write('<script src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/table_sort.js"><\/script>');
    var table_sort_loaded = true;
}
// Define an object with some properties
// We need an object with a unique name (module name) in order to prevent interference between the modules
var mod_<?php echo $mod_name; ?> = {
    mod_name: '<?php echo $mod_name; ?>',
    txt_enable: '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_ENABLE']; ?>',
    txt_disable: '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DISABLE']; ?>',
    txt_toggle_message: '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_TOGGLE_MESSAGE']; ?>',
    txt_dragdrop_message: '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DRAGDROP_MESSAGE']; ?>'
};
// Define some localisation vars for TableSort
var txt_sort_table = '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SORT_TABLE']; ?>',
    txt_sort_by1   = '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SORT_BY1']; ?>',
    txt_sort_by2   = '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2']; ?>';
</script>

<div id="mod_<?php echo $mod_name; ?>_modify_b" class="sid<?php echo $section_id; ?>">
<table style="width:100%; border-collapse: collapse;line-height: 2;">
  <tbody>
    <tr>
        <td style="width:76%;">
          <h2 class="mod_<?php echo $mod_name; ?>_section_header_b"><?php echo $TEXT['PAGE_TITLE'].": ".get_page_title($page_id)." <span>".$TEXT['SECTION'].": ".$section_id; ?></span></h2>
        </td>
        <td style="float:right;vertical-align: middle;">
            <span style="display: block; padding-top: 0.825em;"><input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELDS']; ?>" onclick="window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_fields.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" /></span>
            <span style="display: block; padding-top: 0.525em;"><input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS']; ?>" onclick="window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_page_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" /></span>
        </td>
    </tr>
    <tr>
        <td colspan="2"  style="float:right; vertical-align: top;">
        </td>
    </tr>
    <tr>
        <td >
            <input type="button" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_ADD_ITEM']; ?>" onclick="window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/add_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 60%;margin:auto 20%;" />
        </td>
        <td style="float:right;">
            <?php
            if (function_exists('edit_module_css')) {
                if ($display_settings == 'inline') {
                    edit_module_css($mod_name);
                }
            } else {
                echo '<input type="button" name="edit_module_file" class="mod_'.$mod_name.'_edit_css" value="'.$TEXT['CAP_EDIT_CSS'].'" onclick="alert(\'To take advantage of this feature please upgrade to WB 2.7 or higher.\')" />';
            } ?>
        </td>
    </tr>
  </tbody>
</table>

<br />
<h2><?php echo $TEXT['MODIFY'].' / '.$TEXT['DELETE'].' '.$MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?></h2>

<?php
// Get group names
$query_fields = $database->query('SELECT `field_id`, `extra`, `label` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_fields` WHERE `type` = \'group\' LIMIT 1');
if ($query_fields->numRows() > 0) {
    $field    = $query_fields->fetchRow(MYSQLI_ASSOC);
    $field_id = $field['field_id'];
    $label    = $field['label'];
    $a_groups = explode(',', stripslashes($field['extra']));
    array_unshift($a_groups, '&#8211;');
} else {
    $field_id = false;
}
?>

<table id="mod_<?php echo $mod_name; ?>_items_b" class="sortierbar" style="width:100%; border-collapse: collapse;line-height: 2;">
<thead>
    <tr>
        <th class="sortierbar">ID</th>
        <th></th>
        <th class="sortierbar"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_TITLE']; ?></th>
        <th class="sortierbar"><?php if ($field_id) echo $MOD_ONEFORALL[$mod_name]['TXT_GROUP']; ?></th>
        <?php if($featured) { ?><th class="sortierbar featured center" style="width:100px;" title="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_FEATURED'];?>">Featured</th><?php } ?>
        <th class="sortierbar center"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_ENABLED']; ?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
</thead>
<tbody>

<?php
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
//count featured items
$position_order = $order_by_position_asc ? 'ASC' : 'DESC';
$query_items    = $database->query('SELECT * FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` WHERE `section_id` = '.(int)$section_id.' AND `title` != \'\' ORDER BY `position` '.$position_order);
$featured_count = 0;
if ($query_items->numRows() > 0) {
    $num_items = $query_items->numRows();

    // Loop through existing items
    while ($item = $query_items->fetchRow(MYSQLI_ASSOC)) {

        // Get item scheduling
        $scheduling = __unserialize($item['scheduling']);
        // Ignore start and end time if scheduling is disabled
        $scheduling = $set_scheduling === false ? false : $scheduling;

        // Sanitize
        $item = array_map('stripslashes', $item);
        $item = array_map('htmlspecialchars', $item);

        // Get item group id
        if ($field_id) {
            $group_id = $database->get_one('SELECT `value` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` WHERE `item_id` = '.$item['item_id'].' AND `field_id` = '.(int)$field_id.' ');
            $group_id   = empty($group_id) ? 0 : $group_id;
            //$group_name = $label.': '.$a_groups[$group_id];
            $group_name = $a_groups[$group_id];
        } else {
            $group_name = '';
        }

        ?>
        <tr id="id_<?php echo $item['item_id']; ?>">
            <td><?php echo $item['item_id']; ?></td>
            <td>
                <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/modify_16.png" alt="<?php echo $TEXT['MODIFY']; ?>" />
                </a>
            </td>
            <td>
                <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>">
                    <?php echo stripslashes($item['title']); ?>
                </a>
            </td>
            <td>
                <?php echo $group_name; ?>
            </td>
            <?php if($featured) { ?>
                <td class="featured">
                <?php if ($item['featured'] == 1) {
                        echo '<b title="'.$MOD_ONEFORALL[$mod_name]['TXT_FEATURED'].'">F</b>';
                        $featured_count++; }
                ?> &nbsp;
                </td>
            <?php } ?>
            <td class="cursor-pointer" style="text-align:center;vertical-align: middle;">
<?php
                $scheduling_title = $item['active'] == 1 ? $MOD_ONEFORALL[$mod_name]['TXT_ENABLED'] : $MOD_ONEFORALL[$mod_name]['TXT_DISABLED'];
                $active_title = $item['active'] == 1 ? $MOD_ONEFORALL[$mod_name]['TXT_DISABLE'] : $MOD_ONEFORALL[$mod_name]['TXT_ENABLE'];
                // If scheduling is used, just show a calendar icon to indicate the item status
                if ($scheduling !== false && count(array_filter($scheduling)) > 0) {
?>
                    <img src="<?php echo WB_URL;?>/modules/<?php echo $mod_name;?>/images/scheduled<?php echo $item['active'];?>.png" width="16" height="16" alt="<?php echo $scheduling_title;?>" title="<?php echo $scheduling_title;?> (<?php echo $MOD_ONEFORALL[$mod_name]['TXT_SCHEDULING'];?>)">
<?php
                } else {
                // Users can toggle the item manually if scheduling is not used  <span><?php echo $item['active']</span>
?>
                    <span class="mod_<?php echo $mod_name;?>_active<?php echo $item['active'];?>_b" title="<?php echo $active_title;?>"></span>
<?php } ?>
                &nbsp;
            </td>
            <td>
            <?php if ($item['position'] != 1) { ?>
                <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_up.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>" title="<?php echo $arrow1_title; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/<?php echo $arrow1; ?>_16.png" alt="^" />
                </a>
            <?php } ?>
            </td>
            <td>
            <?php if ($item['position'] != $num_items) { ?>
                <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/move_down.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>" title="<?php echo $arrow2_title; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/<?php echo $arrow2; ?>_16.png" alt="v" />
                </a>
            <?php } ?>
            </td>
            <td class="cursor-pointer">
                <a onclick="confirm_link('<?php echo ($TEXT['ARE_YOU_SURE']); ?>','<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/delete_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                </a>
            </td>
        </tr>
    <?php
    }

} else { ?>

    <tr><td colspan="8"><?php echo $TEXT['NONE_FOUND']; ?></td></tr>

<?php } ?>

    </tbody>
</table>
</div> <!-- enddiv #mod_<?php echo $mod_name; ?>_modify_b -->

<!--
<?php // hide column title if no featured item
//echo 'count: ' . $featured_count; ?>
<script>
    if (<?php echo $featured_count ?> < 1 ) {
        $('.sid<?php echo $section_id; ?> th.featured').text('');
    }
</script>
-->