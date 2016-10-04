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


// SHOW ITEM DETAIL PAGE
// *********************

// Get page settings
$query_page_settings = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_page_settings` WHERE section_id = '$section_id'");
if ($query_page_settings->numRows() > 0) {
	$fetch_page_settings = $query_page_settings->fetchRow();
	$setting_item_header = stripslashes($fetch_page_settings['item_header']);
	$setting_item_footer = stripslashes($fetch_page_settings['item_footer']);
	$setting_lightbox2   = stripslashes($fetch_page_settings['lightbox2']);
} else {
	$setting_item_header = '';
	$setting_item_footer = '';
}
	
// If requested include lightbox2 (css is appended to the frontend.css stylesheet)
if ($setting_lightbox2 == "detail" || $setting_lightbox2 == "all") {
	// Load jQuery if not loaded yet
	?>
	<script>window.jQuery || document.write('<script src="<?php echo WB_URL; ?>/include/jquery/jquery-min.js"><\/script>')</script>
	<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/lightbox2/js/lightbox.js"></script>
	<script type="text/javascript">
	//  Lightbox2 options
	$(function () {
	    var lightbox, options;
	    options = new LightboxOptions;

	    options.fileLoadingImage = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/lightbox2/images/loading.gif';
	    options.fileCloseImage   = '<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/js/lightbox2/images/close.png';
	    options.labelImage       = '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_IMAGE']; ?>';
	    options.labelOf          = '<?php echo $TEXT['OF']; ?>';

	    return lightbox          = new Lightbox(options);
	});
	</script>
	<?php
}

// Get page info
$query_page = $database->query("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".PAGE_ID."'");
if ($query_page->numRows() > 0) {
	$page      = $query_page->fetchRow();
	$page_link = page_link($page['link']);
	if (isset($_GET['p']) AND $position > 0) {
		$page_link .= '?p='.$_GET['p'];
	}
} else {
	exit('Page not found');
}



// If item with given page id exists get item data
$query_item = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE item_id = '".ITEM_ID."' AND active = '1'");
if ($query_item->numRows() > 0) {
	$item = $query_item->fetchRow();



	// CREATE PREVIOUS AND NEXT LINKS

	// Get current position	
	$position = $item['position'];

	// For pagination get total number of items and top position of this section
	$query_total_num = $database->query("SELECT item_id FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id' AND active = '1' AND title != ''");
	$total_num       = $query_total_num->numRows();
	$top_position    = $database->get_one("SELECT MAX(`position`) AS `top_position` FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE section_id = '$section_id' AND active = '1' AND title != ''");

	// Create previous and next links
	$query_surrounding = $database->query("SELECT item_id FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE position != '$position' AND section_id = '$section_id' AND active = '1' LIMIT 1");
	if ($query_surrounding->numRows() > 0) {
		// Get previous
		if ($position > 1) {
			$query_previous = $database->query("SELECT link,position FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE position < '$position' AND section_id = '$section_id' AND active = '1' ORDER BY position DESC LIMIT 1");
			if ($query_previous->numRows() > 0) {
				$previous       = $query_previous->fetchRow();
				$previous_link  = '&laquo; <a href="'.WB_URL.PAGES_DIRECTORY.$page['link'].$previous['link'].'.php">'.$TEXT['PREVIOUS'].'</a>';
				$previous_link2 = '<a href="'.WB_URL.PAGES_DIRECTORY.$page['link'].$previous['link'].'.php">'.$TEXT['NEXT'].'</a> &raquo;';
			}
		} else {
			$previous_link  = '';
			$previous_link2 = '';
		}
		// Get next
		if ($position  >= $top_position) {
			$next_link  = '';
			$next_link2 = '';
		} else {
			$query_next = $database->query("SELECT link,position FROM `".TABLE_PREFIX."mod_".$mod_name."_items` WHERE position > '$position' AND section_id = '$section_id' AND active = '1' ORDER BY position ASC LIMIT 1 ");
			if ($query_next->numRows() > 0) {
				$next       = $query_next->fetchRow();
				$next_link  = '<a href="'.WB_URL.PAGES_DIRECTORY.$page['link'].$next['link'].'.php"> '.$TEXT['NEXT'].'</a> &raquo;';
				$next_link2 = '&laquo; <a href="'.WB_URL.PAGES_DIRECTORY.$page['link'].$next['link'].'.php"> '.$TEXT['PREVIOUS'].'</a>';
			}
		}
	} else {
		$next_link      = '';
		$next_link2     = '';
		$previous_link  = '';
		$previous_link2 = '';
	}
	// If item order is desc we have to interchange the links
	if (!$order_by_position_asc) {
		$previous_link = $next_link2;
		$next_link     = $previous_link2;
	}
	$out_of = $position.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
	$of     = $position.' '.strtolower($TEXT['OF']).' '.$total_num;



	// GENERATE THE PLACEHOLDERS

	// Make array of general placeholders
	$general_placeholders = array('[PAGE_TITLE]', '[THUMB]', '[THUMBS]', '[IMAGE]', '[IMAGES]', '[TITLE]', '[ITEM_ID]', '[BACK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[USER_EMAIL]', '[PREVIOUS]', '[NEXT]', '[OUT_OF]', '[OF]', '[TEXT_OUT_OF]', '[TEXT_OF]', '[TXT_ITEM]', '[TXT_DESCRIPTION]', '[TXT_BACK]');

	// ...and the field placeholders
	$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_fields`");

	if ($query_fields->numRows() > 0) {
		while ($field = $query_fields->fetchRow()) {

			// Array with field placeholders
			$field_id                  = $field['field_id'];
			$field_placeholders_name[] = '['.strtoupper(stripslashes($field['name'])).']';
			$field_placeholders_num[]  = '[FIELD_'.$field_id.']';

			// Array with field types, label and templates
			$types[$field_id]     = $field['type'];
			$extra[$field_id]     = $field['extra'];
			$labels[$field_id]    = $field['label'];
			$templates[$field_id] = $field['template'];
		}
	}
	else {
		$field_placeholders_name = array();
		$field_placeholders_num  = array();
		$templates               = array();
	}



	// ITEM DATA

	// Item title
	$title     = htmlspecialchars(stripslashes($item['title']));
	// User who last modified the item
	$uid       = $item['modified_by'];
	// Workout date and time of last modified item
	$item_date = gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE);
	$item_time = gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE);

	// Get item fields data
	$query_item_fields = $database->query("SELECT field_id, value FROM `".TABLE_PREFIX."mod_".$mod_name."_item_fields` WHERE item_id = ".ITEM_ID);

	if ($query_item_fields->numRows() > 0) {
		while ($item_fields = $query_item_fields->fetchRow()) {
			$field_id          = $item_fields['field_id'];
			$values[$field_id] = trim(stripslashes($item_fields['value']));

			// Display warning if field is disabled
			if ($types[$field_id] == 'disabled') {
				$values[$field_id] = '<span style="color: red;"><strong>'.$MOD_ONEFORALL[$mod_name]['ERR_FIELD_DISABLED'].'</strong><br>'.$MOD_ONEFORALL[$mod_name]['ERR_FIELD_RE_ENABLE'].'</span>';
			}

			// For textareas convert newline to <br>
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
					$link              = $database->get_one("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id' LIMIT 1");
					$wysiwyg_page_link = page_link($link);
					$values[$field_id] = preg_replace($pattern, $wysiwyg_page_link, $values[$field_id]);
				}
			}

			// For wb_link convert page_id to page link
			if ($types[$field_id] == 'wb_link' && is_numeric($values[$field_id])) {
				$link = $database->get_one("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".$values[$field_id]."' LIMIT 1");
				$values[$field_id] = page_link($link);
			}

			// For foldergallery_link convert gallery category to gallery link
			if ($types[$field_id] == 'foldergallery_link') {
				if (is_numeric($values[$field_id])) {
					$query = "SELECT p.link, c.categorie FROM ".TABLE_PREFIX."pages p
							INNER JOIN ".TABLE_PREFIX."sections s ON p.page_id = s.page_id 
							INNER JOIN ".TABLE_PREFIX."mod_foldergallery_categories c ON s.section_id = c.section_id 
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

			// For media and upload add WB_URL and MEDIA_DIRECTORY to the link
			if (($types[$field_id] == 'media' || $types[$field_id] == 'upload') && !empty($values[$field_id])) {
				$values[$field_id] = WB_URL.MEDIA_DIRECTORY.$values[$field_id];
			}

			// If value is serialized, unserialize it and convert it to string
			$unserialized = @unserialize($values[$field_id]);
			if ($unserialized !== false || $values[$field_id] == 'b:0;') {
				// Filter empty values
				$array_size = count(array_filter($unserialized));
				if ($array_size > 0) {
					// For datepickers with start and end use "until" to separate the two dates
					if ($types[$field_id] == 'datepicker_start_end' || $types[$field_id] == 'datetimepicker_start_end') {
						$glue = ' '.$MOD_ONEFORALL[$mod_name]['TXT_DATEDATE_SEPARATOR'].' ';
					} else {
						$glue = ' ';
					}
					$values[$field_id] = implode($glue, $unserialized);
				} else {
					$values[$field_id] = '';
				}
			}

			// For droplet
			if ($types[$field_id] == 'droplet' && !empty($values[$field_id])) {
				// Get the droplet
				$droplet = $database->get_one("SELECT name FROM ".TABLE_PREFIX."mod_droplets WHERE active = 1 AND id = '".$values[$field_id]."' LIMIT 1");
				$values[$field_id] = '[['.$droplet.']]';
			}

			// For select
			if ($types[$field_id] == 'select' && !empty($values[$field_id])) {
				$index     = $values[$field_id] - 1;
				$a_options = explode(',', $extra[$field_id]);
				$values[$field_id] = $a_options[$index];
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
	$thumb_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.ITEM_ID.'/';
	$img_dir   = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.ITEM_ID.'/';
	$thumb_url = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.ITEM_ID.'/';
	$img_url   = WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.ITEM_ID.'/';

	// Get image data from db
	$query_image = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_images` WHERE `item_id` = '".ITEM_ID."' AND `active` = '1' ORDER BY position ASC");
	if ($query_image->numRows() > 0) {
		while ($image = $query_image->fetchRow()) {
			$image       = array_map('stripslashes', $image);
			$image       = array_map('htmlspecialchars', $image);
			$img_id      = $image['img_id'];
			$image_file  = $image['filename'];
			$img_alt     = $image['alt'];
			$img_title   = $image['title'];
			$img_caption = $image['caption'];

			// Thumbs use .jpg extension only
			$thumb_file = str_replace('.png', '.jpg', $image_file);

			// Prepare div image wrapper for image caption
			$caption_prepend = empty($img_caption) ? '' : '<div class="mod_oneforall_item_caption_f">';
			$caption_append  = empty($img_caption) ? '' : '<br />'.$img_caption.'</div>';

			// Make array of all item thumbs and images
			if (file_exists($thumb_dir.$thumb_file) && file_exists($img_dir.$image_file)) {
				// If needed add lightbox2 link to the thumb/image...
				if ($setting_lightbox2 == "detail" || $setting_lightbox2 == 'all') {
					$prepend = "<a href='".$img_url.$image_file."' rel='lightbox[image_".ITEM_ID."]' title='".$img_title."'><img src='";
					$thumb_append = "' alt='".$img_alt."' title='".$img_title."' class='mod_".$mod_name."_item_thumb_f' /></a>";
					$img_append = "' alt='".$img_alt."' title='".$img_title."' class='mod_".$mod_name."_item_img_f' /></a>";
				// ...else add thumb/image only
				} else {
					$prepend = "<img src='";
					$thumb_append = "' alt='".$img_alt."' title='".$img_title."' class='mod_".$mod_name."_item_thumb_f' />";
					$img_append = "' alt='".$img_alt."' title='".$img_title."' class='mod_".$mod_name."_item_img_f' />";
				}
				// Make array
				$thumb_arr[] = $prepend.$thumb_url.$thumb_file.$thumb_append;
				$image_arr[] = $caption_prepend.$prepend.$img_url.$image_file.$img_append.$caption_append;
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

	// Make array of general values of this item
	$general_values = array(PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, ITEM_ID, $page_link, $item_date, $item_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], $previous_link, $next_link, $out_of, $of,  $TEXT['OUT_OF'], $TEXT['OF'], $MOD_ONEFORALL[$mod_name]['TXT_ITEM'], $MOD_ONEFORALL[$mod_name]['TXT_DESCRIPTION'], $TEXT['BACK']);

	// Replace placeholders in field templates by label and value
	$ready_templates = array();
	foreach ($templates as $field_id => $template) {

		// If value is empty return a blank template
		if (!isset($values[$field_id]) || empty($values[$field_id])) {
			$template = '';
		} else {
			$search   = array('[CUSTOM_LABEL]', '[CUSTOM_CONTENT]');
			$replace  = array($labels[$field_id], $values[$field_id]);
			$template = str_replace($search, $replace, $template);
		}

		// Array of templates with replaced placeholders
		$ready_templates[] = $template;
	}

	// Print item header and footer
	$search  = array_merge($general_placeholders, $field_placeholders_name, $field_placeholders_num);
	$replace = array_merge($general_values, $ready_templates, $ready_templates);
	echo str_replace($search, $replace, $setting_item_header);
	echo str_replace($search, $replace, $setting_item_footer);

// No item found
} else {
	echo $TEXT['NONE_FOUND'];
	return;
}

?>