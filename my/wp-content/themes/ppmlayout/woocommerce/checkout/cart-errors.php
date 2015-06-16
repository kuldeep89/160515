<?php
/**
 * Cart errors page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php $woocommerce->show_messages(); ?>

<?php $quote_or_cart = ($woocommerce->cart->has_quoted_item == false) ? 'cart' : 'quote' ?>

<p><?php _e( 'There are some issues with the items in your '.$quote_or_cart.' (shown above). Please go back to the '.$quote_or_cart.' page and resolve these issues before checking out.', 'woocommerce' ) ?></p>

<?php do_action('woocommerce_cart_has_errors'); ?>

<p><a class="button" href="<?php echo get_permalink(woocommerce_get_page_id('cart')); ?>"><?php echo ($woocommerce->cart->has_quoted_item == false) ? '&larr; Return To Cart' : '&larr; Return To Quote' ?></a></p>