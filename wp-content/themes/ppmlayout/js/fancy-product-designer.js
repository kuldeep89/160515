(function(jQuery) {
	
	if( $('.fpd-container').length ) {
		
		//**
		// Page with Fancy Product Designer.
		//**
		$('.attachment-shop_single').hide();
		$('.product').addClass('fancy-product');
		
		//**
		// Hide schema stuff from changing style of page.
		//**
		$('div [itemtype="http://schema.org/Offer"]').css({display: 'none'});
		
	}
	
})($);