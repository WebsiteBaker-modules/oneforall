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




// *****************************************
// SET DEFAULT VALUES OF SOME BASIC SETTINGS
// *****************************************


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
// WARNING: Changing this setting after adding items may cause problems!
// Item access files and their corresponding links will not be updated automatically
// After changing you might have to resave all items manually to be up-to-date
$view_detail_pages = true;

// Show additional field meta description on the modify item page
// This will only take effect if item detail pages are enabled
// For SEO optimization use module SimplePageHead to insert item title
// and a meta description into the html head of every item detail page
$field_meta_desc = true;

// Set extensions accepted by the media field as csv
// Default: image extensions like jpg, png, gif and svg
$media_extensions = 'jpg,png,gif,svg,pdf';

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

// Allow to mark a item as featured.
// Needs the snippet oneforall-featureditems to display them anywhere.
$featured = false;

// ***************
// ITEM SCHEDULING
// ***************

// This feature enables / disables items automatically against a start and end time.
// It is not possible to enable / disable a scheduled item manually since scheduling is prioritized.

// Enable scheduling
$set_scheduling = true;

// Enable scheduling debug mode
$scheduling_debug = false;

// If this format is set, it will overwrite the datetime format given by the jquery ui datepicker language file
// Important! Format must match your datetime format
// eg. 'd#m#Y * H#i'
// See http://php.net/manual/en/datetime.createfromformat.php
$scheduling_format = '';

// Set your timezone adjustment explicitly if the wb constant DEFAULT_TIMEZONE returns unexpected values
// eg. GMT + 1 hour  => $scheduling_timezone = 1;
// eg. GMT - 4 hours => $scheduling_timezone = -4;
$scheduling_timezone = '';



// ****************************************
// IMAGES AND THUMBNAILS DEFAULTS (BACKEND)
// PLUPLOAD, A MULTI RUNTIME FILE UPLOADER
// ****************************************

// Selectable thumbnail default sizes (modify page settings)
$thumb_resize_smallest   = 40;
$thumb_resize_largest    = 400;
$thumb_resize_steps      = 20;

// Accepted max lenght of image filenames (modify item)
$filename_max_length     = 40;

// For item images set image resize default values (modify item)
$img_resize['imgresize'] = 'yes';  // yes = selected by default
$img_resize['quality']   = 75;
$img_resize['maxwidth']  = 1000;
$img_resize['maxheight'] = 800;

// Plupload max file size in MB
$max_file_size           = 2;
