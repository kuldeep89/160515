<?php
	include('wp-blog-header.php');

	// Pre-select whether ther person has chosen monthly or yearly
	global $woocommerce;
	$args = array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => '-1' );
	$products = new WP_Query( $args );
	foreach ($products->posts as $cur_product) {
		// Get monthly product
		if (!isset($monthly_product)) {
			if (stripos($cur_product->post_title, 'monthly') !== false) {
				$monthly_product = array('id' => $cur_product->ID, 'name' => $cur_product->post_title);
			}
		}

		// Get yearly product
		if (!isset($yearly_product)) {
			if (stripos($cur_product->post_title, 'yearly') !== false || stripos($cur_product->post_title, 'annual') !== false) {
				$yearly_product = array('id' => $cur_product->ID, 'name' => $cur_product->post_title);
			}
		}

		// If both monthly and yearly product are set, exit for loop
		if (isset($monthly_product) && isset($yearly_product)) {
			break;
		}
	}

	// EXAMPLE URL: local.my.dev.saltsha.com/shop/checkout/?billing=monthly
	// If setting billing from $_GET var, make the change
	if (isset($_GET['billing']) && !is_null($_GET['billing']) && trim($_GET['billing']) != '') {
		if ($_GET['billing'] == 'monthly') {
			change_subscription($monthly_product['id']);
		} else {
			change_subscription($yearly_product['id']);
		}
	} else {
		change_subscription($monthly_product['id']);
	}

	// Now that we have the right stuff, send the user to the checkout page.
	header("Location: http://".$_SERVER['HTTP_HOST']."/shop/checkout/");
	exit;
?>