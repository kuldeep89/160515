<?php 	/* Template Name: Terminal Request */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

 get_header();

?>


<?php 

	$obj_current_user	= wp_get_current_user();
	$customer_id		= $obj_current_user->data->ID;
	$merchant_info 		= get_the_author_meta('ppttd_merchant_info', $customer_id);
 
    global $woocommerce;

	$get_addresses    = array(
		'billing'	=> __( 'Billing Address', 'woocommerce' ),
		'shipping'	=> __( 'Shipping Address', 'woocommerce' )
	);
	
	
	$arr_addresses	= array();
	
	foreach( $get_addresses as $name => $title ) {

		$arr_addresses[$title] = apply_filters( 'woocommerce_my_account_my_address_formatted_address', array(
			'first_name' 	=> get_user_meta( $customer_id, $name . '_first_name', true ),
			'last_name'		=> get_user_meta( $customer_id, $name . '_last_name', true ),
			'email'			=> get_user_meta( $customer_id, $name . '_email', true ),
			'company'		=> get_user_meta( $customer_id, $name . '_company', true ),
			'address_1'		=> get_user_meta( $customer_id, $name . '_address_1', true ),
			'address_2'		=> get_user_meta( $customer_id, $name . '_address_2', true ),
			'city'			=> get_user_meta( $customer_id, $name . '_city', true ),
			'state'			=> get_user_meta( $customer_id, $name . '_state', true ),
			'postcode'		=> get_user_meta( $customer_id, $name . '_postcode', true ),
			'country'		=> get_user_meta( $customer_id, $name . '_country', true )
		), $customer_id, $name );

	}
		
	//Which address to use?
	$arr_address 	= array();
	
	if( !empty($arr_address['Shipping Address']['first_name']) ) {
		$arr_address = $arr_addresses['Shipping Address'];
	}
	else {
		$arr_address = $arr_addresses['Billing Address'];
	}

    // Get merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);
    $merchant_id = $merchant_info['ppttd_merchant_id'];

    // Check if merchant data is array or not
    if (gettype($merchant_id) != 'array') {
        $merchant_id = explode(',', $merchant_id);
    }
?>


<div class="page-breadcrumb-heading">
	<div class="left">
		<h3 class="page-title">
			<?php the_title(); ?>
			<small>
				<?php if( function_exists('the_field') ) { the_field('page_subtitle'); } ?>
			</small>
		</h3>
		<ul class="breadcrumb">
			<li>
				<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<span id="breadcrumbs">','</span>'); } ?>
			</li>	
		</ul>
	</div>
</div>

<section class="container-fluid">
	<form id="terminal_supplies_form" method="post" action="#">
		
		<div id="error">
			
		</div>
		<div class="row-fluid">	
			<div class="span12">
				<div class="form_portlet ">
					<div class="portlet_title">
						<h3>Merchant Information</h3>
					</div>
					<div class="portlet_body">
						<div class="row-fluid">
							<div class="span6">
								<label>Merchant ID</label>
								<select id="merchant_id" name="merchant_id" class="form-control" required>
								    <?php
								        // Loop through merchant IDs
								        $i = 0;
								        foreach ($merchant_id as $cur_merchant_id => $cur_merchant_value) {
									        $i++;
									        $selected = ($i==1 ? 'selected' : '');
								            $cur_merchant_name = (trim($cur_merchant_value) == "" || $cur_merchant_id == $cur_merchant_value) ? $cur_merchant_id : $cur_merchant_value.' ('.$cur_merchant_id.')';
								            $cur_merchant_name = (strlen($cur_merchant_id) >= 8) ? $cur_merchant_name : $cur_merchant_value;
								            $cur_merchant_id = (strlen($cur_merchant_id) >= 8) ? $cur_merchant_id : $cur_merchant_value;
				                            echo '<option value="'.$cur_merchant_id.'" '.$selected.'>'.$cur_merchant_name.'</option>';
				                        }
								    ?>
								</select>
							</div>
							<div class="span6">
								<label>Contact Name</label>
								<input type="text" class="form-control" name="name" placeholder="Merchant Name" value="<?php looks_good($obj_current_user->data->display_name); ?>" required>
								<input type="hidden" name="email" value="<?php looks_good($arr_address['email']); ?>" disabled>
							</div>
						</div>
						<div class="row-fluid">			
							<div class="span6">
								<label>Business Name</label>
								<input type="text" name="business_name" class="form-control">
							</div>			
							<div class="span6">
								<label>Phone Number</label>
								<input type="text" name="phone_number" class="form-control">
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
		<div class="row-fluid">	
			<div class="span12">
				<div class="form_portlet ">
					<div class="portlet_title">
						<h3>Shipping Information</h3>
					</div>
					<div class="portlet_body">
		
						<div class="row-fluid">			
							<div class="span6">
								<label>Street 1</label>
								<input type="text" class="form-control" name="street_1" value="<?php looks_good($arr_address['address_1']); ?>" required>
							</div>
							<div class="span6">
								<label>Street 2</label>
								<input type="text" class="form-control" name="street_2" value="<?php looks_good($arr_address['address_2']); ?>" >
							</div>
						</div>
						
						<div class="row-fluid">			
							<div class="span6">
								<label>City</label>
								<input type="text" class="form-control" name="city" value="<?php looks_good($arr_address['city']); ?>" required>
							</div>
							<div class="span6">
								<label>State</label>
								<input type="text" class="form-control" name="state" value="<?php looks_good($arr_address['state']); ?>" required>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								<label>Zip</label>
								<input type="text" class="form-control" name="zip" value="<?php looks_good($arr_address['postcode']); ?>" required>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
		<div class="row-fluid">	
			<div class="span12">
				<div class="form_portlet ">
					<div class="portlet_title">
						<h3>Terminal Supplies</h3>
					</div>
					<div class="portlet_body">
						<div class="row-fluid">
							<div class="span6">
								<label>Select Item</label> 
								<select id="select_item" required>
									<option disabled selected>Make a Selection</option>
									<option value="RT">Replace Terminal</option>
									<option value="PR">Paper Rolls</option>
								</select>
							</div>
							<div class="span6">
								<div id="replacement_terminal_selection">
									<label>Replacement Terminal</label> 
									<?php echo do_shortcode('[terminal_select]'); ?>
									<!-- <input type="text" class="form-control" name="replacement_terminal" id="terminal_info" placeholder="Terminal Info" /> -->
								</div>
								<div id="paper_rolls_selection">
									<label>Paper Rolls</label>
									<?php echo do_shortcode('[paper_select]'); ?>
									<!-- <input type="text" class="form-control small" name="paper_rolls" id="paper_rolls" placeholder="Quantity" /> -->
								</div>
							</div>
						</div>
						<div class="row-fluid" id="type_of_terminal">
							<div class="span12">
								<label>Type of Terminal</label>
								<input type="text" class="form-control small" name="terminal_type" id="terminal_type" />
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<label>Additional Comments</label>
								<textarea class="form-control" name="comments" id="comments"></textarea>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<small style="color:#99A1B4; line-height:.5em;">The quantity of free paper allowed is dependant on the quantity of transactions your business has done. Please enter the number of desired rolls, yet understand that the quantity of rolls shipped may differ based on your quantity of transactions. For any questions, please email <a href="mailto:support@saltsha.com">support@saltsha.com</a>, call <a href="tel:5742690792">574.269.0792</a>, or use the Live Chat.</small>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
				
		<div class="row-fluid">
			<div class="span12">
				<button type="submit" id="submit_form" class="btn blue right">Submit Request</button>
				<span id="sending" class="right"></span>
			</div>
		</div>
	</form>
	
</section>

<section id="terminal_thank_you" class="container-fluid">

	<div class="fluid-row">
		<h2>Thank You!</h2>
		<p>
			Your request has been sent and we will be getting in contact with you shortly.
		</p>
	</div>
	
</section>

<?php 
	
	get_footer(); 
	
	//**
	// Helper functions
	//**
	function looks_good( $field ) {
		
		echo (isset($field) && !empty($field))? $field:'';
		
	}
	
?>