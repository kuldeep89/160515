<?php
/*
Author: Ole Fredrik Lie
URL: http://olefredrik.com
*/


// Various clean up functions
require_once('library/cleanup.php'); 

// Required for Foundation to work properly
require_once('library/foundation.php');

// Register all navigation menus
require_once('library/navigation.php');

// Add menu walker
require_once('library/menu-walker.php');

// Create widget areas in sidebar and footer
require_once('library/widget-areas.php');

// Return entry meta information for posts
require_once('library/entry-meta.php');

// Enqueue scripts
require_once('library/enqueue-scripts.php');

// Add theme support
require_once('library/theme-support.php');

// White label Wordpress
require_once('library/rebranding.php');


function get_excerpt(){
	$excerpt = get_the_content();
	$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
	$excerpt = strip_shortcodes($excerpt);
	$excerpt = strip_tags($excerpt);
	$excerpt = substr($excerpt, 0, 350);
	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
	$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
	$excerpt = $excerpt.'... <a href="'.get_permalink().'">Read more.</a>';
	
	return $excerpt;
}

function formSubmit(){
	
	require_once("MailChimp-api.php");
	
	$email = $_POST['email'];

	$MailChimp	= new \Drewm\MailChimp('bcc0cef13b11b2b85eb690de7a0e7abe-us8');
	$result		= $MailChimp->call('lists/subscribe', 
					array(
				        'id'                => '0ebef78817',
				        'email'             => array('email'=>$email),
				        //'merge_vars'        => array(),
				        'double_optin'      => false,
				        'update_existing'   => true,
				        'replace_interests' => false,
				        'send_welcome'      => false,
				    ));		
	
	if( $result[status]=="error" ){
		echo "<span class='fail'>".$result[error]."</span>";
	} else {
		echo "<span class='success'>Sign up successful!</span>";
	}
	
	die();
}
add_action('wp_ajax_formSubmit', 'formSubmit');
add_action('wp_ajax_nopriv_formSubmit', 'formSubmit');


function contactFormSubmit(){
	
	$email	= $_POST['contact_email'];
	$name	= $_POST['contact_name'];
	$phone	= $_POST['contact_phone'];
	
	
	$subject = 'Request for information from Saltsha.com';
	
	$headers	= array();
	$headers[]	= "MIME-Version: 1.0";
	$headers[]	= "X-Mailgun-Native-Send: true";
	$headers[]	= "Content-type: text/html; charset=iso-8859-1";
	$headers[]	= "From: Saltsha <success@saltsha.com>";
	// $headers[]	= "Subject: {$subject}";
	$headers[]	= "X-Mailer: PHP/".phpversion();
	
	$message = '
		<h4>Request for information from Saltsha.com</h4>
		<p>
		Name: '.$name.'<br />
		Email: '.$email.'<br />
		Phone: '.$phone.'
		</p>
	';
	
	$sendMail = wp_mail('kpatrick@payprotec.com', $subject, $message, $headers);
	if($sendMail){
		//echo $email." ".$name." ".$phone;
		echo "We've received your information. We'll get in touch with you soon.";
	} else {
		echo "Something went wrong. Please try again later.";
	}
	
	
	die();	
}
add_action('wp_ajax_contactFormSubmit', 'contactFormSubmit');
add_action('wp_ajax_nopriv_contactFormSubmit', 'contactFormSubmit');


function upgradeFormSubmit(){
	
	$email	= $_POST['upgrade_email'];
	$name	= $_POST['upgrade_name'];
	$phone	= $_POST['upgrade_phone'];
	$mid	= $_POST['upgrade_mid'];
	
	
	$subject = 'Request for account upgrade from Saltsha.com';
	
	$headers	= "MIME-Version: 1.0\r\n";
	$headers	.= "X-Mailgun-Native-Send: true\r\n";
	$headers	.= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers	.= "From: Saltsha <success@saltsha.com>\r\n";
	// $headers[]	= "Subject: {$subject}";
	$headers	.= "X-Mailer: PHP/".phpversion();
	
	$message = '
		<h4>Request for account upgrade from Saltsha.com</h4>
		<p>
		MID: '.$mid.'<br />
		Name: '.$name.'<br />
		Email: '.$email.'<br />
		Phone: '.$phone.'
		</p>
	';
	
	$sendMail = mail('kpatrick@payprotec.com', $subject, $message, $headers);
	//$sendMail = mail('curtis.wolfenberger@gmail.com', $subject, $message, $headers);
	if($sendMail){
		//echo $email." ".$name." ".$phone;
		echo "We've received your information. We'll get in touch with you soon.";
	} else {
		echo "Something went wrong. Please try again later.";
	}
	
	
	die();	
}
add_action('wp_ajax_upgradeFormSubmit', 'upgradeFormSubmit');
add_action('wp_ajax_nopriv_upgradeFormSubmit', 'upgradeFormSubmit');
?>