var alert_num = 0;
jQuery(document).ready(function() {
    // Setup filters
    jQuery('#rsp_per_page,#rsp_min_points,#rsp_sort_by').change(function() {
        window.location.href = jQuery(this).val();
    });

    // Add cart up/down listeners
    add_listeners();

    // Add to cart button
    jQuery('.rsp_add_to_cart').click(function() {
    	// Add to cart
    	rsp_add_to_cart(jQuery(this).attr('rsp-pid'));
    });

    // Search button
    jQuery('#search_button').click(function() {
    	document.location.href = '/loyalty-rewards/search/'+encodeURIComponent(jQuery('#search_term').val())+'/';
    });

    // Search if enter button is pressed
    $('#search_term').keypress(function(e) {
        if (e.keyCode == 13) {
            document.location.href = '/loyalty-rewards/search/'+encodeURIComponent(jQuery(this).val())+'/';
        }
    });

    // Empty cart button
    jQuery('#rsp_empty_cart').click(function(e) {
        e.preventDefault();

        // Send request to empty cart
    	$.ajax({
    		type: "POST",
    		url: ajaxurl,
    		data: { action: 'rsp_empty_cart' },
    		dataType: 'json',
    		success: function(response) {
        		if (response.status === 'success') {
                    jQuery('#cart_items tr').remove();
                    jQuery('#cart_items tbody').append('<tr><td colspan="2"><em>No items in cart.</em></td></tr>');
                    jQuery('#total_points').html('--');
                    jQuery('#points_in_cart').html(response.points_in_cart);
        		} else {
            		if (response.message) {
                		create_alert(response.message, 'error');
            		} else {
                		create_alert('<strong>Sorry</strong> There was an error emptying your cart. Please refresh the page and try again.', 'error');
            		}
        		}

                // Send request for cart contents
                rsp_update_cart();
    		},
    		error: function(response) {
    			create_alert('<strong>Sorry</strong> There was an error emptying your cart. Please refresh the page and try again.', 'error');
    		}
    	});
    });

    // Place order
    $('.place-order-button').click(function() {
        // Show placing order div
        $('#loading_cart').show();

        // Let user know the order is being placed,
        $(this).html('<em>Placing Order...</em>');

        // Unbind click handler so they can't click again
        $(this).unbind('click');

        // Place order
        $.ajax({
    		type: "POST",
    		url: ajaxurl,
    		data: { action: 'rsp_place_order', first_name: $('#first_name').val(), last_name: $('#last_name').val(), address_1: $('#address_1').val(), city: $('#city').val(), state_province: $('#state_province').val(), postal_code: $('#postal_code').val(), country: $('#country').val() },
    		dataType: 'json',
    		success: function(response) {
        		console.log(JSON.stringify(response));
        		if (response.status === 'success') {
                    create_alert(response.message, 'success');

                    // Redirect user
                    window.location.href = response.redirect_url;
        		} else {
            		if (response.message) {
                		create_alert(response.message, 'error');
            		} else {
                		create_alert('<strong>Sorry</strong> There was an error displaying your cart. Please refresh the page and try again.', 'error');
            		}
        		}

                // Hide placing order div
                $('#loading_cart').hide();
    		},
    		error: function(response) {
    			create_alert('<strong>Sorry</strong> There was an error displaying your cart. Please refresh the page and try again.', 'error');

                // Hide placing order div
                $('#loading_cart').hide();

                // Reset place order button
                $(this).html('<em>Place Order</em>');
    		}
    	});
    });

    // Check for alerts to auto-dismiss
    setInterval(function() {
        jQuery('.alert').each(function(index, value) {
            var cur_time = Math.floor(Date.now()/1000);
            if ((cur_time-jQuery(this).attr('alert-time')) > 4) {
                jQuery(this).remove();
            }
        });
    }, 2000);
});

// Update cart function
function rsp_update_cart() {
    // Update cart
	$.ajax({
		type: "POST",
		url: ajaxurl,
		data: { action: 'rsp_view_cart' },
		dataType: 'json',
		success: function(response) {
    		if (response.status === 'success') {
                // Update contents
                jQuery('#cart_contents').html(response.cart_contents);

                // Update points
                jQuery('#points_in_cart').html(response.points_in_cart);
    		} else {
        		if (response.message) {
            		create_alert(response.message, 'error');
        		} else {
            		create_alert('<strong>Sorry</strong> There was an error displaying your cart. Please refresh the page and try again.', 'error');
        		}
    		}

            // Add listeners
            add_listeners();
		},
		error: function(response) {
			create_alert('<strong>Sorry</strong> There was an error displaying your cart. Please refresh the page and try again.', 'error');
		}
	});
}

// Add to cart function
function rsp_add_to_cart(catalog_item_id, quantity, updated_from) {
    if (typeof catalog_item_id === 'undefined') { catalog_item_id = null; };
    if (typeof quantity === 'undefined') { quantity = 1; };
    if (typeof updated_from === 'undefined') { updated_from = null; };

    // Show "loading..." message
    jQuery('#cart_items tr').remove();
    jQuery('#cart_items tbody').append('<tr><td colspan="2"><em>Loading cart contents...</em></td></tr>');
    
    if (jQuery('.rsp_add_to_cart').attr('disabled')) {
        alert('Please wait while we add your item to the cart.');
    } else {
        // Disable add to cart button and set label
        if (updated_from == null) {
            jQuery('.rsp_add_to_cart').html('<em>Adding to cart...</em>');
            jQuery('.rsp_add_to_cart').attr('disabled', 'disabled');
        }

        // Add item to cart
    	$.ajax({
    		type: "POST",
    		url: ajaxurl,
    		data: { action: 'rsp_add_to_cart', catalog_item_id: catalog_item_id, quantity: quantity },
    		dataType: 'json',
    		success: function(response) {
        		if (response.status === 'success') {
            		create_alert(response.message, 'success');
    
                    // Update cart contents
                    jQuery('#cart_contents').html(response.cart_contents);

                    // Update points
                    jQuery('#points_in_cart').html(response.points_in_cart);

                    // Add listeners
                    add_listeners();
        		} else {
            		create_alert(response.message, 'error');
        		}

                // Enable add to cart button and set label
                if (updated_from == null) {
                    jQuery('.rsp_add_to_cart').html('Add To Cart');
                    jQuery('.rsp_add_to_cart').removeAttr('disabled');
                }
    		},
    		error: function(response) {
    			create_alert('<strong>Sorry</strong> There was an error adding this item to your cart. Please refresh the page and try again.', 'error');

                // Enable add to cart button and set label
                if (updated_from == null) {
                    jQuery('.rsp_add_to_cart').html('Add To Cart');
                    jQuery('.rsp_add_to_cart').removeAttr('disabled');
                }
    		}
    	});
    }
}

// Create alert
function create_alert(alert_text, alert_type) {
    // Set default alert to success
    if (typeof alert_type === 'undefined') { alert_type = success; };

    // Set alert time
    var alert_time = Math.floor(Date.now()/1000);

    // Display alert
    jQuery('#rsp_notifications').append('<div class="alert alert-'+alert_type+'" alert-time="'+alert_time+'"><button class="close" data-dismiss="alert" style="padding:5px;"></button>'+alert_text+'</div>');
}

// Add listeners for cart quantity +/-
function add_listeners() {
    // Change cart quantity
    jQuery('.qty-plus,.qty-minus').click(function() {
        var classes = jQuery(this).attr('class');
        var qty_div = 'qty-'+jQuery(this).attr('catalog-item-id');
        if (classes.indexOf('qty-plus') >= 0) {
            // Increment quantity
            var new_quantity = parseInt(jQuery('#'+qty_div).html())+1;
            rsp_add_to_cart(jQuery(this).attr('catalog-item-id'), new_quantity, 'rsp_update_cart');
        } else {
            // Decrement quantity
            if (parseInt(jQuery('#'+qty_div).html()) > 0) {
                var new_quantity = parseInt(jQuery('#'+qty_div).html())-1;
                rsp_add_to_cart(jQuery(this).attr('catalog-item-id'), new_quantity, 'rsp_update_cart');
                jQuery('#'+qty_div).html(new_quantity);
            }
        }
    });

    // Remove item from cart
    jQuery('.remove-item').click(function() {
        rsp_add_to_cart(jQuery(this).attr('catalog-item-id'), 0, 'rsp_update_cart');
    });
}