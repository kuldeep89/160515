<?php
// Check if this is a saltsha domain
if(preg_match('/^(?:.+\.)?saltsha\.com$/', $_SERVER['HTTP_ORIGIN']) !== FALSE) {
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
}

// Require this file so we can access $wpdb
require('../../../wp-blog-header.php'); 

//Build credentials array.	
$arr_credentials	= array(
	'user_login'	=> $_REQUEST['log'],
	'user_password'	=> $_REQUEST['pwd'],
	'remember'		=> TRUE
);

if( get_user_by( 'login', $_REQUEST['log']) == true ){
	$userData = get_user_by( 'login', $_REQUEST['log']);
} else {
	$userData = get_user_by( 'email', $_REQUEST['log']);
}

if( userID_in_group($userData->ID, 1) ){
	$user = wp_signon( $arr_credentials, false );
	if ( is_wp_error($user) ) {
		//Log in failed, return failure indicator.
		echo '{"status":"failed","error":"Invalid username or password."}';
	} else {
		// User log in successful
		echo '{"status":"success","user_type":"other"}';
	}
} else {
	//Log in failed, return failure indicator.
	echo '{"status":"failed","error":"You aren\'t a registered user."}';
}
?>