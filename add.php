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
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}


// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
// Get module name
require($inc_path.'/info.php');

// Look for language File
if (is_readable(__DIR__.'/languages/EN.php')) {require(__DIR__.'/languages/EN.php');}
if (is_readable(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php')) {require(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php');}
if (is_readable(__DIR__.'/languages/'.LANGUAGE.'.php')) {require(__DIR__.'/languages/'.LANGUAGE.'.php');}


// Layout
$header = $admin->add_slashes('<!-- Module Header -->');

$item_loop = $admin->add_slashes('
<div class="mod_'.$mod_name.'_item_loop_f">
    [THUMB]
    <h3 class="mod_'.$mod_name.'_main_title_f"><a href="[LINK]">[TITLE]</a></h3>
    [FIELD_1]
    [FIELD_2]
</div>
');
$footer = $admin->add_slashes('
<table class="mod_'.$mod_name.'_pagination_f" style="width:98% ;display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
    <tr>
        <td style="width:35%;float:left;">[PREVIOUS_PAGE_LINK]</td>
        <td style="width:30%;" >[TXT_ITEM] [OF] </td>
        <td style="width:35%;float:right;">[NEXT_PAGE_LINK]</td>
    </tr>
</table>
');

$item_header = $header;

$item_footer = $admin->add_slashes('
<div class="mod_'.$mod_name.'_item_f">
    [THUMBS]
    <h2 class="mod_'.$mod_name.'_item_title_f">[TITLE]</h2>
    [FIELD_1]
    [FIELD_2]
</div>

<div class="mod_'.$mod_name.'_prev_next_links_f">
    [PREVIOUS] | <a href="[BACK]">[TXT_BACK]</a> | [NEXT]
</div>
');
// Insert default values into table page_settings
$database->query('INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_page_settings` (`section_id`, `page_id`, `header`, `item_loop`, `footer`, `item_header`, `item_footer`)
VALUES ('.(int)$section_id.', '.(int)$page_id.', \''.$header.'\', \''.$item_loop.'\', \''.$footer.'\', \''.$item_header.'\', \''.$item_footer.'\')');


