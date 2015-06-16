(function($,W,D) {
	$(D).ready(function($) {	
		
		$("body").on("click", "#learn_more", function(e){
			var ta_top = $("#transactional_analysis").offset().top;
			$("html, body").animate({ scrollTop: ta_top }, 600);
			e.preventDefault();
		});
		
		$("body").on("submit", "#signup_form", function(){
			
			$('#signUpButton').hide();
			$('#response').hide();
			
			var emailAdd = $("#signup_email").val();
			
			$.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: 'formSubmit',
					email: emailAdd
				}
			}).done(function( data ) {
				//alert(data);
				
				$('#signup_email').val('');
				$('#response').html(data);
				$('#response').show();
				$('#signUpButton').show();
			}).fail(function() {
				//alert('error');
				$('#response').html('<span class="fail">Error. Please try again later.</span>');
				$('#response').show();
				$('#signUpButton').show();
			});
			
			return false;
		});
		
		$("body").on("submit", "#contact_form", function(){
			
			$('#submit_contact_form').hide();
			$('#contact_response').hide();
			
			var c_email	= $("#contact_email").val();
			var c_name	= $("#contact_name").val();
			var c_phone	= $("#contact_phone").val();
			
			$.ajax({
				type: "POST",
				url: "wp-admin/admin-ajax.php",
				data: {
					action: 'contactFormSubmit',
					contact_email: c_email,
					contact_name: c_name,
					contact_phone: c_phone
				}
			}).done(function( data ) {
				//alert(data);
				
				$('#contact_form').hide();
				$('#contact_response').html(data);
				$('#contact_response').show();
				//$('#signUpButton').show();
			}).fail(function() {
				//alert('error');
				$('#contact_response').html('<span class="fail">Error. Please try again later.</span>');
				$('#contact_response').show();
				$('#submit_contact_form').show();
			});
			
			return false;
		});
		
		$("body").on("submit", "#upgradeModal", function(){
			
			$('#submit_upgrade_form').hide();
			$('#upgrade_response').hide();
			
			var u_email	= $("#upgrade_email").val();
			var u_name	= $("#upgrade_name").val();
			var u_phone	= $("#upgrade_phone").val();
			var u_mid	= $("#upgrade_mid").val();
			
			$.ajax({
				type: "POST",
				url: "wp-admin/admin-ajax.php",
				data: {
					action: 'upgradeFormSubmit',
					upgrade_email: u_email,
					upgrade_name: u_name,
					upgrade_mid: u_mid,
					upgrade_phone: u_phone
				}
			}).done(function( data ) {
				//alert(data);
				
				$('#upgrade_form').hide();
				$('#upgrade_response').html(data);
				$('#upgrade_response').show();
				//$('#signUpButton').show();
			}).fail(function() {
				//alert('error');
				$('#upgrade_response').html('<span class="fail">Error. Please try again later.</span>');
				$('#upgrade_response').show();
				$('#submit_upgrade_form').show();
			});
			
			return false;
		});
		
		
		$('#infoModal').easyModal({
			top: 200,
			overlay: 0.2
		});
		$('#upgradeModal').easyModal({
			top: 200,
			overlay: 0.2
		});
		$("#infoModalLink").click(function(e){
			e.preventDefault();
			$("#infoModal").trigger('openModal');
		});
		$("#upgradeModalLink").click(function(e){
			e.preventDefault();
			$("#upgradeModal").trigger('openModal');
		});
		$("body").on('click', '.close-reveal-modal', function(e){
			e.preventDefault();
			$("#infoModal").trigger('closeModal');
			$("#upgradeModal").trigger('closeModal');
		});
	});
	$(W).load(function($) {

	});	
})(jQuery, window, document);

		