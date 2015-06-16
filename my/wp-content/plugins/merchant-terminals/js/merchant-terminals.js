var ajaxurl = '/wp-admin/admin-ajax.php';

jQuery(document).ready(function() {
	merchant_terminals.init();
});

var merchant_terminals = {
	init: function() {
		
	    // Show the paper form when the corresponding button is clicked
    	jQuery('body').on('click','#paper_button',function(e) {
	    	merchant_terminals.show_paper_form();
	    	
		    e.preventDefault();
	    });
	    
	    // Show the terminal form when the corresponding button is clicked
    	jQuery('body').on('click','#terminal_button',function(e) {
	    	merchant_terminals.show_terminal_form();
	    	
		    e.preventDefault();
	    });    
	    
	    // Add a new paper type
	    jQuery('body').on('submit','#paper_form',function(e) {
	    	merchant_terminals.submit_paper_form();
	    	
		    e.preventDefault();
	    });
    
	    // Add a new terminal
	    jQuery('body').on('submit','#terminal_form',function(e) {
	    	merchant_terminals.submit_terminal_form();
			
			e.preventDefault();
	    });
	    // Delete an item
	    jQuery('body').on('click','.delete_terminal_item',function(e) {
		    merchant_terminals.delete_item(jQuery(this));
			
			e.preventDefault();
	    });
	    
	},
	show_paper_form: function(){
	    jQuery('#terminal_form').hide();
	    jQuery('#paper_form').show();
	},
	show_terminal_form: function(){
	    jQuery('#terminal_form').show();
		jQuery('#paper_form').hide();
	},
	submit_paper_form: function(){
		// Use ajax to submit the paper form and call the update_paper() function
		
	    var loading = jQuery('#ajax_form_loading');
		var paper_form = jQuery('#paper_form');
		var paper_form_data = paper_form.serialize();
		
		jQuery("#paper_form input[type='submit']").hide();
		paper_form.hide();
		loading.show();
		
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: 'update_paper',
				paper: paper_form_data
			}
		}).done(function(resp) {
					
			jQuery.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: 'display_paper',
					is_ajax: 1
				}
			}).done(function(resp) {
				jQuery("#paper_table").html(resp);
				loading.hide();
				paper_form[0].reset();
				paper_form.show();
				jQuery("#paper_form input[type='submit']").show();
			});
			
		});
	},
	submit_terminal_form: function(){
		// Use ajax to submit the terminal form and call the update_terminal() function
		
	    var loading = jQuery('#ajax_form_loading');
		var terminal_form = jQuery('#terminal_form');
		var terminal_form_data = terminal_form.serialize();
		
		terminal_form.hide();
		jQuery("#terminal_form input[type='submit']").hide();
		loading.show();
		
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: 'update_terminal',
				terminal: terminal_form_data
			}
		}).done(function(resp) {
			console.log(resp);
			jQuery.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: 'display_terminals',
					is_ajax: 1
				}
			}).done(function(resp) {
				jQuery("#terminal_table").html(resp);
				loading.hide();
				terminal_form[0].reset();
				terminal_form.show();
				jQuery("#terminal_form input[type='submit']").show();
			});
			
		});
	},
	delete_item: function(clicked_button){
		// Deletes the database table corresponding to the link clicked. Uses the delete_terminal_item() function
		
	    var button = clicked_button;
		var type = clicked_button.data('type');
		var item_id = clicked_button.data('id');
		
		// Check for validation before deleting
		if(!confirm('Are you sure you want to delete this?') ){
        	return false;
		}
		
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: 'delete_terminal_item',
				type: type,
				item_id: item_id
			}
		}).done(function(resp) {
			console.log(resp);
			// Removes row upon completion
			button.closest('tr').remove();
		});
	}
		
}