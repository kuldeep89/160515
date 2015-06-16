<?php 
	
	//**
	// Terminal Supplies Request
	//**
	add_action('wp_ajax_nopriv_terminal_supplies', 'terminal_supplies');
	add_action('wp_ajax_terminal_supplies', 'terminal_supplies');
	
	function terminal_supplies() {
		
		$headers[] = "From: Saltsha <support@saltsha.com>\r\n";
		$headers[] = "Reply-To: Saltsha <success@saltsha.com>\r\n";
		$headers[] = "MIME-Version: 1.0\r\n";
		$headers[] = "X-Mailgun-Native-Send: true\r\n";
			
		//send e-mail.
		$message = '';

		foreach( $_POST['fields'] as $name => $value ) {
			$message .= ucwords(str_replace('_', ' ', $name)).': '.$value."\r\n";
		}
		
		//wp_mail('cwolfenberger@payprotec.com', 'Terminal Supply Form', $message, $headers);
		//wp_mail('kpatrick@payprotec.com', 'Terminal Supply Form', $message, $headers);
		$recipients = array(
		    'support@saltsha.com'
		);
		
		
		if( wp_mail($recipients, 'Terminal Supply Form', $message, $headers) ) {
			// the message was sent...
			echo '{"status": "good"}';
		} else {
			echo '{"status": "bad"}';
		}
		
		die();

	}

?>