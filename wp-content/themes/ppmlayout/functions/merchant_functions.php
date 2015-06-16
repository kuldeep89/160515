<?php
/**
 * Change active merchant ID
 */
function change_active_mid() {
    // Set new merchant if not null
    if (!is_null($_POST['merchant_name']) && trim($_POST['merchant_name']) !== '') {
        $_SESSION['active_mid'] = array('merchant_id' => str_pad($_POST['merchant_id'], 16, '0', STR_PAD_LEFT), 'merchant_name' => $_POST['merchant_name']);
    }

    // Return merchant data
    if (!is_null($_POST['goal_select']) && trim($_POST['goal_select']) !== '') {
        echo get_dashboard_transaction_summary_data($_POST['goal_select']);
    } else {
        echo get_dashboard_transaction_summary_data(null);
    }

    die();
}
add_action( 'wp_ajax_change_active_mid', 'change_active_mid' );
add_action( 'wp_ajax_nopriv_change_active_mid', 'change_active_mid' );


/**
 * Get merchant IDs
 */
function get_merchant_ids($user_id = null) {
    global $wpdb;

    // If user ID is null use current user's merchant ID
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }

    // Get merchant IDs for user
    $merchant_ids = $wpdb->get_results("SELECT * FROM wp_merchant_user_relationships WHERE user_id=$user_id");

    // Return merchant IDs
    return $merchant_ids;
}
?>