/**
* Admin UI
*/
(function($){
	crfpAdminUI = {	
		/**
		* Initialises the plugin:
		* - Begins the event listener
		*/
		init: function() {
			crfpAdminUI.toggle($('select[name="comment-rating-field-pro-plugin[enabled][averageExcerpt]"]'));
			crfpAdminUI.toggle($('select[name="comment-rating-field-pro-plugin[enabled][average]"]'));
			crfpAdminUI.toggle($('select[name="comment-rating-field-pro-plugin[enabled][comment]"]'));
			crfpAdminUI.listen();
		},
		
		/**
		* Listens for the change event on dropdown selection fields
		*/
		listen: function() {
			$('select[name="comment-rating-field-pro-plugin[enabled][averageExcerpt]"], select[name="comment-rating-field-pro-plugin[enabled][average]"], select[name="comment-rating-field-pro-plugin[enabled][comment]"]').on('change', function(e) {
				crfpAdminUI.toggle(this);
			});	
		},
		
		toggle: function(obj) {
			if ($(obj).val() > 0) {
				$('div.extra-options', $(obj).parent().parent().parent()).show();	
			} else {
				$('div.extra-options', $(obj).parent().parent().parent()).hide();	
			}	
		}
	}	
	
	// Init
	$(crfpAdminUI.init);	
})(jQuery);

/* TinyMCE Popup */
(function($){
	crfpTinyMCE = {	
		/**
		* Initialises the plugin:
		* - Begins the event listener
		*/
		init: function() {
			crfpTinyMCE.listen();	
		},
		
		/**
		* Listens for the popup form to be submitted
		*/
		listen: function() {
			$('form.crfp-popup').on('submit', function(e) {
				e.preventDefault();
				crfpTinyMCE.insert(this);
			});
		},
		
		/**
		* Inserts a shortcode into the TinyMCE editor, based on the popups settings
		*/
		insert: function(obj) {
			// Build shortcode based on input
			var shortcode = '[crfp enabled="'+$('select[name=enabled]').val()+'"';
			shortcode += ' displayStyle="'+$('select[name=displayStyle]').val()+'"';
			shortcode += ' displayAverage="'+$('select[name=displayAverage]').val()+'"';
			shortcode += ' averageRatingText="'+$('input[name=averageRatingText]').val()+'"';
			shortcode += ' displayTotalRatings="'+$('select[name=displayTotalRatings]').val()+'"';
			shortcode += ' displayBreakdown="'+$('select[name=displayBreakdown]').val()+'"';
			shortcode += ' displayLink="'+$('select[name=displayLink]').val()+'"';
			shortcode += ' id="'+$('input[name=postID]').val()+'"';
			shortcode += ']';

			// Insert into Editor
			tinyMCEPopup.execCommand('mceReplaceContent', false, shortcode);
			tinyMCEPopup.close();
		}
	}	
	
	// Init
	$(crfpTinyMCE.init);	
})(jQuery);