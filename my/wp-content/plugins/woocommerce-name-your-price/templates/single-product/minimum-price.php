<?php
/**
 * Single Product Minimum Price
 */

global $product;

if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) ) {
	$include = array(
				'price' => woocommerce_price( wc_name_your_price::standardize_number( $product->minimum ) ),
				'subscription_length' => false,
				'sign_up_fee'         => false,
				'trial_length'        => false
			);
	$minimum = WC_Subscriptions_Product::get_price_string( $product, $include );
} else {
	$minimum = woocommerce_price( wc_name_your_price::standardize_number( $product->minimum ) );
}

$html = sprintf( _x( '%s: %s', 'In case you need to change the order of Minimum Price: $minimum', 'wc_name_your_price', 'wc_name_your_price' ), stripslashes ( get_option( 'woocommerce_nyp_minimum_text', __('Minimum Price', 'wc_name_your_price' ) ) ), $minimum );

?>

<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">

	<p itemprop="price" class="minimum-price"><?php echo $html; ?></p>

	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>
