<?php
/**
 * Plugin Name: Custom WP admin email templates.
 * Author: Bobbie Stump / Curtis Wolfenberger
 * Description: Sends custom wp admin emails.
 * Version: 0.0.1
*/

/** 
 * Redefine new user notification function
 */
if ( !function_exists('wp_new_user_notification') ) {

	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {

		$user = new WP_User( $user_id );
		
		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );
		
		$subject = 'Welcome to Saltsha!';
		
		$headers	= array();
		$headers[]	= "MIME-Version: 1.0";
		$headers[]	= "X-Mailgun-Native-Send: true";
		$headers[]	= "Content-type: text/html; charset=iso-8859-1";
		$headers[]	= "From: Kerri Patrick - Saltsha <kpatrick@saltsha.com>";
		// $headers[]	= "Subject: {$subject}";
		$headers[]	= "X-Mailer: PHP/".phpversion();
		
		$message  = sprintf( __('New user registration on %s:'), get_option('blogname') ) . "\r\n\r\n";
		$message .= sprintf( __('Username: %s'), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __('E-mail: %s'), $user_email ) . "\r\n\r\n";

		@wp_mail( get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname') ), $message);

		// Return if no password set
		if ( empty( $plaintext_pass ) || trim($plaintext_pass) == '' ){
			$pass = '';
		} else {
			$pass = $plaintext_pass;
		}
			

		// Assign template based on merchant ID (if it's set or not)
		$merchant_info = get_the_author_meta('ppttd_merchant_info', $user_id);
		$company_select = get_the_author_meta( 'company_select', $user_id );
		
		if( isset($company_select) && trim($company_select) == 'Pilothouse' ) {
			$company_select_name = 'Pilothouse';
			$company_select_link = 'http://pilothousepayments.com/';
			$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/pilothouse.png';
		} else {
			$company_select_name = 'PayProTec';
			$company_select_link = 'http://payprotec.com';
			$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt.png';
		}
		
		$MID = array_shift(array_keys($merchant_info['ppttd_merchant_id']));
		$MID = ltrim($MID, '0');
		
		$is_merchant = (!is_null($merchant_info['ppttd_merchant_id']) && isset($merchant_info['ppttd_merchant_id']) && $MID!='') ? true : false;
		if ($is_merchant) {
			$message = file_get_contents(__DIR__.'/templates/customer-new-account-merchant.html');
		} else {
			$message = file_get_contents(__DIR__.'/templates/customer-new-account.html');
		}
		

		// Replace custom tags with content
		$message = str_replace(array('[THE_USER_PASS]', '[MID]', '[company_select_logo]', '[company_select_link]', '[company_select_name]'), array($pass, $MID, $company_select_logo, $company_select_link, $company_select_name), $message);
		
		// Send email
		wp_mail($user_email, $subject, $message, $headers);

	}
}




?>