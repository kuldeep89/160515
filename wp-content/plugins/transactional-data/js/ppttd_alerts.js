jQuery(document).ready(function() {    
 	
 	jQuery('#system_alert_form').submit(function(e) {
 	
 		var alert_text = jQuery('#alert_text');
 		var alert_type = jQuery('#alert_type');
 		
 		//alert('stopped');
 		if( !alert_text.val() ){
	 		alert('You must enter a message.');
	 		return false;
 		} else if( !alert_type.val() ){
	 		alert('You must select an Alert Type');
	 		return false;
 		} else {
			var c = confirm("Please be patient, this can take a minute. The message will appear below after it's been sent to everyone.");
			return c;
 		}
 		
 	});
 	
});