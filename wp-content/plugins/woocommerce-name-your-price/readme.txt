=== WooCommerce Name Your Price ===

The WooCommerce Name Your Price extension lets you be flexible in what price you are willing to accept for selected products. You can use this extension to accept user-determined donations, gather pricing data or to take a new approach to selling products!  You can *suggest* a price to your customers and optionally enforce a minimum acceptable price, but otherwise this extension allows the customer to enter the price he's willing to pay.

[screenshot1.png]

== Installation ==

To install Name Your Price:

1. Download the extension from your dashboard

2. Unzip and upload the 'woocommerce-name-your-price' folder to your site's '/wp-content/plugins/' directory

3. Activate the 'WooCommerce Name Your Price' extension through the 'Plugins' menu in WordPress

== How to Use ==

[screenshot2.png]

To enable flexible, user-determined pricing on any simple product:

1. Edit a product and look for the 'Name Your Price' checkbox in the Product Data metabox. The product must be set as a 'Simple Product' in the dropdown in order to see the checkbox.

2. Tick the checkbox to allow users to set their own price for this product.  The suggested and minimum price fields will not be visible until this is checked.  Note that this might not function properly if you have javascript disabled. 

3. To pre-fill the customer's price with a suggested price, enter it in the Suggested Price input

4. To enforce a minimum price (ie. to not sell your product for less than a specified amount), enter this minimum acceptable price in the Minimum Price input.

5. Save the product.  When viewing the product on the front-end, the suggested and minimum prices will be displayed near the product description and a text input will appear above the Add to Cart Button where the customer can enter what she is willing to pay.  

== Settings ==

You can change any of the front-end "strings", meaning the phrases for "Name Your Price", "Suggested Price", "Minimum Price" and the button text "Set Price". Go to WooCommerce > Settings and click on the Name Your Price Tab

[screenshot3.png]

== FAQ ==

= How do I change the markup? =

Similar to WooCommerce, the Name Your Price extension uses a small template part to generate the markup for the suggested price, minimum price, and price input. For example, you can use your own minimum price template by placing a minimum-product.php inside the /woocommerce/single-product folder of your theme. You can do the same thing for suggested-product.php and price-input.php. 

= How can I move the markup? = 

The suggested price, minimum price and price text input are all attached to different WooCommerce hooks. The suggested and minimum price templates are attached to the WooCommerce 'woocommerce_single_product_summary' action hook, while the text input is attached to the 'woocommerce_before_add_to_cart_button' hook. Following typical WordPress behavior for hooks, to change the location of any of these template you must remove them from their default hook and add them to a new hook.  For example, to relocate the minimum price place the following in your theme's functions.php and be sure to adjust 'the_hook_you_want_to_add_to' with a real hook name.

```
function nyp_move_minimum_price(){
	global $wc_name_your_price, $product;

	remove_action( 'woocommerce_single_product_summary', array($wc_name_your_price,'display_minimum_price'), 15 );
	
	// You should check that the product has a minimum before calling a template to display it
	if( is_product() && $product->minimum )
		add_action( 'the_hook_you_want_to_add_to', array( $wc_name_your_price, 'display_minimum_price' ) );
}
add_action( 'woocommerce_before_main_content' , 'nyp_move_minimum_price', 20 );

```
or for the price's text input:

```
function nyp_move_price_input(){
	global $wc_name_your_price;
	remove_action( 'woocommerce_before_add_to_cart_button', array( $wc_name_your_price, 'display_price_input') );
	add_action( 'the_hook_you_want_to_add_to', array( $wc_name_your_price, 'display_price_input' ) );
}
add_action( 'woocommerce_before_main_content' , 'nyp_move_price_input' );
```

To *not* display a suggested price, you can simply leave the suggested field blank when setting up the product's meta information.  (See the Usage instructions). Similarly, to *not* enforce a minimum price, leave that field blank when setting up the product information. 

= How do I remove the stylesheet? = 

The Name Your Price stylesheet is pretty minimal, only offering a tiny bit of styling for the minimum price and for the text input.  You can easily handle this in your own stylesheet if you so desire.  To remove the stylesheet add this to your theme's functions.php.

```
function nyp_remove_stylesheet(){
	global $wc_name_your_price;
	remove_action( 'wp_enqueue_scripts', array( $wc_name_your_price, 'nyp_style') );
}
add_action( 'init' , 'nyp_remove_stylesheet' );
```

