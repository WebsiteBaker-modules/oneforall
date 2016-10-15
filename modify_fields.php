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


require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Include path
$inc_path = dirname(__FILE__);
// Get module name
require_once($inc_path.'/info.php');

// Look for language file
if (LANGUAGE_LOADED) {
    require_once($inc_path.'/languages/EN.php');
    if (file_exists($inc_path.'/languages/'.LANGUAGE.'.php')) {
        require_once($inc_path.'/languages/'.LANGUAGE.'.php');
    }
}


// Get default field types
require_once($inc_path.'/add_field.php');

echo '<div id="default_templates" style="display: none;">'."\n";
foreach ($field_template as $template) {
	echo '<code>'.$template."\n</code>\n";
}
echo '</div>';


// Get $sync_type_template from the session
$sync_type_template = isset($_SESSION[$mod_name]['sync_type_template']) ? $_SESSION[$mod_name]['sync_type_template'] : ' checked="checked"';

// Load jQuery ui if not loaded yet ...
// and start with fields table
?>
<script type="text/javascript">
jQuery().sortable || document.write('<script src="<?php echo WB_URL; ?>/include/jquery/jquery-ui-min.js"><\/script>');
</script>
<script type="text/javascript">
	var mod_name         = '<?php echo $mod_name; ?>',
	txt_dragdrop_message = '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_DRAGDROP_MESSAGE']; ?>';
</script>

<form name="modify" action="<?php echo WB_URL; ?>/modules/<?php echo $mod_name; ?>/save_fields.php" method="post" style="margin: 0;">
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />

<table id="custom_fields" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<thead>
		<tr>
			<th>
				<h2><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELDS']; ?></h2>
			</th>
			<th>
				<input name="sync_type_template" id="sync_type_template" type="checkbox"<?php echo $sync_type_template; ?>>
				<label for="sync_type_template"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_SYNC_TYPE_TEMPLATE']; ?></label>
			</th>
		</tr>
	</thead>
	<tbody>
<?php

// Get all fields from db
$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_".$mod_name."_fields` ORDER BY position, field_id ASC");
if ($query_fields->numRows() > 0) {
	while ($field = $query_fields->fetchRow()) {

		// Get field infos
		$field    = array_map('stripslashes', $field);
		$id       = $field['field_id'];
		$type     = $field['type'];
		$extra    = $field['extra'];
		$name     = $field['name'];
		$label    = $field['label'];
		$template = $field['template'];

		// Set extra field visibility
		$extra_field_class        = 'hidden_extra_field';
		// Hide extra field label
		$ofa_link_label_class     = 'hidden_extra_label';
		$fg_link_label_class      = 'hidden_extra_label';
		$directory_label_class    = 'hidden_extra_label';
		$upload_dir_label_class   = 'hidden_extra_label';
		$options_label_class      = 'hidden_extra_label';
		$multioptions_label_class = 'hidden_extra_label';
		$checkbox_label_class     = 'hidden_extra_label';
		$switch_label_class       = 'hidden_extra_label';
		$radio_label_class        = 'hidden_extra_label';
		$groups_label_class       = 'hidden_extra_label';

		switch ($type) {
			case 'oneforall_link':
				$extra_field_class        = '';
				$ofa_link_label_class     = '';
				break;
			case 'foldergallery_link':
				$extra_field_class        = '';
				$fg_link_label_class      = '';
				break;
			case 'media':
				$extra_field_class        = '';
				$directory_label_class    = '';
				break;
			case 'upload':
				$extra_field_class        = '';
				$upload_dir_label_class   = '';
				break;
			case 'select':
				$extra_field_class        = '';
				$options_label_class      = '';
				break;
			case 'multiselect':
				$extra_field_class        = '';
				$multioptions_label_class = '';
				break;
			case 'checkbox':
				$extra_field_class        = '';
				$checkbox_label_class     = '';
				break;
			case 'switch':
				$extra_field_class        = 'switches';
				$switch_label_class       = '';
				break;
			case 'radio':
				$extra_field_class        = '';
				$radio_label_class        = '';
				break;
			case 'group':
				$extra_field_class        = '';
				$groups_label_class       = '';
				break;
		}
		$extra_field_label = 
			'<span class="'.$ofa_link_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_MODULE_NAME'].'</span>'.
			'<span class="'.$fg_link_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_FOLDERGALLERY_SECTION_ID'].'</span>'.
			'<span class="'.$directory_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_DIRECTORY'].'</span>'.
			'<span class="'.$upload_dir_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_SUBDIRECTORY_OF_MEDIA'].'</span>'.
			'<span class="'.$options_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_OPTIONS'].'</span>'.
			'<span class="'.$multioptions_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_MULTIOPTIONS'].'</span>'.
			'<span class="'.$checkbox_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_CHECKBOXES'].'</span>'.
			'<span class="'.$switch_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_SWITCHES'].'</span>'.
			'<span class="'.$radio_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_RADIO_BUTTONS'].'</span>'.
			'<span class="'.$groups_label_class.'">'.$MOD_ONEFORALL[$mod_name]['TXT_GROUPS'].'</span>';
		?>
		<tr id="id_<?php echo $id; ?>">
			<td colspan="2">
				<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
					<thead>
						<tr class="custom_fields_header">
							<th>&nbsp;</th>
							<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELD_TYPE']; ?></th>
							<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELD_NAME']; ?></th>
							<th><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELD_LABEL']; ?></th>
							<th><?php echo $extra_field_label; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="field_number"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_CUSTOM_FIELD'].' id '.$id; ?></td>
							<td>
								<select name="fields[<?php echo $id; ?>][type]">
								<?php
								foreach ($field_types as $field_type => $field_type_name) {
									$selected = $type == $field_type ? ' selected="selected"' : '';
									echo '<option value="'.$field_type.'"'.$selected.'>'.$field_type_name.'</option>';
								}
								?>
								</select>
							</td>
							<td><input name="fields[<?php echo $id; ?>][name]" type="text" value="<?php echo htmlspecialchars($name); ?>"></td>
							<td><input name="fields[<?php echo $id; ?>][label]" type="text" value="<?php echo htmlspecialchars($label); ?>"></td>
							<td><input class="<?php echo $extra_field_class; ?>" name="fields[<?php echo $id; ?>][extra]" type="text" value="<?php echo htmlspecialchars($extra); ?>"></td>
						</tr>
						<tr>
							<td class="field_placeholder"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELD_PLACEHOLDER']; ?></td>
							<td colspan="4"><code>[<?php echo htmlspecialchars(strtoupper($name)).']</code> '.$MOD_ONEFORALL[$mod_name]['TXT_OR'].' <code>[FIELD_'.htmlspecialchars($id); ?>]</code></td>
						</tr>
						<tr>
							<td class="field_template"><?php echo $MOD_ONEFORALL[$mod_name]['TXT_FIELD_TEMPLATE']; ?></td>
							<td colspan="4">
								<textarea name="fields[<?php echo $id; ?>][template]"><?php echo htmlspecialchars($template); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		}
	}
	?>
	</tbody>
</table>

<table width="100%" align="center" cellpadding="0" cellspacing="0" class="mod_oneforall_submit_table_b">
	<tr>
		<td class="">
			<input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" onclick="javascript: return confirm_delete('<?php echo $MOD_ONEFORALL[$mod_name]['TXT_CONFIRM_DELETE_FIELD']; ?>', '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_CUSTOM_FIELD']; ?>')" />
		</td>
		<td>
			<select name="new_fields">
			<?php
			foreach (range(1,10) as $key => $num) {
				$selected = $num == 2 ? ' selected="selected"' : '';
				echo '<option value="'.$num.'"'.$selected.'>'.$num.'</option>';
			}
			?>
			</select>
			<input name="add_fields" type="submit" value="<?php echo $MOD_ONEFORALL[$mod_name]['TXT_ADD_NEW_FIELDS']; ?>" onclick="javascript: return confirm_delete('<?php echo $MOD_ONEFORALL[$mod_name]['TXT_CONFIRM_DELETE_FIELD']; ?>', '<?php echo $MOD_ONEFORALL[$mod_name]['TXT_CUSTOM_FIELD']; ?>')" />
		</td>
		<td>
			<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" />
		</td>
	</tr>
</table>
</form>

<?php

// Print admin footer
$admin->print_footer();

?>
