/*
* Script for NYP quick edit fields
*/
;(function ($) {

   $('#the-list').on('click', 'a.editinline', function(){

   	inlineEditPost.revert();

		// get the post ID
		var post_id = $(this).closest('tr').attr('id');

		post_id = post_id.replace("post-", "");

		// find the hidden NYP data
		var $nyp_inline_data = $('#nyp_inline_' + post_id );

		// get nyp status, suggested and minimum price variables
		var nyp 			= $nyp_inline_data.find('.nyp').text();
		var suggested_price 		= $nyp_inline_data.find('.suggested_price').text();
		var min_price 			= $nyp_inline_data.find('.min_price').text();

		// set suggested and min price inputs
		$('input[name="_suggested_price"]', '.inline-edit-row').val(suggested_price);
		$('input[name="_min_price"]', '.inline-edit-row').val(min_price);

		// if NYP show suggested and min inputs
		if ( nyp == 'yes' ) {
			$('input[name="_nyp"]', '.inline-edit-row').attr('checked', 'checked');
			$('.nyp_prices', '.inline-edit-row').show();
			$('.price_fields', '.inline-edit-row').css('opacity', '.33').find('input').prop('disabled', true);
		} else {
			$('input[name="_nyp"]', '.inline-edit-row').removeAttr('checked');
			$('.nyp_prices', '.inline-edit-row').hide();
			$('input', '.price_fields');
		}

   	// Conditional display
		var is_nyp_allowed		= $nyp_inline_data.find('.is_nyp_allowed').text();

		// only show for simple products
		if ( is_nyp_allowed == 'allowed' ) {
			$('#nyp-fields', '.inline-edit-row').show();
		} else {
			$('#nyp-fields', '.inline-edit-row').hide();
		}

	});

	// toggle display of suggested and min prices based on NYP checkbox
    $('#the-list').on('change', '.inline-edit-row input[name="_nyp"]', function(){

    	if ($(this).is(':checked')) {
    		$('.nyp_prices', '.inline-edit-row').show().removeAttr('style');
    		$('.price_fields', '.inline-edit-row').fadeTo('fast', .33).find('input').prop('disabled', true);
    	} else {
    		$('.nyp_prices', '.inline-edit-row').hide();
    		$('.price_fields', '.inline-edit-row').fadeTo('fast', 1).find('input').prop('disabled', false);
    	}

    });

})(jQuery);