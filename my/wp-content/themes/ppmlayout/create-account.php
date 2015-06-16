<?php
/*
Template Name: Create Account
*/


	$username	= $_GET['username'];
	$password	= $_GET['password'];
	$email		= $_GET['email'];


	//Check if username already exists.
	if( username_exists($username) ) {
		
		echo 'handleRegisterAttempt(
		"failed", 
		"Username already exists"
		)';

	}
	else if( email_exists($email) ) {
		
		echo 'handleRegisterAttempt(
		"failed", 
		"Email already exists"
		)';


	}
	else {
	
		wp_create_user($username, $password, $email);
	
		//Build credentials array.	
		$arr_credentials	= array(
			'user_login'	=> $username,
			'user_password'	=> $password,
			'remember'		=> TRUE
		);
		
		$user = wp_signon( $arr_credentials, false );
			
		echo 'handleRegisterAttempt(
		"success"
		)';


		
	}


?>