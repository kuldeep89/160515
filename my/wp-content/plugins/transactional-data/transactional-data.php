<?php
/**
 * Plugin Name: Transactional Reporting
 * Author: Bobbie Stump
 * Description: Transactional data for merchangts.
 * Version: 0.0.1
*/


/**
 * Define constants
 */
define('TD_PATH', ABSPATH.PLUGINDIR.'/transactional-data/');


/**
 * Runs when plugin is activated
 */
register_activation_hook(__FILE__,'ppttd_install'); 


/**
 * Runs on plugin deactivation
 */
register_deactivation_hook( __FILE__, 'ppttd_remove' );


/**
 * Creates new database field(s) associated with plugin
 */
function ppttd_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;

	// Setup db tables
	$td_batchlisting ="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ppttd_batchlisting` (
		`id` varchar(16) NOT NULL,
		`uniq_batch_id` varchar(58) NOT NULL,
		`merchant_id` bigint(16) unsigned zerofill NOT NULL DEFAULT '0000000000000000',
		`terminal_id` varchar(24) NOT NULL,
		`batch_control` varchar(32) NOT NULL,
		`total_volume` varchar(15) NOT NULL,
		`total_trans` varchar(15) NOT NULL,
		`total_purch_amt` varchar(15) NOT NULL,
		`total_purch_trans` varchar(15) NOT NULL,
		`total_return_amt` varchar(15) NOT NULL,
		`total_return_trans` varchar(15) NOT NULL,
		`batch_date` varchar(20) NOT NULL,
		`is_read` tinyint(1) NOT NULL DEFAULT '0',
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	$td_chargebacks = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ppttd_chargebacks` (
		`processor` varchar(50) NOT NULL,
		`id` varchar(20) NOT NULL,
		`case_number` varchar(20) NOT NULL,
		`cardholder_number` varchar(20) NOT NULL,
		`case_amount` varchar(15) NOT NULL,
		`original_transaction_amount` varchar(15) NOT NULL,
		`transaction_date` varchar(15) NOT NULL,
		`merchant_number` varchar(32) NOT NULL,
		`merchant_name` varchar(50) NOT NULL,
		`reason_code` varchar(10) NOT NULL,
		`reason_code_description` varchar(50) NOT NULL,
		`date_received` varchar(15) NOT NULL,
		`mcc_code` varchar(15) NOT NULL,
		`card_type` varchar(15) NOT NULL,
		`original_card_type` varchar(15) NOT NULL,
		`transaction_type` varchar(30) NOT NULL,
		`acquirer_reference_number` varchar(46) NOT NULL,
		`reference_number` varchar(46) NOT NULL,
		`effective_date` varchar(15) NOT NULL,
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	$td_retrievals = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ppttd_retrievals` (
		`processor` varchar(50) NOT NULL,
		`id` varchar(12) NOT NULL,
		`case_number` varchar(20) NOT NULL,
		`cardholder_number` varchar(20) NOT NULL,
		`case_amount` varchar(15) NOT NULL,
		`original_transaction_amount` varchar(15) NOT NULL,
		`transaction_date` varchar(15) NOT NULL,
		`merchant_number` varchar(32) NOT NULL,
		`merchant_name` varchar(50) NOT NULL,
		`reason_code` varchar(10) NOT NULL,
		`reason_code_description` varchar(50) NOT NULL,
		`date_received` varchar(15) NOT NULL,
		`mcc_code` varchar(15) NOT NULL,
		`card_type` varchar(15) NOT NULL,
		`original_card_type` varchar(15) NOT NULL,
		`due_date` varchar(15) NOT NULL,
		`acquirer_reference_number` varchar(46) NOT NULL,
		`reference_number` varchar(46) NOT NULL,
		`effective_date` varchar(15) NOT NULL,
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	$td_transactionlisting = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ppttd_transactionlisting` (
		`id` varchar(20) NOT NULL,
		`uniq_batch_id` varchar(60) NOT NULL,
		`card_type` varchar(15) NOT NULL,
		`reference` varchar(15) NOT NULL,
		`seq_id` varchar(10) NOT NULL,
		`transaction_time` datetime NOT NULL,
		`amt` varchar(15) NOT NULL,
		`processing_code` varchar(15) NOT NULL,
		`auth_code` varchar(15) NOT NULL,
		`input_method` varchar(15) NOT NULL,
		`merchant_id` bigint(16) unsigned zerofill NOT NULL DEFAULT '0000000000000000',
		`card_lastfour` varchar(4) NOT NULL,
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		ADD KEY `uniq_batch_id` (`uniq_batch_id`,`merchant_id`,`transaction_time`,`card_lastfour`);";
    $td_customers = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ppttd_customers` (
        `id` bigint(20) NOT NULL,
        `merchant_id` bigint(16) unsigned zerofill NOT NULL DEFAULT '0000000000000000',
        `card_type` varchar(3) NOT NULL,
        `last_four` varchar(4) NOT NULL,
        `cardholder_name` varchar(40) DEFAULT '',
        `address` varchar(40) DEFAULT NULL,
        `address2` varchar(40) DEFAULT NULL,
        `city` varchar(30) DEFAULT NULL,
        `state` varchar(20) DEFAULT NULL,
        `zip_code` varchar(10) DEFAULT '',
        `phone` varchar(20) DEFAULT NULL,
        `email` varchar(50) DEFAULT NULL
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
	dbDelta( $td_batchlisting );
	dbDelta( $td_chargebacks );
	dbDelta( $td_retrievals );
	dbDelta( $td_transactionlisting );
	dbDelta( $td_customers );
}


/**
 * Save additional user profile field(s)
 */
function ppttd_save_account_field( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

    // Build merchant ID array to store in database
    $ppttd_merchant_id = array();
    foreach ($_POST['merchant_ids'] as $index => $value) {
        if (isset($value) && trim($value) !== '' && is_numeric($value)) {
            // Set merchant ID
            $new_merchant_id = str_pad($value, 16, '0', STR_PAD_LEFT);

            // Update reward points value, if set
            if (isset($_POST['reward_points'][$index])) {
                update_merchant_reward_points($new_merchant_id, $_POST['reward_points'][$index]);
            }

            // Set MID data
            $new_mid = new StdClass();
            $new_mid->merchant_id = $new_merchant_id;
            $new_mid->merchant_name = $_POST['merchant_names'][$index];
            $new_mid->price_override = $_POST['price_overrides'][$index];
            $ppttd_merchant_id[] = $new_mid;
        }
    }

    // Get current filter
	$get_action = current_filter();

	// Set merchant info
	if ($get_action == 'user_register') {
		$ppttd_merchant_info = array(
			'ppttd_sales_rep_id' => null,
			'ppttd_daily_transaction_report' => 'on',
			'ppttd_weekly_transaction_report' => 'on',
			'ppttd_monthly_transaction_report' => 'on',
			'ppttd_week_starts_on' => 'Monday',
			'ppttd_enable_alerts' => 'on',
			'ppttd_alert_batch_above' => null,
			'ppttd_alert_batch_below' => null,
			'ppttd_alert_no_processing' => '5',
			//'ppttd_alert_chargebacks' => 'on',
			//'ppttd_alert_retrievals' => 'on',
			// 'ppttd_alert_duplicate_transaction' => 'on'
		);
	} else {
		$ppttd_merchant_info = array(
			'ppttd_sales_rep_id' => (isset($_POST['ppttd_sales_rep_id'])) ? trim($_POST['ppttd_sales_rep_id']) : null,
			'ppttd_daily_transaction_report' => (isset($_POST['ppttd_daily_transaction_report'])) ? trim($_POST['ppttd_daily_transaction_report']) : null,
			'ppttd_weekly_transaction_report' => (isset($_POST['ppttd_weekly_transaction_report'])) ? trim($_POST['ppttd_weekly_transaction_report']) : null,
			'ppttd_monthly_transaction_report' => (isset($_POST['ppttd_monthly_transaction_report'])) ? trim($_POST['ppttd_monthly_transaction_report']) : null,
			'ppttd_week_starts_on' => (isset($_POST['ppttd_week_starts_on'])) ? $_POST['ppttd_week_starts_on'] : null,
			'ppttd_enable_alerts' => (isset($_POST['ppttd_enable_alerts'])) ? $_POST['ppttd_enable_alerts'] : null,
			'ppttd_alert_batch_above' => (isset($_POST['ppttd_alert_batch_above'])) ? $_POST['ppttd_alert_batch_above'] : null,
			'ppttd_alert_batch_below' => (isset($_POST['ppttd_alert_batch_below'])) ? $_POST['ppttd_alert_batch_below'] : null,
			'ppttd_alert_no_processing' => (isset($_POST['ppttd_alert_no_processing'])) ? $_POST['ppttd_alert_no_processing'] : null,
			//'ppttd_alert_chargebacks' => (isset($_POST['ppttd_alert_chargebacks'])) ? $_POST['ppttd_alert_chargebacks'] : null,
			//'ppttd_alert_retrievals' => (isset($_POST['ppttd_alert_retrievals'])) ? $_POST['ppttd_alert_retrievals'] : null,
			// 'ppttd_alert_duplicate_transaction' => (isset($_POST['ppttd_alert_duplicate_transaction'])) ? $_POST['ppttd_alert_duplicate_transaction'] : null
		);
	}

	// Update user's meta data
	update_user_meta($user_id, 'ppttd_merchant_info', $ppttd_merchant_info);

    // Update MID table
	update_merchant_ids($user_id, $ppttd_merchant_id);
}
add_action( 'personal_options_update', 'ppttd_save_account_field' );
add_action( 'edit_user_profile_update', 'ppttd_save_account_field' );
add_action( 'user_register' , 'ppttd_save_account_field');


/**
 * Update user merchant IDs
 */
function update_merchant_ids($user_id = null, $new_merchant_ids = null) {
    if (is_null($user_id) || is_null($new_merchant_ids)) {
        return false;
    }

    global $wpdb;

    // Merchant IDs to add
    $mids_to_add = array();

    // Get current merchant IDs
    $cur_merchant_ids = get_merchant_ids($user_id);

    // Compare new/current merchant IDs to see if we need to remove any
    foreach ($cur_merchant_ids as $the_mid) {
        $check_exists = check_exists($the_mid, $new_merchant_ids, 'merchant_id');
        if ($check_exists) {
            // Setup MID data
            $add_mid = new stdClass();
            $add_mid->merchant_id = $new_merchant_ids[$check_exists]->merchant_id;
            $add_mid->merchant_name = $new_merchant_ids[$check_exists]->merchant_name;
            $add_mid->price_override = $new_merchant_ids[$check_exists]->price_override;
            $mids_to_add[] = $add_mid;
        } else {
            $wpdb->query("DELETE FROM wp_merchant_user_relationships WHERE user_id=$user_id AND merchant_id=".$the_mid->merchant_id);
        }
    }

    // Compare current/new merchant IDs to see if we need to add any
    foreach ($new_merchant_ids as $merchant_data) {
        if (!check_exists($merchant_data, $cur_merchant_ids, 'merchant_id')) {
            $add_mid = new stdClass();
            $add_mid->merchant_id = $merchant_data->merchant_id;
            $add_mid->merchant_name = $merchant_data->merchant_name;
            $add_mid->price_override = $merchant_data->price_override;
            $mids_to_add[] = $add_mid;
        }
    }

    // Loop through and save merchant information
    foreach ($mids_to_add as $add_merchant_data) {
        // Set price override
        $add_merchant_data->price_override = (isset($add_merchant_data->price_override) && $add_merchant_data->price_override !== '') ? $add_merchant_data->price_override : null;

        // Add merchant IDs
        $the_query = $wpdb->query("INSERT INTO wp_merchant_user_relationships (user_id,merchant_id,merchant_name,price_override) VALUES ($user_id,"
        .$add_merchant_data->merchant_id.",'"
        .addslashes($add_merchant_data->merchant_name)."','"
        .$add_merchant_data->price_override."') ON DUPLICATE KEY UPDATE merchant_name='"
            .addslashes($add_merchant_data->merchant_name)."',price_override='".$add_merchant_data->price_override."'");
    }

    return true;
}


/**
 * Check if MID already exists in array
 */
function check_exists($needle = null, $haystack = null, $elem_key = null) {
    if (is_null($needle) || is_null($haystack) || is_null($elem_key)) {
        return false;
    }

    foreach ($haystack as $key => $cur_element) {
        if ($cur_element->$elem_key === $needle->$elem_key) {
            return $key;
        }
    }

    return false;
}


/**
 * Show plugin account field(s)
 */
function ppttd_show_account_field( $user ) {
    global $wpdb;

    // Enqueue some scripts/styles in all environments
    wp_enqueue_script('td-admin-js', '/wp-content/plugins/transactional-data/js/admin.js', array('jquery'), null, date('y.m.d'));
    wp_enqueue_style('css-styles', '/wp-content/plugins/transactional-data/css/style.css');

	// Get merchant info
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $user->data->ID);

    // Get merchant ID
	$merchant_ids = get_merchant_ids($user->data->ID);

	// Label and table for section
	echo '<h3>Merchant IDs</h3><hr />';
	echo '<input type="button" id="cu_add_mid" name="cu_add_mid" class="button button-primary" value="Add Merchant ID">';
	echo '<table class="form-table">';

	// Show merchange ID field if administrator
	$sales_rep_id = (isset($merchant_info['ppttd_sales_rep_id']) && trim($merchant_info['ppttd_sales_rep_id']) != '') ? $merchant_info['ppttd_sales_rep_id'] : '';

    // Show merchant IDs
    if (current_user_can('add_users')) {
        echo '<tr>
            <td>
                <div id="merchant_ids">
                    <div>
                        <div class="td_div_head">Merchant Name</div>
                        <div class="td_div_head">Merchant ID</div>
                        <div class="td_div_head">Price Override</div>
                        <div class="td_div_head">Reward Points</div>
                        <div class="td_div_head">Actions</div>
                    </div>';
    
                    // Echo merchant IDs
                    foreach ($merchant_ids as $cur_mid) {
                        echo '<div>
                                    <div class="td_div_body">
                                        <input type="text" name="merchant_names[]" value="'.$cur_mid->merchant_name.'" />
                                    </div>
                                    <div class="td_div_body">
                                        <input type="text" name="merchant_ids[]" value="'.$cur_mid->merchant_id.'" />
                                    </div>
                                    <div class="td_div_body">
                                        <input type="text" name="price_overrides[]" value="'.$cur_mid->price_override.'" />
                                    </div>
                                    <div class="td_div_body">
                                        <input type="text" name="reward_points[]" value="'.get_merchant_reward_points($cur_mid->merchant_id).'" />
                                    </div>
                                    <div class="td_div_body">
                                        <input type="button" value="Delete" onclick="javascript:jQuery(this).parent().parent().remove();" class="button button-primary" />
                                    </div>
                                </div>';
                    }
    
        echo '    </div>
            </td>
        </tr>
        </table>';

        echo '<h3>Transactional Reporting Settings</h3><hr />';
        echo '<table class="form-table" style="width:100%;">';

        echo '<tr>
            <th><label for="ppttd_sales_rep_id">Sales Rep ID </label></th>           
            <td>
                <input type="text" name="ppttd_sales_rep_id" id="ppttd_sales_rep_id" value="'.$sales_rep_id.'" class="regular-text" /><br />
            </td>
        <tr>';
	}

	// Manage subscription
	$send_daily = (isset($merchant_info['ppttd_daily_transaction_report']) && $merchant_info['ppttd_daily_transaction_report'] == 'on') ? ' checked' : ' ';
	$send_weekly = (isset($merchant_info['ppttd_weekly_transaction_report']) && $merchant_info['ppttd_weekly_transaction_report'] == 'on') ? ' checked' : ' ';
	$send_monthly = (isset($merchant_info['ppttd_monthly_transaction_report']) && $merchant_info['ppttd_monthly_transaction_report'] == 'on') ? ' checked' : ' ';
	$days_of_week = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	echo '<tr>
            <th>
            	<label for="ppttd_daily_transaction_report">Transaction Reports</label>
            </th>           
            <td>
                <input type="checkbox" class="send_report" name="ppttd_daily_transaction_report" id="ppttd_daily_transaction_report"'.$send_daily.'/> <em>Email daily transaction reports</em><br/>
                <input type="checkbox" class="send_report" name="ppttd_weekly_transaction_report" id="ppttd_weekly_transaction_report"'.$send_weekly.'/> <em>Email weekly transaction reports</em><br/>
                <input type="checkbox" class="send_report" name="ppttd_monthly_transaction_report" id="ppttd_monthly_transaction_report"'.$send_monthly.'/> <em>Email monthly transaction reports</em>
            </th>
        <tr>
        <tr>
        	<th>
				<label for="ppttd_week_starts_on">Week Starts On</label>
			</th>
			<td>
				<select name="ppttd_week_starts_on">';
				foreach ($days_of_week as $cur_day) {
					$day_selected = (isset($merchant_info['ppttd_week_starts_on']) && $cur_day == $merchant_info['ppttd_week_starts_on']) ? ' selected' : '';
					echo '<option value="'.$cur_day.'"'.$day_selected.'>'.$cur_day.'</option>';
				}
	echo '		</select>
			</td>
        </tr>';

	// Close section
	echo '</table>';
}
add_action( 'show_user_profile' , 'ppttd_show_account_field' );
add_action( 'edit_user_profile' , 'ppttd_show_account_field' );


/**
 * Create Alerts page in the WP admin interface
 */
add_action( 'admin_menu', 'ppttd_alert_admin_menu' );

function ppttd_alert_admin_menu() {
	add_menu_page( 'System Alerts', 'Alerts', 'administrator', 'system-alerts', 'ppttd_alert_admin_options', 'dashicons-admin-comments', 3 );
}
function ppttd_alert_admin_options() {
	if ( !current_user_can( 'administrator' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    global $wpdb;
	
	
	if( isset($_POST['alert_type']) && isset($_POST['alert_text']) && trim($_POST['alert_type']) !== '' && trim($_POST['alert_text']) !== ''  ){
		$alert_type = $_POST['alert_type'];
		$alert_text = $_POST['alert_text'];
		
		$userQuery = $wpdb->get_results( "SELECT ID FROM wp_users" );
		
		$length = 10;
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		
		foreach( $userQuery as $userRow ){
			$wpdb->query("INSERT INTO wp_ppttd_batch_alerts (user_id, alert_type, alert_text, alert_batch_id, system) VALUES ('".$userRow->ID."', '".$alert_type."', '".$alert_text."', '".$randomString."', '1')");
			//echo $randomString.' - '.$alert_text.' - '.$alert_type;
		}
	}
	
	$alert_body = '';
	
	// Query for any new alerts
	$alertQuery = $wpdb->get_results( "SELECT * FROM wp_ppttd_batch_alerts WHERE `system`=1 GROUP BY alert_batch_id ORDER BY date_created DESC;" );
	
	$alertBoxes =	'<table style="margin-top:40px;">';
	$alertBoxes .=	'<tr>'.
						'<th>Date</th><th width="60">Type</th><th>Alert Message</th>'.
					'</tr>';
	
	// Set up the alerts and display them below.
	foreach( $alertQuery as $alertRow ){
		if( $alertRow->alert_type == "batch_below" || $alertRow->alert_type == "days_since" || $alertRow->alert_type == "red" ){
			$alert_color = "alert-red";
		} elseif( $alertRow->alert_type == "yellow" ) {
			$alert_color = "alert-yellow";
		} else {
			$alert_color = "alert-green";
		}
		$alertBoxes .=	'<tr>'.
							'<td>'.date('m/d/Y - g:i a', strtotime($alertRow->date_created)).'</td><td align="right" style="padding-right:10px;">'.ucfirst($alertRow->alert_type).'</td><td>'.'<strong>'.$alertRow->alert_text.'</strong>'.'</td>'.
						'</tr>';
	}
	$alertBoxes .=	'</table>';
	
	$alert_body .=	'<div class="wrap">'.
						'<h2>System Alerts</h2>'.
						'<p>'.$stuff.'</p>'.
						'<form id="system_alert_form" method="POST" action="">'.
							'<select name="alert_type" id="alert_type" required>'.
								'<option disabled selected>Alert Type</option>'.
								'<option value="red">Red</option>'.
								'<option value="yellow">Yellow</option>'.
								'<option value="green">Green</option>'.
							'</select> '.
							'<input name="alert_text" id="alert_text" type="text" placeholder="Alert Message" style="width:100%; max-width:600px;" required /> '.
							'<input type="submit" class="button button-primary" value="Create Alert" />'.
						'</form>'.
						$alertBoxes.
					'</div>';
	
	echo $alert_body;
}
/**
 * Admin alert page javascript
 */
function ppttd_alert_js() {
    wp_enqueue_script( 'ppttd_alerts','/wp-content/plugins/transactional-data/js/ppttd_alerts.js', null, date('y.m.d'));
}
add_action('admin_enqueue_scripts', 'ppttd_alert_js');

/**
 * Show plugin account field(s)
 */
function ppttd_show_alert_settings( $user ) {
    // Enqueue some scripts/styles in all environments
    wp_enqueue_script('td-admin-js', '/wp-content/plugins/transactional-data/js/admin.js', array('jquery'), date('y.m.d'));
    wp_enqueue_style('css-styles', '/wp-content/plugins/transactional-data/css/style.css');

	// Get merchant info
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $user->data->ID);

    // Get merchant ID
	$merchant_id = (isset($merchant_info['ppttd_merchant_id'])) ? $merchant_info['ppttd_merchant_id'] : null;
	
    if ( current_user_can('add_users') ) {
		echo "<h3>Alert Settings</h3><hr />";
		
		$ppttd_enable_alerts = (isset($merchant_info['ppttd_enable_alerts']) && $merchant_info['ppttd_enable_alerts'] == 'on') ? ' checked' : ' ';
		//$ppttd_alert_chargebacks = (isset($merchant_info['ppttd_alert_chargebacks']) && $merchant_info['ppttd_alert_chargebacks'] == 'on') ? ' checked' : ' ';
		//$ppttd_alert_retrievals = (isset($merchant_info['ppttd_alert_retrievals']) && $merchant_info['ppttd_alert_retrievals'] == 'on') ? ' checked' : ' ';
		//$ppttd_alert_duplicate_transaction = (isset($merchant_info['ppttd_alert_duplicate_transaction']) && $merchant_info['ppttd_alert_duplicate_transaction'] == 'on') ? ' checked' : ' ';
		$ppttd_alert_batch_above = ( isset($merchant_info['ppttd_alert_batch_above']) ) ? $merchant_info['ppttd_alert_batch_above'] : '';
		$ppttd_alert_batch_below = ( isset($merchant_info['ppttd_alert_batch_below']) ) ? $merchant_info['ppttd_alert_batch_below'] : '';
		$ppttd_alert_no_processing = ( isset($merchant_info['ppttd_alert_no_processing']) ) ? $merchant_info['ppttd_alert_no_processing'] : '';
		echo	'<table class="form-table">'.
					'<tr>'.
						'<th>'.
							'<label for="ppttd_enable_alerts">Alerts enabled</label>'.
						'</th>'.
						'<td>'.
							'<input type="checkbox" name="ppttd_enable_alerts" id="ppttd_enable_alerts" '.$ppttd_enable_alerts.' />'.
						'</td>'.
					'</tr>'.
					'<tr>'.
						'<th>'.
							'<label for="ppttd_alert_batch_above">Batch above</label>'.
						'</th>'.
						'<td>'.
							'$<input type="text" name="ppttd_alert_batch_above" id="ppttd_alert_batch_above" value="'.$ppttd_alert_batch_above.'" />'.
						'</td>'.
					'</tr>'.
					'<tr>'.
						'<th>'.
							'<label for="ppttd_alert_batch_below">Batch below</label>'.
						'</th>'.
						'<td>'.
							'$<input type="text" name="ppttd_alert_batch_below" id="ppttd_alert_batch_below" value="'.$ppttd_alert_batch_below.'" />'.
						'</td>'.
					'</tr>'.
					'<tr>'.
						'<th>'.
							'<label for="ppttd_alert_no_processing">No processing in</label>'.
						'</th>'.
						'<td>'.
							'<input type="text" name="ppttd_alert_no_processing" id="ppttd_alert_no_processing" value="'.$ppttd_alert_no_processing.'" /> days'.
						'</td>'.
					'</tr>'.

					'<tr>'.
						'<th>'.
							'<label>Alerts (Coming Soon)</label>'.
						'</th>'.
						/*
'<td>'.
							'<input type="checkbox" name="ppttd_alert_chargebacks" id="ppttd_alert_chargebacks" '.$ppttd_alert_chargebacks.' /> <em>Chargebacks</em><br />'.
							'<input type="checkbox" name="ppttd_alert_retrievals" id="ppttd_alert_retrievals" '.$ppttd_alert_retrievals.' /> <em>Retrievals</em><br />'.
							'<input type="checkbox" name="ppttd_alert_duplicate_transaction" id="ppttd_alert_duplicate_transaction" '.$ppttd_alert_duplicate_transaction.' /> <em>Duplicate Transaction Found</em>'.
						'</td>'.
*/
						'<td>'.
							'<em>Chargebacks</em><br />'.
							'<em>Retrievals</em><br />'.
							'<em>Duplicate Transaction Found</em>'.
						'</td>'.
					'</tr>'.
				'</table>';
	}
}
add_action( 'show_user_profile' , 'ppttd_show_alert_settings' );
add_action( 'edit_user_profile' , 'ppttd_show_alert_settings' );

/**
 * Embed shortcode for transactional data
 */
function ppttd_data($attributes) {
    // Set merchant Id options
    $merchant_id_options = '';

	// Enqueue some scripts/styles in all environments
    wp_enqueue_script('init-charts', '/wp-content/plugins/transactional-data/js/init_chart.js', null, date('y.m.d'));

    // Enqueue some scripts/styles only in local environment
	if (stripos($_SERVER['HTTP_HOST'], 'local.') !== false) {
    	wp_enqueue_script('flot-charts', '/wp-content/themes/ppmlayout/assets/plugins/flot/jquery.flot.js');
    	wp_enqueue_script('flot-charts-time', '/wp-content/themes/ppmlayout/assets/plugins/flot/jquery.flot.time.js', array('flot-charts'));
    	wp_enqueue_script('flot-charts-resize', '/wp-content/themes/ppmlayout/assets/plugins/flot/jquery.flot.resize.js');
    	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
    	wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
    	wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

    // Set merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

	// Check if user is logged in
	if (!is_user_logged_in()) {
		return '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your transactional reporting.</em>
				</div>
			</div>';
	}

    // Check if merchant ID is null
	if (is_null($merchant_id)) {
		return '<div class="row-fluid">
				<div class="span12">
		            <em>You do not have a merchant ID set for your account. Please contact support to get one added, or modify your profile settings and add it under "Transactional Reporting".</em>
				</div>
			</div>';
	}

    $show_by_source = (isset($_GET['source'])) ? $_GET['source'] : 'daily';

    if( user_in_group(9) || in_array('ppt-user', wp_get_current_user()->roles) ){
	    $admin_options = '<form id="search_merchant_form" >
							<input type="text" id="search_merchant_id" placeholder="Search MID" />
							<input type="submit" value="" />
						</form>';
    } else {
        $admin_options = '';
    }
    
    if( user_in_group(7) || user_in_group(9) || in_array('ppt-user', wp_get_current_user()->roles) ){
		$export_data =	'<a href="#" class="export-btn btn" id="export_to_csv">Export to CSV</a><iframe id="exportIframe" src="" style="display:none;visibility:hidden;"></iframe>';
    } else {
        $export_data = '';
    }

	return '<script>
	        var show_by_source = "'.$show_by_source.'";
			window.onload = function() {
				Charts.init();
			};
			</script>
			<div class="ppttd-container">
				<div class="row-fluid">
					<div class="control-group">
						<div class="controls">
							<div id="form-date-range" class="btn">
								<i class="icon-calendar"></i>
								<span>'.date('F, j Y', strtotime('-6 days')).'-'.date('F, j Y').'</span> 
								<b class="caret"></b>
							</div>
							<select id="show_data_by" style="display:none;">
								<option value="daily">Show Daily</option>
								<option value="weekly">Show Weekly</option>
								<option value="monthly">Show Monthly</option>
								<option value="yearly">Show Yearly</option>
							</select>
							'.$export_data.'
							'.$merchant_id_options.'
							'.$admin_options.'
						</div>
					</div>
				</div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="portlet box tabbable">
                            <div class="portlet-title" style="height:27px;"></div>
                            <div class="portlet-body">
                                <div class="tabbable portlet-tabs">
                                    <ul class="nav nav-tabs transaction-tabs" id="graph-tabs">
                                        <li><a href="#average_sale_tab" data-toggle="tab">Average Sale</a></li>
                                        <li class="active"><a href="#total_sales_tab" data-toggle="tab">Total Sales</a></li>
                                    </ul>
                                    <div class="tab-content" id="sales-data">
                                        <div id="select-transaction-type"></div>
                                        <select id="the_graph_type">
                                            <option value="bar">Show Bar Graph</option>
                                            <option value="line">Show Line Graph</option>
                                        </select>
                                        <div class="tab-pane active" id="total_sales_tab">
                                            <div id="sales_data" class="chart"></div>
                                            <div class="sales-data-graph ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="average_sale_tab">
                                            <div id="average_sale" class="chart"></div>
                                            <div class="average-sale-graph ppttd-data-loading">Loading...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="portlet box tabbable">
                            <div class="portlet-title" style="height:27px;"></div>
                            <div class="portlet-body">
                                <div class="tabbable portlet-tabs">
                                    <ul class="nav nav-tabs transaction-tabs" id="table-tabs">
                                        <li><a href="#batch_detail_tab" data-toggle="tab">Batch Detail</a></li>
                                        <li><a href="#returning_customers_tab" data-toggle="tab">Returning Customers</a></li>
                                        <li><a href="#retrievals_tab" data-toggle="tab">Retrievals</a></li>
                                        <li><a href="#chargebacks_tab" data-toggle="tab">Chargebacks</a></li>
                                        <li><a href="#settlements_tab" data-toggle="tab">Settlements</a></li>
                                        <li class="active"><a href="#transaction_detail_tab" data-toggle="tab">Transaction Detail</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="transaction_detail_tab">
                    						<table class="table table-striped table-hover table-bordered" id="show_transactions">
                    							<thead>
                    								<tr>
                    									<th>Amount</th>
                    									<th>Card Type</th>
                    									<th>Time</th>
                    									<th>Last Four</th>
                    								</tr>
                    							</thead>
                    							<tbody id="transactions_table"></tbody>
                    						</table>
                    						<div class="transactions-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="returning_customers_tab">
                    						<table class="table table-striped table-hover table-bordered" id="show_returning_customers">
                    							<thead>
                    								<tr>
                    									<th>Card Holder</th>
                    									<th>Number of Visits</th>
                    									<th>Total Sales</th>
                    								</tr>
                    							</thead>
                    							<tbody id="returning_customers_table"></tbody>
                    						</table>
                    						<div class="returning-customers-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="batch_detail_tab">
                    						<table class="table table-striped table-hover table-bordered" id="show_batches">
                    							<thead>
                    								<tr>
                    									<th>Batch Date</th>
                    									<th>Batch ID</th>
                    									<th># Sales</th>
                    									<th># Returns</th>
                    									<th># Transactions</th>
                    									<th>Sales Volume</th>
                    									<th>Return Volume</th>
                    									<th>Total Volume</th>
                    								</tr>
                    							</thead>
                    							<tbody id="show_batches_table"></tbody>
                    						</table>
                    						<div class="batch-detail-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="chargebacks_tab">
                                        	<span style="position: absolute; display: block; margin-bottom:.5em; visibility: visible; z-index: 999999; font-size: .75rem;">Have a chargeback? <a href="https://79f7a05e1bf55ecda2cf-90bf115d91d2675e58839f40166e83c0.ssl.cf2.rackcdn.com/2015.03.31.10.41.53.85_chargeback-sample.pdf" >Click here</a> to view a sample chargeback letter you will be receiving in the mail containing the necessary steps you must take to resolve this issue.</span>
                    						<table class="table table-striped table-hover table-bordered" id="show_chargebacks">
                    							<thead>
                    								<tr>
                    									<th>Date Received</th>
                    									<th>Transaction Date</th>
                    									<th>Amount</th>
                    									<th>Cardholder Number</th>
                    								</tr>
                    							</thead>
                    							<tbody id="chargebacks_table"></tbody>
                    						</table>
                    						<div class="chargebacks-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="retrievals_tab">
                    						<table class="table table-striped table-hover table-bordered" id="show_retrievals">
                    							<thead>
                    								<tr>
                    									<th>Date Received</th>
                    									<th>Transaction Date</th>
                    									<th>Amount</th>
                    									<th>Cardholder Number</th>
                    								</tr>
                    							</thead>
                    							<tbody id="retrievals_table"></tbody>
                    						</table>
                    						<div class="retrievals-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                        <div class="tab-pane" id="settlements_tab">
                    						<table class="table table-striped table-hover table-bordered" id="show_settlements">
                    							<thead>
                    								<tr>
                    									<th>Deposit Date</th>
                    									<th>Transit Number</th>
                    									<th>Amount</th>
                    								</tr>
                    							</thead>
                    							<tbody id="show_settlements_table"></tbody>
                    						</table>
                    						<div class="settlements-loading ppttd-data-loading">Loading...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row-fluid text-center">
					<div class="span12">
						<small>Data may vary slightly due to batch cycles and associated fees. If you have questions regarding your data, please contact support. <a href="mailto:success@saltsha.com" target="_BLANK">success@saltsha.com</a> | <a href="tel:+15742690792">(574) 269-0792.</small>
					</div>
				</div>
			</div>';
}
add_shortcode('transactional_data', 'ppttd_data');


/**
 * Get batch transactions
 */
function get_batch_transactions() {
    global $wpdb;

    // Get batch ID
    $uniq_batch_id = $_REQUEST['uniq_batch_id'];

    // Get transactions
    $batch_transactions = $wpdb->get_results("SELECT card_type,amt,transaction_time,card_lastfour FROM  ".$wpdb->prefix."ppttd_transactionlisting WHERE uniq_batch_id = '".$uniq_batch_id."' ORDER BY transaction_time");

    // Echo transactions
    echo json_encode( array( 'status' => 'success', 'transactions' => $batch_transactions ) );

    die();
}
add_action('wp_ajax_get_batch_transactions', 'get_batch_transactions');


/**
 * 
 */
function manage_subscription() {
    // Enqueue some scripts/styles in all environments
    wp_enqueue_script('manage-subscription', '/wp-content/plugins/transactional-data/js/td_subscription.js', array('jquery'), date('y.m.d'));

    // Enqueue some scripts/styles only in local environment
    if (stripos($_SERVER['HTTP_HOST'], 'local.') !== false) {
	    wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

	// Get merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);

    // Get merchant ID
	$merchant_id = (isset($merchant_info['ppttd_merchant_id'])) ? $merchant_info['ppttd_merchant_id'] : null;

	// Label and table for section
	$subscription_data .= '
	<div class="span6">
		<div class="form_portlet">
			<div class="portlet_title">
				<h3>Transactional Reporting Subscriptions</h3>
			</div>
			<div class="portlet_body acc">';


    // Check if merchant data is array or not
    if (gettype($merchant_id) != 'array') {
        $merchant_id = explode(',', $merchant_id);
    }

    // If multiple merchant IDs, show all
    if (count($merchant_id) > 1) {
        // Loop through merchant IDs
        foreach ($merchant_id as $cur_merchant_id => $cur_merchant_value) {
            // echo $cur_merchant_id.' / '.$cur_merchant_value.'<br/>';
            $cur_merchant_name = (trim($cur_merchant_value) == "" || $cur_merchant_id == $cur_merchant_value) ? $cur_merchant_id : $cur_merchant_value.' ('.$cur_merchant_id.')';
            $cur_merchant_name = (strlen($cur_merchant_id) >= 8) ? $cur_merchant_name : '';
            $cur_merchant_id = (strlen($cur_merchant_id) >= 8) ? str_pad($cur_merchant_id, 16, '0', STR_PAD_LEFT).' ('.$cur_merchant_value.')' : $cur_merchant_value;
            $show_merchant_ids .= $cur_merchant_id.' <br />';
        }
        $show_merchant_ids = substr($show_merchant_ids, 0, -2);
        $show_merchant_ids .= '</div>';
    } else {
        
        // Single merchant ID
        $show_merchant_ids = (array_key_exists(0, $merchant_id)) ? $merchant_id[0] : @array_shift(array_keys($merchant_id));
        $show_merchant_ids = str_pad($show_merchant_ids, 16, '0', STR_PAD_LEFT);
    }

			$subscription_data .= '
				<div class="row-fluid padded-row">
					<div class="span4"><label for="ppttd_merchant_id">Merchant IDs </label></div>           
					<div class="span8"> '.$show_merchant_ids.' </div>
				</div>';
		
		
			// Manage subscription
			$send_daily = (isset($merchant_info['ppttd_daily_transaction_report']) && $merchant_info['ppttd_daily_transaction_report'] == 'on') ? ' checked' : ' ';
			$send_weekly = (isset($merchant_info['ppttd_weekly_transaction_report']) && $merchant_info['ppttd_weekly_transaction_report'] == 'on') ? ' checked' : ' ';
			$send_monthly = (isset($merchant_info['ppttd_monthly_transaction_report']) && $merchant_info['ppttd_monthly_transaction_report'] == 'on') ? ' checked' : ' ';
			$days_of_week = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
			$subscription_data .= '
				<div class="row-fluid padded-row">
		            <div class="span4">
		            	<label for="ppttd_daily_transaction_report">Transaction Reports</label>
		            </div>           
		            <div class="span8">
		                <div id="saving_subscription"><em>Saving...</em></div>
		                <input type="checkbox" class="send_report" name="ppttd_daily_transaction_report" id="ppttd_daily_transaction_report"'.$send_daily.'/> <em>Email daily transaction reports</em><br/>
		                <input type="checkbox" class="send_report" name="ppttd_weekly_transaction_report" id="ppttd_weekly_transaction_report"'.$send_weekly.'/> <em>Email weekly transaction reports</em><br/>
		                <input type="checkbox" class="send_report" name="ppttd_monthly_transaction_report" id="ppttd_monthly_transaction_report"'.$send_monthly.'/> <em>Email monthly transaction reports</em>
		            </div>
		        </div>
		        <div class="row-fluid padded-row">
		        	<div class="span4">
						<label for="ppttd_week_starts_on">Week Starts On</label>
					</div>
					<div class="span8">
						<div style="position:relative;">
							<div id="saving_week_starts_on"><em>Saving...</em></div>
							<select name="ppttd_week_starts_on" id="ppttd_week_starts_on">';
							foreach ($days_of_week as $cur_day) {
								$day_selected = (isset($merchant_info['ppttd_week_starts_on']) && $cur_day == $merchant_info['ppttd_week_starts_on']) ? ' selected' : '';
								$subscription_data .= '						<option value="'.$cur_day.'"'.$day_selected.'>'.$cur_day.'</option>';
							}
			$subscription_data .= '	</select>
						</div>
					</div>
		        </div>';
	$subscription_data .= '
			</div>
		</div>
	</div>
</div>
	';
	
	if(isset($merchant_info['ppttd_enable_alerts']) && $merchant_info['ppttd_enable_alerts'] == 'on') {
		$ppttd_enable_alerts = ' checked';
		$ppttd_enable_alerts_checked = '';
	} else {
		$ppttd_enable_alerts = '';
		$ppttd_enable_alerts_checked = 'style="display: none;"';
	}
	// $ppttd_alert_chargebacks = (isset($merchant_info['ppttd_alert_chargebacks']) && $merchant_info['ppttd_alert_chargebacks'] == 'on') ? ' checked' : ' ';
	// $ppttd_alert_retrievals = (isset($merchant_info['ppttd_alert_retrievals']) && $merchant_info['ppttd_alert_retrievals'] == 'on') ? ' checked' : ' ';
	// $ppttd_alert_duplicate_transaction = (isset($merchant_info['ppttd_alert_duplicate_transaction']) && $merchant_info['ppttd_alert_duplicate_transaction'] == 'on') ? ' checked' : ' ';
	$ppttd_alert_batch_above = ( isset($merchant_info['ppttd_alert_batch_above']) ) ? $merchant_info['ppttd_alert_batch_above'] : '';
	$ppttd_alert_batch_below = ( isset($merchant_info['ppttd_alert_batch_below']) ) ? $merchant_info['ppttd_alert_batch_below'] : '';
	$ppttd_alert_no_processing = ( isset($merchant_info['ppttd_alert_no_processing']) ) ? $merchant_info['ppttd_alert_no_processing'] : '';
	
	$subscription_data .= 	'<div class="row-fluid">'.
								'<div class="span12">'.
						'<form id="alertsForm">'.
							
								'<div class="form_portlet">'.
									'<div class="portlet_title">'.
										'<h3>Alert Settings</h3>'.
									'</div>'.
									'<div class="portlet_body acc">'.
										'<table class="form-table">'.
										'<tr>'.
											'<th>'.
												'<label for="ppttd_enable_alerts">Alerts enabled</label>'.
											'</th>'.
											'<td>'.
												'<input type="checkbox" name="ppttd_enable_alerts" id="ppttd_enable_alerts" '.$ppttd_enable_alerts.' />'.
											'</td>'.
										'</tr>'.
										'<tr><td colspan="2"><table width="100%" id="alert_settings_table" '.$ppttd_enable_alerts_checked.' >'.
											'<tr>'.
												'<th>'.
													'<label for="ppttd_alert_batch_above">Batch above</label>'.
												'</th>'.
												'<td>'.
													'<input type="text" class="input-white dollar_before" name="ppttd_alert_batch_above" id="ppttd_alert_batch_above" value="'.$ppttd_alert_batch_above.'" />'.
												'</td>'.
											'</tr>'.
											'<tr>'.
												'<th>'.
													'<label for="ppttd_alert_batch_below">Batch below</label>'.
												'</th>'.
												'<td>'.
													'<input type="text" class="input-white dollar_before" name="ppttd_alert_batch_below" id="ppttd_alert_batch_below" value="'.$ppttd_alert_batch_below.'" />'.
												'</td>'.
											'</tr>'.
											'<tr>'.
												'<th>'.
													'<label for="ppttd_alert_no_processing">No processing in</label>'.
												'</th>'.
												'<td style="vertical-align:middle !important;">'.
													'<input type="text" class="input-white days_after" name="ppttd_alert_no_processing" id="ppttd_alert_no_processing" value="'.$ppttd_alert_no_processing.'" />'.
												'</td>'.
											'</tr>'.
											'<tr>'.
												'<th>'.
													'<label>Alerts (Coming Soon)</label>'.
												'</th>'.
											/*
												'<td>'.
													'<input type="checkbox" name="ppttd_alert_chargebacks" id="ppttd_alert_chargebacks" '.$ppttd_alert_chargebacks.' /> <em>Chargebacks</em><br />'.
													'<input type="checkbox" name="ppttd_alert_retrievals" id="ppttd_alert_retrievals" '.$ppttd_alert_retrievals.' /> <em>Retrievals</em><br />'.
													'<input type="checkbox" name="ppttd_alert_duplicate_transaction" id="ppttd_alert_duplicate_transaction" '.$ppttd_alert_duplicate_transaction.' /> <em>Duplicate Transaction Found</em>'.
												'</td>'.
			*/
												'<td>'.
													'<em>Chargebacks</em><br />'.
													'<em>Retrievals</em><br />'.
												'</td>'.
											'</tr>'.
										'</table></td></tr>'.
										'</table>'.
										'</div>';

	// Close section
	$subscription_data .= '<input id="acc_submit" type="submit" class="right" value="Save Settings"/></form>
						</div>
					</div>
				</div>';

	// Return data
	return $subscription_data;
}
add_shortcode('transactional_data_subscription', 'manage_subscription');


/**
 * Change email subscription(s)
 */
function ppttd_change_subscription($params) {
	// Get current merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);

	// Set meta info
	if ($_REQUEST['subscribe'] === 'true') {
		$merchant_info[$_REQUEST['subscription_timeframe']] = 'on';
	} else {
		unset($merchant_info[$_REQUEST['subscription_timeframe']]);
	}

	// Update user meta
	update_user_meta($current_user->ID, 'ppttd_merchant_info', $merchant_info);
	die();
}
add_action('wp_ajax_change_subscription', 'ppttd_change_subscription');
add_action('wp_ajax_nopriv_change_subscription', 'ppttd_change_subscription');


/**
 * Change week starts on day
 */
function ppttd_change_week_starts_on($params) {
	// Get current merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);

	// Set week starts on meta
	$merchant_info['ppttd_week_starts_on'] = $_REQUEST['week_starts_on'];

	// Update user meta
	update_user_meta($current_user->ID, 'ppttd_merchant_info', $merchant_info);
	die();
}
add_action('wp_ajax_change_week_starts_on', 'ppttd_change_week_starts_on');
add_action('wp_ajax_nopriv_change_week_starts_on', 'ppttd_change_week_starts_on');


/**
 * Export Data
 */
function ppttd_export_to_csv() {
	// Get current merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);

	$data = $_REQUEST['the_data'];

	echo $data;

	die();
}
add_action('wp_ajax_export_to_csv', 'ppttd_export_to_csv');
add_action('wp_ajax_nopriv_export_to_csv', 'ppttd_export_to_csv');

/**
 * Update Alert settings
 */
function ppttd_update_alert_settings($params) {
	// Get current merchant info
	$current_user = wp_get_current_user();
	$merchant_info = get_the_author_meta('ppttd_merchant_info', $current_user->ID);

	// Set week starts on meta
	if( $_REQUEST['ppttd_enable_alerts'] === 'true' ){
		$merchant_info['ppttd_enable_alerts'] = 'on';
		
		/*
if( $_REQUEST['ppttd_alert_chargebacks'] === 'true' ){
			$merchant_info['ppttd_alert_chargebacks'] = 'on';
		} else {
			$merchant_info['ppttd_alert_chargebacks'] = '';
		}
*/
		
		/*
if( $_REQUEST['ppttd_alert_retrievals'] === 'true' ){
			$merchant_info['ppttd_alert_retrievals'] = 'on';
		} else {
			$merchant_info['ppttd_alert_retrievals'] = '';
		}
*/
		
		/*
if( $_REQUEST['ppttd_alert_duplicate_transaction'] === 'true' ){
			$merchant_info['ppttd_alert_duplicate_transaction'] = 'on';
		} else {
			$merchant_info['ppttd_alert_duplicate_transaction'] = '';
		}
*/
		
		$merchant_info['ppttd_alert_batch_above'] = $_REQUEST['ppttd_alert_batch_above'];
		$merchant_info['ppttd_alert_batch_below'] = $_REQUEST['ppttd_alert_batch_below'];
		$merchant_info['ppttd_alert_no_processing'] = $_REQUEST['ppttd_alert_no_processing'];
	} else {
		$merchant_info['ppttd_enable_alerts'] = '';
		
		//$merchant_info['ppttd_alert_chargebacks'] = '';
		//$merchant_info['ppttd_alert_retrievals'] = '';
		//$merchant_info['ppttd_alert_duplicate_transaction'] = '';
		$merchant_info['ppttd_alert_batch_above'] = '';
		$merchant_info['ppttd_alert_batch_below'] = '';
		$merchant_info['ppttd_alert_no_processing'] = '';
	}

	// Update user meta
	update_user_meta($current_user->ID, 'ppttd_merchant_info', $merchant_info);
	die();
}
add_action('wp_ajax_update_alert_settings', 'ppttd_update_alert_settings');
add_action('wp_ajax_nopriv_update_alert_settings', 'ppttd_update_alert_settings');

/**
 * Add custom merchant ID field to user admin section
 */
function ppttd_new_user_custom_fields( $buffer ) {
    if (!isset($_GET['user_id'])) {
        // Enqueue some scripts/styles in all environments
        wp_enqueue_script('td-admin-js', '/wp-content/plugins/transactional-data/js/admin.js', array('jquery'), date('y.m.d'));
        wp_enqueue_style('css-styles', '/wp-content/plugins/transactional-data/css/style.css');

    	$input_html = '<table class="form-table">
            <tr>
                <td>
                    <input type="button" id="cu_add_mid" name="cu_add_mid" class="button button-primary" value="Add Merchant ID">
                    <div id="merchant_ids">
                        <div>
                            <div class="td_div_head">Merchant Name</div>
                            <div class="td_div_head">Merchant ID</div>
                            <div class="td_div_head">Price Override</div>
                            <div class="td_div_head">Actions</div>
                        </div>
                        <div>
                            <div class="td_div_body">
                                <input type="text" name="merchant_names[]" placeholder="Merchant Name" />
                            </div>
                            <div class="td_div_body">
                                <input type="text" name="merchant_ids[]" placeholder="Merchant ID" />
                            </div>
                            <div class="td_div_body">
                                <input type="text" name="price_override[]" placeholder="Price Override" />
                            </div>
                            <div class="td_div_body">
                                <input type="button" value="Delete" onclick="javascript:jQuery(this).parent().parent().remove();" class="button button-primary" />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>';

        // Replace text in buffer
    	$buffer = preg_replace( '~<label\s+for="role">(.*?)</tr>~ims', '<label for="role">$1</tr><tr class="form-field"><th colspan="2" style="padding-bottom:0px !important;margin:0px !important;"><h3 style="margin-bottom:0px !important;">Merchant IDs</h3></th></tr><tr><th colspan="2" style="padding-top:0px !important;">'.$input_html.'</th></tr>', $buffer );
    }

	return $buffer;
}


/**
 * Add admin page buffer
 */
function ppttd_new_user_buffer_start() {
	ob_start("ppttd_new_user_custom_fields");
}
add_action('admin_head', 'ppttd_new_user_buffer_start');


/**
 * End admin page buffer
 */
function new_user_buffer_end() {
	ob_end_flush();
}
add_action('admin_footer', 'new_user_buffer_end');


/**
 * Get transactional data
 */
function get_transactional_data() {
    global $wpdb;

	// Get start and end dates
	$start_date = isset($_REQUEST['start']) ? date('Y-m-d', strtotime(preg_replace('/\([^)]+\)/','', $_REQUEST['start']))) : date('Y-m-d', strtotime('-7 days'));
	$end_date = isset($_REQUEST['end']) ? date('Y-m-d', strtotime(preg_replace('/\([^)]+\)/','', $_REQUEST['end']))) : date('Y-m-d', time());

    // If no merchant ID in $_REQUEST, show default
    if (isset($_REQUEST['the_merchant_id']) && trim($_REQUEST['the_merchant_id']) !== '') {
        // Get merchant ID from $_REQUEST data
        $merchant_id = str_pad($_REQUEST['the_merchant_id'], 16, '0', STR_PAD_LEFT);
    } else {
    	// Get merchant ID from database
    	$merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;
    }

	// Setup card type array
	$transactional_data = array();
	$returning_customers = array();
	$merchant_batches = array();
	$daily_totals = array();
	$card_types = array();
	array_push($card_types, 'all_cards');

	// Send merchant query
	if (is_null($merchant_id)) {
    	echo json_encode(array('status' => 'error', 'message' => 'There are no merchant IDs associated with your account. Please login and add one or contact support.'));
    	die();
    } else {
    	// Get unique transactions
    	$transaction_batches = $wpdb->get_results("SELECT uniq_batch_id,total_volume,total_trans,total_purch_amt,total_purch_trans,total_return_amt,total_return_trans,batch_date FROM  ".$wpdb->prefix."ppttd_batchlisting WHERE  merchant_id = '".$merchant_id."' AND batch_date BETWEEN '".$start_date."' AND '".$end_date." 23:59:59' ORDER BY batch_date DESC");

		// Get transactions for each batch
		foreach ($transaction_batches as $cur_batch) {
    		// Add batch data
    		$merchant_batches[] = $cur_batch;

			// Set daily transaction amounts
			$batch_date = date('m-d-Y', strtotime($cur_batch->batch_date));
			$daily_totals[$batch_date] = (isset($daily_totals[$batch_date])) ? $daily_totals[$batch_date]+$cur_batch->total_volume : 0;

			// Get transactions
			$get_transactional_data = $wpdb->get_results("SELECT card_type,amt,transaction_time,card_lastfour FROM  ".$wpdb->prefix."ppttd_transactionlisting WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."' ORDER BY transaction_time");

            // Get card types
			foreach ($get_transactional_data as $cur_transaction) {
			    // Set card identifier, card type and card last four digits
			    $card_identifier = $cur_transaction->card_type.'-'.$cur_transaction->card_lastfour;

                // Check that vars are set
                if (!isset($returning_customers[$card_identifier]['total_sales'])) {
                    $returning_customers[$card_identifier]['total_sales'] = 0;
                }
                if (!isset($returning_customers[$card_identifier]['number_of_visits'])) {
                    $returning_customers[$card_identifier]['number_of_visits'] = 0;
                }

			    // Check for customer loyalty
			    if (!array_key_exists($card_identifier, $returning_customers)) {
    			    $returning_customers[$card_identifier] = array();
			    }

                // Increment total number of sales AND total sales
			    $returning_customers[$card_identifier]['number_of_visits']++;
			    $returning_customers[$card_identifier]['total_sales'] = number_format(floatval($returning_customers[$card_identifier]['total_sales'])+floatval($cur_transaction->amt), 2);

			    // Add transaction to array
			    $cur_transaction->amt = number_format($cur_transaction->amt, 2);
				$transactional_data[] = $cur_transaction;
				if (!in_array($cur_transaction->card_type, $card_types)) {
					array_push($card_types, $cur_transaction->card_type);
				}
			}
		}

        // Get chargebacks
        $chargeback_data = $wpdb->get_results("SELECT cardholder_number,case_amount,transaction_date,date_received FROM  ".$wpdb->prefix."ppttd_chargebacks WHERE merchant_number='".$merchant_id."' AND date_received BETWEEN '".$start_date."' AND '".$end_date."'");

        // Get retrievals
        $retrieval_data = $wpdb->get_results("SELECT cardholder_number,case_amount,transaction_date,date_received FROM  ".$wpdb->prefix."ppttd_retrievals WHERE merchant_number='".$merchant_id."' AND date_received BETWEEN '".$start_date."' AND '".$end_date."'");

        // Get settlements
        $settlement_data = $wpdb->get_results("SELECT deposit_date,transit_number,amount_to_clear FROM  ".$wpdb->prefix."ppttd_settlement WHERE merchant_number='".$merchant_id."' AND deposit_date BETWEEN '".$start_date."' AND '".$end_date."'");
	}

    // Remove from returning customers if only one visit
    foreach ($returning_customers as $card_holder => $card_data) {
        if ($card_data['number_of_visits'] == 1) {
            unset($returning_customers[$card_holder]);
        }
    }

	// Echo data
	if( isset($_REQUEST['export_data']) && $_REQUEST['export_data']==true ){
        // Define exports folder
        $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
		
		// Set start date, end date, and file name
		$start	= date('m-d-Y', strtotime($_REQUEST['start']));
		$end	= date('m-d-Y', strtotime($_REQUEST['end']));
		$filename = $merchant_id."-".$start."-".$end.".csv";
		$file_uri = $folder.$filename;
		
		// Set output file
		$output = fopen($file_uri,'w+');		
		
		// Add header line to CSV
		fputcsv($output, array('Card Type','Card Last Four','Transaction Amount','Transaction Time'));
		
		// Add each row to CSV
		foreach($transactional_data as $row) {
		    fputcsv( $output, array('card_type' => $row->card_type, 'card_lastfour' => $row->card_lastfour, 'amount' => $row->amt, 'transaction_time' => $row->transaction_time ) );
		}

        // Close CSV file
		fclose($output);

        // Echo success
		echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );
		
		
	} else {
		echo json_encode( array( 'card_types' => $card_types, 'data' => $transactional_data, 'daily_totals' => $daily_totals, 'returning_customers' => $returning_customers, 'batches' => $merchant_batches, 'chargebacks' => $chargeback_data, 'retrievals' => $retrieval_data, 'settlements' => $settlement_data ) );
	}
	die();
}
add_action( 'wp_ajax_get_transactional_data', 'get_transactional_data' );
add_action( 'wp_ajax_nopriv_get_transactional_data', 'get_transactional_data' );


/**
 * Export batch detail
 */
function export_batch_detail() {
    // Get global vars
    global $wpdb;

    // Get transactions
    $get_transactional_data = $wpdb->get_results("SELECT card_type,amt,transaction_time,card_lastfour FROM  ".$wpdb->prefix."ppttd_transactionlisting WHERE uniq_batch_id = '".$_REQUEST['batch_id']."' ORDER BY transaction_time");

    // Define exports folder
    $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
	
	// Set start date, end date, and file name
	$filename = "Batch_".$_REQUEST['batch_id'].".csv";
	$file_uri = $folder.$filename;
	
	// Set output file
	$output = fopen($file_uri,'w+');		
	
	// Add header line to CSV
	fputcsv($output, array('Batch ID', 'Card Type','Card Last Four','Transaction Amount','Transaction Time'));
	
	// Add each row to CSV
	foreach($get_transactional_data as $row) {
	    fputcsv( $output, array('batch_id' => $_REQUEST['batch_id'], 'card_type' => $row->card_type, 'card_lastfour' => $row->card_lastfour, 'amount' => $row->amt, 'transaction_time' => $row->transaction_time ) );
	}

    // Close CSV file
	fclose($output);

    // Echo success
	echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );

    die();
}
add_action( 'wp_ajax_export_batch_detail', 'export_batch_detail' );
add_action( 'wp_ajax_nopriv_export_batch_detail', 'export_batch_detail' );

/**
 * Display data on TR page
 */
function transactional_data_summary() {
	global $wpdb;

    // Set display data
    $displayData = '';

	// Check if user is logged in
	if (!is_user_logged_in()) {
		return '';
	}

    // If no merchant ID in $_REQUEST, show default
    $is_ajax = false;
    if (isset($_REQUEST['the_merchant_id']) && trim($_REQUEST['the_merchant_id']) != '') {
        // Get merchant ID from $_REQUEST data
        $merchant_id = str_pad($_REQUEST['the_merchant_id'], 16, '0', STR_PAD_LEFT);
        $is_ajax = true;
    } else {
    	// Get merchant ID
        $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;
    }

    // Pad merchant ID to 16 digits
    $merchant_id = str_pad($merchant_id, 16, '0', STR_PAD_LEFT);

	// Check if user is merchant
	if (is_null($merchant_id)) {
		return '';
	}

    // Query add get the results as an array
    $ppttd_goals				= $wpdb->prefix."ppttd_goals";
	$ppttd_batchlisting			= $wpdb->prefix."ppttd_batchlisting";
    $ppttd_transactionlisting	= $wpdb->prefix."ppttd_transactionlisting";
    
	// Set up arrays
	$transactional_data = array();
	$returning_customers = array();
	$amounts = array();
	$prevDaySales = array();
	$prev30DaySales = array();
	$ytdSales = array();
    
	$salesResults = $wpdb->get_results( "SELECT `uniq_batch_id`, `batch_date`, `total_volume` FROM ".$ppttd_batchlisting." WHERE `merchant_id` = '".$merchant_id."' AND batch_date BETWEEN '".date('Y-m-d', strtotime('-1 year'))." 00:00:00' AND '".date('Y-m-d')." 23:59:59'" );

	// Timeframes
	//$one_day = date('Y-m-d 00:00:00', time() - 60 * 60 * 24);
	$one_day = strtotime('yesterday');
	$thirty_days = strtotime('-30 days');
	$this_year = strtotime('first day of January this year');

	// Loop through the query results array and grab necessary data
	foreach ( $salesResults as $ytdArray ) {
		$batchDate = $ytdArray->batch_date;
		$total_volume = $ytdArray->total_volume;
		$uniq_batch_id = $ytdArray->uniq_batch_id;
		
		// Get transactions
		$get_transactional_data = $wpdb->get_results("SELECT `card_type`,`amt`,`transaction_time`,`card_lastfour` FROM ".$ppttd_transactionlisting." WHERE uniq_batch_id = '".$uniq_batch_id."' ORDER BY transaction_time");
		
		
        // Loop through results to get data
		foreach ($get_transactional_data as $cur_transaction) {
			
			// Get Previous Day's Sales data
			if( strtotime($cur_transaction->transaction_time) >= $one_day ){
				array_push($prevDaySales, $cur_transaction->amt);
			}
			// Get Previous 30 Day's Sales data
			if( strtotime($cur_transaction->transaction_time) >= $thirty_days ){
				array_push($prev30DaySales, $cur_transaction->amt);
			}
			// Get YTD Sales data
			if( strtotime($cur_transaction->transaction_time) >= $this_year ) {
				array_push($ytdSales, $cur_transaction->amt);
			}
			
			// Get amounts
			array_push($amounts, $cur_transaction->amt);
			
		}
	}

	// Get total amount sum
    $amt_sum = array_sum($amounts);

	// Sales variables
	$prevDaySales = array_sum($prevDaySales);
	$prevDaySales = isset($prevDaySales) ? '$'.number_format( $prevDaySales, 2, '.', ',' ) : '$0.00';
	
    $prev30DaySales		= array_sum($prev30DaySales);
	$prev30DaySales = isset($prev30DaySales) ? '$'.number_format( $prev30DaySales, 2, '.', ',' ) : '$0.00';
	
    $avgTicketSales	= (count($amounts) == 0) ? 0 : $amt_sum / count($amounts);
	$avgTicketSales = '$'.number_format( $avgTicketSales, 2, '.', ',' );
	
    $ytdSales = array_sum($ytdSales);
	$ytdSales = isset($ytdSales) ? '$'.number_format( $ytdSales, 2, '.', ',' ) : '$0.00';

    if ($is_ajax == false) {
        $displayData = '<div id="transaction_summary_container" style="position:relative;">';
    }

    // Only show loading div on transactional reporting page
    if( is_page('sales-data') ) {
        $displayData .= '<div id="td_summary_loading"><br/><br/><em>Loading...</em></div>';
    }

	$displayData .= '<ul class="large-block-grid-4 medium-block-grid-2 small-block-grid-1">
						<li>
							<div class="dashboard-stat ppttd-dash-stat dark-blue">
								<div class="visual">
									<i class="icon-file"></i>
								</div>
								<div class="details">
									<div id="mynum" class="number">'.
										$prevDaySales.
									'</div>
									<div class="desc">'.                        
										'Previous Day Sales'.
									'</div>
								</div> 
							</div>
						</li>
						<li>
							<div class="dashboard-stat ppttd-dash-stat dark-blue">
								<div class="visual">
									<i class="icon-copy"></i>
								</div>
								<div class="details">
									<div class="number">'.
										$prev30DaySales.
									'</div>
									<div class="desc">'.                        
										'Previous 30 Day Sales'.
									'</div>
								</div>
							</div>
						</li>
						<li>
							<div class="dashboard-stat ppttd-dash-stat dark-blue">
								<div class="visual">
									<i class="icon-bar-chart"></i>
								</div>
								<div class="details">
									<div class="number">'.
										$ytdSales.
									'</div>
									<div class="desc">                           
										YTD Sales
									</div>
								</div>
							</div>
						</li>
						<li>
							<div class="dashboard-stat ppttd-dash-stat dark-blue">
								<div class="visual">
									<i class="icon-tag"></i>
								</div>
								<div class="details">
									<div class="number">'.
										$avgTicketSales.
									'</div>
									<div class="desc">'.
										'30 Day Average Ticket'.
									'</div>
								</div>
							</div>
						</li>
					</ul>';

    // Echo / return data
    if ($is_ajax == true) {
        echo $displayData;
        die();
    } else {
        return $displayData.'</div>';
    }
}
add_shortcode('transactional_data_summary', 'transactional_data_summary');
add_action( 'wp_ajax_ppttd_td_summary', 'transactional_data_summary' );
add_action( 'wp_ajax_nopriv_ppttd_td_summary', 'transactional_data_summary' );



/**
 * Dashboard Transactional Data and Goals 
 */
function transactional_data_summary_dashboard() {
	global $wpdb;

	// Check if user is logged in
	if (!is_user_logged_in()) {
		return '';
	}
	
    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Return blank if no merchant ID set
    if (is_null($merchant_id)) {
        return '';
    }

    // Set goal timeframe
    $goal_select = $wpdb->get_var( "SELECT default_selection FROM ".$wpdb->prefix."ppttd_goals WHERE user_id='".get_current_user_id()."' AND mid='".$_SESSION['active_mid']['merchant_id']."'");
    $goal_select = (!isset($goal_select) || trim($goal_select) === '') ? 'monthly' : $goal_select;

    // Get data
    $summary_data = json_decode(get_dashboard_transaction_summary_data($goal_select));

    // Display data
    $displayData = '<div class="row-fluid">
                        <div class="span6">'.
							'<div class="td_block">'.
								'<div class="block_header">'.
									//'<h4>This '.$timeframe.'\'s Sales <i class="saltsha-icon saltsha-cat-sales"></i></h4>'.
									'<form action="/" method="POST" id="goal_select_form"><select id="goal_select" name="goal_select">'.
										'<option value="yearly" '.($goal_select=="yearly" ? "selected" : "").'>This Year</option>'.
										'<option value="monthly" '.($goal_select=="monthly" ? "selected" : "").'>This Month</option>'.
										'<option value="weekly" '.($goal_select=="weekly" ? "selected" : "").'>This Week</option>'.
									'</select></form>'.
									'<span class="sales_amount">'.$summary_data->total.'</span>'.
								'</div>'.
								'<div class="block_content">'.
									'<div class="summary_section">'.
										'<div class="ppttd_section_image"><img src="/wp-content/plugins/transactional-data/images/tag.png" /></div>'.
										'<div class="summary_text">'.
											'<div class="ppttd_section_header">Total Sales</div>'.
											'<div class="ppttd_section_result ppttd_section_total">'.$summary_data->total.'</div>'.
										'</div>'.
									'</div>'.
									'<div class="summary_section_divide"></div>'.
									'<div class="summary_section">'.
										'<div class="ppttd_section_image"><img src="/wp-content/plugins/transactional-data/images/new-customers.png" /></div>'.
										'<div class="summary_text">'.
											'<div class="ppttd_section_header">New Customers</div>'.
											'<div class="ppttd_section_result ppttd_section_total_customers">'.$summary_data->total_customers.'</div>'.
										'</div>'.
									'</div>'.
									'<div class="summary_section_divide"></div>'.
									'<div class="summary_section">'.
										'<div class="ppttd_section_image"><img src="/wp-content/plugins/transactional-data/images/returning-customers.png" /></div>'.
										'<div class="summary_text">'.
											'<div class="ppttd_section_header">Returning Customers</div>'.
											'<div class="ppttd_section_result ppttd_section_returning_customers">'.$summary_data->returning_customers.'</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="span6">
                    		<div class="portlet ">
                    			<div class="portlet-title "style="padding:0px;">
                    				<div class="top-news">
                    					<a class="btn dashboard-stat" style="margin-bottom: -10px;">
                    						<span>Cash Advance</span>
                    						<i class="saltsha-cat-icon saltsha-cat-goals"></i>
                    					</a>	
                    				</div>
                    			</div>
                    			<div class="portlet-body cash_advance">
                    				<p>You could be eligible for a cash advance of:</p>
                    				<span class="cash_advance_amount" href="#">'.$summary_data->dashboard_cash_advance.'</span>
                    				<a class="redeem_points" href="#cashAdvanceModal" data-toggle="modal">Claim Money</a>
                    				<a class="learn_more" href="#cashAdvanceVideo" data-toggle="modal">Learn More</a>
                    			</div> 
                    		</div>
						</div>
					</div>
                    <div class="row-fluid">
                    <div class="span6">'.
							'<div class="td_block">'.
								'<div class="block_header">'.
									'<h4>Your Goals</h4>'.
									'<a href="#" id="editGoals"><img src="/wp-content/themes/ppmlayout/images/edit.png" alt="edit" /></a>'.
									//'<h4>Your Goals <i class="saltsha-icon saltsha-cat-goals"></i></h4>'.
									//'<span id="goal_amount" class="goal_amount">$'.number_format( $goal_total, 2, '.', ',' ).'</span>'.
								'</div>'.
								'<div class="block_content goal">'.
									'<form id="setGoal" action="/" method="POST">'.
										'<h5>Set Goal</h5>'.
										'<label><span>Weekly</span><input type="text" name="goal_weekly" id="goal_weekly" value="'.$summary_data->goal_weekly.'" /></label>'.
										'<label><span>Monthly</span><input type="text" name="goal_monthly" id="goal_monthly" value="'.$summary_data->goal_monthly.'" /></label>'.
										'<label><span>Yearly</span><input type="text" name="goal_yearly" id="goal_yearly" value="'.$summary_data->goal_yearly.'" /></label>'.
										'<input type="submit" class="green_button" value="Re-Calculate" />'.
									'</form>'.
									'<div id="goalBox">'.
										'<span class="smallGoalText">Weekly Goal: <span class="green-text">$'.number_format( $summary_data->goal_weekly ).'</span></span>'.
										'<div class="goalMeter">'.
											'<span class="goalBar" style="width:'.number_format( $summary_data->weekly_percentage ).'%"></span>'.
											'<span class="goalPercentage">'.number_format( $summary_data->weekly_percentage ).'%</span>'.
										'</div>'.
										'<span class="smallGoalText">Monthly Goal: <span class="green-text">$'.number_format( $summary_data->goal_monthly ).'</span></span>'.
										'<div class="goalMeter">'.
											'<span class="goalBar" style="width:'.number_format( $summary_data->monthly_percentage ).'%"></span>'.
											'<span class="goalPercentage">'.number_format( $summary_data->monthly_percentage ).'%</span>'.
										'</div>'.
										'<span class="smallGoalText">Yearly Goal: <span class="green-text">$'.number_format( $summary_data->goal_yearly ).'</span></span>'.
										'<div class="goalMeter">'.
											'<span class="goalBar" style="width:'.number_format( $summary_data->yearly_percentage ).'%"></span>'.
											'<span class="goalPercentage">'.number_format( $summary_data->yearly_percentage ).'%</span>'.
										'</div>'.
										'<div class="circleDiv">'.
											'<div id="weeklyGoal" class="goalData" data-animationstep="0" data-text="'.number_format( $summary_data->weekly_percentage ).'%'.($summary_data->weekly_percentage >= 999 ? '+' : '').'" data-percent="'.($summary_data->weekly_percentage >= 999 ? '999' : number_format( $summary_data->weekly_percentage )).'" data-info="Weekly" data-dimension="150" data-width="150" data-bordersize="20" data-fontsize="30" data-fill="transparent" data-fgcolor="#5CD562" data-bgcolor="#E9F0FA"></div>'.
											'<div class="goalAmount goal_monthly">$'.number_format( $summary_data->goal_monthly, 2 ).'</div>'.
										'</div>'.
										'<div class="circleDiv">'.
											'<div id="monthlyGoal" class="goalData" data-animationstep="0" data-text="'.number_format( $summary_data->monthly_percentage ).'%'.($summary_data->monthly_percentage >= 999 ? '+' : '').'" data-percent="'.($summary_data->monthly_percentage >= 999 ? '999' : number_format( $summary_data->monthly_percentage )).'" data-info="Monthly" data-dimension="150" data-width="150" data-bordersize="20" data-fontsize="30" data-fill="transparent" data-fgcolor="#5CD562" data-bgcolor="#E9F0FA"></div>'.
											'<div class="goalAmount goal_monthly">$'.number_format( $summary_data->goal_monthly, 2 ).'</div>'.
										'</div>'.
										'<div class="circleDiv">'.
											'<div id="yearlyGoal" class="goalData" data-animationstep="0" data-text="'.number_format( $summary_data->yearly_percentage ).'%'.($summary_data->yearly_percentage >= 999 ? '+' : '').'" data-percent="'.($summary_data->yearly_percentage >= 999 ? '999' : number_format( $summary_data->yearly_percentage )).'" data-info="Yearly" data-dimension="150" data-width="150" data-bordersize="20" data-fontsize="30" data-fill="transparent" data-fgcolor="#5CD562" data-bgcolor="#E9F0FA"></div>'.
											'<div class="goalAmount goal_yearly">$'.number_format( $summary_data->goal_yearly, 2 ).'</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <div class="span6">
							<div class="portlet ">
								<div class="portlet-title "style="padding:0px;">
									<div class="top-news">
										<a class="btn dashboard-stat" style="margin-bottom: -10px;">
											<span>Loyalty Rewards</span>
											<i class="saltsha-cat-icon saltsha-cat-trophy"></i>
										</a>	
									</div>
								</div>
								<div class="portlet-body dash_rewards">
									<div class="dash_rewards_content">
										<h4>You have <span class="green_text">'.number_format(get_user_reward_points($user->ID)).'</span> points!</h4>
										<p>You can redeem points to use on travel, merchandise, etiam, cursus, and more!</p>
										<a href="/loyalty-rewards/" class="redeem_points">Claim your rewards!</a>
									</div><div class="dash_trophy">
										<img src="/wp-content/themes/ppmlayout/images/trophy.png" />
									</div>
								</div> 
							</div>
						</div>';
				
	$displayData .= '</div>';
	
	// Echo / return data
	return $displayData;
}
add_shortcode( 'transactional_data_summary_dashboard', 'transactional_data_summary_dashboard' );
add_action( 'wp_ajax_ppttd_td_summary', 'transactional_data_summary_dashboard' );
add_action( 'wp_ajax_nopriv_ppttd_td_summary', 'transactional_data_summary_dashboard' );


/**
 * Dashboard summary data
 */
function get_dashboard_transaction_summary_data($goal_select = null) {
	global $wpdb;

    // Store summary data
    $summary_data_array = array();

	// Check if user is logged in
	if (!is_user_logged_in()) {
		return json_encode(array('total' => '$0.00', 'total_customers' => '0', 'returning_customers' => '0', 'goal_weekly' => '0', 'goal_monthly' => '0', 'goal_yearly' => '0', 'weekly_percentage' => '0', 'monthly_percentage' => '0', 'yearly_percentage' => '0', 'user_reward_points' => '0'));
	}
	
    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Return blank if no merchant ID set
    if (is_null($merchant_id)) {
        return json_encode(array('total' => '0', 'total_customers' => '0', 'returning_customers' => '0', 'goal_weekly' => '0', 'goal_monthly' => '0', 'goal_yearly' => '0', 'weekly_percentage' => '0', 'monthly_percentage' => '0', 'yearly_percentage' => '0', 'user_reward_points' => '0'));
    }

	// Get user data
	$user = wp_get_current_user();

    // Get points
    $summary_data_array['dashboard_cash_advance'] = dashboard_cash_advance();

    // table names
    $ppttd_goals				= $wpdb->prefix."ppttd_goals";
	$ppttd_batchlisting			= $wpdb->prefix."ppttd_batchlisting";
    $ppttd_transactionlisting	= $wpdb->prefix."ppttd_transactionlisting";

	// Timeframes
	// $test_first_day			= date('Y-m-d', strtotime('28 september 2014'));
	// $test_last_day			= date('Y-m-d', strtotime('4 october 2014'));
	$first_day_of_this_week	= date('Y-m-d 00:00:00', strtotime('sunday last week'));
	$first_day_of_month		= date('Y-m-d 00:00:00', strtotime('first day of this month'));
	$first_day_of_year		= date('Y-m-d 00:00:00', strtotime('first day of January this year'));
	$today					= date('Y-m-d 23:59:59');

	// Set new goals on form submit
	if(isset($_POST['goal_weekly']) && isset($_POST['goal_monthly']) && isset($_POST['goal_yearly'])){
		$wpdb->update( $ppttd_goals, array('weekly' => $_POST['goal_weekly'], 'monthly' => $_POST['goal_monthly'], 'yearly' => $_POST['goal_yearly']), array('user_id' => $user->ID, 'mid' => $merchant_id) );
	}

	// Set up Goal data
    $goal_data = $wpdb->get_row( "SELECT * FROM ".$ppttd_goals." WHERE `user_id` = '".$user->ID."' AND `mid`='".$merchant_id."'; " );

    // If user doesn't have goals, set up default goals
    if (is_null($goal_data)) {
	    // Add goals
	    $wpdb->insert( $ppttd_goals, array('user_id' => $user->ID, 'mid' => $merchant_id) );

        // Run query to get new goals
		$goal_data		= $wpdb->get_row( "SELECT * FROM ".$ppttd_goals." WHERE `user_id` = '".$user->ID."' AND `mid`='".$merchant_id."'; " );
    }

    // Set goal data
	$summary_data_array['goal_weekly']		= $goal_data->weekly;
	$summary_data_array['goal_monthly']		= $goal_data->monthly;
	$summary_data_array['goal_yearly']		= $goal_data->yearly;

	// Update selection timeframe
	if (is_null($goal_select)) {
        // Set goal timeframe
        $goal_select = $wpdb->get_var( "SELECT default_selection FROM ".$wpdb->prefix."ppttd_goals WHERE user_id='".get_current_user_id()."' AND mid='".$_SESSION['active_mid']['merchant_id']."'");
        $goal_select = (!isset($goal_select) || trim($goal_select) === '') ? 'monthly' : $goal_select;
	} else {
    	$wpdb->update($ppttd_goals, array('default_selection' => $goal_select), array( 'user_id' => $user->ID, 'mid' => $merchant_id ) );
    }

    // Set value
    $summary_data_array['goal_select'] = $goal_select;

	// Set sql timeframe
	switch ($goal_select) {
	    case "weekly":
	        //$sql_timeframe = "batch_date BETWEEN '".$first_day_of_this_week."' AND '".$today."'";
	    	$timeframe = "Week";
	        break;
	    case "monthly":
	        //$sql_timeframe = "batch_date BETWEEN '".$first_day_of_month."' AND '".$today."'";
	    	$timeframe = "Month";
	        break;
	    case "yearly":
	        //$sql_timeframe = "batch_date BETWEEN '".$first_day_of_year."' AND '".$today."'";
	    	$timeframe = "Year";
	        break;
	}

	// Set up arrays
	$transactional_data = array();
	$returning_customers = array();
	$amounts = array();
	$weekly_goal_amounts = array();
	$monthly_goal_amounts = array();
	$yearly_goal_amounts = array();
	
	
	// Select timeframe based on #goal_select  AND ".$sql_timeframe."
	$transaction_batches = $wpdb->get_results("SELECT uniq_batch_id,total_volume,batch_date FROM ".$ppttd_batchlisting." WHERE merchant_id='".$merchant_id."' AND batch_date BETWEEN '".date('Y-m-d', strtotime('-1 year'))." 00:00:00' AND '".date('Y-m-d')." 23:59:59' ORDER BY batch_date");
	
	// Get transactions for each batch
	foreach ($transaction_batches as $cur_batch) {
		// Set daily transaction amounts
		$batch_date = date('m-d-Y', strtotime($cur_batch->batch_date));
		$total_volume = $cur_batch->total_volume;
		$uniq_batch_id = $cur_batch->uniq_batch_id;
		
		// Get transactions
		$get_transactional_data = $wpdb->get_results("SELECT card_type,amt,transaction_time,card_lastfour FROM  ".$ppttd_transactionlisting." WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."' ORDER BY transaction_time");

        // Loop through results to get data
		foreach ($get_transactional_data as $cur_transaction) {

			// Get this week's sales data
			if( strtotime($cur_transaction->transaction_time) >= strtotime('sunday last week') ){
				if($timeframe == 'Week'){
				    // Set card identifier, card type and card last four digits
				    $card_identifier = $cur_transaction->card_type.'-'.$cur_transaction->card_lastfour;
				    
				    // Check for customer loyalty
				    if (!array_key_exists($card_identifier, $returning_customers)) {
					    $returning_customers[$card_identifier] = array();
				    }

		            // Increment total number of sales AND total sales
		            if (!isset($returning_customers[$card_identifier]['total_sales'])) {
		                $returning_customers[$card_identifier]['total_sales'] = 0;
		            }
		            if (!isset($returning_customers[$card_identifier]['number_of_visits'])) {
		                $returning_customers[$card_identifier]['number_of_visits'] = 0;
		            }
					$returning_customers[$card_identifier]['number_of_visits']++;
					$returning_customers[$card_identifier]['total_sales'] = number_format(floatval($returning_customers[$card_identifier]['total_sales'])+floatval($cur_transaction->amt), 2);
					array_push($amounts, $cur_transaction->amt);
				}
				// Get weekly goal amounts
				array_push($weekly_goal_amounts, $cur_transaction->amt);
			}
			
			// Get this month's sales data
			if( strtotime($cur_transaction->transaction_time) >= strtotime('first day of this month') ){
				if($timeframe == 'Month'){
				    // Set card identifier, card type and card last four digits
				    $card_identifier = $cur_transaction->card_type.'-'.$cur_transaction->card_lastfour;
				    
				    // Check for customer loyalty
				    if (!array_key_exists($card_identifier, $returning_customers)) {
					    $returning_customers[$card_identifier] = array();
				    }
		
		            // Increment total number of sales AND total sales
		            if (!isset($returning_customers[$card_identifier]['total_sales'])) {
		                $returning_customers[$card_identifier]['total_sales'] = 0;
		            }
		            if (!isset($returning_customers[$card_identifier]['number_of_visits'])) {
		                $returning_customers[$card_identifier]['number_of_visits'] = 0;
		            }
					$returning_customers[$card_identifier]['number_of_visits']++;
					$returning_customers[$card_identifier]['total_sales'] = number_format(floatval($returning_customers[$card_identifier]['total_sales'])+floatval($cur_transaction->amt), 2);
					array_push($amounts, $cur_transaction->amt);
				}
				// Monthly goal amounts
				array_push($monthly_goal_amounts, $cur_transaction->amt);
			}
			
			// Get this year's sales data
			if( strtotime($cur_transaction->transaction_time) >= strtotime('first day of January this year') ) {
				if($timeframe == 'Year'){
				    // Set card identifier, card type and card last four digits
				    $card_identifier = $cur_transaction->card_type.'-'.$cur_transaction->card_lastfour;
				    
				    // Check for customer loyalty
				    if (!array_key_exists($card_identifier, $returning_customers)) {
					    $returning_customers[$card_identifier] = array();
				    }
		
		            // Increment total number of sales AND total sales
		            if (!isset($returning_customers[$card_identifier]['total_sales'])) {
		                $returning_customers[$card_identifier]['total_sales'] = 0;
		            }
		            if (!isset($returning_customers[$card_identifier]['number_of_visits'])) {
		                $returning_customers[$card_identifier]['number_of_visits'] = 0;
		            }
					
					$returning_customers[$card_identifier]['number_of_visits']++;
					$returning_customers[$card_identifier]['total_sales'] = number_format(floatval($returning_customers[$card_identifier]['total_sales'])+floatval($cur_transaction->amt), 2);
					array_push($amounts, $cur_transaction->amt);
				}
				// Yearly goal amounts
				array_push($yearly_goal_amounts, $cur_transaction->amt);
			}
			
			
		}
	}

	// Grab the total customer count before removing single visit customers
	$summary_data_array['total_customers'] = count($returning_customers);

    // Remove from returning customers if only one visit
    foreach ($returning_customers as $card_holder => $card_data) {
        if ( $card_data['number_of_visits'] == 1) {
            unset($returning_customers[$card_holder]);
        }
    }
    
    // Get the average ticket size
    $amt_sum		= array_sum($amounts);
    $amt_average	= (count($amounts) == 0) ? 0 : $amt_sum / count($amounts);
    
    $weekly_goal_amt_sum		= array_sum($weekly_goal_amounts);
    
    $monthly_goal_amt_sum		= array_sum($monthly_goal_amounts);
    
    $yearly_goal_amt_sum		= array_sum($yearly_goal_amounts);
    
    // Format results to correct monetary amounts
	$avgTicketSales	= '$'.number_format( $amt_average, 2, '.', ',' );
	$summary_data_array['total']		= '$'.number_format( $amt_sum, 2, '.', ',' );

	/*
	$goalAvgTicketSales	= '$'.number_format( $goal_amt_average, 2, '.', ',' );
	$goal_total		= '$'.number_format( $goal_amt_sum, 2, '.', ',' );
*/
    
    // Get the amount of returning and total customers
	$summary_data_array['returning_customers']	= count($returning_customers);

	// Set weekly goal data
    if($summary_data_array['goal_weekly']==0){
        $summary_data_array['weekly_percentage'] = 0;
    } else {
		$summary_data_array['weekly_percentage'] = $weekly_goal_amt_sum/$summary_data_array['goal_weekly'];
		$summary_data_array['weekly_percentage'] = $summary_data_array['weekly_percentage'] * 100;
	    if($summary_data_array['weekly_percentage'] >= 999){
		    $summary_data_array['weekly_percentage'] = 999;
	    }
	    $summary_data_array['weekly_percentage'] = round($summary_data_array['weekly_percentage']);
    }

    // Set monthly goal data
    if($summary_data_array['goal_monthly']==0){
        $summary_data_array['monthly_percentage'] = 0;
    } else {
		$summary_data_array['monthly_percentage'] = $monthly_goal_amt_sum/$summary_data_array['goal_monthly'];
		$summary_data_array['monthly_percentage'] = $summary_data_array['monthly_percentage'] * 100;
	    if($summary_data_array['monthly_percentage'] >= 999){
		    $summary_data_array['monthly_percentage'] = 999;
	    }
	    $summary_data_array['monthly_percentage'] = round($summary_data_array['monthly_percentage']);
    }

    // Set yearly goal data
    if($summary_data_array['goal_yearly']==0){
        $summary_data_array['yearly_percentage'] = 0;
    } else {
		$summary_data_array['yearly_percentage'] = $yearly_goal_amt_sum/$summary_data_array['goal_yearly'];
		$summary_data_array['yearly_percentage'] = $summary_data_array['yearly_percentage'] * 100;
	    if($summary_data_array['yearly_percentage'] >= 999){
		    $summary_data_array['yearly_percentage'] = 999;
	    }
	    $summary_data_array['yearly_percentage'] = round($summary_data_array['yearly_percentage']);
    }

    // Return data
    return json_encode($summary_data_array);
}


/**
 * Display all customers' data page
 */
function transactional_data_customers() {
    global $wpdb;

    // Check if merchant ID is being passed via AJAX
    $is_ajax = false;
    if (isset($_REQUEST['the_merchant_id']) && trim($_REQUEST['the_merchant_id']) !== '') {
        // Set AJAX to true
        $is_ajax = true;

        // Set merchant ID
        $merchant_id = str_pad($_REQUEST['the_merchant_id'], 16, '0', STR_PAD_LEFT);
    } else {
        // Enqueue some scripts/styles in all environments
        wp_enqueue_script('init-customers', '/wp-content/plugins/transactional-data/js/init_customers.js', null, date('y.m.d'));

        // Enqueue some scripts/styles only in local environment
        if (stripos($_SERVER['HTTP_HOST'], 'local.') !== false) {
            wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
            wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
            wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
        }

    	// Get merchant ID
    	$merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;
    }

	// Check if user is logged in
	if (!is_user_logged_in()) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your customer data.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo $the_error_message;
            die();
        } else {
            return $the_error_message;
        }
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo $the_error_message;
            die();
        } else {
            return $the_error_message;
        }
    }

    // Display single or multiple merchant IDs
    $customer_data = "";

    // If ajax, load data, Else, load table
    if ($is_ajax == true) {
        // Set tables
        $bl_table = $wpdb->prefix."ppttd_batchlisting";
        $td_table = $wpdb->prefix."ppttd_transactionlisting";
        $cd_table = $wpdb->prefix."ppttd_customers";

        // Setup vars
        $returning_customers = array();

        // Get batch listings
        $transaction_batches = $wpdb->get_results("SELECT uniq_batch_id FROM ".$bl_table." WHERE merchant_id='".$merchant_id."'");
        foreach ($transaction_batches as $cur_batch) {
            // Get transactions for this batch
            $returning_customers_query = $wpdb->get_results("SELECT ".$td_table.".amt,".$td_table.".card_lastfour,".$td_table.".card_type,".$td_table.".merchant_id,".$cd_table.".cardholder_name,".$cd_table.".id FROM ".$td_table." LEFT JOIN ".$cd_table." ON ".$td_table.".card_lastfour = ".$cd_table.".last_four AND ".$td_table.".card_type = ".$cd_table.".card_type AND ".$td_table.".merchant_id = ".$cd_table.".merchant_id WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."'");

            // Return customer data
            foreach ($returning_customers_query as $cur_returning_customer) {
    		    // Set card identifier, card type and card last four digits
    		    $card_identifier = $cur_returning_customer->card_type.'-'.$cur_returning_customer->card_lastfour;
    
    		    // Check for customer loyalty
    		    if (!array_key_exists($card_identifier, $returning_customers)) {
    			    $returning_customers[$card_identifier] = array();
    			    if (isset($cur_returning_customer->cardholder_name) && trim($cur_returning_customer->cardholder_name) != '') {
        			    $returning_customers[$card_identifier]['cardholder_name'] = $cur_returning_customer->cardholder_name;
    			    }
    			    if (isset($cur_returning_customer->id) && trim($cur_returning_customer->id) != '') {
        			    $returning_customers[$card_identifier]['cardholder_id'] = $cur_returning_customer->id;
    			    }
    		    }
    
                // Increment total number of sales AND total sales
    		    $returning_customers[$card_identifier]['number_of_visits']++;

                // Increment total sales
                $returning_customers[$card_identifier]['total_sales'] = floatval($returning_customers[$card_identifier]['total_sales']) + floatval($cur_returning_customer->amt);
    		}
        }

    	// Echo data
    	$customer_data = json_encode(array('status' => 'success', 'returning_customers' => $returning_customers));
    } else {
        // Display table
        $customer_data .= '    <script>
	        window.onload = function() {
				Customers.init();
			};
			</script>
        <div class="row-fluid">
            <div class="span12">
                <div class="portlet box">
                    <div class="portlet-title">
                        <div class="caption"><i class="icon-reorder white-icon"></i>Customers</div>
                    </div>
                    <div class="portlet-body">
                        <div class="content">
                            <div class="tab-pane active" id="transaction_detail_tab">
        						<table class="table table-striped table-hover table-bordered" id="show_returning_customers">
        							<thead>
        								<tr>
        									<th>Card Holder</th>
        									<th>Number of Visits</th>
        									<th>Total Sales</th>
        									<th>Actions</th>
        								</tr>
        							</thead>
        							<tbody id="returning_customers_table">
                                        <tr>
                                            <td colspan="4"><em class="ppttd-table-loading">Loading...</em></td>
                                        </tr>
        							</tbody>
        						</table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Echo / return data
    if ($is_ajax == true) {
        echo $customer_data;
        die();
    } else {
        return $customer_data;
    }
}
add_shortcode('transactional_data_customers', 'transactional_data_customers');
add_action( 'wp_ajax_ppttd_td_customers', 'transactional_data_customers' );
add_action( 'wp_ajax_nopriv_ppttd_td_customers', 'transactional_data_customers' );


/**
 * Display Registered Customers
 */
function transactional_data_registered_customers() {
    global $wpdb;

    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Check if merchant ID is being passed via AJAX
    $is_ajax = false;
    if (isset($_REQUEST['action'])) {
        $is_ajax = true;
    } else {
    	// Enqueue all required scripts and styles
    	wp_enqueue_script('init-customers-dashboard', '/wp-content/plugins/transactional-data/js/init_cust_dashboard.js', null, date('y.m.d'));
    	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
    	wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
    	wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

	// Check if user is logged in
	if (!is_user_logged_in()) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your customer data.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to view your customer data.'));
            die();
        } else {
            return $the_error_message;
        }
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'There are no merchant IDs associated with your account. Please login and add one or contact support.'));
            die();
        } else {
            return $the_error_message;
        }
    }

    // Display single or multiple merchant IDs
    $customer_data = "";

    // If ajax, load data, Else, load table
    if ($is_ajax == true) {
        // Set tables
        $bl_table = $wpdb->prefix."ppttd_batchlisting";
        $td_table = $wpdb->prefix."ppttd_transactionlisting";
        $cd_table = $wpdb->prefix."ppttd_customers";

        // Setup vars
        $registered_customers = array();

        // Get batch listings
        $transaction_batches = $wpdb->get_results("SELECT uniq_batch_id FROM ".$bl_table." WHERE merchant_id='".$merchant_id."'");
        foreach ($transaction_batches as $cur_batch) {
            // Get transactions for this batch
            $customers_query = $wpdb->get_results("SELECT ".$td_table.".amt,".$td_table.".card_lastfour,".$td_table.".card_type,".$td_table.".transaction_time,".$td_table.".merchant_id,".$cd_table.".cardholder_name,".$cd_table.".id FROM ".$td_table." LEFT JOIN ".$cd_table." ON ".$td_table.".card_lastfour = ".$cd_table.".last_four AND ".$td_table.".card_type = ".$cd_table.".card_type AND ".$td_table.".merchant_id = ".$cd_table.".merchant_id WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."'");

            // Return customer data
            foreach ($customers_query as $cur_customer) {
    		    // Set card identifier, card type and card last four digits
    		    $card_identifier = $cur_customer->card_type.'-'.$cur_customer->card_lastfour;
    
				/*
				* Get the Registered Customers	
				*/
			    $registered_customers[$card_identifier]['cardholder_name'] = $cur_customer->cardholder_name;
			    $registered_customers[$card_identifier]['cardholder_id'] = $cur_customer->id;
			    $registered_customers[$card_identifier]['number_of_visits']++;
			    $registered_customers[$card_identifier]['total_sales'] = floatval($registered_customers[$card_identifier]['total_sales']) + floatval($cur_customer->amt);
                
    		}
        }
	    
		/*
		* Finalize the Registered Customers array
		*/
		// Remove customer if their name is not set
	    foreach ($registered_customers as $card_holder => $card_data) {
	        if ($card_data['cardholder_name'] == '' || trim($card_data['cardholder_name']) == '' ) {
	            unset($registered_customers[$card_holder]);
	        }
	    }
		    
	    
    	// Send data to JSON
    	$customer_data = json_encode(
    		array(
    			'status' => 'success',
    			'registered_customers' => $registered_customers
    		)
    	);
    } else {
	    
        // Display table
        $customer_data .= '
        <script>
	        window.onload = function() {
				Customers.init();
			};
		</script>
        <div class="row-fluid">
            <div class="span12">
                <div class="portlet box">
                    <div class="portlet-title">
                        <div class="caption">Registered Customers</div>
                    </div>
                    <div class="portlet-body">
                        <div class="content">
    						<table class="table table-striped table-hover table-bordered" id="show_registered_customers">
    							<thead>
    								<tr>
    									<th>Card Holder</th>
    									<th>Number of Visits</th>
    									<th>Total Sales</th>
    									<th>Actions</th>
    								</tr>
    							</thead>
    							<tbody id="registered_customers_table">
                                    <tr>
                                        <td colspan="4"><em class="ppttd-table-loading">Loading...</em></td>
                                    </tr>
    							</tbody>
    						</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Echo / return data
    if ($is_ajax == true) {
        echo $customer_data;
        die();
    } else {
        return $customer_data;
    }
}
add_shortcode('transactional_data_registered_customers', 'transactional_data_registered_customers');
add_action( 'wp_ajax_ppttd_td_registered_customers', 'transactional_data_registered_customers' );
add_action( 'wp_ajax_nopriv_ppttd_td_registered_customers', 'transactional_data_registered_customers' );


/**
 * Display Repeat Customers
 */
function transactional_data_repeat_customers() {
    global $wpdb;

    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Check if merchant ID is being passed via AJAX
    $is_ajax = false;
    if (isset($_REQUEST['action'])) {
        $is_ajax = true;
    } else {
    	// Enqueue all required scripts and styles
    	wp_enqueue_script('init-customers-dashboard', '/wp-content/plugins/transactional-data/js/init_cust_dashboard.js', null, date('y.m.d'));
    	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
    	wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
    	wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

	// Check if user is logged in
	if (!is_user_logged_in()) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your customer data.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to view your customer data.'));
            die();
        } else {
            return $the_error_message;
        }
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'There are no merchant IDs associated with your account. Please login and add one or contact support.'));
            die();
        } else {
            return $the_error_message;
        }
    }

    // Display single or multiple merchant IDs
    $customer_data = "";

    // If ajax, load data, Else, load table
    if ($is_ajax == true) {
        // Set tables
        $bl_table = $wpdb->prefix."ppttd_batchlisting";
        $td_table = $wpdb->prefix."ppttd_transactionlisting";
        $cd_table = $wpdb->prefix."ppttd_customers";

        // Setup vars
		$repeat_customers = array();

        // Get batch listings
        $transaction_batches = $wpdb->get_results("SELECT uniq_batch_id FROM ".$bl_table." WHERE merchant_id='".$merchant_id."'");
        foreach ($transaction_batches as $cur_batch) {
            // Get transactions for this batch
            $customers_query = $wpdb->get_results("SELECT ".$td_table.".amt,".$td_table.".card_lastfour,".$td_table.".card_type,".$td_table.".transaction_time,".$td_table.".merchant_id,".$cd_table.".cardholder_name,".$cd_table.".id FROM ".$td_table." LEFT JOIN ".$cd_table." ON ".$td_table.".card_lastfour = ".$cd_table.".last_four AND ".$td_table.".card_type = ".$cd_table.".card_type AND ".$td_table.".merchant_id=".$cd_table.".merchant_id WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."'");

            // Return customer data
            foreach ($customers_query as $cur_customer) {
    		    // Set card identifier, card type and card last four digits
    		    $card_identifier = $cur_customer->card_type.'-'.$cur_customer->card_lastfour;
    
				/*
				* Get the Repeat Customers	
				*/
    		    // Check for customer loyalty
    		    if (!array_key_exists($card_identifier, $repeat_customers)) {
    			    $repeat_customers[$card_identifier] = array();
    			    if (isset($cur_customer->cardholder_name) && trim($cur_customer->cardholder_name) != '') {
        			    $repeat_customers[$card_identifier]['cardholder_name'] = $cur_customer->cardholder_name;
    			    }
    			    if (isset($cur_customer->id) && trim($cur_customer->id) != '') {
        			    $repeat_customers[$card_identifier]['cardholder_id'] = $cur_customer->id;
    			    }
    		    }
    
                // Increment total number of sales AND total sales
    		    $repeat_customers[$card_identifier]['number_of_visits']++;
                // Increment total sales
                $repeat_customers[$card_identifier]['total_sales'] = floatval( $repeat_customers[$card_identifier]['total_sales'] ) + floatval( $cur_customer->amt );
    		}
        }
		
		
		/*
		* Finalize the Repeat Customers array
		*/
		// Remove from returning customers if only one visit
	    foreach ($repeat_customers as $card_holder => $card_data) {
	        if ($card_data['number_of_visits'] == 1) {
	            unset($repeat_customers[$card_holder]);
	        }
	    }
		    
	    
    	// Send data to JSON
    	$customer_data = json_encode(
    		array(
        		'status' => 'success',
    			'repeat_customers' => $repeat_customers
    		)
    	);
    } else {
	    
        // Display table
        $customer_data .= '
        <script>
	        window.onload = function() {
				Customers.init();
			};
		</script>
        <div class="row-fluid">
            <div class="span12">
                <div class="portlet box">
                    <div class="portlet-title">
                        <div class="caption">Repeat Customers</div>
                    </div>
                    <div class="portlet-body">
                        <div class="content">
    						<table class="table table-striped table-hover table-bordered" id="show_repeat_customers">
    							<thead>
    								<tr>
    									<th>Card Holder</th>
    									<th>Number of Visits</th>
    									<th>Total Sales</th>
    									<th>Actions</th>
    								</tr>
    							</thead>
    							<tbody id="repeat_customers_table">
                                    <tr>
                                        <td colspan="4"><em class="ppttd-table-loading">Loading...</em></td>
                                    </tr>
    							</tbody>
    						</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Echo / return data
    if ($is_ajax == true) {
        echo $customer_data;
        die();
    } else {
        return $customer_data;
    }
}
add_shortcode('transactional_data_repeat_customers', 'transactional_data_repeat_customers');
add_action( 'wp_ajax_ppttd_td_repeat_customers', 'transactional_data_repeat_customers' );
add_action( 'wp_ajax_nopriv_ppttd_td_repeat_customers', 'transactional_data_repeat_customers' );


/**
 * Display Top Spenders
 */
function transactional_data_top_spenders() {
    global $wpdb;

    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Check if merchant ID is being passed via AJAX
    $is_ajax = false;
    if (isset($_REQUEST['action'])) {
        $is_ajax = true;
    } else {
    	// Enqueue all required scripts and styles
    	wp_enqueue_script('init-customers-dashboard', '/wp-content/plugins/transactional-data/js/init_cust_dashboard.js', null, date('y.m.d'));
    	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
    	wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
    	wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

	// Check if user is logged in
	if (!is_user_logged_in()) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your customer data.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to view your customer data.'));
            die();
        } else {
            return $the_error_message;
        }
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'There are no merchant IDs associated with your account. Please login and add one or contact support.'));
            die();
        } else {
            return $the_error_message;
        }
    }

    // Display single or multiple merchant IDs
    $customer_data = "";

    // If ajax, load data, Else, load table
    if ($is_ajax == true) {
        // Set tables
        $bl_table = $wpdb->prefix."ppttd_batchlisting";
        $td_table = $wpdb->prefix."ppttd_transactionlisting";
        $cd_table = $wpdb->prefix."ppttd_customers";

        // Setup vars
		$top_spenders = array();

        // Get batch listings
        $transaction_batches = $wpdb->get_results("SELECT uniq_batch_id FROM ".$bl_table." WHERE merchant_id='".$merchant_id."'");
        foreach ($transaction_batches as $cur_batch) {
            // Get transactions for this batch
            $customers_query = $wpdb->get_results("SELECT ".$td_table.".amt,".$td_table.".card_lastfour,".$td_table.".card_type,".$td_table.".transaction_time,".$td_table.".merchant_id,".$cd_table.".cardholder_name,".$cd_table.".id FROM ".$td_table." LEFT JOIN ".$cd_table." ON ".$td_table.".card_lastfour = ".$cd_table.".last_four AND ".$td_table.".card_type = ".$cd_table.".card_type AND ".$td_table.".merchant_id=".$cd_table.".merchant_id WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."'");

            // Return customer data
            foreach ($customers_query as $cur_customer) {
    		    // Set card identifier, card type and card last four digits
    		    $card_identifier = $cur_customer->card_type.'-'.$cur_customer->card_lastfour;
			    
				/*
				* Get the Top Spenders	
				*/
			    $top_spenders[$card_identifier]['cardholder_name'] = $cur_customer->cardholder_name;
			    $top_spenders[$card_identifier]['cardholder_id'] = $cur_customer->id;
			    $top_spenders[$card_identifier]['number_of_visits']++;
			    $top_spenders[$card_identifier]['total_sales'] =  floatval($top_spenders[$card_identifier]['total_sales']) + floatval($cur_customer->amt);
                
    		}
        }
        
		/*
		* Finalize the Top Spenders array
		*/
        // Sort the Top Spenders array by total_sales (highest first)
        uasort($top_spenders, function ($a, $b) { return $b['total_sales'] - $a['total_sales']; });
        // Count the top 10 percent of the Top Spenders Array
		$top_spenders_count = count($top_spenders) * (10 / 100);
        // Remove everything except for the top 10 percent of the Top Spenders Array
		$top_spenders = array_slice($top_spenders, 0, round($top_spenders_count));
		    
	    
    	// Send data to JSON
    	$customer_data = json_encode(
    		array(
    			'status' => 'success',
    			'top_spenders' => $top_spenders
    		)
    	);
    } else {
	    
        // Display table
        $customer_data .= '
        <script>
	        window.onload = function() {
				Customers.init();
			};
		</script>
        <div class="row-fluid">
            <div class="span12">
                <div class="portlet box">
                    <div class="portlet-title">
                        <div class="caption">Top Spenders</div>
                    </div>
                    <div class="portlet-body">
                        <div class="content">
    						<table class="table table-striped table-hover table-bordered" id="show_top_spenders">
    							<thead>
    								<tr>
    									<th>Card Holder</th>
    									<th>Number of Visits</th>
    									<th>Total Sales</th>
    									<th>Actions</th>
    								</tr>
    							</thead>
    							<tbody id="top_spenders_table">
                                    <tr>
                                        <td colspan="4"><em class="ppttd-table-loading">Loading...</em></td>
                                    </tr>
    							</tbody>
    						</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Echo / return data
    if ($is_ajax == true) {
        echo $customer_data;
        die();
    } else {
        return $customer_data;
    }
}
add_shortcode('transactional_data_top_spenders', 'transactional_data_top_spenders');
add_action( 'wp_ajax_ppttd_td_top_spenders', 'transactional_data_top_spenders' );
add_action( 'wp_ajax_nopriv_ppttd_td_top_spenders', 'transactional_data_top_spenders' );


/**
 * Display Recent Customers
 */
function transactional_data_recent_customers() {
    global $wpdb;

    // Get merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

    // Check if merchant ID is being passed via AJAX
    $is_ajax = false;
    if (isset($_REQUEST['action'])) {
        $is_ajax = true;
    } else {
    	// Enqueue all required scripts and styles
    	wp_enqueue_script('init-customers-dashboard', '/wp-content/plugins/transactional-data/js/init_cust_dashboard.js', null, date('y.m.d'));
    	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
    	wp_enqueue_script('data-tables-bootstrap', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js');
    	wp_enqueue_style('css-styles-frontend', '/wp-content/plugins/transactional-data/css/style-frontend.css');
    }

	// Check if user is logged in
	if (!is_user_logged_in()) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to view your customer data.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to view your customer data.'));
            die();
        } else {
            return $the_error_message;
        }
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		$the_error_message = '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';

        // Echo if ajax, return if not
        if ($is_ajax == true) {
            echo json_encode(array('status' => 'error', 'message' => 'There are no merchant IDs associated with your account. Please login and add one or contact support.'));
            die();
        } else {
            return $the_error_message;
        }
    }

    // Display single or multiple merchant IDs
    $customer_data = "";

    // If ajax, load data, Else, load table
    if ($is_ajax == true) {
        // Set tables
        $bl_table = $wpdb->prefix."ppttd_batchlisting";
        $td_table = $wpdb->prefix."ppttd_transactionlisting";
        $cd_table = $wpdb->prefix."ppttd_customers";

        // Setup vars
		$recent_customers = array();

        // Get batch listings
        $transaction_batches = $wpdb->get_results("SELECT uniq_batch_id FROM ".$bl_table." WHERE merchant_id='".$merchant_id."'");
        foreach ($transaction_batches as $cur_batch) {
            // Get transactions for this batch
            $customers_query = $wpdb->get_results("SELECT ".$td_table.".amt,".$td_table.".card_lastfour,".$td_table.".card_type,".$td_table.".transaction_time,".$td_table.".merchant_id,".$cd_table.".cardholder_name,".$cd_table.".id FROM ".$td_table." LEFT JOIN ".$cd_table." ON ".$td_table.".card_lastfour = ".$cd_table.".last_four AND ".$td_table.".card_type = ".$cd_table.".card_type AND ".$td_table.".merchant_id=".$cd_table.".merchant_id WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."'");

            // Return customer data
            foreach ($customers_query as $cur_customer) {
    		    // Set card identifier, card type and card last four digits
    		    $card_identifier = $cur_customer->card_type.'-'.$cur_customer->card_lastfour;
    
				/*
				* Get the Recent Customers	
				*/
			    $recent_customers[$card_identifier]['cardholder_name'] = $cur_customer->cardholder_name;
			    $recent_customers[$card_identifier]['transaction_date'] = $cur_customer->transaction_time;
			    $recent_customers[$card_identifier]['transaction_time'] = date('m-d-Y', strtotime($cur_customer->transaction_time));
			    $recent_customers[$card_identifier]['cardholder_id'] = $cur_customer->id;
			    $recent_customers[$card_identifier]['number_of_visits']++;
			    $recent_customers[$card_identifier]['total_sales'] = floatval($recent_customers[$card_identifier]['total_sales']) + floatval($cur_customer->amt);
                
    		}
        }
		
		
		/*
		* Finalize the Recent Customers array
		*/
	    foreach ($recent_customers as $card_holder => $card_data) {
	        $transaction_time = strtotime($card_data['transaction_date']);
	        if ( $transaction_time < strtotime('-30 days') ) {
	            unset($recent_customers[$card_holder]);
	        }
	    }
		    
	    
    	// Send data to JSON
    	$customer_data = json_encode(
    		array(
    			'status' => 'success',
    			'recent_customers' => $recent_customers
    		)
    	);
    } else {
	    
        // Display table
        $customer_data .= '
        <script>
	        window.onload = function() {
				Customers.init();
			};
		</script>
        <div class="row-fluid">
            <div class="span12">
                <div class="portlet box">
                    <div class="portlet-title">
                        <div class="caption">Recent Customers</div>
                    </div>
                    <div class="portlet-body">
                        <div class="content">
    						<table class="table table-striped table-hover table-bordered" id="show_recent_customers">
    							<thead>
    								<tr>
    									<th>Card Holder</th>
    									<th>Visit Date</th>
    									<th>Number of Visits</th>
    									<th>Total Sales</th>
    									<th>Actions</th>
    								</tr>
    							</thead>
    							<tbody id="recent_customers_table">
                                    <tr>
                                        <td colspan="4"><em class="ppttd-table-loading">Loading...</em></td>
                                    </tr>
    							</tbody>
    						</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Echo / return data
    if ($is_ajax == true) {
        echo $customer_data;
        die();
    } else {
        return $customer_data;
    }
}
add_shortcode('transactional_data_recent_customers', 'transactional_data_recent_customers');
add_action( 'wp_ajax_ppttd_td_recent_customers', 'transactional_data_recent_customers' );
add_action( 'wp_ajax_nopriv_ppttd_td_recent_customers', 'transactional_data_recent_customers' );



/**
 * Edit customer data
 */
function ppttd_td_edit_customer() {
    global $wpdb;

    // Get merchant ID and customer ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;
    $customer_id = (isset($_REQUEST['the_customer_id'])) ? $_REQUEST['the_customer_id'] : null;

	// Check if user is logged in
	if (!is_user_logged_in()) {
		echo '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to modify customer data.</em>
				</div>
			</div>';
        die();
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		echo '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please add one or contact support.</em>
				</div>
			</div>';
        die();
    }

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($customer_id)) {
		echo '<div class="row-fluid">
				<div class="span12">
		            <em>Invalid customer ID. Please try again.</em>
				</div>
			</div>';
        die();
    }

    // Get customer record
    if (is_numeric($customer_id)) {
        $customer_record = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ppttd_customers WHERE id='".$customer_id."' AND merchant_id='".$merchant_id."'");
        $customer_record = $customer_record[0];
    } else {
        // Get card type and last four digits
        $card_data = explode('-', $customer_id);

        // Try to insert record
        $the_data = $wpdb->insert($wpdb->prefix.'ppttd_customers', array('merchant_id' => $merchant_id, 'card_type' => $card_data[0], 'last_four' => $card_data[1]));
            
        if ($the_data !== false) {
            // Set customer ID
            $customer_id = $wpdb->insert_id;
        } else {
            echo '  <div class="row-fluid">
                <div class="span12">
                    <em>There was an error inserting your customer information. Please reload the page and try again.</em>
                </div>
            </div>';
            die();
        }
    }

    // Set card type and last four vars
    $the_card_type = (isset($card_data)) ? $card_data[0] : $customer_record->card_type;
    $the_card_last_four = (isset($card_data)) ? $card_data[1] : $customer_record->last_four;
    $the_customer_id = (isset($customer_id)) ? $customer_id : $customer_record->id;

    // Display user editing table
    echo '    <div class="row-fluid">
        <div class="span12">
            <div class="portlet box">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-reorder white-icon"></i>Edit Customer</div>
                </div>
                <div class="portlet-body">
                    <div class="content">
                        <div class="tab-pane active">
                            <form>
        						<table class="table table-striped table-hover table-bordered">
        							<tbody id="returning_customers_table">
                                        <tr>
                                            <td>Card Type</td>
                                            <td>'.$the_card_type.'</td>
                                        </tr>
                                        <tr>
                                            <td>Card Last Four Digits</td>
                                            <td>'.$the_card_last_four.'</td>
                                            <input type="hidden" class="customer-data" id="id" value="'.$the_customer_id.'" />
                                            <input type="hidden" class="customer-data" id="merchant_id" value="'.$merchant_id.'" />
                                        </tr>
                                        <tr>
                                            <td>Cardholder Name</td>
                                            <td><input type="text" class="customer-data" id="cardholder_name" value="'.$customer_record->cardholder_name.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder Address</td>
                                            <td><input type="text" class="customer-data" id="address" value="'.$customer_record->address.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder Address 2</td>
                                            <td><input type="text" class="customer-data" id="address2" value="'.$customer_record->address2.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder City</td>
                                            <td><input type="text" class="customer-data" id="city" value="'.$customer_record->city.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder State</td>
                                            <td><input type="text" class="customer-data" id="state" value="'.$customer_record->state.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder Zip Code</td>
                                            <td><input type="text" class="customer-data" id="zip_code" value="'.$customer_record->zip_code.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder Phone</td>
                                            <td><input type="text" class="customer-data" id="phone" value="'.$customer_record->phone.'" /></td>
                                        </tr>
                                        <tr>
                                            <td>Cardholder Email</td>
                                            <td><input type="text" class="customer-data" id="email" value="'.$customer_record->email.'" /></td>
                                        </tr>
        							</tbody>
        						</table>
        						<input type="hidden" class="customer-data" id="id" value="'.$the_customer_id.'" />
                            </form>
    						<input type="button" class="btn blue" onclick="Customers.save_customer_data();" value="Save Customer" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    // Echo form
    die();
}
add_action( 'wp_ajax_ppttd_td_edit_customer', 'ppttd_td_edit_customer' );
add_action( 'wp_ajax_nopriv_ppttd_td_edit_customer', 'ppttd_td_edit_customer' );


/**
 * Save new customer data
 */
function ppttd_td_save_customer() {
    global $wpdb;

    // Get all data
    $update_data = array();
    $remove_keys = array('id','action');
    foreach ($_REQUEST as $key => $value) {
        if (!in_array($key, $remove_keys)) {
            $update_data[$key] = urldecode($value);
        }
    }

    // Check for merchant ID
    $merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;

	// Check if user is logged in
	if (!is_user_logged_in()) {
		echo '<div class="row-fluid">
				<div class="span12">
		            <em>You must be logged in to update customer data.</em>
				</div>
			</div>';
        die();
	}

    // Make sure there is at least one merchant ID associated with the account
    if (is_null($merchant_id)) {
		echo '<div class="row-fluid">
				<div class="span12">
		            <em>There are no merchant IDs associated with your account. Please login and add one or contact support.</em>
				</div>
			</div>';
        die();
    }

    // Try to insert record
    $the_data = $wpdb->update($wpdb->prefix.'ppttd_customers', $update_data, array('id' => $_REQUEST['id']));

    // Check if query was successful or not
    if ($the_data === false) {
        echo '  <div class="row-fluid">
            <div class="span12">
                <em>There was an error updating your customer information. Please reload the page and try again.</em>
            </div>
        </div>';
        die();
    }

    // Display user editing table
    echo '  <div class="row-fluid">
        <div class="span12">
            <em>Customer updated successfully!</em>
        </div>
    </div>';

    // Echo form
    die();
}
add_action( 'wp_ajax_ppttd_td_save_customer', 'ppttd_td_save_customer' );
add_action( 'wp_ajax_nopriv_ppttd_td_save_customer', 'ppttd_td_save_customer' );


// Register User Contact Methods
function user_contactmethods( $user_contact_method ) {
    $user_contact_method['merchant_emails'] = __( 'Additional Emails <span class="description">(multiple: Comma separated emails)</span>', 'GWP' );
    return $user_contact_method;
}
// Hook into the 'user_contactmethods' filter
add_filter( 'user_contactmethods', 'user_contactmethods' );



// Cash Advance dashboard ad
function dashboard_cash_advance() {
	global $wpdb;

	// Check if user is logged in
	if (!is_user_logged_in()) {
		return '$0.00';
	}
	
	// Get merchant ID
	$merchant_data = get_the_author_meta('ppttd_merchant_info', get_current_user_id());

	// Get merchant ID
    $merchant_id = get_merchant_ids();

    // Return blank if no merchant ID set
    if (is_null($merchant_id)) {
        return '$0.00';
    }

    // table names
    $ppttd_goals				= $wpdb->prefix."ppttd_goals";
	$ppttd_batchlisting			= $wpdb->prefix."ppttd_batchlisting";
    $ppttd_transactionlisting	= $wpdb->prefix."ppttd_transactionlisting";

	// Timeframes
	$three_months_ago		= date('Y-m-d 00:00:00', strtotime('-3 months'));
	$today					= date('Y-m-d 23:59:59');
	
	
	$sql_timeframe = "batch_date BETWEEN '".$three_months_ago."' AND '".$today."'";
	
	$total_amt = 0;
	foreach ($merchant_id as $cur_merchant_data) {
		// Set up arrays
		$transactional_data = array();
		$returning_customers = array();
		$amounts = array();
		
		
		// Select timeframe based on #goal_select
		$transaction_batches = $wpdb->get_results("SELECT uniq_batch_id,total_volume,batch_date FROM ".$ppttd_batchlisting." WHERE merchant_id='".$cur_merchant_data->merchant_id."' AND ".$sql_timeframe." ORDER BY batch_date");
		
		// Get transactions for each batch
		foreach ($transaction_batches as $cur_batch) {
			// Set daily transaction amounts
			$batch_date = date('m-d-Y', strtotime($cur_batch->batch_date));
			
			// Get transactions
			$get_transactional_data = $wpdb->get_results("SELECT card_type,amt,transaction_time,card_lastfour FROM  ".$ppttd_transactionlisting." WHERE uniq_batch_id = '".$cur_batch->uniq_batch_id."' ORDER BY transaction_time");
	
	        // Loop through results to get data
			foreach ($get_transactional_data as $cur_transaction) {
			    // Set card identifier, card type and card last four digits
			    $card_identifier = $cur_transaction->card_type.'-'.$cur_transaction->card_lastfour;
	
			    // Check for customer loyalty
			    if (!array_key_exists($card_identifier, $returning_customers)) {
				    $returning_customers[$card_identifier] = array();
			    }
	
				// Get amounts
				array_push($amounts, $cur_transaction->amt);
			}
		}
		// Grab the total customer count before removing single visit customers
		$total_customers = $returning_customers;
	
	    $amt_sum		= array_sum($amounts);
	    $amt_average	= (count($amounts) == 0) ? 0 : $amt_sum / count($amounts);
	    
		$avgTicketSales	= '$'.number_format( $amt_average, 2, '.', ',' );
		$total		= '$'.number_format( $amt_sum, 2, '.', ',' );
		
		$three_month_avg = $amt_sum/3;
		$three_month_avg = .7 * $three_month_avg;
		$total_amt = $total_amt+$three_month_avg;
	}
	
	$three_month_avg = '$'.number_format( $total_amt, 2, '.', ',' );
    
    return $three_month_avg;
}


/**
 * START TRANSACTIONAL DATA LOGS
 **/
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Get cron logs
 */
function get_cron_logs() {
    global $wpdb;

    $return_logs = array();
    
    $cron_logs = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ppttd_log ORDER BY id,timestamp DESC', ARRAY_A);
    foreach ($cron_logs as $cur_log) {
        $cur_log['timestamp'] = date('F d, Y @ H:i:s', $cur_log['timestamp']);
        $return_logs[] = $cur_log;
    }

    return $return_logs;
}

// Cron logs table
class My_Example_List_Table extends WP_List_Table {
    var $example_data = array();

    function __construct() {
        $this->example_data = get_cron_logs();

        global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'cron_error', 'mylisttable' ),
            'plural'    => __( 'cron_errors', 'mylisttable' ),
            'ajax'      => false
        ));
        add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }

    function admin_header() {
        // Had to add this to make WP happy
    }

    function no_items() {
        _e( '<em>No logs found.</em>' );
    }
    
    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'timestamp':
            case 'error_code':
            case 'error_text':
                return $item[ $column_name ];
            default:
                return print_r( $item, true );
        }
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'timestamp'  => array('timestamp',false),
            'error_code'  => array('error_code',false),
            'error_text' => array('error_text',false)
        );
        return $sortable_columns;
    }
    
    function get_columns(){
        $columns = array(
            'timestamp' => __( 'Time', 'mylisttable' ),
            'error_code' => __( 'Error Code', 'mylisttable' ),
            'error_text'    => __( 'Error', 'mylisttable' )
        );
        return $columns;
    }
    
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'timestamp';
    
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
    
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
    
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        usort( $this->example_data, array( &$this, 'usort_reorder' ) );

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count( $this->example_data );

        // only ncessary because we have sample data
        $this->found_data = array_slice( $this->example_data,( ( $current_page-1 )* $per_page ), $per_page );

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page                     //WE have to determine how many items to show on a page
        ) );
        $this->items = $this->found_data;
    }
}
   
function sales_data_logs_menu_item(){
  $hook = add_menu_page( 'Sales Data Logs', 'Sales Data Logs', 'activate_plugins', 'sales_data_cron_logs', 'sales_data_cron_logs' );
  add_action( "load-$hook", 'add_options' );
}

function add_options() {
  global $myListTable;
  $myListTable = new My_Example_List_Table();
}
add_action( 'admin_menu', 'sales_data_logs_menu_item' );



function sales_data_cron_logs(){
  global $myListTable;
  echo '</pre><div class="wrap"><h2>Sales Data Cron Job Logs</h2>'; 
  $myListTable->prepare_items(); 
?>
  <form method="post">
    <input type="hidden" name="page" value="ttest_list_table">
    <?php
        $myListTable->display(); 
        echo '</form></div>'; 
}
/**
 * END TRANSACTIONAL DATA LOGS
 **/
?>