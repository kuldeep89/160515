<?php
/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', "<img src='https://my.saltsha.com/wp-content/themes/ppmlayout/assets/img/logo-big.png' alt='Saltsha Logo' />" ); ?>

<h3><?php echo $email_heading; ?></h3>

<p>Saltsha is a service of <a href="http://payprotec.com/" target="_BLANK">PayProTec</a>, built to help you succeed. With Saltsha, you gain access to tools and resources that we hope will change your business forever. </p>

<ul>
	<li>Learn sales insights through your daily transactional data reports.</li>
	<li>Grow your business with tips you receive through compelling Saltsha Academy resources.</li>
	<li>Stay current with business news and trends through our daily updates and weekly newsletter.</li>
</ul>

<p>We are continuously building more features that are helpful to you and your business. As you familiarize yourself with Saltsha, we value your feedback and would love to answer your questions.</p>

<p>Your account has been set up.<p>
<p>Username: Your Merchant ID or Email Address.</p>
<p><?php printf(__("Password: %s", 'woocommerce'), esc_html( $user_pass ) ); ?></p>

<p>We hope you love Saltsha!</p>
<p>Feel free to contact us anytime at <a href="mailto:support@saltsha.com">support@saltsha.com</a>.</p>

<p>You can access your account here: <a href="http://bit.ly/WiFxZQ" target="_BLANK">https://my.saltsha.com/account/</a></p>

<?php
// Google Analytics tracking code with randomly generated 30-character alphanumeric User ID
$randcharset='abcdefghijklmnopqrstuvwxyz0123456789';
$rando=substr($randcharset,rand(0,strlen($randcharset)-1),1);
for ($i=0; $i<30; $i++) $rando.=substr($randcharset,rand(0,strlen($randcharset)-1),1);

echo '<img src="http://www.google-analytics.com/collect?v=1&tid=UA-52596840-2&cid='.$rando.'&t=event&ec=email&ea=open&cs=welcome&cm=email" />';
?>

<?php do_action( 'woocommerce_email_footer' ); ?>