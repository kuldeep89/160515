<?php
/*
Template Name: Reset Password!
*/
$email = $_GET['email'];

$user = get_user_by_email($email);
$user_ID = $user->ID; 
$name = $user->first_name; 


// Now that the password has been update, set meta value
// to direct user to reset their password once they login.
if( $user_ID !== FALSE ) {
	update_user_meta($user_ID, 'reset_password', '1');
}
//End of setting reset meta value.


if (!$user_ID){
echo 'handleResetAttempt(
		"failed", 
		"That email does not appear to be valid."
		)';
}
else{

	////////////////
	// Reset passwd.
	////////////////
	$length = 8;
	$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	$password = $randomString;
	 
	
	
	$from = 'info@saltsha.com';
	$headers="From: $from\r\nReply-To: $from \r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$email_message	= file_get_contents(dirname(__FILE__).'/inc/email/reset-password.php');
	$email_message	= str_replace(array('[password]'), array($password), $email_message);
	
	//$headers = 'From: Saltsha <'.$from.'>' . "\r\n";
	
	$subject = 'Your New Password';
	$to = $email;
	
	if( wp_mail($to,$subject,$email_message,$headers) ) {
		
		//Reset password after the e-mail was sent.
		//This is done so that we know the e-mail was sent, before resetting
		//their password.
		
		wp_set_password( $password, $user_ID );
		
		echo 'handleResetAttempt(
			"success", 
			"An email was sent with your new information."
			)';
		
	}
	else {
		
		echo 'handleResetAttempt(
			"success",
			"There was an error in the password reset process, your password has not been reset. It may take 5-10 minutes to receive the reset password email."
			)';
		
	}
	
}

/* wp_redirect( home_url() ); */
?>