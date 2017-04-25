<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2017, Christoph Marti
  
  This code is based on wb_searchext_mod_bakery v2.2 by thorn.
  It is adopted to Bakery v0.9 by thorn (thanks to thorn!).
  For further information see:
  http://nettest.thekk.de/pages/testing/new-search-function.php
  
  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/


function oneforall_search($func_vars) {
	extract($func_vars, EXTR_PREFIX_ALL, 'func');
	$mod_name = 'oneforall';

	// How many lines of excerpt we want to have at most
	$max_excerpt_num = $func_default_max_excerpt;
	// Show thumbnails?
	$show_thumb = true;
	$divider    = '.';
	$result     = false;

	// Search these tables
	$table_pages       = TABLE_PREFIX."pages";
	$table_items       = TABLE_PREFIX."mod_".$mod_name."_items";
	$table_fields      = TABLE_PREFIX."mod_".$mod_name."_fields";
	$table_item_fields = TABLE_PREFIX."mod_".$mod_name."_item_fields";

	// Fetch all active items in this section
	$query = $func_database->query("
		SELECT tp.link AS page_link, ti.item_id, ti.title, ti.link AS item_link, ti.modified_when, ti.modified_by
		FROM $table_pages tp
		INNER JOIN $table_items ti
		ON tp.page_id = ti.page_id
		WHERE section_id = '$func_section_id' AND active = '1'
		ORDER BY title ASC
	");

	// Now call print_excerpt() for every single item
	if ($query->numRows() > 0) {
		while ($res = $query->fetchRow()) {

			// Link
			$page_link = WB_URL.PAGES_DIRECTORY.$res['page_link'].$res['item_link'].PAGE_EXTENSION;

			// Thumbnail
			$pic_link = '';
			if ($show_thumb) {
				$thumb_dir  = '/'.$mod_name.'/thumbs/item'.$res['item_id'].'/';
				$main_thumb = $func_database->get_one("SELECT filename FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '{$res['item_id']}' AND active = '1' ORDER BY position ASC LIMIT 1");
				$main_thumb = str_replace('.png', '.jpg', $main_thumb);
				if (is_file(WB_PATH.MEDIA_DIRECTORY.$thumb_dir.$main_thumb)) {
					$pic_link = $thumb_dir.$main_thumb;
				}
			}

			// Values of text, textarea and wysiwyg fields
			$values = '.';
			$query_fields = $func_database->query("
				SELECT value
				FROM $table_item_fields INNER JOIN $table_fields USING(field_id)
				WHERE item_id = '{$res['item_id']}' AND type IN ('text', 'textarea', 'wysiwyg')
				ORDER BY $table_item_fields.value ASC
			");

			if ($query_fields->numRows() > 0) {
				while ($res_fields = $query_fields->fetchRow()) {
					$values .= $res_fields['value'].'.';
				}
			}

			$mod_vars = array(
				'page_link'           => $page_link,
				'page_link_target'    => "#wb_section_$func_section_id",
				'page_title'          => $res['title'],
				'page_modified_when'  => $res['modified_when'],
				'page_modified_by'    => $res['modified_by'],
				'text'                => $res['title'].$divider.
										 $values.$divider,
				'max_excerpt_num'     => $max_excerpt_num,
				'pic_link'            => $pic_link
			);
			if (print_excerpt2($mod_vars, $func_vars)) {
				$result = true;
			}
		}
	}
	return $result;
}

?>
