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


/*
 -----------------------------------------------------------------------------------------
  OneForAll module for WebsiteBaker v2.7 (http://www.websitebaker.org)
  The OneForAll module provides the facility to add customized pages to WebsiteBaker
 -----------------------------------------------------------------------------------------
*/


// Set your own module name that matches the module's scope of data handling
// Allowed characters are a-z, A-Z, 0-9, - (hyphen), _ (underscore) and spaces.
// Min 3, max 20 characters
$module_name = 'OneForAll'; // default: OneForAll




/*
 -----------------------------------------------------------------------------------------

	DEVELOPMENT HISTORY:

   v0.8  (Christoph Marti; 10/06/2015)
	 + Added drag&drop sorting to the item table

   v0.7  (Christoph Marti; 09/27/2015)
	 + [search.php] Bugfix: Fixed search script that produced multiple identical search results (reported by instantflorian)
	 + [FR.php] Added french language file
	 + [upgrade.php] Bugfix: Fixed renaming of module on upgrade

   v0.6  (Christoph Marti; 09/13/2015)
	 + Bugfix: Added backticks to unquoted mysql table names since hyphens are not accepted in unquoted identifiers
	   (reported by dbs)

   v0.5  (Christoph Marti; 05/29/2015)
	 + [config.php] Added option to set wysiwyg editor to full width (both columns = 100%) (suggested by dbs)
	 + [install.php] Bugfix: On manual installation allow installation even though
	   the uploaded directory name is identical to the new module directory name (compares lowercase)
	 + [modify.php] Order items in the backend by position based on config.php setting
	 + [modify.php] Fixed up and down arrows depending on ordering
	 + [save_item.php] Bugfix: When incrementing the image position and the item had no image yet,
	   db function returned NULL instead of 0
	 + [view_item.php] Bugfix: Fixed pagination if items are ordered by position descending
	 + [backend.js] Bugfix: Fixed jQuery function to sinc selected file with file link or preview thumb
	 + [save_item.php] Bugfix: Under certain circumstances false images have been deleted (reported by Boudi)
	 + [modify_fields.php] Bugfix: Added localisation of "Add new fields"
	 + Added new field type oneforall_link (module name of renamed OneForAll module must be provided)
	 + Added new field type foldergallery_link (section id(s) can be specified as csv)
	 + Added new field type upload (target subdirectory of media must be specified as a path)
	 + [view_overview.php] Added «None found» message if no active item is found

   v0.4  (Christoph Marti; 02/06/2015)
	 + [save_item.php] Bugfix: If png image file is not resized keep jpg file extension
	 + [functions.php] Bugfix: Field wb_link now allows to select sibling as well as ancestor pages
	 + Changed general placeholder [EMAIL] to [USER_EMAIL] to prevent conflicts with customized email field
	 + [save_field.php] Bugfix: Prevent conflicts between customized field names and general placeholders
	 + [functions.php] Bugfix: Fixed fatal error "Cannot redeclare show_wysiwyg_editor()"
	   when using more than one wysiwyg editor (reported by instantflorian)
	 + [view_overview.php, view_item.php] Bugfix: Fixed shifted option of field type select (reported by instantflorian)

   v0.3  (Christoph Marti; 01/22/2015)
	 + Fixed some warnings which were thrown when no fields had been defined (reported by BlackBird)
	 + [save_item.php] Bugfix: When saving a new item, the page access file still was deleted by mistake
	 + [config.php] Added option to deactivate item detail pages and suspend corresponding access files (suggested by jacobi22)
	 + Added field type group. Items are grouped on the overview page. (suggested by jacobi22)
	   Note: Just one group field allowed!
	   [config.php] Also see group settings for headers and ordering at config.php

   v0.2  (Christoph Marti; 12/31/2014)
       Thanks a lot for testing, bug reports and further feedback to fischstäbchenbrenner and jacobi22
	 + Bugfix: Various small fixes for PHP 5.3
	 + Changed: OneForAll now sets a default item title if user has left it blank
	 + Bugfix: Fixed PHP include pathes using dirname(__FILE__)
	 + [modify.php] Bugfix: Display settings to admin only
	 + [view_overview.php] Bugfix: If an item field is empty, prevent field data of previous items to be displayed
	 + [delete_item.php] Bugfix: Fixed fatal error when deleting an item
	 + [delete_item.php] Bugfix: When deleting an item also delete the corresponding item fields
	 + Added various $admin->add_slashes() and stripslashes()
	 + [modify_fields.php] Checkbox "sync_type_template" is now checked by default and saved in the session
	 + [view_overview.php and view_item.php] Bugfix: Display [[Droplet]] instead of droplet id
	 + [save_item.php] Bugfix: When saving an item, occasionally the page access file was deleted wrongly
	 + [install.php] Changed: Made renaming of the module optional

   v0.1  (Christoph Marti; 12/19/2014)
	 + Initial release of OneForAll
	 + OneForAll is based on the module Showcase v0.5

 -----------------------------------------------------------------------------------------
*/



// Do not change anything below
$module_directory   = str_replace(' ', '_', strtolower($module_name));
$mod_name           = $module_directory;
$renamed_to         = $mod_name == 'oneforall' ? '' : '(renamed to <strong>'.$module_name.'</strong>) ';

$module_function    = 'page';
$module_version     = '0.8';
$module_platform    = '2.8.x';
$module_author      = 'Christoph Marti. OneForAll '.$renamed_to.'is based on the module Showcase v0.5';
$module_license     = 'GNU General Public License';
$module_description = 'This page type is designed for making full customized '.$module_name.' pages.';

?>
