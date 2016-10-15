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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit('Cannot access this file directly'); 
}



// SHOW OVERVIEW PAGE
// ******************

// Get page settings
$query_page_settings = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '$section_id'");
if ($query_page_settings->numRows() > 0) {
	$fetch_page_settings    = $query_page_settings->fetchRow();
	$setting_header         = stripslashes($fetch_page_settings['header']);
	$setting_item_loop      = stripslashes($fetch_page_settings['item_loop']);
	$setting_footer         = stripslashes($fetch_page_settings['footer']);
	$setting_items_per_page = $fetch_page_settings['items_per_page'];
	$setting_img_section    = $fetch_page_settings['img_section'];
	$setting_resize         = stripslashes($fetch_page_settings['resize']);
	$setting_lightbox2      = stripslashes($fetch_page_settings['lightbox2']);
} else {
	$setting_header         = '';
	$setting_item_loop      = '';
	$setting_footer         = '';
	$setting_items_per_page = '';
	$setting_resize         = '';
}

// If requested include lightbox2 (css is appended to the frontend.css stylesheet)
if ($setting_lightbox2 == "overview" || $setting_lightbox2 == "all") {
	// Load jQuery if not loaded yet
	?>
	<script>window.jQuery || document.write('<script src="<?php echo WB_URL; ?>/include/jquery/jquery-min.js"><\/script>')</script>
	<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/lightbox2/js/lightbox.js"></script>
	<script type="text/javascript">
	//  Lightbox2 options
	lightbox.option({
		'albumLabel': '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE']; ?> %1 <?php echo $TEXT['OF']; ?> %2'
	})
	</script>
	<?php
}

// Get total number of items
$query_total_num = $database->query("SELECT item_id FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id' AND active = '1' AND title != ''");
$total_num = $query_total_num->numRows();

// Work-out if we need to add limit code to sql
if ($setting_items_per_page != 0) {
	$limit_sql = " LIMIT $position, $setting_items_per_page";
} else {
	$limit_sql = '';
}

// Get group field from db
$group_field = $database->query("SELECT field_id, extra, label, template FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` WHERE type = 'group' LIMIT 1");

// Order asc or desc?
$group_order    = $order_by_group_asc    ? 'ASC' : 'DESC';
$position_order = $order_by_position_asc ? 'ASC' : 'DESC';

// If a group field is defined order by group and position
if ($group_field->numRows() == 1) {
	$group = $group_field->fetchRow();
	$group_field_id = $group['field_id'];
	$group_names    = explode(',', $group['extra']);
	$group_label    = $group['label'];
	$group_template = $group['template'];
	$sql = "SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` i INNER JOIN `".TABLE_PREFIX."mod_".$mod_name."_item_fields` f ON i.item_id = f.item_id WHERE i.section_id = '$section_id' AND i.active = '1' AND i.title != '' AND f.field_id = '$group_field_id' ORDER BY f.value $group_order, i.position ".$position_order.$limit_sql;
}
// If no group field is defined just order by position
else {
	$sql = "SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id' AND active = '1' AND title != '' ORDER BY position ".$position_order.$limit_sql;
}

// Query items of this page
$query_items = $database->query($sql);
$num_items   = $query_items->numRows();

// Create previous and next links
if ($setting_items_per_page != 0) {
	if ($position > 0) {
		if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
			$pl_prepend = '&laquo; <a href="?p='.($position-$setting_items_per_page).'&g='.$_GET['g'].'">';
		} else {
			$pl_prepend = '&laquo; <a href="?p='.($position-$setting_items_per_page).'">';
		}
		$pl_append = '</a>';
		$previous_link = $pl_prepend.$TEXT['PREVIOUS'].$pl_append;
		$previous_page_link = $pl_prepend.$TEXT['PREVIOUS_PAGE'].$pl_append;
	} else {
		$previous_link = '';
		$previous_page_link = '';
	}
	if ($position+$setting_items_per_page >= $total_num) {
		$next_link = '';
		$next_page_link = '';
	} else {
		if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
			$nl_prepend = '<a href="?p='.($position+$setting_items_per_page).'&g='.$_GET['g'].'"> ';
		} else {
			$nl_prepend = '<a href="?p='.($position+$setting_items_per_page).'"> ';
		}
		$nl_append = '</a> &raquo;';
		$next_link = $nl_prepend.$TEXT['NEXT'].$nl_append;
		$next_page_link = $nl_prepend.$TEXT['NEXT_PAGE'].$nl_append;
	}
	if ($position+$setting_items_per_page > $total_num) {
		$num_of = $position+$num_items;
	} else {
		$num_of = $position+$setting_items_per_page;
	}
	$out_of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
	$of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OF']).' '.$total_num;
	$display_previous_next_links = '';
} else {
	$display_previous_next_links = 'none';
}


	
// HEADER

if ($display_previous_next_links == 'none') {
	echo  str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,'','','','','','', $display_previous_next_links, $MOD_ONEFORALL[$mod_name]['TXT_ITEM']), $setting_header);
} else {
	echo str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE, $next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links, $MOD_ONEFORALL[$mod_name]['TXT_ITEM']), $setting_header);
}



// GENERATE THE PLACEHOLDERS

// Make array of general placeholders
$general_placeholders = array('[PAGE_TITLE]', '[THUMB]', '[THUMBS]', '[IMAGE]', '[IMAGES]', '[TITLE]', '[ITEM_ID]', '[LINK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[USER_EMAIL]', '[TEXT_READ_MORE]', '[TXT_ITEM]');

// ...and the field placeholders
$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_fields`");

if ($query_fields->numRows() > 0) {
	while ($field = $query_fields->fetchRow()) {

		// Array with field placeholders
		$field_id                  = $field['field_id'];
		$field_placeholders_name[] = '['.strtoupper(stripslashes($field['name'])).']';
		$field_placeholders_num[]  = '[FIELD_'.$field_id.']';

		// Array with field types, label and templates
		$types[$field_id]     = stripslashes($field['type']);
		$extra[$field_id]     = stripslashes($field['extra']);
		$labels[$field_id]    = stripslashes($field['label']);
		$templates[$field_id] = stripslashes($field['template']);
	}
}
else {
	$field_placeholders_name = array();
	$field_placeholders_num  = array();
	$templates               = array();
}



// LOOP TROUGH ITEMS AND SHOW THEM
$printed_group       = -1;
$count_groups        = 0;
$close_group_wrapper = '</div>'."\n";

if ($num_items > 0) {
	while ($item = $query_items->fetchRow()) {

		// Get item data
		$item_id = stripslashes($item['item_id']);
		$title   = htmlspecialchars(stripslashes($item['title']));
		$uid     = $item['modified_by']; // User who last modified the item
		// Workout date and time of last modified item
		$item_date = gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE);
		$item_time = gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE);
		// Work-out the item link
		$item_link = WB_URL.PAGES_DIRECTORY.$wb->page['link'].$item['link'].PAGE_EXTENSION;
		if (isset($_GET['p']) AND $position > 0) {
			$item_link .= '?p='.$position;
		}
		if (isset($_GET['g']) AND is_numeric($_GET['g'])) {
			if (isset($_GET['p']) AND $position > 0) { $item_link .= '&'; } else { $item_link .= '?'; }
			$item_link .= 'g='.$_GET['g'];
		}

		// Get item fields data
		$query_item_fields = $database->query("SELECT field_id, value FROM `".TABLE_PREFIX."mod_".$mod_name."_item_fields` WHERE item_id = ".$item_id);

		if ($query_item_fields->numRows() > 0) {
			while ($item_fields = $query_item_fields->fetchRow()) {

				$field_id          = $item_fields['field_id'];
				$values[$field_id] = trim(stripslashes($item_fields['value']));

				// If needed print group title
				$current_group = $values[$field_id] - 1;
				if ($types[$field_id] == 'group' && $printed_group !== $current_group && $show_group_headers) {
					// Count groups to set the wrappers properly
					$count_groups++;
					// Set the printed group to the current item group
					$printed_group = $current_group;
					// Opening group wrapper with 2 separate css classes
					$open_group_wrapper = '<div class="mod_'.$mod_name.'_group_wrapper_f mod_'.$mod_name.'_group_'.$values[$field_id].'_f">';
					// Print the wrapped group template
					$group_template = $open_group_wrapper.$group['template'];
					$search         = array('[CUSTOM_LABEL]', '[GROUP_NAME]');
					$replace        = array($group_label, $group_names[$current_group]);
					if ($count_groups > 1) {
				    	echo $close_group_wrapper;
					}
					echo str_replace($search, $replace, $group_template);
				}

				// Display warning if field is disabled
				if ($types[$field_id] == 'disabled') {
					$values[$field_id] = '<span style="color: red;"><strong>'.$MOD_ONEFORALL[$mod_name]['ERR_FIELD_DISABLED'].'</strong><br />'.$MOD_ONEFORALL[$mod_name]['ERR_FIELD_RE_ENABLE'].'</span>';
				}

				// For textareas convert newline to <br />
				if ($types[$field_id] == 'textarea') {
					$values[$field_id] = nl2br($values[$field_id]);
				}

				// For wysiwyg replace [wblinkXX] by real link (XX = PAGE_ID)
				if ($types[$field_id] == 'wysiwyg') {
					$pattern = '/\[wblink(.+?)\]/s';
					preg_match_all($pattern, $values[$field_id], $ids);
					foreach ($ids[1] as $page_id) {
						$pattern = '/\[wblink'.$page_id.'\]/s';
						// Get page link
						$link              = $database->get_one("SELECT link FROM `".TABLE_PREFIX."pages` WHERE page_id = '$page_id' LIMIT 1");
						$page_link         = page_link($link);
						$values[$field_id] = preg_replace($pattern, $page_link, $values[$field_id]);
					}
				}

				// For code evaluate the php code
				if ($types[$field_id] == 'code' && $field_type_code) {
					$retval = eval($values[$field_id]);
					// If return is used in the evaluated code
					if (is_string($retval)) {
						$values[$field_id] = $retval;
					// Parse error in the evaluated code
					} else if (false === $retval) {
						$values[$field_id] = '<span style="color: red;">ERROR: Parse error in the evaluated code. Please check your code.</span>';
					}
				}

				// For wb_link convert page_id to page link
				if ($types[$field_id] == 'wb_link' && is_numeric($values[$field_id])) {
					$link = $database->get_one("SELECT link FROM `".TABLE_PREFIX."pages` WHERE page_id = '".$values[$field_id]."' LIMIT 1");
					$values[$field_id] = page_link($link);
				}

				// For foldergallery_link convert gallery category to gallery link
				if ($types[$field_id] == 'foldergallery_link') {
					if (is_numeric($values[$field_id])) {
						$query = "SELECT p.link, c.categorie FROM `".TABLE_PREFIX."pages` p
								INNER JOIN `".TABLE_PREFIX."sections` s ON p.page_id = s.page_id 
								INNER JOIN `".TABLE_PREFIX."mod_foldergallery_categories` c ON s.section_id = c.section_id 
								WHERE c.id = ".$values[$field_id]." LIMIT 1";
						$get_categories    = $database->query($query);
						$cat               = $get_categories->fetchRow();				
						$link              = htmlspecialchars(stripslashes($cat['link']));
						$categorie         = htmlspecialchars(stripslashes($cat['categorie']));
						$values[$field_id] = $admin->page_link($link).'?cat=/'.$categorie;
					} else {
						$values[$field_id] = '';
					}
				}

				// For oneforall_link convert item_id to detail page link
				if ($types[$field_id] == 'oneforall_link') {
					if (is_numeric($values[$field_id])) {
						$query = "SELECT p.link as page_link, i.link as item_link FROM `".TABLE_PREFIX."pages` p
								INNER JOIN `".TABLE_PREFIX."mod_".strtolower($extra[$field_id])."_items` i
								ON p.page_id = i.page_id 
								WHERE i.item_id = ".$values[$field_id]." AND active = '1'
								ORDER BY i.item_id
								LIMIT 1";
						$get_links = $database->query($query);
						if ($get_links->numRows() > 0) {
							$links             = $get_links->fetchRow();				
							$values[$field_id] = WB_URL.PAGES_DIRECTORY.$links['page_link'].$links['item_link'].PAGE_EXTENSION;
						} else {
							$values[$field_id] = '';
						}
					}
				}

				// For media and upload add WB_URL and MEDIA_DIRECTORY to the link
				if ($types[$field_id] == 'media' || $types[$field_id] == 'upload' && !empty($values[$field_id])) {
					$values[$field_id] = WB_URL.MEDIA_DIRECTORY.$values[$field_id];
				}

				// Serialized values
				$unserialized = @unserialize($values[$field_id]);
				if ($unserialized !== false || $values[$field_id] == 'b:0;') {
					if (!empty($unserialized)) {
	
						// Some defaults
						$glue         = ', ';
						$pieces       = null;
						$unserialized = array_filter($unserialized, 'strlen'); // Filter empty values except 0
						$a_options    = empty($extra[$field_id]) ? null : explode(',', $extra[$field_id]);
	
						// Deal with different field types
						switch ($types[$field_id]) {
							case 'datepicker_start_end':
								$glue   = ' '.$MOD_ONEFORALL[$mod_name]['TXT_DATEDATE_SEPARATOR'].' ';
								$pieces = $unserialized;
								break;
							case 'datetimepicker_start_end':
								$glue   = ' '.$MOD_ONEFORALL[$mod_name]['TXT_DATEDATE_SEPARATOR'].' ';
								$pieces = $unserialized;
								break;
							case 'multiselect':
								foreach ($unserialized as $index) {
									$pieces[] = trim($a_options[$index]);
								}
								break;
							case 'checkbox':
								$pieces = array_intersect_key($a_options, $unserialized);
								break;
							case 'switch':
								$pieces = array_key_exists(1, $unserialized) ? $a_options[0] : $a_options[1];
								break;
							case 'radio':
								$pieces = array_key_exists(1, $unserialized) ? $a_options[$unserialized[1]] : '';
								break;
							default:
								$glue   = ' ';
								$pieces = $unserialized;
						}
	
						// If needed make array to string conversion
						$values[$field_id] = is_array($pieces) ? implode($glue, $pieces) : $pieces;
					} else {
						$values[$field_id] = '';
					}
				}

				// For droplet
				if ($types[$field_id] == 'droplet' && !empty($values[$field_id])) {
					// Get the droplet
					$droplet = $database->get_one("SELECT name FROM `".TABLE_PREFIX."mod_droplets` WHERE active = 1 AND id = '".$values[$field_id]."' LIMIT 1");
					$values[$field_id] = '[['.$droplet.']]';
				}

				// For select
				if ($types[$field_id] == 'select' && !empty($values[$field_id])) {
					$index     = $values[$field_id] - 1;
					$a_options = explode(',', $extra[$field_id]);
					$values[$field_id] = trim($a_options[$index]);
				}
			}
		}



		// ITEM THUMB(S) AND IMAGE(S)

		// Initialize or reset thumb(s) and image(s) befor laoding next item
		$thumb_arr = array();
		$image_arr = array();
		$thumb     = '';
		$image     = '';

		// Prepare thumb and image directory pathes and urls
		$thumb_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/';
		$img_dir   = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/';
		$thumb_url = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/';
		$img_url   = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/';

		// Get image data from db
		$query_image = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE item_id = '$item_id' AND active = '1' ORDER BY position ASC");
		if ($query_image->numRows() > 0) {
			while ($image = $query_image->fetchRow()) {
				$image       = array_map('stripslashes', $image);
				$image       = array_map('htmlspecialchars', $image);
				$img_id      = $image['img_id'];
				$image_file  = $image['filename'];
				$img_alt     = $image['alt'];
				$img_title   = $image['title'];
				$img_caption = $image['caption'];

				// Check if png image has a jpg thumb (version < 0.9 used jpg thumbs only)
				$thumb_file = $image_file;
				if (!file_exists($thumb_dir.$thumb_file)) {
					$thumb_file = str_replace('.png', '.jpg', $thumb_file);
				}

				// Make array of all item thumbs and images
				if (file_exists($thumb_dir.$thumb_file) && file_exists($img_dir.$image_file)) {
					// If needed add lightbox2 link to the thumb/image...
					if ($setting_lightbox2 == 'overview' || $setting_lightbox2 == 'all') {
						$thumb_prepend = '<a href="'.$img_url.$image_file.'" rel="lightbox[image_'.$item_id.']" title="'.$img_title.'"><img src="';
						$img_prepend   = '<a href="'.$img_url.$image_file.'" rel="lightbox[image_'.$item_id.']" title="'.$img_title.'"><img src="';
						$thumb_append  = '" alt="'.$img_alt.'" title="'.$img_title.'" class="mod_'.$mod_name.'_main_thumb_f" /></a>';
						$img_append    = '" alt="'.$img_alt.'" title="'.$img_title.'" class="mod_'.$mod_name.'_main_img_f" /></a>';
					// ...else add thumb/image only
					} else {
						$thumb_prepend = '<a href="'.$item_link.'"><img src="';
						$img_prepend   = '<img src="';
						$thumb_append  = '" alt="'.$img_alt.'" title="'.$img_title.'" class="mod_'.$mod_name.'_main_thumb_f" /></a>';
						$img_append    = '" alt="'.$img_alt.'" title="'.$img_title.'" class="mod_'.$mod_name.'_main_img_f" />';
					}
					// Make array
					$thumb_arr[] = $thumb_prepend.$thumb_url.$thumb_file.$thumb_append;
					$image_arr[] = $img_prepend.$img_url.$image_file.$img_append;
				}
			}
		}
		// Main thumb/image (image position 1)
		$thumb = empty($thumb_arr[0]) ? '' : $thumb_arr[0];
		$image = empty($image_arr[0]) ? '' : $image_arr[0];
		unset($thumb_arr[0]);
		unset($image_arr[0]);

		// Make strings for use in the item templates
		$thumbs = implode("\n", $thumb_arr);
		$images = implode("\n", $image_arr);



		// REPLACE PLACEHOLDERS BY VALUES

		// Get user data
		if (empty($users[$uid]['username'])) {
			$uid                         = '';
			$users[$uid]['username']     = '';
			$users[$uid]['display_name'] = '';
			$users[$uid]['email']        = '';
		}

		// Make array of general values of current item
		$general_values = array(PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, $item_id, $item_link, $item_date, $item_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], $TEXT['READ_MORE'], $MOD_ONEFORALL[$mod_name]['TXT_ITEM']);

		// Replace placeholders in field templates by label and value
		$ready_templates = array();
		foreach ($templates as $field_id => $template) {

			// If value is empty or a group title return a blank template
			if (!isset($values[$field_id]) || empty($values[$field_id]) || $types[$field_id] == 'group') {
				$template = '';
			} else {
				$search   = array('[CUSTOM_LABEL]', '[CUSTOM_CONTENT]');
				$replace  = array($labels[$field_id], $values[$field_id]);
				$template = str_replace($search, $replace, $template);
			}

			// Array of templates with replaced placeholders
			$ready_templates[] = $template;
		}

		// Print item loop
		$search  = array_merge($general_placeholders, $field_placeholders_name, $field_placeholders_num);
		$replace = array_merge($general_values, $ready_templates, $ready_templates);
		echo str_replace($search, $replace, $setting_item_loop);

		// Clear arrays for next item
		unset($values);
		unset($ready_templates);
	}

	// Print closing group wrapper for last group
	if ($count_groups > 0) {
		echo $close_group_wrapper;
	}
}
else {
	// No active item
	echo '<p class="mod_'.$mod_name.'_none_found_f">'.$TEXT['NONE_FOUND'].'.</p>';
}



// FOOTER

if ($display_previous_next_links == 'none') {
	echo  str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,'','','','','','', $display_previous_next_links, $MOD_ONEFORALL[$mod_name]['TXT_ITEM']), $setting_footer);
} else {
	echo str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,$next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links, $MOD_ONEFORALL[$mod_name]['TXT_ITEM']), $setting_footer);
}

?> 