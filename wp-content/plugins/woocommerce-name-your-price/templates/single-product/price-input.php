<?php
/**
 * Single Product Suggested Price, including microdata for SEO
 */

global $product;

// go through a few options to find the $price we should display in the input (typically will be the suggested price)
$posted = isset( $_POST['nyp'] ) ?  ( wc_name_your_price::standardize_number( $_POST['nyp'] ) ) : '';
$suggested = isset ( $product->suggested ) ? wc_name_your_price::standardize_number( $product->suggested ) : '';
$minimum = isset ( $product->minimum ) ? wc_name_your_price::standardize_number( $product->minimum ) : '';

if ( $posted && $posted >= 0 ) {
	$price = $posted;
} elseif ( $suggested && $suggested > 0 ) {
	$price = $suggested;
} elseif ( $minimum && $minimum > 0 ) {
	$price =  $minimum;
} else {
	$price = '';
}


?>

<div class="nyp">

	<label for="nyp">
		<?php printf( _x( '%s ( %s )', 'In case you need to change the order of Name Your Price ( $currency_symbol )', 'wc_name_your_price' ), stripslashes ( get_option( 'woocommerce_nyp_label_text', __('Name Your Price', 'wc_name_your_price' ) ) ), get_woocommerce_currency_symbol() ); ?>
	</label>

	<?php echo wc_name_your_price::price_input_helper( esc_attr( $price ), array( 'name'=>'nyp' )); ?>

</div>