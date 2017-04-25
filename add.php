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
	exit('Cannot access this file directly'); 
}


// Include path
$inc_path = dirname(__FILE__);
// Get module name
require_once($inc_path.'/info.php');

// Look for language File
if (LANGUAGE_LOADED) {
    require_once($inc_path.'/languages/EN.php');
    if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
        require_once($inc_path.'/languages/'.LANGUAGE.'.php');
    }
}


// Layout
$header = $admin->add_slashes('
<h1>Hello '.$_SESSION['DISPLAY_NAME'].'</h1>
<p>This is your module '.$module_name.' speaking:</p>
<p>Before taking off there have to been made some modifications.</p>
<h2>Adding custom fields</h2>
<ol>
	<li>Login to your WebsiteBaker installation.</li>
	<li>Add a new page with page type &quot;'.$module_name.'&quot;.</li>
	<li>Go to the &quot;Fields Settings&quot; and add the customized fields you intend to collect the data.</li>
	<li>Select the field type and modify the field templates to fit your needs.</li>
	<li>Note that every field must have a unique field name. Allowed characters are a-z, A-Z, 0-9, . (dot), _ (underscore) and - (hyphen).</li>
	<li>Consider the displayed placeholders. You will need them later when adding the item templates.</li>
</ol>
<h2>Adding item templates</h2>
<p>Create your own item templates using the generated and general placeholders:</p>
<ol>
	<li>Go to the &quot;Page Settings&quot; and use the mentioned placeholders in your templates to view the items just the way you want.</li>
	<li>Replace this text by them HTML code of your template.</li>
	<li>[PLACEHOLDERS] are uppercase and enclosed by square brackets.</li>
	<li>For a list of more general placeholders to accomplish your template click the &quot;Help&quot;-button on top of the &quot;Page Settings&quot; page.</li>
	<li>Add an item or two, enter some data and have fun!</li>
</ol>
');

$item_loop = $admin->add_slashes('
<div class="mod_'.$mod_name.'_item_loop_f">
[THUMB]
<h3 class="mod_'.$mod_name.'_main_title_f"><a href="[LINK]">[TITLE]</a></h3>
<!-- ADD YOUR PLACEHOLDERS LIKE THIS: -->
[FIELD_1]
[FIELD_2]
</div>
');

$footer = $admin->add_slashes('
<table class="mod_'.$mod_name.'_pagination_f" cellpadding="0" cellspacing="0" border="0" width="98%" style="display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
<tr>
<td width="35%" align="left">[PREVIOUS_PAGE_LINK]</td>
<td width="30%" align="center">[TXT_ITEM] [OF] </td>
<td width="35%" align="right">[NEXT_PAGE_LINK]</td>
</tr>
</table>
');

$item_header = $header;

$item_footer = $admin->add_slashes('
<div class="mod_'.$mod_name.'_item_f">
[THUMBS]
<h2 class="mod_'.$mod_name.'_item_title_f">[TITLE]</h2>
<!-- ADD YOUR PLACEHOLDERS LIKE THIS: -->
[FIELD_1]
[FIELD_2]
</div>
<div class="mod_'.$mod_name.'_prev_next_links_f">
[PREVIOUS] | <a href="[BACK]">[TXT_BACK]</a> | [NEXT]
</div>
');


// Insert default values into table page_settings 
$database->query("INSERT INTO `".TABLE_PREFIX."mod_".$mod_name."_page_settings` (section_id, page_id, header, item_loop, footer, item_header, item_footer)
VALUES ('$section_id', '$page_id', '$header', '$item_loop', '$footer', '$item_header', '$item_footer')");

?>
