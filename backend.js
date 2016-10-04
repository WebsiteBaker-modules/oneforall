/*
  Javascript routines for WebsiteBaker module OneForAll
  Copyright (C) 2015, Christoph Marti

  This Javascript routines are free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  The Javascript routines are distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/




// **********************************************************************************
//   OneForAll SORTABLE ITEMS TABLE
//   Function to sort items by drag&drop
// **********************************************************************************

$(document).ready(function() {

	// Add hover effect for item rows
	var row = $('#mod_oneforall_sortable_b tr');
	row.mouseover(function() {
		$(this).addClass('mod_oneforall_mouseover_b');
	}).mouseout(function() {
		$(this).removeClass('mod_oneforall_mouseover_b');
	});

	// Get module name
	var mod_name = $('#mod_oneforall_sortable_b').attr('class'); // attr class ???

	// Check if sortable is available
	if (jQuery().sortable) {

		// Fix the width of the cells so they do not collapse on drag&drop
		$('td, th', '#mod_oneforall_sortable_b').each(function() {
			var cell = $(this);
			cell.width(cell.width());
		});

		// Remove the up/down icons
		$("a[href*='move_up.php'], a[href*='move_down.php']", '#mod_oneforall_sortable_b').hide();

		// Make table sortable
		$('#mod_oneforall_sortable_b tbody').sortable({
			opacity: 0.5,
			forcePlaceholderSize: true,
			placeholder: 'mod_oneforall_placeholder_b',
			update: function() {
				var order = $(this).sortable('serialize') + '&action=update_pos&mod_name=' + mod_name;

				// Post ordering to the server
				$.post('../../modules/' + mod_name + '/move_item.php', order, function() {
					// Show animated ajax-loader gif
					$('#mod_oneforall_ajax_loader_b').fadeIn();	
					$('#mod_oneforall_ajax_loader_b').fadeOut(2000);
				}); 	
			}				
		});
	}
});




// **********************************************************************************
//   MODIFY ITEM
//   Function to sinc selected file with file link or preview thumb
// **********************************************************************************

$(document).ready(function() {

	$('#modify_item').change(function() {

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
});




// **********************************************************************************
//   MODIFY FIELDS
//   Function to sinc form with selected field type
// **********************************************************************************

$(document).ready(function() {

	$('#custom_fields select').change(function() {

		// Get type (from select option)
		var type = $(this).val();
		// Get the elements
		var textarea    = $(this).closest('tr').nextAll().eq(1).find('textarea');
		var extra_label = $(this).parent().parent().prev().find('th:last');
		var extra_field = $(this).parent().nextAll().eq(2).find('input');
		// First hide all labels and fields ...
		extra_label.find('span').addClass('hidden_extra_label');
		extra_field.addClass('hidden_extra_field');
		// ...then display it if the type matches
		var types = ['oneforall_link', 'foldergallery_link', 'media', 'upload', 'select', 'group'];
		var tl    = types.length;
		for	(i = 0; i < tl; i++) {
			if (type == types[i]) {
				child = i + 1;
				extra_label.find(':nth-child('+child+')').removeClass('hidden_extra_label');
				extra_field.removeClass('hidden_extra_field');
			}
		}
		// If sync is activated, get default template (tml) and insert it into the textarea
		if ($('#sync_type_template').prop('checked')) {
			var default_tml = $("#default_templates [class$='_"+type+"_f']").parent().html().trim();
			if (default_tml !== undefined) {
				textarea.html(default_tml).addClass('highlight');
				// Delay until css animation has finished
				setTimeout(function() {
					textarea.removeClass('highlight');
				}, 1050);
			}
		}
	});	
});




// **********************************************************************************
//   MODIFY FIELDS
//   Function to confirm that a field and all associated data will be deleted
// **********************************************************************************

function confirm_delete(message, txt_field) {

	var fields = ''; // null oder false oder ???

	$('select').each(function(index) {
		var type = $(this).val();
		if (type == 'delete') {
			index++;
			fields += "\n   • "+txt_field+' '+index;
		}
	});
	if (fields != '') {
		return confirm(message+fields);
	}
}




// **********************************************************************************
//   MODIFY ITEM
//   Function to add and remove file type inputs
//   (http://codingforums.com/showthread.php?t=65390)
// **********************************************************************************

function addFile(delTxt) {
	var root = document.getElementById('upload').getElementsByTagName('tr')[0].parentNode;
	var oR   = cE('tr');
	var oC   = cE('td');
	var oI   = cE('input');
	var oS   = cE('span');
	cA(oI,'type','file');
	cA(oI,'name','image[]');
	oS.style.cursor = 'pointer';

	oS.onclick = function() {
		this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
	}

	oS.appendChild(document.createTextNode(delTxt));
	oC.appendChild(oI);
	oC.appendChild(oS);
	oR.appendChild(oC);
	root.appendChild(oR);
}

function cE(el){
	this.obj = document.createElement(el);
	return this.obj;
}

function cA(obj,att,val) {
	obj.setAttribute(att,val);
	return;
}
