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


//Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit('Cannot access this file directly'); 
}




// **********************************************
// SET DEFAULT VALUES OF SOME ADDITIONAL SETTINGS
// **********************************************


// GENERAL SETTINGS
// ****************

// Display settings to admin only (user_id = 1)
$settings_admin_only = true;

// Order items by position ascending (true) or descending (false)
$order_by_position_asc = true;

// Group headers (only invoked if the group field is defined)
// Show group headers on overview page
$show_group_headers = true;
// Order groups ascending (true) or descending (false)
$order_by_group_asc = true;

// Generate item detail pages and corresponding access files
// Changing this setting after adding items may cause problems!
$view_detail_pages = true;

// Show additional field meta description on the modify item page
// Will set title and meta description to the html header
// of every item detail page using jquery
$field_meta_desc = true;

// Set extensions accepted by the media field as csv
// Default: image extensions like jpg, png, gif and svg
$media_extensions = 'jpg,png,gif,svg';

// Set extensions accepted by the upload field as csv
// Default: text doc extensions like txt, rtf, doc, docx, odt and pdf
$upload_extensions = 'txt,rtf,doc,docx,odt,pdf';

// Allow moving and/or duplicating an item from one section to another
// Set to false if there is for example just one module section
$show_item_mover = true;

// If moving/duplicating is disabled you can still allow duplicating an item
// This setting is overwritten by the setting above
$show_item_duplicator = true;

// Backend item wysiwyg and code editor width
// Both columns = 100% (true) or right column = 80% (false)
$wysiwyg_full_width = false;

// Allow the field type code which makes use of the language construct eval()
// CAUTION: eval() is dangerous because it allows execution of arbitrary PHP code
// Any user provided php code is not validated by the module OneForAll!
$field_type_code = false;




// IMAGES AND THUMBNAILS DEFAULTS (BACKEND)
// ****************************************

// Selectable thumbnail default sizes (modify page settings)
$thumb_resize_smallest   = 40;
$thumb_resize_largest    = 200;
$thumb_resize_steps      = 20;

// Accepted max lenght of image filenames (modify item)
$filename_max_length     = 40;

// For item images set image resize default values (modify item)
$img_resize['imgresize'] = '';  // yes = selected by default
$img_resize['quality']   = 75;
$img_resize['maxwidth']  = 400;
$img_resize['maxheight'] = 300;






