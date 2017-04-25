/*
  Javascript routines for WebsiteBaker module OneForAll
  Copyright (C) 2017, Christoph Marti

  This Javascript routines are free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  The Javascript routines are distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/




$(document).ready(function() {




// **********************************************************************************
//   OneForAll AJAX MESSAGE
//   Function to show a message if an ajax request has been successful
// **********************************************************************************

	// Add the message div if not existing yet
	if (document.getElementById('success_msg_b') === null) {
		$('body').prepend('<div id="success_msg_b" class="animated bounceInDown" style="display: none;"></div>');
    }
	var success_msg = $('#success_msg_b');	

	// Ajax success message
	var loading = success_msg.hide();
	$(document)
		.ajaxStart(function () {
			loading.show();
		})
		.ajaxStop(function () {
			loading.show();
			setTimeout(function() {
				loading.hide();
			}, 2000);
	});




// **********************************************************************************
//   OneForAll SORTABLE ITEMS TABLE
//   Function to enable/disable an item
// **********************************************************************************

	$('[class^="mod_oneforall_active"]').click(function() {

		// Get current active button class
		var button = $(this);
		var current_class = button.prop('class');
		
		// Check if item is enabled/disabled
		if (current_class == 'mod_oneforall_active0_b') {
			var bt_value = 1;
			var bt_title = mod_oneforall.txt_disable;
		} else {
			var bt_value = 0;
			var bt_title = mod_oneforall.txt_enable;
		}

		// Get the item_id
		var item_id = button.closest('tr').prop('id').replace('id_', '');

		// Post active to the server
		$.post('../../modules/' + mod_oneforall.mod_name + '/ajax/toggle_active.php',
		{
			value:      bt_value,
			item_id:    item_id,
			mod_name:   mod_oneforall.mod_name,
			action:     'update_active'
		},
		function() {
			// On success remove current class and set new class, change title text
			button.removeClass().addClass('mod_oneforall_active' + bt_value + '_b');
			button.prop('title', bt_title);
			// Set the success message used by ajaxStop(), see on top of this file 
			success_msg.text(mod_oneforall.txt_toggle_message);
		});
	});




// **********************************************************************************
//   OneForAll SORTABLE ITEMS TABLE
//   Function to sort items by drag&drop
// **********************************************************************************

	// Check if sortable is available
	if (jQuery().sortable) {

		// Fix the width of the cells so they do not collapse on drag&drop
		$('td, th', '#mod_oneforall_items_b').each(function() {
			var cell = $(this);
			cell.width(cell.width());
		});

		// Remove the up/down icons
		$("a[href*='move_up.php'], a[href*='move_down.php']", '#mod_oneforall_items_b').hide();

		// Make table sortable
		$('#mod_oneforall_items_b tbody').sortable({
			opacity: 0.8,
			update: function() {
				var order = $(this).sortable('serialize') + '&action=update_pos&mod_name=' + mod_oneforall.mod_name;
				// Post ordering to the server
				$.post('../../modules/' + mod_oneforall.mod_name + '/ajax/move_item.php', order, function() {
					// Set the success message used by ajaxStop(), see on top of this file 
					success_msg.text(mod_oneforall.txt_dragdrop_message);
				}); 	
			}				
		});
	}




// **********************************************************************************
//   OneForAll MODIFY ITEM
//   Function to sort images by drag&drop
// **********************************************************************************

	// Check if sortable is available
	if (jQuery().sortable) {

		// Fix the width of the cells so they do not collapse on drag&drop
		$('td, th', '#mod_oneforall_images_b').each(function() {
			var cell = $(this);
			cell.width(cell.width());
		});

		// Remove the up/down icons
		$("a[href*='move_img_up.php'], a[href*='move_img_down.php']", '#mod_oneforall_images_b').hide();
		$('#mod_oneforall_images_b th:eq(4)').text('');

		// Make table sortable
		$('#mod_oneforall_images_b tbody').sortable({
			opacity: 0.8,
			items: "[id^='id_']",
			update: function() {
				var order = $(this).sortable('serialize') + '&action=update_pos&mod_name=' + mod_oneforall.mod_name;
				// Post ordering to the server
				$.post('../../modules/' + mod_oneforall.mod_name + '/ajax/move_image.php', order, function() {

					// On success move 'main image' note to the top image
					var txt_main_img = $('b, b + br', '#mod_oneforall_images_b');
					$('#mod_oneforall_images_b tr:eq(1) td:eq(1)').prepend(txt_main_img);

					// Set the success message used by ajaxStop(), see on top of this file 
					success_msg.text(mod_oneforall.txt_dragdrop_message);
				}); 	
			}				
		});
	}




// **********************************************************************************
//   OneForAll MODIFY ITEM
//   Function to sinc selected file with file link or preview thumb
// **********************************************************************************

	$('#mod_oneforall_modify_item_b').change(function() {

		// Hide all images and links ...
		$('a.media_img, a.media_link').addClass('hidden');

		$('select.media').each(function() {
	        // Get url (from select option)
			var url = $(this).val();
			// ...then display the selected one
			if (url) {
				$(this).nextAll("a[href$='"+url+"']").removeClass('hidden');
			}
		});
	});	




// **********************************************************************************
//   OneForAll MODIFY FIELDS
//   Function to sort fields by drag&drop
// **********************************************************************************

	// Check if sortable is available
	if (jQuery().sortable) {

		// Fix the width of the cells so they do not collapse on drag&drop
		$('td, th', '#mod_oneforall_custom_fields_b').each(function() {
			var cell = $(this);
			cell.width(cell.width());
		});

		// Make table sortable
		$('#mod_oneforall_custom_fields_b > tbody').sortable({
			opacity: 0.8,
			update: function() {
				var order = $(this).sortable('serialize') + '&action=update_pos&mod_name=' + mod_oneforall.mod_name;
				// Post ordering to the server
				$.post('../../modules/' + mod_oneforall.mod_name + '/ajax/move_field.php', order, function() {
					// Set the success message used by ajaxStop(), see on top of this file 
					success_msg.text(mod_oneforall.txt_dragdrop_message);
				});
			}
		});
	}




// **********************************************************************************
//   OneForAll MODIFY FIELDS
//   Function to sinc form with selected field type
// **********************************************************************************

	$('#mod_oneforall_custom_fields_b select').change(function() {

		// Get type (from select option)
		var type = $(this).val();
		// Get the elements
		var textarea    = $(this).closest('tr').nextAll().eq(1).find('textarea');
		var extra_label = $(this).closest('tbody').prev().find('th:last');
		var extra_field = $(this).parent().nextAll().eq(2).find('input');
		// First hide all labels and fields ...
		extra_label.find('span').addClass('mod_oneforall_hidden_extra_label_b');
		extra_field.addClass('mod_oneforall_hidden_extra_field_b');
		// ...then display it if the type matches
		var types = ['oneforall_link', 'foldergallery_link', 'media', 'upload', 'select', 'multiselect', 'checkbox', 'switch', 'radio', 'group'];
		var tl    = types.length;
		for	(i = 0; i < tl; i++) {
			if (type == types[i]) {
				child = i + 1;
				extra_label.find(':nth-child('+child+')').removeClass('mod_oneforall_hidden_extra_label_b');
				extra_field.removeClass('mod_oneforall_hidden_extra_field_b');
			}
		}
		// If sync is activated, get default template (tml) and insert it into the textarea
		if ($('#sync_type_template').prop('checked')) {
			var default_tml = $("#mod_oneforall_default_templates_b [class$='_"+type+"_f']").parent().html();
			if (default_tml !== null) {
				textarea.html(default_tml.trim()).addClass('highlight');
				// Delay until css animation has finished
				setTimeout(function() {
					textarea.removeClass('highlight');
				}, 1050);
			}
		}
	});	
});




// **********************************************************************************
//   OneForAll MODIFY FIELDS
//   Function to confirm that a field and all associated data will be deleted
// **********************************************************************************

function confirm_delete(message, txt_field) {

	var fields = '';
	$('select[name^="fields"]').each(function(index) {
		var type = $(this).val();
		var id   = $(this).prop('name').replace(/[^0-9\.]/g, '');
		if (type == 'delete') {
			index++;
			fields += "\n   • "+txt_field+' id '+id;
		}
	});
	if (fields != '') {
		return confirm(message+fields);
	}
}



