<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2017, Christoph Marti

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


// Get item id
if (!isset($_POST['item_id']) OR !is_numeric($_POST['item_id'])) {
    header("Location: ".ADMIN_URL."/pages/index.php");
    exit();
} else {
    $id      = $_POST['item_id'];
    $item_id = $id;
}

// Include path
$inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);

// Includes
if (!defined('SYSTEM_RUN')) {require( (dirname(dirname((__DIR__)))).'/config.php');}
require($inc_path.'/resize_img.php');
require($inc_path.'/pngthumb.php');
if (!function_exists('make_dir')){require(WB_PATH.'/framework/functions.php');}

// Get module name and config
require($inc_path.'/info.php');
require($inc_path.'/config.php');

// Look for language file
if (is_readable(__DIR__.'/languages/EN.php')) {require(__DIR__.'/languages/EN.php');}
if (is_readable(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php')) {require(__DIR__.'/languages/'.DEFAULT_LANGUAGE.'.php');}
if (is_readable(__DIR__.'/languages/'.LANGUAGE.'.php')) {require(__DIR__.'/languages/'.LANGUAGE.'.php');}

// Create new order object
require(WB_PATH.'/framework/class.order.php');
$order = new order(TABLE_PREFIX.'mod_'.$mod_name.'_items', 'position', 'item_id', 'section_id');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Work-out item dir
$item_dir = $page['link'];

// Remove any tags and add slashes
$old_link         = strip_tags($admin->get_post('link'));
$old_section_id   = strip_tags($admin->get_post('section_id'));
$new_section_id   = strip_tags($admin->get_post('new_section_id'));
$action           = strip_tags($admin->get_post('action'));
$title            = $admin->add_slashes(strip_tags($admin->get_post('title')));
$scheduling_start = strip_tags($admin->get_post('scheduling_start'));
$scheduling_end   = strip_tags($admin->get_post('scheduling_end'));
$description      = $admin->add_slashes(strip_tags($admin->get_post('description')));

// Images
$images = array();
if (!empty($_POST['images'])) {
    foreach ($_POST['images'] as $img_id => $image) {
        // Strip tags and add slashes
        $image = array_map('strip_tags', $image);
        $image = array_map('addslashes', $image);
        // Sanitize vars
        $image['active']       = empty($image['active'])       ? 0 : 1;
        $image['delete_image'] = empty($image['delete_image']) ? FALSE : $image['delete_image'];
        // Rejoin images array
        $images[$img_id]       = $image;
    }
}

$imgresize = strip_tags($admin->get_post('imgresize'));
$quality   = strip_tags($admin->get_post('quality'));
$maxheight = strip_tags($admin->get_post('maxheight'));
$maxwidth  = strip_tags($admin->get_post('maxwidth'));
$active    = strip_tags($admin->get_post('active'));
$featured  = strip_tags($admin->get_post('featured'));
$featured_pos  = strip_tags($admin->get_post('featured_pos'));

// SCHEDULING

// Get the datetime format defined in config.php or ...
if (!empty($scheduling_format)) {
    $format = $scheduling_format;
}
// ... get the datetime format defined in the jquery ui datepicker language file
else {
    // Load jquery ui datepicker language file
    $datepicker_lang      = defined('LANGUAGE') && strlen(LANGUAGE) == 2 ? strtolower(LANGUAGE) : 'en';
    $datepicker_lang_path = '/include/jquery/i18n/jquery.ui.datepicker-'.$datepicker_lang.'.js';
    $format = DATE_FORMAT;
    // Get the date format used by datepicker language file
    if (file_exists(WB_PATH.$datepicker_lang_path)) {
        // Read file line by line into array
        $lines = file(WB_PATH.$datepicker_lang_path);
        foreach ($lines as $line) {
            // Check if the line contains the string we are looking for
            if (strpos($line, 'dateFormat') !== false) {
                // Extract the format part
                $pattern     = '#.+\'(.+)\'.+#is';
                $result      = preg_match($pattern, $line, $subpattern);
                // Make the format we will use to parse the datetime with php
                $date_format = str_replace(array('.', '/', '-'), '#', $subpattern[1]);
                $format      = str_ireplace(array('dd', 'mm', 'yy'), array('d', 'm', 'Y'), $date_format).' * H#i';
                break;
            }
        }
    }
}

// Keep the original datetime with the jquery ui datepicker format
$scheduling['start'] = $admin->add_slashes($scheduling_start);
$scheduling['end']   = $admin->add_slashes($scheduling_end);

// Parse the datetime into a timestamp using the given format
$scheduling['ts_start'] = 0;
$scheduling['ts_end']   = 0;

if (!empty($scheduling_start)) {
    $s                      = date_parse_from_format($format, $scheduling_start);
    $scheduling['ts_start'] = mktime($s['hour'], $s['minute'], 0, $s['month'], $s['day'], $s['year']);
}
if (!empty($scheduling_end)) {
    $e                    = date_parse_from_format($format, $scheduling_end);
    $scheduling['ts_end'] = mktime($e['hour'], $e['minute'], 0, $e['month'], $e['day'], $e['year']);
}

// FIELD VALIDATION

// Enter a default title if it has been left blank
if (empty($title)) {
    $title = $MOD_ONEFORALL[$mod_name]['TXT_ITEM'].' '.$id;
}

// Only change 'active' if $_POST['active'] has been sent
// Otherwise the item would be disabled after going back to manual toggling of active state
$query_active = isset($_POST['active']) ? ' `active` = '.$active.',' : '';
$query_featured = isset($_POST['featured']) ? ' `featured` = '.$featured.',' : '';
// $query_featured_pos = isset($_POST['featured_pos']) ? ' `featured_pos` = '.$featured_pos.',' : '';
// Check start and end time of item scheduling
if ($scheduling['ts_start'] && $scheduling['ts_end'] && $scheduling['ts_start'] > $scheduling['ts_end']) {
//    $scheduling['end']    = $scheduling['start'];
//    $scheduling['ts_end'] = $scheduling['ts_start'];
    $errors[] = sprintf($MOD_ONEFORALL[$mod_name]['ERR_INVALID_SCHEDULING'], htmlspecialchars($scheduling_start), htmlspecialchars($scheduling_end));
}
// Serialize start and end time
$scheduling = serialize($scheduling);

// Check email fields
$query_fields = $database->query('SELECT `field_id` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_fields` WHERE `type` = \'email\' ');
if ($query_fields->numRows() > 0) {
    while ($field = $query_fields->fetchRow(MYSQLI_ASSOC)) {
        $field_id = $field['field_id'];
        $email    = $_POST['fields'][$field_id];
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = sprintf($MOD_ONEFORALL[$mod_name]['ERR_INVALID_EMAIL'], htmlspecialchars($email));
        }
    }
}

// Check url fields
$query_fields = $database->query('SELECT `field_id` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_fields` WHERE `type` = \'url\' ');
if ($query_fields->numRows() > 0) {
    while ($field = $query_fields->fetchRow(MYSQLI_ASSOC)) {
        $field_id = $field['field_id'];
        $url      = $_POST['fields'][$field_id];
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = sprintf($MOD_ONEFORALL[$mod_name]['ERR_INVALID_URL'], htmlspecialchars($url));
        }
    }
}

// Check upload fields
$query_fields = $database->query('SELECT `field_id`, `extra` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_fields` WHERE `type` = \'upload\' ');
if ($query_fields->numRows() > 0) {
    while ($field = $query_fields->fetchRow(MYSQLI_ASSOC)) {
        $field_id = $field['field_id'];
        $path     = $field['extra'];

        // Directory path
        $media_dir = WB_PATH.MEDIA_DIRECTORY;
        $path      = trim($path, '/');
        $dir_path  = $media_dir.'/'.$path.'/';

        // Directory url
        $dir_url   = '/'.$path.'/';

        // Make the directory if it is not existing
        if (!is_dir($dir_path)) {
            mkdir($dir_path, OCTAL_DIR_MODE, true);
        }

        // Add index.php file(s) recursively
        $count = substr_count($path, '/');
        for ($i = 0; $i <= $count; $i++) {
            $file = $dir_path.'index.php';
            if (!is_file($file)) {
                $content = ''.
"<?php
header('Location: /index.php');
exit();
?>";
                $handle = fopen($file, 'w');
                fwrite($handle, $content);
                fclose($handle);
                change_mode($file, 'file');
            }
            // Get parent directory
            $path = dirname($path);
        }

        // Delete file if requested
        if (isset($_POST['fields'][$field_id]) && $_POST['fields'][$field_id] == 'delete') {
            $file_path = $database->get_one('SELECT `value` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` WHERE `item_id` = '.(int)$item_id.' AND `field_id` = '.(int)$field_id.' ');
            // Try unlinking file
            if (file_exists($media_dir.$file_path)) {
                unlink($media_dir.$file_path);
            }
            // Reset the post array value 'delete' to make sure it will not be saved in the db
            $_POST['fields'][$field_id] = '';
        }

        // Get the file data
        if (isset($_FILES['fields']['tmp_name'][$field_id]) AND $_FILES['fields']['tmp_name'][$field_id] != '') {

            // Get real filename and set new filename
            $file       = $_FILES['fields']['name'][$field_id];
            $path_parts = pathinfo($file);
            $filename   = $path_parts['basename'];
            $fileext    = $path_parts['extension'];
            $filename   = str_replace('.'.$fileext, '', $filename);  // Filename without extension
            $filename   = str_replace(' ', '_', $filename);          // Replace spaces by underscores
            $fileext    = strtolower($fileext);

            // New file
            $file_path  = $dir_path.$filename.'.'.$fileext;
            $file_url   = $dir_url.$filename.'.'.$fileext;

            // Check the extensions
            if (false === strpos($upload_extensions, $fileext)) {
                $ext_list = empty($upload_extensions) ? $TEXT['NONE_FOUND'] : '.'.str_replace(',', ' / .', $upload_extensions);
                $errors[] = $MESSAGE['GENERIC_FILE_TYPES'].' '.$ext_list.'<br />';
                continue;
            }
            // Check for invalid chars in filename
            if (!preg_match('#^[a-zA-Z0-9._-]*$#', $filename)) {
                $errors[] = $MOD_ONEFORALL[$mod_name]['ERR_INVALID_FILE_NAME'].": ".htmlspecialchars($filename.'.'.$fileext);
                continue;
            }
            // Check if filename already exists
            if (file_exists($file_path)) {
                $errors[] = $MESSAGE['MEDIA_FILE_EXISTS'].": ".htmlspecialchars($filename.'.'.$fileext);
                continue;
            }

            // Add file to the post array so it will be saved in the db
            $_POST['fields'][$field_id] = $file_url;

            // Upload file
            move_uploaded_file($_FILES['fields']['tmp_name'][$field_id], $file_path);
            change_mode($file_path);
        }
    }
}



// Put item data into the session var to prepopulate the text fields after an error message
if (!empty($errors)) {
    foreach ($admin->get_post('fields') as $field_id => $value) {
        $_SESSION[$mod_name]['item']['fields'][$field_id] = $value;
    }
    $_SESSION[$mod_name]['item']['images']         = $images;
    $_SESSION[$mod_name]['item']['imgresize']      = $imgresize;
    $_SESSION[$mod_name]['item']['quality']        = $quality;
    $_SESSION[$mod_name]['item']['maxheight']      = $maxheight;
    $_SESSION[$mod_name]['item']['maxwidth']       = $maxwidth;
    $_SESSION[$mod_name]['item']['active']         = $active;
    $_SESSION[$mod_name]['item']['featured']       = $featured;
    $_SESSION[$mod_name]['item']['featured_pos']   = $featured_pos;
    $_SESSION[$mod_name]['item']['new_section_id'] = $new_section_id;
    $_SESSION[$mod_name]['item']['action']         = $action;
    $_SESSION[$mod_name]['item']['scheduling']     = $scheduling;
    $_SESSION[$mod_name]['item']['description']    = $description;
}



// MOVE ITEM TO ANOTHER ONEFORALL SECTION / PAGE

$moved = false;
if ($old_section_id != $new_section_id AND $action == 'move') {
    // Get new page id, page link and section id
    $query_move = $database->query('SELECT `p`.`page_id`, `p`.`link` '
                                  .'FROM `'.TABLE_PREFIX.'pages` `p` '
                                  .'INNER JOIN `'.TABLE_PREFIX.'sections` `s` '
                                  .'ON `p`.`page_id` = `s`.`page_id` '
                                  .'WHERE `s`.`section_id` = '.(int)$new_section_id.' ');
    $moved      = $query_move->fetchRow(MYSQLI_ASSOC);
    $page_id    = $moved['page_id'];
    $item_dir   = $moved['link'];
    $section_id = $new_section_id;
    // Get new order position
    $position   = $order->get_new($section_id);
    $moved      = true;
}



// ACCESS FILE

// New item filename
$filename = '/'.page_filename($title);
// New item link (replace double and triple by single page spacer)
$new_link = str_replace(PAGE_SPACER.PAGE_SPACER.PAGE_SPACER, PAGE_SPACER, $filename);
$new_link = str_replace(PAGE_SPACER.PAGE_SPACER, PAGE_SPACER, $filename);
// New item dir path
$dir_path = WB_PATH.PAGES_DIRECTORY.$item_dir;

// Old access file (full path)
$old_path = WB_PATH.PAGES_DIRECTORY.$old_link.PAGE_EXTENSION;

// New access file (full path)
$new_path = $dir_path.$new_link.PAGE_EXTENSION;
if (file_exists($new_path) && $new_path != $old_path) {
   $new_path = $dir_path.$new_link.PAGE_SPACER.$item_id.PAGE_EXTENSION;
   $new_link = $new_link.PAGE_SPACER.$item_id;
}

// echo 'oldpath: ' .$old_path . '<br>';
// echo 'newpath: '. $new_path;


// Check if we have a new item
$new = $database->get_one('SELECT EXISTS (SELECT 1 FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` WHERE `item_id` = '.(int)$item_id.' AND `title` = \'\' AND `modified_when` = \'0\' AND `modified_by` = \'0\')');

// Make item access file dir
if ($view_detail_pages) {
    make_dir($dir_path);
    if (!is_writable($dir_path)) {
        $admin->print_error($MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE'], WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id);
    }
    // Create a new access file
    else {
        // First, delete old access file if it exists
        if (file_exists($old_path) && !$new) {
            unlink($old_path);
        }
        // The depth of the page directory in the directory hierarchy
        // 'PAGES_DIRECTORY' is at depth 1
        $pages_dir_depth = count(explode('/',$item_dir))-1;
        // Work-out how many ../'s we need to get to the index page
        $index_location = '../';
        for ($i = 0; $i < $pages_dir_depth; $i++) {
            $index_location .= '../';
        }
        // Write to the file
        $content = ''.
'<?php
$page_id    = '.$page_id.';
$section_id = '.$section_id.';
$item_id    = '.$item_id.';
$item_sid   = '.$section_id.';
define("ITEM_ID",  $item_id);
require("'.$index_location.'index.php");
?>';
        $handle = fopen($new_path, 'w');
        fwrite($handle, $content);
        fclose($handle);
        change_mode($new_path);
    }
}
else {
    $new_link = '';
}



// IMAGE AND THUMBNAIL

// Make sure the target directories exist
// Set array of all directories needed
$directories = array(
    '',
    '/images',
    '/thumbs',
    '/images/item'.$item_id,
    '/thumbs/item'.$item_id
);

// Try and make the directories
$img_section = $database->get_one('SELECT `img_section` FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_page_settings` WHERE `section_id` = '.(int)$section_id.' ');
if ($img_section == 0) {
    foreach ($directories as $directory) {
        $directory_path = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.$directory;
        make_dir($directory_path);

        // Add index.php files if not yet existing
        if (!is_file($directory_path.'/index.php')) {
            $content = ''.
    "<?php
    header('Location: ../');
    exit();
    ?>";
            $handle = fopen($directory_path.'/index.php', 'w');
            fwrite($handle, $content);
            fclose($handle);
            change_mode($directory_path.'/index.php', 'file');
        }
    }
}

// Delete image if requested
foreach ($images as $img_id  => $image) {
    if ($image['delete_image'] !== FALSE) {
        $img_file = $image['delete_image'];

        // Try unlinking image
        if (file_exists(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/'.$img_file)) {
            unlink(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/'.$img_file);
        }

        // Try unlinking thumb
        if (file_exists(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/'.$img_file)) {
            unlink(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/'.$img_file);
        } else {
            // Check if png image has a jpg thumb (version < 0.9 used jpg thumbs only)
            $img_file = str_replace('.png', '.jpg', $img_file);
            if (file_exists(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/'.$img_file)) {
                unlink(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/'.$img_file);
            }
        }

        // Delete image in database
        $database->query('DELETE FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` WHERE `img_id` = '.(int)$img_id.' ');
        // Check if there was a db error
        if ($database->is_error()) {
            $errors[] = $database->get_error();
        }
    }
}



// UPDATE DATABASE

// Only update if position is set and has been changed
$query_position = isset($position) ? ' `position` = '.(int)$position.',' : '';
$query_featured_pos = isset($featured_pos) ? ' `featured_pos` = '.(int)$featured_pos.',' : '';
// Item images
foreach ($images as $img_id => $image) {

    // Set image alt-text if left blank
    if (empty($image['alt'])) {
        if (!empty($image['caption'])) { $image['alt'] = $image['caption']; }
        if (!empty($image['title']))   { $image['alt'] = $image['title']; }
    }

    // Update image data
    $database->query('UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` '
                    .'SET   `active` = '.$image['active'].', '
                    .'         `alt` = \''.$image['alt'].'\', '
                    .'       `title` = \''.$image['title'].'\', '
                    .'     `caption` = \''.$image['caption'].'\' '
                    .'WHERE `img_id` = '.(int)$img_id.' ');
}

// Update or insert item fields
if ($admin->get_post('fields')) {
    foreach ($admin->get_post('fields') as $field_id => $value) {

        // Serialize the array if there is more than one value in this field OR replace URL with SysVar
        // check if 2.11 method exists else use older one
        if (method_exists($admin,'ReplaceAbsoluteMediaUrl')) {
            $value = (is_array($value) ? serialize($value) : $admin->ReplaceAbsoluteMediaUrl($value));
        } else {
            $sRelUrl = preg_replace('/^https?:\/\/[^\/]+(.*)/is', '\1', WB_URL);
            $sDocumentRootUrl = str_replace($sRelUrl, '', WB_URL);
            $sMediaUrl = WB_URL.MEDIA_DIRECTORY.'/';
            $aSearchfor = array(
              '@(<[^>]*=\s*")('.preg_quote($sMediaUrl).
              ')([^">]*".*>)@siU', '@(<[^>]*=\s*")('.preg_quote( WB_URL.'/').')([^">]*".*>)@siU',
              '/(<[^>]*?=\s*\")(\/+)([^\"]*?\"[^>]*?)/is',
              '/(<[^>]*=\s*")('.preg_quote($sMediaUrl, '/').')([^">]*".*>)/siU'
              );
            $aReplacements = array( '$1{SYSVAR:AppUrl.MediaDir}$3', '$1{SYSVAR:AppUrl}$3','\1'.$sDocumentRootUrl.'/\3','$1{SYSVAR:MEDIA_REL}$3' );
            $value = (is_array($value) ? serialize($value) : preg_replace($aSearchfor, $aReplacements, $value));
        }
        //$value = $database->escapeString($value);

        // if (is_numeric($field_id)) {
            // // Update if field is in db otherwise insert
            // $db_action = $database->get_one('SELECT EXISTS (SELECT 1 FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` WHERE `item_id` = \''.$item_id.'\' AND `field_id` = \''.$field_id.'\')');
            // if ($db_action) {
                // $database->query('UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` SET `value` = \''.$value.'\' WHERE `item_id` = \''.$item_id.'\' AND `field_id` = \''.$field_id.'\' ');
            // } else {
                // $database->query('INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` (`item_id`, `field_id`, `value`) VALUES (\''.$item_id.'\',\''.$field_id.'\',\''.$value.'\')');
            // }
            // // Check if there was a db error
            // if ($database->is_error()) {
                // $errors[] = $database->get_error();
            // }
        // }
        if (is_numeric($field_id)) {
            // Update if field is in db otherwise insert
            $sSqlWhere  = 'WHERE `item_id`  = '.(int)$item_id.' '
                        . 'AND `field_id` = '.(int)$field_id.' ';
            $sFindSql   = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` ';
            $db_action  = $database->get_one($sFindSql.$sSqlWhere);
            //
            $sSqlBody   = '`item_id`  = '.(int)$item_id.','
                        . '`field_id` = '.(int)$field_id.','
                        . '`value`    = \''.$database->escapeString($value).'\' ';
            //
            if ($db_action) {
                $sSql      = 'UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` SET ';
            } else {
                $sSql      = 'INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` SET ';
                $sSqlWhere = '';
            }
            if (!$database->query($sSql.$sSqlBody.$sSqlWhere)) {
            // Check if there was a db error
            $errors[] = ($database->is_error()?$database->get_error():$errors);
            }
        }
    }
}

// Item itself
$database->query(' UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` '
                .' SET `section_id` = '.(int)$section_id.', '
                .' `page_id` = '.(int)$page_id.', '
                .' `title` = \''.$title.'\', '
                .' `link` = \''.$new_link.'\', '
                .' `description` = \''.$description.'\', '
                .' '.$query_featured_pos.' '
                .' '.$query_featured.' '
                .' '.$query_active.' '
                .' `scheduling` = \''.$scheduling.'\', '
                .' '.$query_position.' '
                .' `modified_when` = \''.time().'\', '
                .' `modified_by` = '.$admin->get_user_id().' '
                .' WHERE `item_id` = '.(int)$item_id.' ');
// Check if there was a db error
if ($database->is_error()) {
    $errors[] = $database->get_error();
}

// Clean up item ordering of former section id
$order->clean($old_section_id);





// DUPLICATE ITEM
// **************

if ($action == 'duplicate') {


    // DUPLICATE ITEM

    // Get new page id, page link and section id
    if ($old_section_id != $new_section_id) {
        $query_duplicate = $database->query('SELECT `p`.`page_id`, `p`.`link` '
                                           .'FROM `'.TABLE_PREFIX.'pages` `p` '
                                           .'INNER JOIN `'.TABLE_PREFIX.'sections` `s` '
                                           .'ON `p`.`page_id` = `s`.`page_id` '
                                           .'WHERE `s`.`section_id` = '.(int)$new_section_id.' ');
        $duplicate  = $query_duplicate->fetchRow(MYSQLI_ASSOC);
        $page_id    = $duplicate['page_id'];
        $item_dir   = $duplicate['link'];
        $section_id = $new_section_id;
    }
    // Get new order position
    $position = $order->get_new($section_id);
    // Insert new row into database
    $database->query('INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` (`section_id`, `page_id`, `position`) VALUES ('.(int)$section_id.', '.(int)$page_id.', '.(int)$position.')');
    // Get the item id
    $orig_item_id = $item_id;
    $item_id      = $database->get_one('SELECT LAST_INSERT_ID()');



    // ACCESS FILE

    // New item filename
    $filename = '/'.page_filename($title);
    // New item link (replace double and triple by single page spacer)
    $new_link = $filename.PAGE_SPACER.$item_id;
    $new_link = str_replace(PAGE_SPACER.PAGE_SPACER.PAGE_SPACER, PAGE_SPACER, $new_link);
    $new_link = str_replace(PAGE_SPACER.PAGE_SPACER, PAGE_SPACER, $new_link);
    // New item dir path
    $dir_path = WB_PATH.PAGES_DIRECTORY.$item_dir;
    // New access file (full path)
    $new_path = $dir_path.$new_link.PAGE_EXTENSION;

    // Make new item access file dir
    if ($view_detail_pages) {
        make_dir($dir_path);
        if (!is_writable($dir_path)) {
            $errors[] = $MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE'];
        }
        // Create a new access file
        else {
            // The depth of the page directory in the directory hierarchy
            // 'PAGES_DIRECTORY' is at depth 1
            $pages_dir_depth = count(explode('/',$item_dir))-1;
            // Work-out how many ../'s we need to get to the index page
            $index_location = '../';
            for ($i = 0; $i < $pages_dir_depth; $i++) {
                $index_location .= '../';
            }
            // Write to the file
            $content = ''.
'<?php
$page_id    = '.$page_id.';
$section_id = '.$section_id.';
$item_id    = '.$item_id.';
$item_sid   = '.$section_id.';
define("ITEM_ID",  $item_id);
require("'.$index_location.'index.php");
?>';
            $handle = fopen($new_path, 'w');
            fwrite($handle, $content);
            fclose($handle);
            change_mode($new_path);
        }
    }
    else {
        $new_link = '';
    }


    // IMAGE AND THUMBNAIL

    // Duplicate image data in the db
    $query_images = $database->query('SELECT * FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` WHERE `item_id` = '.(int)$orig_item_id.' ');
    if ($query_images->numRows() > 0) {
        while ($image = $query_images->fetchRow(MYSQLI_ASSOC)) {
            // Insert duplicated images
            $database->query('INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` (`item_id`, `filename`, `active`, `position`, `alt`, `title`, `caption`) '
                            .'VALUES ('.(int)$item_id.', \''.$image['filename'].'\', '.$image['active'].', '.$image['position'].', \''.$image['alt'].'\', \''.$image['title'].'\', \''.$image['caption'].'\')');
        }
    }

    // Prepare pathes to the source image and thumb directories
    $img_source_dir   = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$orig_item_id;
    $thumb_source_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$orig_item_id;

    // Make sure the target directories exist
    make_dir(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id);
    make_dir(WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id);

    // Check if the image and thumb source directories exist
    if (is_dir($img_source_dir) && is_dir($thumb_source_dir)) {

        // Open the image directory then loop through its contents
        $dir = dir($img_source_dir);
        while (false !== $image_file = $dir->read()) {

            // Skip index file and pointers
            if (strpos($image_file, '.php') !== false || substr($image_file, 0, 1) == '.') {
                continue;
            }

            // Path to the image source and destination
            $img_source      = $img_source_dir.'/'.$image_file;
            $img_destination = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/images/item'.$item_id.'/'.$image_file;

            // Check if png image has a jpg thumb (version < 0.9 used jpg thumbs only)
            if (!file_exists($thumb_source_dir.'/'.$image_file)) {
                $image_file = str_replace('.png', '.jpg', $image_file);
            }

            // Path to the thumb source and destination
            $thumb_source      = $thumb_source_dir.'/'.$image_file;
            $thumb_destination = WB_PATH.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item_id.'/'.$image_file;

            // Try duplicating image and thumb
            if (file_exists($img_source)) {
                if (copy($img_source, $img_destination)) {
                    change_mode($img_destination);
                }
            }
            if (file_exists($thumb_source)) {
                copy($thumb_source, $thumb_destination);
                change_mode($thumb_destination);
            }
        }
    }



    // UPDATE DATABASE

    // Insert duplicated item fields
    if ($admin->get_post('fields')) {
        foreach ($admin->get_post('fields') as $field_id => $value) {

            // Serialize the array if there is more than one value in this field OR replace URL with SysVar
            // check if 2.11 method exists else use older one
            if (method_exists($admin,'ReplaceAbsoluteMediaUrl')) {
                $value = (is_array($value) ? serialize($value) : $admin->ReplaceAbsoluteMediaUrl($value));
            } else {
                $sRelUrl = preg_replace('/^https?:\/\/[^\/]+(.*)/is', '\1', WB_URL);
                $sDocumentRootUrl = str_replace($sRelUrl, '', WB_URL);
                $sMediaUrl = WB_URL.MEDIA_DIRECTORY.'/';
                $aSearchfor = array(
                  '@(<[^>]*=\s*")('.preg_quote($sMediaUrl).
                  ')([^">]*".*>)@siU', '@(<[^>]*=\s*")('.preg_quote( WB_URL.'/').')([^">]*".*>)@siU',
                  '/(<[^>]*?=\s*\")(\/+)([^\"]*?\"[^>]*?)/is',
                  '/(<[^>]*=\s*")('.preg_quote($sMediaUrl, '/').')([^">]*".*>)/siU'
                  );
                $aReplacements = array( '$1{SYSVAR:AppUrl.MediaDir}$3', '$1{SYSVAR:AppUrl}$3','\1'.$sDocumentRootUrl.'/\3','$1{SYSVAR:MEDIA_REL}$3' );
                $value = (is_array($value) ? serialize($value) : preg_replace($aSearchfor, $aReplacements, $value));
            }

            if (is_numeric($field_id)) {
                // Update if field is in db otherwise insert
                $sSqlWhere  = 'WHERE `item_id`  = '.(int)$item_id.' '
                            . 'AND `field_id` = '.(int)$field_id.' ';
                $sFindSql   = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` ';
                $db_action  = $database->get_one($sFindSql.$sSqlWhere);
                //
                $sSqlBody   = '`item_id`  = '.(int)$item_id.','
                            . '`field_id` = '.(int)$field_id.','
                            . '`value`    = \''.$database->escapeString($value).'\' ';
                //
                if ($db_action) {
                    $sSql      = 'UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` SET ';
                } else {
                    $sSql      = 'INSERT INTO `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` SET ';
                    $sSqlWhere = '';
                }
                if (!$database->query($sSql.$sSqlBody.$sSqlWhere)) {
                // Check if there was a db error
                $errors[] = ($database->is_error()?$database->get_error():$errors);
                }
            }
        }
    }

    // Update duplicated item data
    $database->query('UPDATE `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` SET '
                    .'`section_id`   = '.(int)$section_id.', '
                    .'`page_id`      = '.(int)$page_id.', '
                    .'`title`        = \''.$title.'\', '
                    .'`link`         = \''.$new_link.'\', '
                    .'`description`  = \''.$description.'\', '
                    .'`active`       = \'0\', '
                    .'`featured`     = \'0\', '
                    .'`featured_pos` = \'0\', '
                    .'`modified_when`= \''.time().'\', '
                    .'`modified_by`  = \''.$admin->get_user_id().'\' '
                    .'WHERE `item_id`= '.(int)$item_id.' ');
    // Check if there was a db error
    if ($database->is_error()) {
        $errors[] = $database->get_error();
    }
}





// MANAGE ERROR OR SUCCESS MESSAGES
// ********************************

// Generate error message
$error = false;
if (!empty($errors)) {
    $error     = true;
    $error_msg = implode("<br />", $errors);
}

// Different targets depending on the save action
if (!empty($_POST['save_and_return_to_images'])) {
    $return_url = WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id.'#images';
}
elseif (!empty($_POST['save_and_return']) OR $error) {
    $return_url = WB_URL.'/modules/'.$mod_name.'/modify_item.php?page_id='.$page_id.'&section_id='.$section_id.'&item_id='.$item_id;
}
else {
    $return_url = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;
}

// Print error or success message and return
if ($error) {
    $admin->print_error($error_msg, $return_url);
}
else {
    $admin->print_success($TEXT['SUCCESS'], $return_url);
}


// Print admin footer
$admin->print_footer();
