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

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
require_once(WB_PATH.'/framework/class.admin.php');

// Include path
$inc_path = dirname(__FILE__);
// Get module name
require_once($inc_path.'/info.php');

// Look for language file
if (LANGUAGE_LOADED) {
    require_once($inc_path.'/languages/EN.php');
    if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
        require_once($inc_path.'/languages/'.LANGUAGE.'.php');
    }
}

// Get page settings
$query_page_settings = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE page_id = '$page_id'");
if ($query_page_settings->numRows() > 0) {
	$page_settings = $query_page_settings->fetchRow();
	$page_settings = array_map('stripslashes', $page_settings);
}

// Get field
$query_field = $database->query("SELECT field_id, name FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` ORDER BY position, field_id ASC LIMIT 1");
if ($query_field->numRows() > 0) {
	$field = $query_field->fetchRow();
	$field = array_map('stripslashes', $field);
}

// Get item
$query_item = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` ORDER BY item_id DESC LIMIT 1");
if ($query_item->numRows() > 0) {
	$item = $query_item->fetchRow();
	$item = array_map('stripslashes', $item);
}

// Get page info
$query_page = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
if ($query_page->numRows() > 0) {
	$page = $query_page->fetchRow();
}
?>



<h2>KEYS TO THE PLACEHOLDERS USED IN THE <?php echo $module_name; ?> TEMPLATES</h2>
<br />
<table id="mod_oneforall_placeholders_b" width="100%" cellpadding="5" cellspacing="0">
  <tr>
    <td><blockquote><strong>Template</strong></blockquote>      
      <ul>
        <li>PH = Page Header</li>
        <li>PIL = Page Item Loop</li>
        <li>PF = Page Footer</li>
        <li>IH = Item Header</li>
        <li>IF = Item Footer</li>
      </ul>
	</td>
    <td><blockquote><strong>Output Example Data</strong></blockquote>
	  <ul>
		<li><span class="mod_oneforall_placeholders_localisation_b">Blue: Localisation (example language = <?php echo(defined('LANGUAGE') ? LANGUAGE : "EN"); ?>)</span></li>
		<li><span class="mod_oneforall_placeholders_page_settings_b">Brown: Page settings (example page id = <?php echo $page_id; ?>)</span></li>
		<li><span class="mod_oneforall_placeholders_fields_b">Green: Field (example field id = <?php echo (isset($field['field_id']) ? $field['field_id'] : $TEXT['NONE_FOUND']); ?>)</span></li>
		<li><span class="mod_oneforall_placeholders_items_b">Orange: Item (example item id = <?php echo (isset($item['item_id']) ? $item['item_id'] : $TEXT['NONE_FOUND']); ?>)</span></li>
		<li><span class="mod_oneforall_placeholders_page_b">Pink: Page (example page id = <?php echo $page_id; ?>)</span></li>
      </ul>
	</td>
  </tr>
</table>
<br />
<table width="100%" cellpadding="5" cellspacing="0"  id="mod_oneforall_placeholders_b">
  <tr>
    <td height="30" align="right"><input name="button" type="button" style="margin-right: 20px;" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_page_settings.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>';" value="&lt;&lt; <?php echo $MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS']; ?>" />
	</td>
  </tr>
</table>
<br />
<table  id="mod_oneforall_placeholders_b" width="100%" cellpadding="5" cellspacing="0">
  <tr class="mod_oneforall_placeholders_header_b">
    <td colspan="8"><p><strong><a name="html"></a>Main Page and Item HTML Templates &nbsp;&nbsp;&nbsp;</strong>( &gt; Page Settings &gt; Layout Settings )</p></td>
  </tr>
  <tr>
    <th width="27%" align="left">Placeholder</th>
    <th width="3%">PH </th>
    <th width="4%">PIL</th>
    <th width="3%">PF</th>
    <th width="3%">IH</th>
    <th width="3%">IF</th>
    <th width="24%" align="left">Explanation</th>
    <th width="33%" align="left">Output Example Data</th>
  </tr>
  <tr valign="top">
    <td>[BACK]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>URL to the current (overview) page</td>
    <td class="mod_oneforall_placeholders_page_b"><?php echo (isset($page['link']) ? WB_URL.'<wbr>'.PAGES_DIRECTORY.$page['link'].PAGE_EXTENSION : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[DATE]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Item modification date</td>
    <td class="mod_oneforall_placeholders_items_b"><?php echo (isset($item['modified_when']) ? gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE) : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[DISPLAY_NAME]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>User display name </td>
    <td><?php echo (isset($_SESSION['DISPLAY_NAME']) ? $_SESSION['DISPLAY_NAME'] : "Administrator"); ?></td>
  </tr>
  <tr valign="top">
    <td>[DISPLAY_PREVIOUS_NEXT_LINKS]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>CSS display: </td>
    <td>none</td>
  </tr>
  <tr valign="top">
    <td>[USER_EMAIL]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>User email</td>
    <td><?php echo (isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[FIELD_NAME] or [FIELD_1]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Custom item fields which can be defined in the field settings. Either use the uppercase field_name or the uppercase field-string and field_id combination as a placeholder. For exact notation see the working examples at the <a href="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_fields.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>">field settings page</a>.</td>
    <td>
      <span class="mod_oneforall_placeholders_fields_b">
        <?php echo (isset($field['name']) ? '['.htmlspecialchars(strtoupper($field['name'])).']' : ''); ?>
      </span>
       <?php echo (isset($field['name']) ? ' or ' : ''); ?>
      <span class="mod_oneforall_placeholders_fields_b">
        <?php echo (isset($field['field_id']) ? '[FIELD_'.$field['field_id'].']' : ''); ?>
      </span>
    </td>
  </tr>
  <tr valign="top">
    <td>[IMAGE]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Main item image - only displayed if selected at section &quot;2. Item Images&quot; of the &quot;Add/modify item&quot; page</td>
    <td class="mod_oneforall_placeholders_items_b">Depends on various settings: For an example please see the source code of your <?php echo $module_name; ?> page! </td>
  </tr>
  <tr valign="top">
    <td>[IMAGES]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>All item images except for the item main image </td>
    <td class="mod_oneforall_placeholders_items_b">Depends on various settings: For an example please see the source code of your <?php echo $module_name; ?> page! </td>
  </tr> 
  <tr valign="top">
    <td>[ITEM_ID]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Item id</td>
    <td class="mod_oneforall_placeholders_items_b"><?php echo (isset($item['item_id']) ? $item['item_id'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[LINK]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Link to the item detail page</td>
    <td class="mod_oneforall_placeholders_items_b"><?php echo (isset($item['link']) ? WB_URL.PAGES_DIRECTORY.'<wbr>'.$page['link'].$item['link'].PAGE_EXTENSION : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[NEXT]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Link to the next item</td>
    <td class="mod_oneforall_placeholders_items_b"><a><?php echo $TEXT['NEXT']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[NEXT_LINK]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Link to the next page</td>
    <td class="mod_oneforall_placeholders_page_b"><a><?php echo $TEXT['NEXT']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[NEXT_PAGE_LINK]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Link to the next page</td>
    <td class="mod_oneforall_placeholders_page_b"><a><?php echo $TEXT['NEXT_PAGE']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[OF]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>View number of items out of total number of items </td>
    <td class="mod_oneforall_placeholders_page_settings_b">1-3 <?php echo $TEXT['OF']; ?> 10 </td>
  </tr>
  <tr valign="top">
    <td>[OUT_OF]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>View item number out of total number of items</td>
    <td class="mod_oneforall_placeholders_items_b">2 <?php echo $TEXT['OUT_OF']; ?> 10 </td>
  </tr>
  <tr valign="top">
    <td>[PAGE_TITLE]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Page title</td>
    <td class="mod_oneforall_placeholders_page_b"><?php echo (isset($page['page_title']) ? $page['page_title'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Link to the previous item</td>
    <td class="mod_oneforall_placeholders_items_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS_LINK]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Link to the previous page</td>
    <td class="mod_oneforall_placeholders_page_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS_PAGE_LINK]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Link to the previous page</td>
    <td class="mod_oneforall_placeholders_page_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS_PAGE']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_OF]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>of</em>&quot;</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $TEXT['OF']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_OUT_OF]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>out of</em>&quot;</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $TEXT['OUT_OF']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_READ_MORE]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>Localisation of "<em>Read more</em>"</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $TEXT['READ_MORE']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_BACK]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Back</em>"</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $TEXT['BACK']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_DESCRIPTION]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Description</em>&quot;</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_DESCRIPTION']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_ITEM]</td>
    <td class="mod_oneforall_placeholders_column_b">PH</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Item</em>"</td>
    <td class="mod_oneforall_placeholders_localisation_b"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_ITEM']; ?></td>
  </tr>
  <tr valign="top">
    <td>[THUMB]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Thumbnail of the main item image - only displayed if selected at section &quot;2. Item Images&quot; of the &quot;Add/modify item&quot; page</td>
    <td class="mod_oneforall_placeholders_items_b">Depends on various settings: For an example please see the source code of your <?php echo $module_name; ?> page!	</td>
  </tr>
  <tr valign="top">
    <td>[THUMBS]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>All thumbnails of the item images except for  the item main image </td>
    <td class="mod_oneforall_placeholders_items_b">Depends on various settings: For an example please see the source code of your <?php echo $module_name; ?> page! </td>
  </tr>
  <tr valign="top">
    <td>[TIME]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Item modification time</td>
    <td class="mod_oneforall_placeholders_items_b"><?php echo (isset($item['modified_when']) ? gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE) : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[TITLE]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>Item title</td>
    <td class="mod_oneforall_placeholders_items_b"><?php echo (isset($item['title']) ? $item['title'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[USERNAME]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>User name</td>
    <td><?php echo (isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : "admin"); ?></td>
  </tr>
  <tr valign="top">
    <td>[USER_ID]</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>PIL</td>
    <td class="mod_oneforall_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_oneforall_placeholders_column_b">IF</td>
    <td>User id</td>
    <td><?php echo (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "1"); ?></td>
  </tr>
  <tr valign="bottom">
    <td colspan="8" height="30" align="right">
	  <input name="button" type="button" style="margin-right: 20px;" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/modify_page_settings.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>';" value="&lt;&lt; <?php echo $MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS']; ?>" /></td>
  </tr>
</table>


<?php

// Print admin footer
$admin->print_footer();

?>