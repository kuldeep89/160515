jQuery(document).ready(function($) {
	// Invoke Rating Plugin
	$('p.crfp-field').each(function() {
		$('input.star', $(this)).rating({
			cancel: $(this).parent().data('cancel-text'),
			cancelValue: 0,
			split: ((crfp.halfRatings == 1) ? 2 : 1),
			callback: function(rating) {
				var parentElement = $(this).closest('.crfp-field');
				$('input[type=hidden]', $(parentElement)).val(rating);
			}
		});
	});
	
	// Reset to zero if cancel clicked
	$('div.rating-cancel a').bind('click', function(e) { 
		var parentElement = $(this).closest('.crfp-field');
		$('input[type=hidden]', $(parentElement)).val('0');	
	});
	
	// If rating required, check it has been supplied before submitting a comment				
	$('form#commentform').bind('submit', function(e) {
		$('.crfp-field').each(function() {
			// If field hidden, this is a reply that has ratings disabled, so don't require it
			if ($(this).css('display') != 'none') {			
				var field = $(this);
				var required = $(this).data('required');
				var requiredText = $(this).data('required-text');
				if (required == '1') {
					if ($('input[type=hidden]', $(this)).val() == '' || $('input[type=hidden]', $(this)).val() == 0) {
						alert(requiredText);
						e.preventDefault();
						return false;
					}	
				}
			}
		});
	});
	
	// If JS comments are enabled
	if (typeof crfp !== 'undefined' && typeof addComment !== 'undefined') {
		// Check if replies are disabled
		if (crfp.ratingDisableReplies == '1') {
			// Hide CRFP rating fields when Reply button clicked
			$('a.comment-reply-link').on('click', function(e) {
				$('p.crfp-field').hide();
			});	
			
			// Show CRFP rating fields when Cancel button clicked
			$('a#cancel-comment-reply-link').on('click', function(e) {
				$('p.crfp-field').show();
			});
		}
	}
});