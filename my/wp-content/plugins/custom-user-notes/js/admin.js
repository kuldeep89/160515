jQuery(document).ready(function() {
	jQuery('#cu_add_note').click(function() {
		// Assigne data
		var cu_note_content = jQuery('#add_new_custom_user_note').val();
		var cu_note_user_id = jQuery('#add_new_custom_user_note_user_id').val();

		// Disable textarea and let user know we're saving the note
		jQuery('#add_new_custom_user_note').val('Saving...');
		jQuery('#add_new_custom_user_note').attr('disabled','disabled');

		// Send AJAX request
		jQuery.ajax({
			url: ajaxurl,
			data: { action: 'cu_notes_save_note', add_new_custom_user_note: cu_note_content, add_new_custom_user_note_user_id: cu_note_user_id }
		}).done(function(resp) {
			// Parse data and add to notes table
			resp = jQuery.parseJSON(resp);
			jQuery('#list_of_custom_notes tbody tr:first-child').after('<tr><td style="padding:7px !important;"><strong>'+resp.note_author+'</strong> <em>'+resp.note_created+'</em><br>'+cu_note_content+'</td></tr>');
			
			// Re-enable note textarea
			jQuery('#add_new_custom_user_note').val('');
			jQuery('#add_new_custom_user_note').removeAttr('disabled');
		});
	});
});