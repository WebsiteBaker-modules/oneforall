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
  OneForAll module for WebsiteBaker >v2.7 (http://www.websitebaker.org)
  The OneForAll module provides the facility to add customized pages to WebsiteBaker
 -----------------------------------------------------------------------------------------
*/


// Set your own module name that matches the module's scope of data handling
// Allowed characters are a-z, A-Z, 0-9, - (hyphen), _ (underscore) and spaces.
// Min 3, max 20 characters
$module_name = 'OneForAll'; // default: OneForAll
#$module_directory   = 'oneforall';
// Do not change anything below
$module_directory   = str_replace(' ', '_', strtolower($module_name));
$mod_name           = $module_directory;
$renamed_to         = $mod_name == 'oneforall' ? '' : '(renamed to <strong>'.$module_name.'</strong>) ';

$module_function    = 'page';
$module_version     = '2.0.0-dev9';
$module_platform    = '2.8.3';
$module_author      = 'Christoph Marti. OneForAll '.$renamed_to.'is based on the module Showcase v0.5';
$module_license     = 'GNU General Public License';
$module_description = 'This page type is designed for making full customized '.$module_name.' pages.';


