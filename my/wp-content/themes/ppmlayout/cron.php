<?php
if ($_GET['action'] == 'subscription_change') {
	$to = 'bstump@paypromedia.com';
	$subject = 'Subscription Change';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Saltsha <success@saltsha.com>' . "\r\n";
	$new_subscription = (isset($_REQUEST['levelname']) && trim($_REQUEST['levelname']) != '') ? ' ('.$_REQUEST['levelname'].')' : '';
	$message = "User '$_REQUEST[username]' has changed their subscription to '$_REQUEST[subscriptionname]$new_subscription'.";
	mail($to, $subject, $message, $headers);
}
?>