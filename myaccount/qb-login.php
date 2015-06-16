<?php
/**
 * 4june2015: Chetu: QB login page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$customer_id = get_current_user_id();

$page_title = apply_filters( 'woocommerce_my_account_my_address_title', __( 'QuickBooks Login', 'woocommerce' ) );
// require config file for including QB library 
require_once get_home_path().'wp-includes/qb_api/app_ipp_v3/config.php';

?>
<style>
.qb_setting_button {
	background		: #FFFFFF;
	color			: #0D0101;
	float			: right;
	margin-left		: -244px;
	padding			: 0 3em;
	height			: 50px;
	line-height		: 51px;
	font-size		: 13pt;
	font-weight		: 700;
	text-align		: center;
	text-transform	: uppercase;
}
</style>
<link href="<?php echo get_template_directory_uri() ?>/css/dashboard.css" rel="stylesheet" type="text/css" />
<div class="form_portlet">
	<div class="portlet_title">
		<h3><?php echo $page_title; ?></h3>
	</div>
	<div class="portlet_body acc">
		<div class="row-fluid">
			<div>
				<p>
					QuickBooks connection status: 

					<?php if ($quickbooks_is_connected): ?>
						<div style="border: 2px solid green; text-align: center; padding: 8px; color: green;">
							CONNECTED!
							
							<br>
							<i>
								Realm: <?php print($realm); ?><br>
								Oauth Key: <?php print($oauth_token); ?><br/>
								Company: <?php print($quickbooks_CompanyInfo->getCompanyName()); ?><br>
								Email: <?php print($quickbooks_CompanyInfo->getEmail()->getAddress()); ?><br>
								Country: <?php print($quickbooks_CompanyInfo->getCountry()); ?>
							</i><br/>
							<a  href="<?php echo includes_url('qb_api/app_ipp_v3/'); ?>disconnect.php">Disconnect from QuickBooks</a> 
						</div>
							

					<?php else: ?>
						<div style="border: 2px solid black; text-align: center; padding: 8px; color: black;">
							<b>NOT</b> CONNECTED! &nbsp;&nbsp; 
							<br>
							<ipp:connectToIntuit></ipp:connectToIntuit> 
							<br>
							<br>
							You must authenticate to QuickBooks <b>once</b> before you can exchange data with it. <br>
							<br>
							<strong>You only have to do this once!</strong> <br><br>
							
							After you've authenticated once, you never have to go 
							through this connection process again. <br>
							Click the button above to 
							authenticate and connect.
						</div>	
					<?php endif; ?>		

				</p>
			</div>
	</div>
</div>
