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



/*
  ***********************
  TRANSLATORS PLEASE NOTE
  ***********************
  
  Thank you for translating OneForAll!
  Include your credits in the header of this file right above the licence terms.
  Please post your localisation file on the WB forum at http://www.websitebaker.org/forum/

*/


// MODUL DESCRIPTION
$module_description = 'OneForAll is a WebsiteBaker module that is versatile like a chameleon. It can be installed more than one time on the same WebsiteBaker installation by setting a different module name in the info.php file before upload and installation. Furthermore the module provides a feature to add highly customized pages. On the one hand you can define custom fields in the backend and on the other hand free definable html templates for the frontend output.<br />By default it provides just one title field and  an image upload. Additionally you can add different custom field types. Items can be displayed in an overview and if needed in a corresponding detail page. OneForAll makes use of the Lightbox2 JavaScript to overlay item images on the current page.';

// MODUL ONEFORALL VARIOUS TEXT
$MOD_ONEFORALL[$mod_name]['TXT_SETTINGS'] = 'Settings';
$MOD_ONEFORALL[$mod_name]['TXT_FIELDS'] = 'Fields';
$MOD_ONEFORALL[$mod_name]['TXT_SYNC_TYPE_TEMPLATE'] = 'Adapt field template automatically when selecting field type.';

$MOD_ONEFORALL[$mod_name]['TXT_CUSTOM_FIELD'] = 'Custom field';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_TYPE'] = 'Type';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_NAME'] = 'Field name';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_LABEL'] = 'Field label';
$MOD_ONEFORALL[$mod_name]['TXT_DIRECTORY'] = 'Directory';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_PLACEHOLDER'] = 'Placeholder';
$MOD_ONEFORALL[$mod_name]['TXT_OR'] = 'or';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_TEMPLATE'] = 'Field template';
$MOD_ONEFORALL[$mod_name]['TXT_NEW_FIELD_NAME'] = 'field';
$MOD_ONEFORALL[$mod_name]['TXT_ADD_NEW_FIELDS'] = 'Add new fields';
$MOD_ONEFORALL[$mod_name]['TXT_SUCCESS_MESSAGE'] = 'Moved item successfully';
$MOD_ONEFORALL[$mod_name]['TXT_TOGGLE_MESSAGE'] = 'Saved new status successfully.';
$MOD_ONEFORALL[$mod_name]['TXT_DRAGDROP_MESSAGE'] = 'Moved item successfully.';

$MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS'] = 'Page Settings';
$MOD_ONEFORALL[$mod_name]['TXT_LAYOUT'] = 'Layout';
$MOD_ONEFORALL[$mod_name]['TXT_OVERVIEW'] = 'Overview';
$MOD_ONEFORALL[$mod_name]['TXT_DETAIL'] = 'Item Detail';

$MOD_ONEFORALL[$mod_name]['TXT_DISABLED'] = 'Disabled';
$MOD_ONEFORALL[$mod_name]['TXT_TEXT'] = 'Short text';
$MOD_ONEFORALL[$mod_name]['TXT_TEXTAREA'] = 'Textarea';
$MOD_ONEFORALL[$mod_name]['TXT_WYSIWYG'] = 'WYSIWYG-Editor';
$MOD_ONEFORALL[$mod_name]['TXT_CODE'] = 'PHP-Code';
$MOD_ONEFORALL[$mod_name]['TXT_WB_LINK'] = 'WebsiteBaker Link';
$MOD_ONEFORALL[$mod_name]['TXT_ONEFORALL_LINK'] = 'Module OneForAll Link';
$MOD_ONEFORALL[$mod_name]['TXT_MODULE_NAME'] = 'Module Name';
$MOD_ONEFORALL[$mod_name]['TXT_FOLDERGALLERY_LINK'] = 'Module Foldergallery Link';
$MOD_ONEFORALL[$mod_name]['TXT_FOLDERGALLERY_SECTION_ID'] = 'FG Section-IDs (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_URL'] = 'External link';
$MOD_ONEFORALL[$mod_name]['TXT_EMAIL'] = 'Email link';
$MOD_ONEFORALL[$mod_name]['TXT_MEDIA'] = 'File from a subdirectory of media';
$MOD_ONEFORALL[$mod_name]['TXT_UPLOAD'] = 'File Upload';
$MOD_ONEFORALL[$mod_name]['TXT_DATEPICKER'] = 'Datepicker';
$MOD_ONEFORALL[$mod_name]['TXT_DATEPICKER_START_END'] = 'Date from &#8230; to &#8230;';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIMEPICKER'] = 'Datetimepicker';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIMEPICKER_START_END'] = 'Datetime from &#8230; to &#8230;';
$MOD_ONEFORALL[$mod_name]['TXT_JS_SELECT_DATE'] = 'Select date';
$MOD_ONEFORALL[$mod_name]['TXT_JS_SELECT_DATETIME'] = 'Select datetime';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIME_SEPARATOR'] = 'at';
$MOD_ONEFORALL[$mod_name]['TXT_DATEDATE_SEPARATOR'] = 'until';
$MOD_ONEFORALL[$mod_name]['TXT_DROPLET'] = 'WebsiteBaker droplet';
$MOD_ONEFORALL[$mod_name]['TXT_SELECT'] = 'Select';
$MOD_ONEFORALL[$mod_name]['TXT_MULTISELECT'] = 'Multiselect';
$MOD_ONEFORALL[$mod_name]['TXT_MULTIOPTIONS'] = 'Options (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_CHECKBOX'] = 'Checkbox';
$MOD_ONEFORALL[$mod_name]['TXT_CHECKBOXES'] = 'Checkboxes (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_SWITCH'] = 'Switch on / off';
$MOD_ONEFORALL[$mod_name]['TXT_SWITCHES'] = 'on,off';
$MOD_ONEFORALL[$mod_name]['TXT_RADIO'] = 'Radio buttons';
$MOD_ONEFORALL[$mod_name]['TXT_RADIO_BUTTONS'] = 'Radio buttons (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_SUBDIRECTORY_OF_MEDIA'] = 'Subdir of media';
$MOD_ONEFORALL[$mod_name]['TXT_OPTIONS'] = 'Options (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_GROUP'] = 'Group'; 
$MOD_ONEFORALL[$mod_name]['TXT_GROUPS'] = 'Groups (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_DELETE_FIELD'] = 'Delete field';
$MOD_ONEFORALL[$mod_name]['TXT_CONFIRM_DELETE_FIELD'] = 'Do you really want to delete the fields below with all its associated item data?';

$MOD_ONEFORALL[$mod_name]['TXT_ITEM'] = 'Item';
$MOD_ONEFORALL[$mod_name]['TXT_ITEMS'] = 'Items';
$MOD_ONEFORALL[$mod_name]['TXT_ITEMS_PER_PAGE'] = 'Items per Page';
$MOD_ONEFORALL[$mod_name]['TXT_BACKEND_ITEM_PAGE'] = 'Item page (Backend)';
$MOD_ONEFORALL[$mod_name]['TXT_HIDE_IMG_SECTION'] = 'Hide image settings and upload';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_THIS'] = 'Update page settings of <b>current</b> &quot;'.$module_name.'&quot; page only.';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_ALL'] = 'Update page settings of <b>all</b> &quot;'.$module_name.'&quot; pages.';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_MULTIPLE'] = 'Update page settings of <b>selected</b> &quot;'.$module_name.'&quot; page(s) (Multiple choice):';

$MOD_ONEFORALL[$mod_name]['TXT_ADD_ITEM'] = 'Add item';
$MOD_ONEFORALL[$mod_name]['TXT_DISABLE'] = 'Disable';
$MOD_ONEFORALL[$mod_name]['TXT_ENABLE'] = 'Enable';
$MOD_ONEFORALL[$mod_name]['TXT_ENABLED'] = 'Enabled';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_TABLE'] = 'Click a table heading to sort the table.';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_BY1'] = 'Sort the table by';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2'] = '';

$MOD_ONEFORALL[$mod_name]['TXT_TITLE'] = 'Item title';
$MOD_ONEFORALL[$mod_name]['TXT_DESCRIPTION'] = 'Description';
$MOD_ONEFORALL[$mod_name]['TXT_PREVIEW'] = 'Preview';
$MOD_ONEFORALL[$mod_name]['TXT_FILE_NAME'] = 'File name';
$MOD_ONEFORALL[$mod_name]['TXT_MAIN_IMAGE'] = 'Main image';
$MOD_ONEFORALL[$mod_name]['TXT_THUMBNAIL'] = 'Thumbnail';
$MOD_ONEFORALL[$mod_name]['TXT_CAPTION'] = 'Caption';
$MOD_ONEFORALL[$mod_name]['TXT_POSITION'] = 'Position';
$MOD_ONEFORALL[$mod_name]['TXT_IMAGE'] = 'Image';
$MOD_ONEFORALL[$mod_name]['TXT_IMAGES'] = 'Images';
$MOD_ONEFORALL[$mod_name]['TXT_SHOW_GENUINE_IMAGE'] = 'Show genuine image';
$MOD_ONEFORALL[$mod_name]['TXT_FILE_LINK'] = 'File link';
$MOD_ONEFORALL[$mod_name]['TXT_MAX_WIDTH'] = 'max. Width';
$MOD_ONEFORALL[$mod_name]['TXT_MAX_HEIGHT'] = 'max. Height';
$MOD_ONEFORALL[$mod_name]['TXT_JPG_QUALITY'] = 'JPG Quality';
$MOD_ONEFORALL[$mod_name]['TXT_NON'] = 'non';
$MOD_ONEFORALL[$mod_name]['TXT_ITEM_TO_PAGE'] = 'Move item to page';
$MOD_ONEFORALL[$mod_name]['TXT_MOVE'] = 'move';
$MOD_ONEFORALL[$mod_name]['TXT_DUPLICATE'] = 'duplicate';
$MOD_ONEFORALL[$mod_name]['TXT_SAVE_AND_BACK_TO_LISTING'] = 'Save and go back to listing';

$MOD_ONEFORALL[$mod_name]['ERR_INVALID_EMAIL'] = 'Email address &quot;%s&quot; is invalid.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_URL'] = 'URL &quot;%s&quot; is invalid.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_FILE_NAME'] = 'Invalid file name';
$MOD_ONEFORALL[$mod_name]['ERR_ONLY_ONE_GROUP_FIELD'] = 'Could not save field &quot;%s&quot; since only 1 &quot;group&quot; field is allowed.';
$MOD_ONEFORALL[$mod_name]['ERR_BLANK_FIELD_NAME'] = 'Please enter for all fields a valid and unique field name!';
$MOD_ONEFORALL[$mod_name]['ERR_CONFLICT_WITH_RESERVED_NAME'] = 'The field name &quot;%s&quot; can not be used since it is reserved for a general placeholder.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_FIELD_NAME'] = 'The field name &quot;%s&quot; is invalid! Allowed characters are a-z, A-Z, 0-9, . (dot), _ (underscore) and - (hyphen).';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_NAME_EXISTS'] = 'The field name &quot;%s&quot; is already used. Please try another one.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_DISABLED'] = 'This field is disabled.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_RE_ENABLE'] = 'You can either re-enable it or remove the placeholder from the template.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_TYPE_NOT_EXIST'] = 'Sorry, this field type does not exist!';
$MOD_ONEFORALL[$mod_name]['ERR_SET_A_LABEL'] = 'Set a label';
$MOD_ONEFORALL[$mod_name]['ERR_INSTALL_MODULE'] = 'In order to use this field you have to install the module &quot;%s&quot; and add at least one &quot;%s&quot; section.';

$GLOBALS['TEXT']['CAP_EDIT_CSS'] = 'Edit CSS';
?>