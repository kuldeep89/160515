<?php
global $woocommerce;

$product_types = array();
foreach($woocommerce->cart->cart_contents as $cur_item) {
	$product_types[] = $cur_item['data']->product_type;
}

// Choose one-time or subscription review
if (in_array('subscription', $product_types)) {
	include('review-order-subscription.php');
} else {
	include('review-order-one-time.php');
}
?>
