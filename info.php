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

   v1.0.6  (Christoph Marti; 04/05/2017)
	 + Added Dutch language file (thanks to CodeALot)
	 + Bugfix: Using multiple fields of type wb_link the select options representing the wb page tree
	   have been multiplied with each new field (reported by astricia)
	 + [save_fields.php] Defining a field type oneforall_link now it is possible to enter either the module
	   directory name as well as the module name as initially defined in the info.php (reported by astricia)
	 + [functions.php] Added more checks to verify that the entered module name is a oneforall module

   v1.0.5  (Christoph Marti; 03/03/2017)
	 + [view_overview.php] Fixed warning in PHP 7.1 about a non-numeric value (reported by dbs)
	 + [modify.php] Fixed undefined index when group_id is empty

   v1.0.4  (Christoph Marti; 01/27/2017)
	 + [view.php] Bugfix: Fixed double opening <div> tag when there is more than one OneForAll section and
	   an item detail page is viewed (thanks to jacobi22)
	 + Added section id to the module wrapper, eg. section id 22: mod_oneforall_wrapper_22_f (thanks to jacobi22)

   v1.0.3  (Christoph Marti; 01/22/2017)
	 + [backend.css] Added support for the advanced theme wbce flat
	 + Fixed some hardcoded attributes prefixed with mod_oneforall_[...]
	 + Fixed item scheduling (reported by CodeALot and jacobi22)

   v1.0.2  (Christoph Marti; 01/17/2017)
	 + [upgrade.php] Improved updating of item access files (thanks to jacobi22)

   v1.0.1  (Christoph Marti; 01/16/2017)
	 + [upgrade.php] Only update module access files if item detail pages are enabled in config.php
	 + [upgrade.php] Check for missing item link before creating the access file (reported by jacobi22)
	 + [modify.php, table_sort.js] Fixed an issue with the TableSort localisation vars (reported by jacobi22)

   v1.0.0  (Christoph Marti; 01/14/2017)
	 + [ajax/upload.php] Fixed generating of multiple database records when uploading an image in chunks
	   (reported by dbs, thanks to Ruud)
	 + Added plupload max file size to the config.php (default is 2MB)
	 + [modify_item.php] Fixed undefined variable $image_file that broke plupload js (reported by dbs and Boudi)
	 + [modify_fields.php] Fixed interchange of extra field label depending on field type
	 + Added item scheduling: Enable / disable items against a start and end time (suggested by astricia)
	   Enable scheduling in the config.php file. Experimental, depends on wb constant TIMEZONE which can be buggy
	 + [backend.js] Changed delete field confirmation message to display the field id instead of the field position
	 + [backend.js] Fixed toggling of the button title when enabling/disabling an item of the sortable items table
	 + Added support for multiple oneforall module sections on the same page,
	   including the renamed oneforall module versions (suggested by flipoflip)
		 + [view_item.php, view_overview.php] Prevent fields from beeing mixed up
		 + Eliminate js and css interferences between oneforall modules by making backend attributes module specific
		 + [install.php, upgrade.php] Added module name converting of the files backend.css and backend.js
		 + [save_item.php, view.php] Display only one item on oneforall detail pages
		   Added constant ITEM_SID (item section id) which helps to assign an item to its original section
		 + [upgrade.php] Update all module access files and add the constant ITEM_SID

   v0.9.9  (Christoph Marti; 12/03/2016)
	 + Added an image tooltip to the thumbs at the sortable image table
	   To change tooltip image size use css selector '.arrow_box > img' at backend.css
	 + If image was to small for resizing it has not been added to the image table (reported by dbs)

   v0.9.8  (Christoph Marti; 12/01/2016)
	 + Added hover effect to the newly uploaded images of the sortable image table to indicate drag&drop
	   (reported by dbs)

   v0.9.7  (Christoph Marti; 11/29/2016)
	 + [upgrade.php] Bugfix: Added trailing backtick to table name on line 213 (reported by dbs)
	 + [view_item.php] Bugfix: Groups were not displayed at item detail view (reported by dbs)
	 + [functions.php] Bugfix: When function field_multiselect() is called for the first time
	   it tries to unset a array key which has not been defined
	 + [view_item.php] Changed: Removed experimental jquery function to insert item title and a meta description
	   into the html head of every item detail page since Google does not recognize it.
	   Use module SimplePageHead instead.
	 + Bugfix: Changed mysql fields type TEXT NOT NULL DEFAULT '' to TEXT NOT NULL
	   since default values for TEXT and BLOG fields are not allowed (thanks to Ruud)
	 + [modify_item.php] Changed select options of item mover/duplicater from page_title to menu_title
	   (reported by dbs)
	 + Added Plupload, a multi runtime file uploader (http://www.plupload.com)
	 + Added jQuery v1.11.1 and jQuery UI v1.10.2 for better support of Plupload

   v0.9.5  (Christoph Marti; 10/27/2016)
	 + Bugfix: After unchecking all options of multiselect or checkboxes, one still remained checked after saving
	   (reported by Roych)

   v0.9.4  (Frank Fischer, Christoph Marti; 10/12/2016)
	 + [frontend.css] Added css class .mod_oneforall_group_wrapper_f
	 + [backend.css] Added transition to the css classes .switch-label and .switch-handel

   v0.9.3  (Frank Fischer, Christoph Marti; 10/11/2016)
	 + Bugfix: First selected item of multiselect and radio have not been displaying in frontend view
	 + Added group wrapper div with 2 separate css classes to make styling of groups easier
	 + Added css styling to the switches, checkboxes and radio buttons of the item table 
	 + Bugfix: Function to enable/disable an item using ajax

   v0.9.2  (Uwe Jacobsen, Frank Fischer, Christoph Marti; 10/08/2016)
	 + Added field types code, multiselect, checkbox, switch and radio
	 + Field type code must be allowed in config.php due to security reasons
	 + [modify_item.php] Added setting to still duplicate items even when item mover/duplicator is disabled
	 + [modify_item.php] Added field meta description for item detail pages
	   Adds the title and a meta description to the html head of every item detail page
	 + [modify.php] Enable/disable item via ajax
	 + [modify.php] Added sorting of item table by clicking the column headers 'id', 'title' and 'enabled'
	 + [modify_item.php] Added drag&drop sorting to the item images
	 + [modify_fields.php] Added drag&drop sorting to the fields table
	 + Bugfix: Fixed view of field type oneforall_link
	 + Bugfix: Fixed item and image positions in db when reordered by drag&drop (position must be > 0)

   v0.9  (Christoph Marti; 09/29/2016)
	 + [view_item.php] Bugfix: Initialized vars properly for item pagination
	 + Improved image resizing: png images will no longer be converted to jpg images.
	   png images and thumbs now will be saved as png images (reported by dbs)
	 + If "Hide image settings and upload" is checked, no media directory will be generated (reported by dbs)
	   Only works, if the option is checked in all module sections.
	 + Updated Lightbox2 to v2.8.2 (responsive slideshow) (reported by CodeALot)
	 + [view_overview.php, view_item.php] Changed js method to set the Lightbox2 options
	 + [lightbox2/css/lightbox.css] Moved css definitions of Lightbox2 to file frontend.css
	 + [config.php, modify_item.php] Added setting to hide item mover (thanks to CodeALot and dbs)
	 + Bugfix: Fixed problem with single quotes in image title and alt attributes (reported by dbs)
	 + Replaced @mktime() by time() function since as of PHP 5.1 mktime() throws a notice when called without arguments

   v0.8  (Christoph Marti; 10/06/2015)
	 + Added drag&drop sorting to the item table

   v0.7  (Christoph Marti; 09/27/2015)
	 + [search.php] Bugfix: Fixed search script that produced multiple identical search results
	   (reported by instantflorian)
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
	 + [view_overview.php, view_item.php] Bugfix: Fixed shifted option of field type select
	   (reported by instantflorian)

   v0.3  (Christoph Marti; 01/22/2015)
	 + Fixed some warnings which were thrown when no fields had been defined (reported by BlackBird)
	 + [save_item.php] Bugfix: When saving a new item, the page access file still was deleted by mistake
	 + [config.php] Added option to deactivate item detail pages and suspend corresponding access files
	   (suggested by jacobi22)
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
$module_version     = '1.0.6';
$module_platform    = '2.8.x';
$module_author      = 'Christoph Marti. OneForAll '.$renamed_to.'is based on the module Showcase v0.5';
$module_license     = 'GNU General Public License';
$module_description = 'This page type is designed for making full customized '.$module_name.' pages.';

?>
