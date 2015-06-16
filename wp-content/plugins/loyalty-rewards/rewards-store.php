<?php
/*
Plugin Name: Loyalty Rewards
Plugin URI: http://www.212mediastudios.com
Description: This plugin runs the Saltsha Loyalty Rewards.
Version: 0.0.1
Contributors: bstump
Author URI: http://www.212mediastudios.com/team/bobbie-stump/
License: GPLv2
*/

defined('WP_PLUGIN_URL') or die('Restricted access');


/**
 *  Define constants
 */
define('RSP_PATH', ABSPATH.PLUGINDIR.'/loyalty-rewards/');
define('RSP_URL', WP_PLUGIN_URL.'/loyalty-rewards/');
define('RSP_OPTIONS', "wp_rsp_settings" );
define('RSP_QUERY_VARS', "rsp_category|rsp_search|rsp_page|rsp_per_page|rsp_sort_by|rsp_min_points|rsp_max_points|rsp_cart|rsp_product|rsp_orders");


/**
 *  Require scripts and libraries
 */
require_once("admin/functions.php");
require_once( RSP_PATH . 'lib/loyalty_rewards.class.php' );


/**
 * Register session if not already registered
 */
function rsp_register_session(){
    @session_destroy();

    if( !session_id() )
        session_start();

    // Create rewards store object, if not created already
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));

        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }
}
add_action('init','rsp_register_session');


/**
 * Define globals
 */
global $wpdb;


/**
 *  Run when plugin is installed
 */
function rsp_install() {
	// Add plugin options
	add_option( RSP_OPTIONS, json_encode(array('token' => 'Token')), '', 'yes' );
}
register_activation_hook( __FILE__, 'rsp_install' );


/**
 *  Run when plugin is uninstalled
 */
function rsp_uninstall() {
	// Remove plugin options
	delete_option( RSP_OPTIONS );
}
register_uninstall_hook( __FILE__, 'rsp_uninstall' );


/**
 *  Register and enqueue admin JavaScript
 */
/*
function rsp_admin_js() {
	wp_enqueue_script('rsp-admin-js', RSP_URL.'assets/js/admin.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'rsp_admin_js');
*/


/**
 *  Register and enqueue frontend JavaScript
 */
function rsp_frontend_js() {
	wp_enqueue_script('rsp-frontend-js', RSP_URL.'assets/js/frontend.js', array('jquery'));
	wp_enqueue_style('rsp-frontend-css', RSP_URL.'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'rsp_frontend_js');


/**
 * Shortcode for displaying store
 */
function rsp_rewards_store(){
    global $wp_query;

    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_redirect(home_url());
        exit;
    }

    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));
    
        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Set merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Get current user info
    $get_current_user = wp_get_current_user();
    $current_user_id = $get_current_user->ID;
    $user_reward_points = get_user_reward_points();

    // Set points in cart text
    $points_in_cart = rsp_points_in_cart();
    $points_in_cart = ($points_in_cart > 0) ? ' ('.$points_in_cart.' In Cart / '.($user_reward_points-$points_in_cart).' Available)' : '';

    // View cart / orders
    echo '<table style="width:100%;"><tr><td><h3 id="points_in_cart" style="float:left;">You have '.number_format($user_reward_points).' Reward Points'.$points_in_cart.'.</h3><a href="'.site_url('/loyalty-rewards/cart/view/').'" class="view-cart-button" style="margin:0px 0px 10px 0px;padding:15px 25px;cursor:pointer;">View Cart</a>';
    echo '<a href="'.site_url('/loyalty-rewards/orders/list/').'" class="view-cart-button" style="margin:0px 20px;padding:15px 25px;cursor:pointer;">View Orders</a></td></tr></table>';

    // Echo notifications div and ajax URL
    echo '<div id="rsp_notifications"></div>';
    echo '<script>var ajaxurl = "'.admin_url('/admin-ajax.php').'";</script>';

    // Build continue shopping link
    $continue_shopping_link = site_url('/loyalty-rewards');
    $continue_shopping_link = (isset($wp_query->query_vars['rsp_category'])) ? $continue_shopping_link.'/category/'.$wp_query->query_vars['rsp_category'] : $continue_shopping_link;
    $continue_shopping_link = (isset($wp_query->query_vars['rsp_page'])) ? $continue_shopping_link.'/page/'.$wp_query->query_vars['rsp_page'] : $continue_shopping_link;

    // Display cart functions
    if (isset($wp_query->query_vars['rsp_cart'])) {
        // View cart
        if ($wp_query->query_vars['rsp_cart'] == "view") {
            $cart_contents = rsp_retrive_cart_contents();
            $cart_contents = json_decode($cart_contents);
            echo '<div id="cart_contents">'.$cart_contents->cart_contents.'</div>';
        }
        return;
    }

    // Display orders
    if (isset($wp_query->query_vars['rsp_orders'])) {
        if ($wp_query->query_vars['rsp_orders'] === 'list') {
            // Get order list
            $order_list = $_SESSION['rsp_object']->order_list(array('external_user_id' => get_current_user_id()));
    
            // Get cart contents
            echo '<table class="rsp_cart_table" cellspacing="0">
                <thead>
                    <tr>
            			<th class="rsp-order-id">Order ID</th>
            			<th class="rsp-order-date">Order Date</th>
                    </tr>
                </thead>
                <tbody style="background-color:#FAF6EB;">';
    
                // List orders
                if (count($order_list->orders->OrderSummary) > 0) {
                    foreach ($order_list->orders->OrderSummary as $cur_order) {
                        echo '<tr>
                            <td class="rsp-order-id">
                                <a href="'.site_url('/loyalty-rewards/orders/'.$cur_order->order_number.'/').'">'.$cur_order->order_number.'</a>
                            </td>
                            <td class="rsp-order-date">
                                '.date('M d, Y @ H:i', strtotime($cur_order->date_placed)).'
                            </td>
                        </tr>';
                    }
                } else {
                    echo '<tr>
                        <td class="rsp-order-id" colspan="2">
                            <em>No orders found.</em>
                        </td>
                    </tr>';
                }
            echo '</tbody>
            </table>';
        } else {
            // Track order
            $order_list = $_SESSION['rsp_object']->order_track(array('order_number' => $wp_query->query_vars['rsp_orders']));

            echo '<table class="rsp_order_table" cellspacing="0" style="margin-top:20px;">
            <thead class="shipping-info">
                <tr>
        			<th class="product-name product-name-order">Product</th>
        			<th class="product-price">Fulfillment Status</th>
        			<th class="product-price">Shipping / Digital Goods Status</th>
                </tr>
            </thead>
            <tbody style="background-color:#FAF6EB;">';

            // Display cart item(s)
            $total_points = 0;
            if (!isset($order_list->Fault)) {
                if (count($order_list->items->OrderItem) > 0) {
                    foreach ($order_list->items->OrderItem as $cur_order_item) {
                        // Set shipping status
                        $cur_shipping_status = get_fulfillment_status($order_list, $cur_order_item->order_item_id);

                        // Display item status
                        echo '<tr class="cart_table_item">
						<td class="product-name" style="padding:5px;">
							<a href="'.site_url().'/loyalty-rewards/product/'.$cur_order_item->catalog_item_id.'/">'.$cur_order_item->name.'</a>
                        </td>
						<td class="product-status">
							<span class="amount">'.$cur_order_item->order_item_status.'</span>
                        </td>
                        <td class="product-shipping-status">
                            '.$cur_shipping_status.'
                        </td>
					</tr>';
    		        }
                } else {
    		        echo '<tr>
    		            <td colspan="2" class="no-items-in-cart"><em>No items in order.</em></td>
    		        </tr>';
    		        die();
                }
            } else {
		        echo '<tr>
		            <td colspan="2" class="no-items-in-cart"><em>Order not found or order is still being processed.</em></td>
		        </tr>';
		        die();
            }

            echo '
                </tbody>
            </table>
            <table class="rsp_cart_table" cellspacing="0">
                <thead class="shipping-info">
                    <tr>
                        <th colspan="2" class="product-remove">Shipping Information</th>
                    </tr>
                </thead>
                <tbody class="shipping-info" style="background-color:#FAF6EB;">
                    <tr>
                        <td>First Name</td>
                        <td>'.$order_list->first_name.'</td>
                    </tr>
                    <tr>
                        <td>Last Name</td>
                        <td>'.$order_list->last_name.'</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>'.$order_list->address_1.'</td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td>'.$order_list->city.'</td>
                    </tr>
                    <tr>
                        <td>State</td>
                        <td>'.$order_list->state_province.'</td>
                    </tr>
                    <tr>
                        <td>Zip Code</td>
                        <td>'.$order_list->postal_code.'</td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td>'.$order_list->country.'</td>
                    </tr>
                </tbody>
            </table>';
        }
        return;
    }

    // Display individual product
    if (isset($wp_query->query_vars['rsp_product'])) {
        // Send item request
        $item_response = $_SESSION['rsp_object']->view_item(array('catalog_item_id' => $wp_query->query_vars['rsp_product']));

        // Display item
        if (!isset($item_response->Fault)) {
            echo '<div class="row-fluid">';
                echo '<div class="span4" style="text-align:center;">';
                    echo '<img src="'.$item_response->image_300.'" style="height:auto;width:100%;max-width:300px;" /><br/><br/>';
                    echo '<a class="add_to_cart_button button product_type_simple rsp_add_to_cart" rsp-pid="'.$wp_query->query_vars['rsp_product'].'" style="height:auto;width:100%;max-width:200px;">Add To Cart</a>';
                    echo '<br/><a class="add_to_cart_button button product_type_simple" href="'.$continue_shopping_link.'" style="margin-top:10px;height:auto;width:100%;max-width:200px;">Continue Shopping</a>';
                echo '</div>';
                echo '<div class="span8">';
                    echo '<h1>'.$item_response->name.'</h1>';
                    echo '<h4><strong>'.number_format($item_response->points).' Points</strong></h4>';
                    echo $item_response->description;
                echo '</div>';

            echo '</div>';  
        } else {
            echo '<em>Item not found.</em>';
        }
        return;
    }

    // Build search arguments
    $search_args = array();
    
    // Search term
    if (isset($wp_query->query_vars['rsp_search']) && trim($wp_query->query_vars['rsp_search']) !== '') {
        $search_args['search'] = $wp_query->query_vars['rsp_search'];
    }
    
    // Category
    if (isset($wp_query->query_vars['rsp_category']) && trim($wp_query->query_vars['rsp_category']) !== '') {
        $search_args['category_id'] = get_category_id_from_slug($wp_query->query_vars['rsp_category']);
    }

    // Page number
    if (isset($wp_query->query_vars['rsp_page']) && trim($wp_query->query_vars['rsp_page']) !== '') {
        $search_args['page'] = $wp_query->query_vars['rsp_page'];
    } else {
        $search_args['page'] = (isset($wp_query->query_vars['paged'])) ? $wp_query->query_vars['paged'] : 1;
    }

    // Sort by
    if (isset($wp_query->query_vars['rsp_sort_by']) && trim($wp_query->query_vars['rsp_sort_by']) !== '') {
        $search_args['sort'] = urldecode($wp_query->query_vars['rsp_sort_by']);
    }

    // Items per page
    if (isset($wp_query->query_vars['rsp_per_page']) && trim($wp_query->query_vars['rsp_per_page']) !== '') {
        $search_args['per_page'] = urldecode($wp_query->query_vars['rsp_per_page']);
    }

    // Minimum rewards points
    if (isset($wp_query->query_vars['rsp_min_points']) && trim($wp_query->query_vars['rsp_min_points']) !== '') {
        $search_args['min_points'] = urldecode($wp_query->query_vars['rsp_min_points']);
    }

    // Send search request
    $search_response = $_SESSION['rsp_object']->search_catalog($search_args);
    
    // Load view with data
    $_SESSION['rsp_object']->load_view('index', $search_response);
    
    // Pagination
    if (isset($search_response->pagination)) {
        echo rsp_build_pagination($search_response->pagination);
    }
}
add_shortcode('rsp_rewards_store', 'rsp_rewards_store');


/**
 * Add item to cart
 */
function rsp_add_to_cart() {
    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));

        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Set merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Get current user info
    $get_current_user = wp_get_current_user();
    $current_user_id = $get_current_user->ID;
    $user_reward_points = get_user_reward_points();

    // Check if user has points
    if (count($user_reward_points) == 0) {
        echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> You have no Reward Points to spend.'));
        die();
    }

    // Retrieve cart item(s)
    $cart_items = $_SESSION['rsp_object']->cart_view(array('external_user_id' => get_current_user_id()));

    // Get cart points without current item
    $total_points = 0;
    if (!isset($cart_items->Fault)) {
        if (count($cart_items) > 0) {
            foreach ($cart_items as $cur_cart_item) {
		        if ($cur_cart_item->catalog_item_id != $_POST['catalog_item_id']) {
    		        $total_points += $cur_cart_item->catalog_points*$cur_cart_item->quantity;
		        }
	        }
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> There was an error adding your item to the cart, please refresh the page and try again.'));
        die();
    }

    // Return success/error
    if (!is_null($_SESSION['rsp_object']->socket_id) && is_user_logged_in() && !is_null($current_user_id)) {
        // Get new quantity
        $new_quantity = (isset($_POST['quantity'])) ? $_POST['quantity'] : 1;

        // Get item
        $item_response = @$_SESSION['rsp_object']->view_item(array('catalog_item_id' => $_POST['catalog_item_id']));

        // Display item
        if (!isset($item_response->Fault)) {
            // Get new amount by multiplying new quantity by points
            $points_to_add = ($new_quantity*$item_response->points);

            // Get new total cart points WITH new item
            $total_new_cart_points = $total_points+$points_to_add;

            // Check total points PLUS new amount
            if ($total_new_cart_points > $user_reward_points) {
                if (isset($_POST['quantity']) && $_POST['quantity'] != 1) {
                    echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> You do not have enough reward points available to add ('.$_POST['quantity'].') of this item to your cart.')); 
                } else {
                    echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> You do not have enough reward points available to add this item to your cart.')); 
                }
                die();
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => '<strong>Error!</strong> '.$item_response->Fault->faultstring));
            die();
        }

        // Add item to cart OR modify number of items
        if (isset($_POST['quantity'])) {
            // Add item to cart
            $add_to_cart = $_SESSION['rsp_object']->cart_set_item_quantity(array('external_user_id' => $current_user_id, 'catalog_item_id' => $_POST['catalog_item_id'], 'quantity' => $_POST['quantity']));
        } else {
            $add_to_cart = $_SESSION['rsp_object']->cart_add_item(array('external_user_id' => $current_user_id, 'catalog_item_id' => $_POST['catalog_item_id'], 'quantity' => 1));
        }

        // Check success/fail
        if (!isset($add_to_cart->Fault)) {
            // Return success message
            $cart_contents = json_decode(rsp_retrive_cart_contents());
            if ($cart_contents->status === 'success') {
                // Set points in cart text
                $points_in_cart = rsp_points_in_cart();
                $points_in_cart = ($points_in_cart > 0) ? ' ('.$points_in_cart.' In Cart / '.($user_reward_points-$points_in_cart).' Available)' : '';
                
                // Show points
                $show_points = 'You have '.number_format($user_reward_points).' Reward Points'.$points_in_cart.'.';

                // Return success
                if (isset($_POST['quantity'])) {
                    echo json_encode(array('status' => 'success', 'message' => 'Your your cart was successfully updated.', 'cart_contents' => $cart_contents->cart_contents, 'points_in_cart' => $show_points));
                } else {
                    echo json_encode(array('status' => 'success', 'message' => '<strong>Congrats!</strong> Your item was successfully added to the cart.', 'cart_contents' => $cart_contents->cart_contents, 'points_in_cart' => $show_points));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> There was an error adding your item to the cart, please refresh the page and try again.', 'system_fault' => $cart_contents->system_fault));
            }
        } else {
            // Return success message
            echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> There was an error adding your item to the cart, please refresh the page and try again.', 'system_fault' => $add_to_cart->Fault));
        }
    } else {
        // Return error message
        echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> There was an error adding your item to the cart, please refresh the page and try again.'));
    }

    die();
}
add_action( 'wp_ajax_rsp_add_to_cart', 'rsp_add_to_cart' );
add_action( 'wp_ajax_nopriv_rsp_add_to_cart', 'rsp_add_to_cart' );


/**
 * Place order
 */
function rsp_place_order() {
    // Set merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Get current user info
    $get_current_user = wp_get_current_user();
    $current_user_id = $get_current_user->ID;
    $user_reward_points = get_user_reward_points();

    // Check if user has points
    if (count($user_reward_points) == 0) {
        echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry!</strong> You have no reward points to spend.'));
        die();
    }

    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));
    
        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Check that login is valid and rewards store object is legit
    if (!is_null($_SESSION['rsp_object']->socket_id) && is_user_logged_in() && !is_null(get_current_user_id())) {
        // Get cart contents
        $cart_contents = $_SESSION['rsp_object']->cart_view(array('external_user_id' => get_current_user_id()));

        // Get total points to verify user isn't ordering more than their points balance
        $total_points = 0;
        if (!isset($cart_contents->Fault)) {
            if (count($cart_contents) > 0) {
                foreach ($cart_contents as $cur_cart_item) {
    		        // Add points to total
    		        $total_points += $cur_cart_item->catalog_points*$cur_cart_item->quantity;
		        }
            } else {
                // No cart contents
                echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry</strong> You must add items to your cart to place an order.'));
                die();
            }
        } else {
	        echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error placing your order (001). Please refresh the page and try again.'));
	        die();
        }

        // If cart points are greater than user reward points, don't allow order to be placed
        if ($total_points > $user_reward_points) {
            echo json_encode(array('status' => 'error', 'message' => '<strong>Sorry</strong> There seems to have been an error adding items to your cart. Please verify that the points balance of your cart doesn\'t add up to be more than your available reward points and try placing your order again.'));
	        die();
        }

        // Remove action from post
        unset($_POST['action']);

        // Add user id to args
        $args = $_POST;

        // Add user ID
        $current_user = wp_get_current_user();
        $args['external_user_id'] = $current_user->ID;

        // Set cart address
        $cart_set_address = $_SESSION['rsp_object']->cart_set_address($args);
        if (!isset($cart_set_address->Fault)) {
            // Set new points value
            $new_points_value = $user_reward_points-$total_points;

            // Place cart order if meta update is successful
            $subtract_user_reward_points = subtract_user_reward_points($total_points);
            if ($subtract_user_reward_points !== false) {
                $cart_order_place = $_SESSION['rsp_object']->cart_order_place($args);
                if (!isset($cart_order_place->Fault)) {
                    // Echo order placement success
                    echo json_encode(array('status' => 'success', 'message' => 'Your order was placed successfully!', 'redirect_url' => site_url('/loyalty-rewards/orders/list/')));

                    // Set admin address and email subject
                    $admin_email = get_option('admin_email', 'webmaster@saltsha.com');

                    // Set admin/user email subject and headers
                    $subject = 'Loyalty Rewards Order Placed';
                    $headers 	= "From: Saltsha <success@saltsha.com>\r\n";
                    $headers 	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
                    $headers 	.= "MIME-Version: 1.0\r\n";
                    $headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $headers 	.= "X-Mailgun-Native-Send: true\r\n";

                    // Set admin and user email templates
                    $admin_body = file_get_contents(RSP_PATH.'template/admin_email.html');
                    $user_body = file_get_contents(RSP_PATH.'template/user_email.html');

                    // Add each cart item to table
                    $email_cart_contents = '<table style="width:100%;text-align:left;" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                			<th style="width:25%;">Quantity</th>
                			<th style="width:75%;">Product</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($cart_contents as $cur_cart_item) {
                        $email_cart_contents .= '<tr>
                                            <td style="width:25%;">
                                                '.$cur_cart_item->quantity.'
                                            </td>
                    						<td style="padding:5px;">
                    							<a href="'.site_url().'/loyalty-rewards/product/'.$cur_cart_item->catalog_item_id.'">'.$cur_cart_item->name.'</a>
                                            </td>
                    					</tr>';
                    }
                    $email_cart_contents .= '</tbody>
                    </table>';
                    
					$company_select = get_the_author_meta( 'company_select', $current_user->ID );
					
					if( isset($company_select) && trim($company_select) == 'Pilothouse' ) {
						$company_select_name = 'Pilothouse';
						$company_select_link = 'http://pilothousepayments.com/';
						$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/pilothouse.png';
					} else {
						$company_select_name = 'PayProTec';
						$company_select_link = 'http://payprotec.com';
						$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt.png';
					}

                    // Set admin email cart contents and username, then send email
                    $admin_body = str_replace(array('[CART_CONTENTS]', '[WP_USER]', '[company_select_logo]', '[company_select_link]'), array($email_cart_contents, $current_user->user_login, $company_select_logo, $company_select_link), $admin_body);
                    wp_mail($admin_email, $subject, $admin_body, $headers );

                    // Set user email cart contents, then send email
                    $user_body = str_replace(array('[CART_CONTENTS]', '[company_select_logo]', '[company_select_link]'), array($email_cart_contents, $company_select_logo, $company_select_link), $user_body);
                    wp_mail($current_user->user_email, $subject, $user_body, $headers );
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error placing your order (001). Please refresh the page and try again.'));
                }

                // Retrieve cart item(s)
                $cart_items = $_SESSION['rsp_object']->cart_view(array('external_user_id' => get_current_user_id()));
            } else {
                // User meta update failed, let admin know
                echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error placing your order (002). An admin has been notified and will be in contact shortly.'));

                // Get current user info
                global $current_user;
                get_currentuserinfo();

                // Let admin know of the order failure
                $admin_email = get_option('admin_email', 'webmaster@saltsha.com');
                wp_mail($admin_email, 'Loyalty Rewards Order Error', 'User "'.$current_user->user_login.'" just had an issue placing their Loyalty Rewards order.');
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error placing your order (003). Please refresh the page and try again.'));
        }
    } else {
        // Return error message
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error placing your order (004). Please refresh the page and try again.'));
    }

    die();
}
add_action( 'wp_ajax_rsp_place_order', 'rsp_place_order' );
add_action( 'wp_ajax_nopriv_rsp_place_order', 'rsp_place_order' );


/**
 * Empty cart
 */
function rsp_empty_cart() {
    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));
    
        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Set merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Get current user info
    $get_current_user = wp_get_current_user();
    $current_user_id = get_current_user_id();
    $user_reward_points = get_user_reward_points();

    // Return success/error
    if (!is_null($_SESSION['rsp_object']->socket_id) && is_user_logged_in() && !is_null(get_current_user_id())) {
        // Empty cart
        $cart_empty = $_SESSION['rsp_object']->cart_empty(array('external_user_id' => get_current_user_id()));

        // Check success/fail
        if (!isset($cart_empty->Fault)) {
            // Show points
            $show_points = 'You have '.number_format($user_reward_points).' Reward Points.';

            // Return success message
            echo json_encode(array('status' => 'success', 'message' => '<strong>Congrats!</strong> Your cart was emptied successfully.', 'points_in_cart' => $show_points));
        } else {
            // Return success message
            echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error emptying your cart. Please refresh the page and try again.', 'system_fault' => $cart_empty->Fault));
        }
    } else {
        // Return error message
        echo json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error emptying your cart. Please refresh the page and try again.'));
    }

    die();
}
add_action( 'wp_ajax_rsp_empty_cart', 'rsp_empty_cart' );
add_action( 'wp_ajax_nopriv_rsp_empty_cart', 'rsp_empty_cart' );


/**
 * Retrieve cart contents
 */
function rsp_retrive_cart_contents() {
    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));
    
        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Return success/error
    if (!is_null($_SESSION['rsp_object']->socket_id) && is_user_logged_in() && !is_null(get_current_user_id())) {
        // Get cart contents
        $cart_contents = '
        <div id="loading_cart"><h3>Placing Order...</h3></div>
        <table class="rsp_cart_table" cellspacing="0">
            <thead>
                <tr>
                    <th class="product-remove">&nbsp;</th>
        			<th class="product-thumbnail">&nbsp;</th>
        			<th class="product-name">Product</th>
        			<th class="product-price">Price</th>
        			<th class="product-quantity">Quantity</th>
        			<th class="product-subtotal">Total</th>
                </tr>
            </thead>
            <tbody style="background-color:#FAF6EB;">';

            // Retrieve cart item(s)
            $cart_items = $_SESSION['rsp_object']->cart_view(array('external_user_id' => get_current_user_id()));

            // Display cart item(s)
            $total_points = 0;
            if (!isset($cart_items->Fault)) {
                if (count($cart_items) > 0) {
                    foreach ($cart_items as $cur_cart_item) {
                        $cart_contents .= '<tr class="cart_table_item">
						<!-- Remove from cart link -->
						<td class="product-remove">
							<a class="remove-item" catalog-item-id="'.$cur_cart_item->catalog_item_id.'" title="Remove this item">×</a>
                        </td>

						<!-- The thumbnail -->
						<td class="product-thumbnail">
							<a href="">
							    <img width="90" height="90" src="'.$cur_cart_item->image_uri.'" class="attachment-shop_thumbnail wp-post-image" title="'.$cur_cart_item->name.'"></a>
                        </td>

						<!-- Product Name -->
						<td class="product-name">
							<a href="'.site_url().'/loyalty-rewards/product/'.$cur_cart_item->catalog_item_id.'">'.$cur_cart_item->name.'</a>
                        </td>

						<!-- Product Price -->
						<td class="product-price">
							<span class="amount">'.number_format($cur_cart_item->catalog_points).' Pts</span>
                        </td>

						<!-- Quantity inputs -->
						<td class="product-quantity">
							<img src="/wp-content/plugins/loyalty-rewards/assets/images/qty-plus.gif" class="qty-buttons qty-plus" catalog-item-id="'.$cur_cart_item->catalog_item_id.'" /><div class="qty-amount" id="qty-'.$cur_cart_item->catalog_item_id.'">'.$cur_cart_item->quantity.'</div><img src="/wp-content/plugins/loyalty-rewards/assets/images/qty-minus.gif" class="qty-buttons qty-minus" catalog-item-id="'.$cur_cart_item->catalog_item_id.'" />
                        </td>

						<!-- Product subtotal -->
						<td class="product-subtotal">
							<span class="amount">'.number_format($cur_cart_item->catalog_points*$cur_cart_item->quantity).' Pts</span>
                        </td>
					</tr>';

        		        // Add points to total
        		        $total_points += $cur_cart_item->catalog_points*$cur_cart_item->quantity;
    		        }
                } else {
    		        $cart_contents .= '<tr>
    		            <td colspan="6" class="no-items-in-cart"><em>No items in cart.</em></td>
    		        </tr>';
                }
            } else {
		        $cart_contents .= '<tr>
		            <td colspan="6" class="no-items-in-cart"><em>No items in cart.</em></td>
		        </tr>';
            }

            $total_points = ($total_points == 0) ? '--' : number_format($total_points).' Pts';

            $wp_user = get_user_meta(get_current_user_id());
            $first_name = (isset($wp_user['billing_first_name'])) ? trim($wp_user['billing_first_name'][0]) : '';
            $last_name = (isset($wp_user['billing_last_name'])) ? trim($wp_user['billing_last_name'][0]) : '';
            $address_1 = (isset($wp_user['billing_address_1'])) ? trim($wp_user['billing_address_1'][0]) : '';
            $city = (isset($wp_user['billing_city'])) ? trim($wp_user['billing_city'][0]) : '';
            $state = (isset($wp_user['billing_state'])) ? trim($wp_user['billing_state'][0]) : '';
            $postal_code = (isset($wp_user['billing_postcode'])) ? trim($wp_user['billing_postcode'][0]) : '';

            $cart_contents .= '
                    <tr>
                        <td colspan="5"></td>
                        <td  style="padding:5px;">
                            <h3 style="font-weight:bold;">'.$total_points.'</h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="rsp_cart_table" cellspacing="0">
                <thead class="shipping-info">
                    <tr>
                        <th colspan="2" class="product-remove">Shipping Information</th>
                    </tr>
                </thead>
                <tbody class="shipping-info" style="background-color:#FAF6EB;">
                    <tr>
                        <td>First Name</td>
                        <td><input type="text" id="first_name" value="'.$first_name.'" /></td>
                    </tr>
                    <tr>
                        <td>Last Name</td>
                        <td><input type="text" id="last_name" value="'.$last_name.'" /></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><input type="text" id="address_1" value="'.$address_1.'" /></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><input type="text" id="city" value="'.$city.'" /></td>
                    </tr>
                    <tr>
                        <td>State</td>
                        <td><input type="text" id="state_province" value="'.$state.'" /></td>
                    </tr>
                    <tr>
                        <td>Zip Code</td>
                        <td><input type="text" id="postal_code" value="'.$postal_code.'" /></td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td><select id="country"><option value="US">United States</option></select></td>
                    </tr>
                </tbody>
            </table>
            <a class="view-cart-button place-order-button">Place Order</a>';

        // Check success/fail
        if (!isset($cart_empty->Fault)) {
            // Return success message
            return json_encode(array('status' => 'success', 'message' => '<strong>Congrats!</strong> Your cart was updated successfully.', 'cart_contents' => $cart_contents));
        } else {
            // Return success message
            return json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error updating your cart. Please refresh the page and try again.', 'system_fault' => $cart_empty->Fault));
        }
    } else {
        // Return error message
        return json_encode(array('status' => 'error', 'message' => 'Sorry, there was an error updating your cart. Please refresh the page and try again.'));
    }
}


/**
 * Add additional URL vars
 */
function rsp_query_vars($query_vars) {
    $my_query_vars = explode("|", RSP_QUERY_VARS);
    foreach ($my_query_vars as $cur_query_var) {
        $query_vars[] = $cur_query_var;
    }
    return $query_vars;
}
add_filter('query_vars', 'rsp_query_vars');


/**
 * Add additional rewrite rules
 */
function rsp_rewrite_rules($rewrite_rules) {
    // Get URL params
    $url_params = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    // If this is the rewards store page, setup URLs
    if ($url_params[0] === 'loyalty-rewards') {
        // Remove "loyalty-rewards" from URL params
        unset($url_params[0]);

        // Get parameter keys and values
        $param_values = $param_keys = array();
        foreach($url_params as $key => $value) {
            if($key&1) {
                array_push($param_keys, $value);
            } else {
                array_push($param_values, $value);
            }
        }

        // Build regex replace URL
        $reg_ex_replace = 'index.php?pagename=loyalty-rewards&'.implode('&', array_map(function ($v, $k) { return 'rsp_'.$v.'=$matches['.($k+1).']'; }, $param_keys, array_keys($param_keys)));

        // Build regex with params sent
        $reg_ex = 'loyalty-rewards/'.implode('/([^/]+)/', $param_keys).'/([^/]+)/?$';

        // Write rules and return them
        $cat_rules = array($reg_ex => $reg_ex_replace);
        $rewrite_rules += $cat_rules;
    }

    return $rewrite_rules; 
}
add_filter('rewrite_rules_array', 'rsp_rewrite_rules');


/**
 * Add rewards to navigation menu
 */
function add_rewards_to_nav( $items, $args ) {
    global $post;
    global $wp_query;

    // Check if user is logged in
    if (!is_user_logged_in()) {
        return $items;
    }

    // Check if current menu
    $is_current_menu = (isset($post) && $post->post_name === 'loyalty-rewards') ? true : false;

    // Check if no categories
    $no_category_selected = (!isset($wp_query->query_vars['rsp_category'])) ? ' current-menu-item' : '';

    // Check active or not
    $current_class = ($is_current_menu) ? 'menu-item menu-item-type-post_type menu-item-object-page current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent current_page_ancestor menu-item-has-children open' : 'menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children';

    // Create rewards store object, if not created already
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));

        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Get rewards store categories
    $categories = $_SESSION['rsp_object']->catalog_breakdown();
    if (isset($categories->Fault)) {
        return $items;
    }
	    
		// Add store categories to menu
	    $newItem = '<li class="'.$current_class.'">
	        <a href="'.site_url().'/loyalty-rewards/">
	            <div class="icon-money"></div>
	            <span class="title">Loyalty Rewards</span>
	            <span class="selected"></span>
	            <span class="selected"></span>
	        </a>
	        <ul class="sub-menu" style="overflow: hidden; display: none;">
	        	<li class="menu-item menu-item-type-post_type menu-item-object-page'.$no_category_selected.'">
	        	    <a href="'.site_url().'/loyalty-rewards/">
	        	        <div class="icon-briefcase"></div>
	        	        <span class="title">All Categories</span>
	                </a>
	            </li>';
	
	    // Display rewards store categories
	    if (isset($categories->categories) && count($categories->categories) > 0) {
	        foreach ($categories->categories as $cur_category) {
	                // Create category slug
	                $cur_category_slug = create_slug($cur_category->name);
	
	                // Check if current category
	                $cur_category_selected = (isset($wp_query->query_vars['rsp_category']) && $wp_query->query_vars['rsp_category'] === $cur_category_slug) ? ' current-menu-item' : '';
	
	                $newItem .= '<li class="menu-item menu-item-type-post_type menu-item-object-page'.$cur_category_selected.'">
	            	    <a href="'.site_url().'/loyalty-rewards/category/'.$cur_category_slug.'/">
	            	        <div class="icon-briefcase"></div>
	            	        <span class="title">'.$cur_category->name.'</span>
	                    </a>
	                </li>';
	        }
	    }

	    // Close menu
	    $newItem .= '</ul></li>';
	    
	    
		$items = preg_replace('/\<li id="menu-item-5976"(.*)\<\/li\>/i', $newItem, $items);
		$items = preg_replace('/\<li id="menu-item-6262"(.*)\<\/li\>/i', $newItem, $items);

    // Return menu with new navigation
    return $items;
}
add_filter('wp_nav_menu_items', 'add_rewards_to_nav', 10, 2);


/**
 * Build pagination
 */
function rsp_build_pagination($pages = null) {
    // Return blank if pagination is null
    if (is_null($pages)) {
        return '';
    }

    // Globals
    global $wp_query;

    // If page number is not set, default to page 1
    $cur_page = (isset($wp_query->query_vars['rsp_page'])) ? $wp_query->query_vars['rsp_page'] : ((isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0) ? $wp_query->query_vars['paged'] : 1);

    // Build "previous" link
    $pagination = '<div class="clear"></div><div class="dataTables_paginate paging_bootstrap pagination"><ul>';
    if ($pages->has_previous) {
        $pagination .= '<li class="prev"><a href="'.rsp_build_url(array('rsp_page' => ($cur_page-1))).'">← Prev</a></li>';
    } else {
        $pagination .= '<li class="prev disabled"><a href="#">← Prev</a></li>';
    }

    // Other page pagination
    foreach ($pages->pages->integer as $cur_pagination_page) {
        if ($cur_pagination_page == $cur_page) {
            $pagination .= '<li class="active"><a href="'.rsp_build_url(array('rsp_page' => $cur_pagination_page)).'">'.$cur_pagination_page.'</a></li>';
        } else {
            $pagination .= '<li><a href="'.rsp_build_url(array('rsp_page' => $cur_pagination_page)).'">'.$cur_pagination_page.'</a></li>';
        }
    }

    // Build "next" link
    if ($pages->has_next) {
        $pagination .= '<li class="next"><a href="'.rsp_build_url(array('rsp_page' => ($cur_page+1))).'">Next → </a></li>';
    } else {
        $pagination .= '<li class="next disabled"><a href="#">Next → </a></li>';
    }

    $pagination .= '</ul></div>';

    return $pagination;
}

/**
 * Create category slug
 */
function create_slug($string) {
    $result = strtolower($string);
    $result = preg_replace("/[^A-Za-z0-9\s-._\/]/", "", $result);
    $result = trim(preg_replace("/[\s-]+/", " ", $result));
    $result = trim(substr($result, 0, strlen($string)));
    $result = preg_replace("/\s/", "-", $result);
    return $result;
}


/**
 * Get category ID from slug
 */
function get_category_id_from_slug($category_slug) {
    // Get categories
    $categories = $_SESSION['rsp_object']->catalog_breakdown();
    foreach($categories->categories as $cur_category) {
        $cur_category_slug = create_slug($cur_category->name);
        if ($cur_category_slug === $category_slug) {
            return $cur_category->category_id;
        }
    }
    return null;
}

/**
 * Build URL with parameters
 */
function rsp_build_url($replacement_args = array()) {
    // Globals
    global $wp_query;

    // Loop through WP query vars and get only the ones we are looking for
    $param_values = $param_keys = array();
    $rsp_vars = explode('|', RSP_QUERY_VARS);
    foreach ($wp_query->query_vars as $key => $value) {
        if (in_array($key, $rsp_vars)) {
            // Push key to 'keys' array
            array_push($param_keys, str_replace('rsp_', '', $key));

            // Check for replacement args
            if (array_key_exists($key, $replacement_args)) {
                // Push new value to 'values' array
                array_push($param_values, $replacement_args[$key]);
            } else {
                // Push current value to 'values' array
                array_push($param_values, $value);
            }
        }
    }

    // Overwrite arguments with custom-defined $replacement_args
    if (count($replacement_args) > 0) {
        foreach ($replacement_args as $ra_key => $ra_value) {
            $ra_key = str_replace('rsp_', '', $ra_key);
            $search_keys = array_search($ra_key, $param_keys);
            if ($search_keys !== false) {
                // Key found, replace it
                $param_values[$search_keys] = $ra_value;
            } else {
                // Key NOT found, add it
                $param_keys[] = $ra_key;
                $param_values[$ra_key] = $ra_value;
            }
        }
    }

    // Build URL with parameters
    $new_url = '/loyalty-rewards/'.implode('/', array_map(function ($k, $v) { return $k.'/'.$v.'/'; }, $param_keys, $param_values));
    $new_url = preg_replace('/\/{2,}/', "/", $new_url);

    // Return new URL
    return site_url().$new_url;
}


/**
 * Get points in cart
 */
function rsp_points_in_cart() {
    global $wp_query;

    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_redirect(home_url());
        exit;
    }

    // Create rewards store object
    if (!isset($_SESSION['rsp_object'])) {
        // Get options
        $rsp_options = json_decode(get_option(RSP_OPTIONS));
    
        // Set session var
        $_SESSION['rsp_object'] = new RewardsStore($rsp_options);
    }

    // Get cart contents
    $cart_contents = $_SESSION['rsp_object']->cart_view(array('external_user_id' => get_current_user_id()));

    // Get total points to verify user isn't ordering more than their points balance
    $total_points = 0;
    if (!isset($cart_contents->Fault)) {
        if (count($cart_contents) > 0) {
            foreach ($cart_contents as $cur_cart_item) {
		        // Add points to total
		        $total_points += $cur_cart_item->catalog_points*$cur_cart_item->quantity;
	        }
            return $total_points;
        } else {
            return 0;
        }
    } else {
        return 0;
    }    
}


/**
 * Show filters
 */
function rsp_filters() {
    // Globals
    global $wp_query;

    // Sort by options
    $sort_by_options = array(rsp_build_url(array('rsp_sort_by' => 'rank asc')) => 'Most Popular', rsp_build_url(array('rsp_sort_by' => 'score desc')) => 'Most Relevant', rsp_build_url(array('rsp_sort_by' => 'points asc')) => 'Points Ascending', rsp_build_url(array('rsp_sort_by' => 'points desc')) => 'Points Descending');
    echo '<select id="rsp_sort_by">
        <option value="" disabled>Sort By...</option>
        '.implode('', array_map(function ($k, $v)  use ($wp_query) {
                if (isset($wp_query->query_vars['rsp_sort_by']) && stripos($k, '/sort_by/'.urldecode($wp_query->query_vars['rsp_sort_by']).'/') !== false) {
                    return '<option value="'.$k.'" selected>'.$v.'</option>';
                } else {
                    return '<option value="'.$k.'">'.$v.'</option>';
                }
            }, array_keys($sort_by_options), $sort_by_options)).'</select> ';

    // Items per page options
    $per_page_options = array(rsp_build_url(array('rsp_per_page' => '10')) => '10 Items Per Page', rsp_build_url(array('rsp_per_page' => '20')) => '20 Items Per Page', rsp_build_url(array('rsp_per_page' => '30')) => '30 Items Per Page', rsp_build_url(array('rsp_per_page' => '40')) => '40 Items Per Page', rsp_build_url(array('rsp_per_page' => '50')) => '50 Items Per Page');
    echo '<select id="rsp_per_page">
        <option value="" disabled>Show...</option>
        '.implode('', array_map(function ($k, $v)  use ($wp_query) {
                if (isset($wp_query->query_vars['rsp_per_page']) && stripos($k, '/per_page/'.$wp_query->query_vars['rsp_per_page'].'/') !== false) {
                    return '<option value="'.$k.'" selected>'.$v.'</option>';
                } else {
                    return '<option value="'.$k.'">'.$v.'</option>';
                }
            }, array_keys($per_page_options), $per_page_options)).'</select> ';

    // Point value options
    $point_value_options = array(rsp_build_url(array('rsp_min_points' => '500')) => 'Rewards > 500 Points', rsp_build_url(array('rsp_min_points' => '1000')) => 'Rewards > 1000 Points', rsp_build_url(array('rsp_min_points' => '2000')) => 'Rewards > 2000 Points', rsp_build_url(array('rsp_min_points' => '5000')) => 'Rewards > 5000 Points', rsp_build_url(array('rsp_min_points' => '10000')) => 'Rewards > 10000 Points', rsp_build_url(array('rsp_min_points' => '15000')) => 'Rewards > 15000 Points');
    echo '<select id="rsp_min_points">
        <option value="" disabled>Show...</option>
        '.implode('', array_map(function ($k, $v)  use ($wp_query) {
                if (isset($wp_query->query_vars['rsp_min_points']) && stripos($k, '/min_points/'.$wp_query->query_vars['rsp_min_points'].'/') !== false) {
                    return '<option value="'.$k.'" selected>'.$v.'</option>';
                } else {
                    return '<option value="'.$k.'">'.$v.'</option>';
                }
            }, array_keys($point_value_options), $point_value_options)).'</select> ';

    // Search box
    echo '<input id="search_term" type="text" placeholder="Search term(s)..." value="'.urldecode($wp_query->query_vars['rsp_search']).'" /><input id="search_button" type="button" value="Search Store" />';

    echo '<div class="clear"></div>';
}


/**
 * Add reward points column to user admin table
 */
function new_modify_user_table( $column ) {
    $column['rsp_reward_points'] = 'Reward Points';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );


/**
 * Populate reward points column with user points in admin table
 */
function new_modify_user_table_row( $val, $column_name, $user_id ) {
    $user = get_userdata( $user_id );
    switch ($column_name) {
        case 'rsp_reward_points' :
            // Get user info
            // $get_current_user = get_userdata($user_id);

            // Get reward points
            return get_user_reward_points($user_id);
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );


/**
 * Get fulfillment status for item
 */
function get_fulfillment_status($order_list, $order_item_id) {
    $virtual_code_details = $shipping_company = $shipping_tracking_number = '';

    foreach ($order_list->fulfillments->Fulfillment[0]->items->FulfillmentItem as $cur_order_item_id) {
        if ($cur_order_item_id->order_item_id == $order_item_id) {
            foreach ($order_list->fulfillments->Fulfillment[0]->metadata->Meta as $cur_meta_info) {
                if (isset($cur_meta_info->key) && $cur_meta_info->key === 'VIRTUAL_CODE_DETAILS') {
                    $virtual_code_details = $cur_meta_info->value;
                }
                if (isset($cur_meta_info->key) && $cur_meta_info->key === 'SHIPPER') {
                    $shipping_company = $cur_meta_info->value;
                }
                if (isset($cur_meta_info->key) && $cur_meta_info->key === 'TRACKING') {
                    $shipping_tracking_number = (trim($cur_meta_info->uri) !== '') ? '<a href="'.$cur_meta_info->uri.'" target="_blank">'.$cur_meta_info->value.'</a>' : $cur_meta_info->value;
                }
            }

            // Fulfillment data
            $fulfillment_data = (empty($shipping_company)) ? $virtual_code_details : '<strong>Shipping Company</strong><br/>'.$shipping_company.'<br/><br/><strong>Tracking Number</strong><br/>'.$shipping_tracking_number;
            return (empty($fulfillment_data)) ? 'N/A' : '<a href="#digital_goods_modal_'.$order_item_id.'" data-toggle="modal">Click To View</a><div id="digital_goods_modal_'.$order_item_id.'" class="fulfillment_modal modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div class="modal-header">
			<h3 id="myModalLabel">Shipping / Digital Goods Status <a data-dismiss="modal" aria-hidden="true" style="float:right;cursor:pointer;">[ close ]</a></h3>
		</div>
		<div class="modal-body fulfillment_modal_body">
			<div class="row-fluid">	
			    '.nl2br($fulfillment_data).'
			</div>
		</div>
	</div>';
        }
    }
    return '--';
}


/**
 * Neat trim text
 */
function neat_trim($str, $n, $delim='...') {
    $len = strlen($str);
    if ($len > $n) {
        preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
        return rtrim($matches[1]) . $delim;
    } else {
        return $str;
    }
}

/**
 * Use HTML emails
 */
function set_html_content_type() {
	return 'text/html';
}
add_filter( 'wp_mail_content_type', 'set_html_content_type' );


/**
 * Get user reward points
 */
function get_user_reward_points($user_id = null) {
    global $wpdb;

    // Total points
    $total_points = 0;

    // If user ID is null, set to current user ID
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }

    // Get merchant IDs for user
    $merchant_ids = get_merchant_ids($user_id);

    // Get points
    if (count($merchant_ids) == 0) {
        return $total_points;
    } else {
        foreach ($merchant_ids as $cur_merchant_id) {
            $points = $wpdb->get_row("SELECT points FROM ".$wpdb->prefix."ppttd_reward_points WHERE merchant_id='".$cur_merchant_id->merchant_id."'");
            if ($wpdb->num_rows > 0) {
                $total_points += $points->points;
            }
        }
    }

    // Return total points
    return $total_points;
}


/**
 * Get user reward points
 */
function get_merchant_reward_points($merchant_id = null) {
    global $wpdb;

    // Total points
    $merchant_points = 0;

    // If user ID is null, set to current user ID
    if (is_null($merchant_id)) {
        return $merchant_points;
    }

    // Get points
    $points = $wpdb->get_row("SELECT points FROM ".$wpdb->prefix."ppttd_reward_points WHERE merchant_id='".$merchant_id."'");
    if ($wpdb->num_rows > 0) {
        $merchant_points += $points->points;
    }

    // Return total points
    return $merchant_points;
}


/**
 * Update reward points
 */
function update_merchant_reward_points($merchant_id = null, $new_points = null) {
    global $wpdb;

    if (is_null($merchant_id) || is_null($new_points)) {
        return false;
    } else {
        // Pad merchant ID
        $merchant_id = str_pad($merchant_id, 16, '0', STR_PAD_LEFT);

        // Insert / update points
        $wpdb_update = $wpdb->update($wpdb->prefix.'ppttd_reward_points', array('points' => $new_points), array('merchant_id' => $merchant_id));
        if ($wpdb_update) {
            return true;
        } else {
            $wpdb_insert = $wpdb->insert($wpdb->prefix.'ppttd_reward_points', array('points' => $new_points, 'merchant_id' => $merchant_id));
            if ($wpdb_insert) {
                return true;
            } else {
                return false;
            }
        }
    }
}


/**
 * Subtract reward points from user
 */
function subtract_user_reward_points($num_points_to_subtract = null, $user_id = null) {
    global $wpdb;

    // If number of points is null, return false
    if (is_null($num_points_to_subtract)) {
        return false;
    }

    // If user ID is null, set to current user ID
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }

    // Get merchant IDs for user
    $merchant_ids = get_merchant_ids($user_id);

    // Get points
    if (count($merchant_ids) == 0) {
        return false;
    } else {
        foreach ($merchant_ids as $cur_merchant_id) {
            try {
                // Get merchant's reward points
                $points = $wpdb->get_row("SELECT merchant_id,points FROM ".$wpdb->prefix."ppttd_reward_points WHERE merchant_id='".$cur_merchant_id->merchant_id."'");
                if ($wpdb->num_rows > 0) {
                    // Get number of points to subtract
                    if ($num_points_to_subtract > 0 && $points->points > 0) {
                        // Subtract current points from number of points to subtract
                        $num_points_to_subtract = $num_points_to_subtract-$points->points;
    
                        // If number is negative, make it positive
                        if ($num_points_to_subtract < 0) {
                            // Multiply reward points by -1 to make a positive number
                            $num_points_to_subtract = $num_points_to_subtract * -1;
                            
                            // Update merchant's reward points
                            update_merchant_reward_points($points->merchant_id, $num_points_to_subtract);
                        } else {
                            // Update merchant's reward points
                            update_merchant_reward_points($points->merchant_id, 0);
                        }
                    }
                }
            } catch (Exception $exc) {
                return false;
            }
        }
    }
    
    // Return points array
    return true;
}
?>