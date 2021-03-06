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
  ***********************
  TRANSLATORS PLEASE NOTE
  ***********************
  
  Thank you for translating OneForAll!
  Include your credits in the header of this file right above the licence terms.
  Please post your localisation file on the WB forum at http://www.websitebaker.org/forum/

*/

$mod_name = basename(dirname(__DIR__));
// MODUL DESCRIPTION
$module_description = 'OneForAll ist ein WebsiteBaker Modul so vielseitig wie ein Chamäleon. Es kann mehrmals in derselben WebsiteBaker Installation eingesetzt werden, indem vor dem Upload und der Installation in der Datei info.php ein anderer Modulname angegeben wird. Es können komplett massgeschneiderte Modulseiten erstellt werden: Einerseits können im Backend beliebig Felder definiert und andererseits für die Anzeige im Frontend Templates frei gestaltet werden.<br />Als Standard gibt es nur ein Titel-Feld sowie einen Bilder-Upload. Zusätzlich können verschiedene Feldtypen ergänzt werden. Einträge können auf einer übersichtsseite und falls erwünscht auch auf einer Detailseite dargestellt werden. OneForAll kann die Bilder als Lightbox2-Slideshow anzeigen.';

// MODUL ONEFORALL VARIOUS TEXT
$MOD_ONEFORALL[$mod_name]['TXT_SETTINGS'] = 'Einstellungen';
$MOD_ONEFORALL[$mod_name]['TXT_FIELDS'] = 'Eingabefelder';
$MOD_ONEFORALL[$mod_name]['TXT_SYNC_TYPE_TEMPLATE'] = 'Feld-Template automatisch anpassen, wenn Feld-Typ geändert wird.';
$MOD_ONEFORALL[$mod_name]['TXT_FEATURED'] = 'Featured';

$MOD_ONEFORALL[$mod_name]['TXT_CUSTOM_FIELD'] = 'Frei definierbares Feld';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_TYPE'] = 'Typ';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_NAME'] = 'Feldname';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_LABEL'] = 'Feldbezeichnung';
$MOD_ONEFORALL[$mod_name]['TXT_DIRECTORY'] = 'Verzeichnis';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_PLACEHOLDER'] = 'Platzhalter';
$MOD_ONEFORALL[$mod_name]['TXT_OR'] = 'oder';
$MOD_ONEFORALL[$mod_name]['TXT_FIELD_TEMPLATE'] = 'Feld-Template';
$MOD_ONEFORALL[$mod_name]['TXT_NEW_FIELD_NAME'] = 'feld';
$MOD_ONEFORALL[$mod_name]['TXT_ADD_NEW_FIELDS'] = 'neue Felder hinzufügen';
$MOD_ONEFORALL[$mod_name]['TXT_TOGGLE_MESSAGE'] = 'Der neue Zustand wurde gespeichert.';
$MOD_ONEFORALL[$mod_name]['TXT_DRAGDROP_MESSAGE'] = 'Das Element wurde erfolgreich verschoben.';

$MOD_ONEFORALL[$mod_name]['TXT_PAGE_SETTINGS'] = 'Seiten Einstellungen';
$MOD_ONEFORALL[$mod_name]['TXT_LAYOUT'] = 'Layout';
$MOD_ONEFORALL[$mod_name]['TXT_OVERVIEW'] = 'Übersicht';
$MOD_ONEFORALL[$mod_name]['TXT_DETAIL'] = 'Detailansicht';
//Felder
$MOD_ONEFORALL[$mod_name]['TXT_DISABLED'] = 'Deaktiviert';
$MOD_ONEFORALL[$mod_name]['TXT_TEXT'] = 'Kurzer Text';
$MOD_ONEFORALL[$mod_name]['TXT_TEXTAREA'] = 'Langer Text';
$MOD_ONEFORALL[$mod_name]['TXT_WYSIWYG'] = 'WYSIWYG-Editor';
$MOD_ONEFORALL[$mod_name]['TXT_CODE'] = 'PHP-Code';
$MOD_ONEFORALL[$mod_name]['TXT_WB_LINK'] = 'WebsiteBaker Link';
$MOD_ONEFORALL[$mod_name]['TXT_ONEFORALL_LINK'] = 'Modul OneForAll Link';
$MOD_ONEFORALL[$mod_name]['TXT_MODULE_NAME'] = 'Modulname';
$MOD_ONEFORALL[$mod_name]['TXT_FOLDERGALLERY_LINK'] = 'Modul Foldergallery Link';
$MOD_ONEFORALL[$mod_name]['TXT_FOLDERGALLERY_SECTION_ID'] = 'FG Section-IDs (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_URL'] = 'Externer Link';
$MOD_ONEFORALL[$mod_name]['TXT_EMAIL'] = 'E-Mail Link';
$MOD_ONEFORALL[$mod_name]['TXT_MEDIA'] = 'Datei aus Unterverzeichnis von Media';
$MOD_ONEFORALL[$mod_name]['TXT_UPLOAD'] = 'Datei Upload';
$MOD_ONEFORALL[$mod_name]['TXT_DATEPICKER'] = 'Datum';
$MOD_ONEFORALL[$mod_name]['TXT_DATEPICKER_START_END'] = 'Datum von &#8230; bis &#8230;';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIMEPICKER'] = 'Datum, Zeit';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIMEPICKER_START_END'] = 'Datum, Zeit von &#8230; bis &#8230;';
$MOD_ONEFORALL[$mod_name]['TXT_JS_SELECT_DATE'] = 'Datum auswählen';
$MOD_ONEFORALL[$mod_name]['TXT_JS_SELECT_DATETIME'] = 'Datum und Zeit auswählen';
$MOD_ONEFORALL[$mod_name]['TXT_DATETIME_SEPARATOR'] = 'um';
$MOD_ONEFORALL[$mod_name]['TXT_DATEDATE_SEPARATOR'] = 'bis';
$MOD_ONEFORALL[$mod_name]['TXT_DROPLET'] = 'WebsiteBaker Droplet';
$MOD_ONEFORALL[$mod_name]['TXT_SELECT'] = 'Auswahlliste';
$MOD_ONEFORALL[$mod_name]['TXT_MULTISELECT'] = 'Multi-Auswahlliste';
$MOD_ONEFORALL[$mod_name]['TXT_MULTIOPTIONS'] = 'Optionen (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_CHECKBOX'] = 'Checkboxen';
$MOD_ONEFORALL[$mod_name]['TXT_CHECKBOXES'] = 'Checkboxen (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_SWITCH'] = 'Schalter ein / aus';
$MOD_ONEFORALL[$mod_name]['TXT_SWITCHES'] = 'ein,aus';
$MOD_ONEFORALL[$mod_name]['TXT_RADIO'] = 'Radio Buttons';
$MOD_ONEFORALL[$mod_name]['TXT_RADIO_BUTTONS'] = 'Radio Buttons (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_SUBDIRECTORY_OF_MEDIA'] = 'Media Unterverz.';
$MOD_ONEFORALL[$mod_name]['TXT_OPTIONS'] = 'Optionen (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_GROUP'] = 'Gruppe';
$MOD_ONEFORALL[$mod_name]['TXT_GROUPS'] = 'Gruppen (csv)';
$MOD_ONEFORALL[$mod_name]['TXT_DELETE_FIELD'] = 'Feld löschen';
$MOD_ONEFORALL[$mod_name]['TXT_CONFIRM_DELETE_FIELD'] = 'Möchten sie folgende Felder und die damit verbundenen Daten wirklich löschen?';

$MOD_ONEFORALL[$mod_name]['TXT_ITEM'] = 'Eintrag';
$MOD_ONEFORALL[$mod_name]['TXT_ITEMS'] = 'Einträge';
$MOD_ONEFORALL[$mod_name]['TXT_ITEMS_PER_PAGE'] = 'Einträge pro Seite';
$MOD_ONEFORALL[$mod_name]['TXT_BACKEND_ITEM_PAGE'] = 'Eintragseite (im Backend)';
$MOD_ONEFORALL[$mod_name]['TXT_HIDE_IMG_SECTION'] = 'Bildeinstellungen und -Upload verstecken';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_THIS'] = 'Die Seiteneinstellungen nur für <b>diese</b> &quot;'.$mod_name.'&quot; Seite übernehmen.';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_ALL'] = 'Die Seiteneinstellungen für <b>alle</b> &quot;'.$mod_name.'&quot; Seiten übernehmen.';
$MOD_ONEFORALL[$mod_name]['TXT_MODIFY_MULTIPLE'] = 'Die Seiteneinstellungen nur für die <b>ausgewählte(n)</b> &quot;'.$mod_name.'&quot; Seite(n) übernehmen (Mehrfachauswahl):';

$MOD_ONEFORALL[$mod_name]['TXT_ADD_ITEM'] = 'Eintrag hinzufügen';
$MOD_ONEFORALL[$mod_name]['TXT_DISABLE'] = 'Deaktivieren';
$MOD_ONEFORALL[$mod_name]['TXT_ENABLE'] = 'Aktivieren';
$MOD_ONEFORALL[$mod_name]['TXT_ENABLED'] = 'Aktiv';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_TABLE'] = 'Ein Klick auf die Spaltenüberschrift sortiert die Tabelle.';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_BY1'] = 'Die Tabelle nach';
$MOD_ONEFORALL[$mod_name]['TXT_SORT_BY2'] = 'sortieren';

$MOD_ONEFORALL[$mod_name]['TXT_TITLE'] = 'Name';
$MOD_ONEFORALL[$mod_name]['TXT_DESCRIPTION'] = 'Beschreibung';
$MOD_ONEFORALL[$mod_name]['TXT_SCHEDULING'] = 'Zeitsteuerung';
$MOD_ONEFORALL[$mod_name]['TXT_PREVIEW'] = 'Vorschau';
$MOD_ONEFORALL[$mod_name]['TXT_FILE_NAME'] = 'Dateiname';
$MOD_ONEFORALL[$mod_name]['TXT_MAIN_IMAGE'] = 'Hauptbild';
$MOD_ONEFORALL[$mod_name]['TXT_THUMBNAIL'] = 'Vorschaubild';
$MOD_ONEFORALL[$mod_name]['TXT_CAPTION'] = 'Bildlegende';
$MOD_ONEFORALL[$mod_name]['TXT_POSITION'] = 'Position';
$MOD_ONEFORALL[$mod_name]['TXT_IMAGE'] = 'Bild';
$MOD_ONEFORALL[$mod_name]['TXT_IMAGES'] = 'Bilder';
$MOD_ONEFORALL[$mod_name]['TXT_SHOW_GENUINE_IMAGE'] = 'Originalbild zeigen';
$MOD_ONEFORALL[$mod_name]['TXT_FILE_LINK'] = 'Link zur Datei';
$MOD_ONEFORALL[$mod_name]['TXT_MAX_WIDTH'] = 'max. Breite';
$MOD_ONEFORALL[$mod_name]['TXT_MAX_HEIGHT'] = 'max. Höhe';
$MOD_ONEFORALL[$mod_name]['TXT_JPG_QUALITY'] = 'JPG Qualität';
$MOD_ONEFORALL[$mod_name]['TXT_NON'] = 'keines';
$MOD_ONEFORALL[$mod_name]['TXT_ITEM_TO_PAGE'] = 'Eintrag zur Seite';
$MOD_ONEFORALL[$mod_name]['TXT_MOVE'] = 'verschieben';
$MOD_ONEFORALL[$mod_name]['TXT_DUPLICATE'] = 'duplizieren';
$MOD_ONEFORALL[$mod_name]['TXT_SAVE_AND_BACK_TO_LISTING'] = 'Speichern und zurück zur Übersicht';

$MOD_ONEFORALL[$mod_name]['ERR_INVALID_SCHEDULING'] = 'Die geplante Startzeit &quot;%s&quot; muss vor dem Endzeitpunkt &quot;%s&quot; liegen.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_EMAIL'] = 'Die angegebene E-Mail Adresse &quot;%s&quot; ist nicht gültig.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_URL'] = 'Die angegebene URL &quot;%s&quot; ist nicht gültig.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_FILE_NAME'] = 'Der Dateiname ist nicht gültig';
$MOD_ONEFORALL[$mod_name]['ERR_ONLY_ONE_GROUP_FIELD'] = 'Das Feld &quot;%s&quot; konnte nicht gespeichert werden, da es nur 1 Feld vom Typ &quot;Gruppe&quot; geben darf.';
$MOD_ONEFORALL[$mod_name]['ERR_BLANK_FIELD_NAME'] = 'Bitte geben Sie für alle Felder einen gültigen und eindeutigen Feldnamen ein!';
$MOD_ONEFORALL[$mod_name]['ERR_CONFLICT_WITH_RESERVED_NAME'] = 'Der Feldname &quot;%s&quot; kann nicht benutzt werden, da er bereits für einen allgemeinen Platzhalter reserviert ist.';
$MOD_ONEFORALL[$mod_name]['ERR_INVALID_FIELD_NAME'] = 'Der Feldname &quot;%s&quot; ist ungültig! Erlaubte Zeichen sind: a-z A-Z 0-9 . (Punkt) _ (Unterstrich) - (Minus)';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_NAME_EXISTS'] = 'Der Feldname &quot;%s&quot; ist bereits vergeben. Bitte wählen sie einen anderen.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_DISABLED'] = 'Dieses Feld ist deaktiviert.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_RE_ENABLE'] = 'Sie können es wieder aktivieren oder den Platzhalter im Template entfernen.';
$MOD_ONEFORALL[$mod_name]['ERR_FIELD_TYPE_NOT_EXIST'] = 'Dieser Feld-Typ existiert nicht!';
$MOD_ONEFORALL[$mod_name]['ERR_SET_A_LABEL'] = 'Feldbezeichnung ergänzen';
$MOD_ONEFORALL[$mod_name]['ERR_INSTALL_MODULE'] = 'Bitte das Modul &quot;%s&quot; installieren und mindestens einen &quot;%s&quot;-Abschnitt anlegen, um dieses Feld benutzen zu können.';


$GLOBALS['TEXT']['CAP_EDIT_CSS'] = 'CSS bearbeiten';
?>