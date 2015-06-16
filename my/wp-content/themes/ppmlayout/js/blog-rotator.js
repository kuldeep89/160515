(function($,W,D) {
	$(W).load(function($) {
		
		var imageRotator = function() {
			
			var SliderArray = [];
			array_counter = 0; 
			
			// Adds each slider to the array.
			jQuery('div[class^="slider"]').each(function() { 
				  
				SliderArray.push({
					SliderName : jQuery(this).attr('id'), 
					Slides : []
				});  
				  
				// Adds slides to each slider in the array.
				jQuery(this).find('.slide').each(function() {
					SliderArray[array_counter].Slides.push(this);
				});
			
				array_counter++;
			
			});
			
			var CounterArray = [];
			
			// Sets up separate counter for each slider in an array.
			for (i = 0; i < SliderArray.length; i++) {
				CounterArray.push(0);
			}
			
			// Navigation between slides.
			var nextNav = function(slider_number) {			
				jQuery(SliderArray[slider_number]['Slides'][CounterArray[slider_number]]).removeClass('current');
				CounterArray[slider_number] = (CounterArray[slider_number] + 1) % SliderArray[slider_number]['Slides'].length;
				jQuery(SliderArray[slider_number]['Slides'][CounterArray[slider_number]]).addClass('current');				
			}
			
			var prevNav = function(slider_number) {
				jQuery(SliderArray[slider_number]['Slides'][CounterArray[slider_number]]).removeClass('current');
				CounterArray[slider_number] = (CounterArray[slider_number] + SliderArray[slider_number]['Slides'].length - 1) % SliderArray[slider_number]['Slides'].length;
				jQuery(SliderArray[slider_number]['Slides'][CounterArray[slider_number]]).addClass('current');
			}
			
			jQuery('div[class^="next"]').click(function() {
				var cls = jQuery(this).attr('class');
				nextNav(cls.substr(cls.length - 1));
			});	
			
			jQuery('div[class^="prev"]').click(function() {
				var cls = jQuery(this).attr('class');
				prevNav(cls.substr(cls.length - 1));
			});	
		}

		imageRotator();
		
		// Detects height of slides and applies the greatest height to each slide and it's containing img.
		var homogenizeHeight = function(parent_element, group_selector) {
			var maxHeight = -1;
			
			jQuery(parent_element).each(function() {
				jQuery(this).find(group_selector).each(function() {
					maxHeight = maxHeight > jQuery(this).height() ? maxHeight : jQuery(this).height();
				});
				
				jQuery(this).find(group_selector).each(function() {
					jQuery(this).height(maxHeight);
					jQuery('img', this).css('max-height', maxHeight);
				});
			});
		};
		
		homogenizeHeight('div[class^="slider"]', '.slide');

	});
})(jQuery, window, document);
