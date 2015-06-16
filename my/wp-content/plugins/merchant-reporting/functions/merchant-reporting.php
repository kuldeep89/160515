<?php
/**
 * Get data
 */
function smrt_get_merchant_data($action = null) {
    global $wpdb;

    // Array to store merchants in
    $return_merchants = array();

    // Reward points
    if ($_GET['page'] === 'smrt_merchant_rewards_list' || $action === 'smrt_merchant_rewards_list') {
        // Get reward points
        $merchant_points = $wpdb->get_results('SELECT rp.merchant_id,rp.points,mur.user_id FROM '.$wpdb->prefix.'ppttd_reward_points AS rp JOIN '.$wpdb->prefix.'merchant_user_relationships AS mur ON rp.merchant_id=mur.merchant_id');

        // Add merchant and points
        foreach ($merchant_points as $cur_merchant) {
            // Set merchant if not already set
            if (!isset($return_merchants[$cur_merchant->merchant_id])) {
                // Setup merchant info
                $return_merchants[$cur_merchant->merchant_id] = array('merchant_id' => $cur_merchant->merchant_id, 'reward_points' => $cur_merchant->points, 'users' => array());
            }

            // Add merchant ID(s) to user
            $return_merchants[$cur_merchant->merchant_id]['users'][] = $cur_merchant->user_id;
        }
    }

    // Merchants with no data
    if ($_GET['page'] === 'smrt_merchant_data_list' || $action === 'smrt_merchant_data_list') {
        // Get merchants in batch table
        $batch_merchants = $wpdb->get_results("SELECT DISTINCT merchant_id FROM ".$wpdb->prefix."ppttd_batchlisting");

        // Select specific user type
        if (isset($_SESSION['member_level']) && trim($_SESSION['member_level']) !== '') {
            $mdl_select_type = " ON gt.group_id = ".$_SESSION['member_level'];
        } else {
            $mdl_select_type = " ON gt.group_id > 0";
        }

        // Get merchants assigned to users
        $user_merchants = $wpdb->get_results("SELECT mur.user_id,mur.merchant_id FROM ".$wpdb->prefix."merchant_user_relationships AS mur LEFT JOIN ".$wpdb->prefix."groups_user_group AS gt ".$mdl_select_type." WHERE gt.user_id=mur.user_id");

        // Check if user merchant exists in batch table
        foreach ($user_merchants as $cur_user_merchant) {
            if (smrt_search_object($cur_user_merchant->merchant_id, $batch_merchants, 'merchant_id') === false) {
                // Check if already in array
                if (!array_key_exists($cur_user_merchant->merchant_id, $return_merchants)) {
                    // Add to merchant
                    $return_merchants[$cur_user_merchant->merchant_id] = array('merchant_id' => $cur_user_merchant->merchant_id, 'users' => array());
                }

                // Add to merchant
                if (!in_array($cur_user_merchant->user_id, $return_merchants[$cur_user_merchant->merchant_id]['users'])) {
                    $return_merchants[$cur_user_merchant->merchant_id]['users'][] = $cur_user_merchant->user_id;
                }
            }
        }
    } 

    // List users and their merchant IDs
    if ($_GET['page'] === 'smrt_merchant_list' || $action === 'smrt_merchant_list' || ($_GET['page'] === '' && $action === '') || (!isset($_GET['page']) && !isset($action))) {
        // Select specific log types
        $user_select_type = '';
        if (isset($_SESSION['member_level']) && trim($_SESSION['member_level']) !== '') {
            $user_select_type = " ON gt.user_id=mur.user_id WHERE gt.group_id = '".$_SESSION['member_level']."'";
        } else {
            $user_select_type = " ON gt.user_id=mur.user_id";
        }
    
        // Get all users in db
        $users = $wpdb->get_results('SELECT gt.user_id,mur.merchant_id FROM '.$wpdb->prefix.'groups_user_group AS gt JOIN '.$wpdb->prefix.'merchant_user_relationships AS mur '.$user_select_type.' GROUP BY gt.user_id,mur.merchant_id');
    
        // Loop through merchants, return them
        foreach ($users as $cur_user) {
            // If user is not in array, add them
            if (!array_key_exists($cur_user->user_id, $return_merchants)) {
                $return_merchants[$cur_user->user_id] = array('user_id' => $cur_user->user_id, 'merchant_ids' => array());
            }
    
            // Add merchant ID(s) to user
            $return_merchants[$cur_user->user_id]['merchant_ids'][] = $cur_user->merchant_id;
        }
    }

    // Return merchant IDs
    return $return_merchants;
}


/**
 * Show merchant IDs table
 */
function smrt_merchant_list(){
    global $wpdb;
    global $SMRTMerchantIdsTable;

    echo '</pre><div class="wrap"><h2>List Merchant IDs</h2>'; 

    $SMRTMerchantIdsTable = new SMRTMerchantIdsTable();
    $SMRTMerchantIdsTable->prepare_items(); 
?>
  <form method="post">
    <input type="button" id="export-merchant-ids" value="Export" onclick="MerchantIdsReport.export();" class="button button-primary button-large" />
    <select name="member_level" onchange="this.form.submit()">
        <option value="">Show All Merchant Types</option>
        <?php
            // Get membership types
            $membership_levels = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'groups_group');
            foreach ($membership_levels as $cur_level) {
                // Echo membership levels
                if ($cur_level->group_id === $_SESSION['member_level']) {
                    echo '<option value="'.$cur_level->group_id.'" selected>'.$cur_level->name.'</option>';
                } else {
                    echo '<option value="'.$cur_level->group_id.'">'.$cur_level->name.'</option>';
                }
            }
        ?>
    </select>
    <select name="per_page" onchange="this.form.submit()">
        <?php
            $per_page_options = array(10, 25, 50, 100);
            foreach ($per_page_options as $cur_option) {
                if ($cur_option == $_SESSION['smrt_per_page']) {
                    echo '<option value="'.$cur_option.'" selected>'.$cur_option.' Items Per Page</option>';
                } else {
                    echo '<option value="'.$cur_option.'">'.$cur_option.' Items Per Page</option>';
                }
            }  
        ?>
        <option value="all">Show All Merchants</option>
    </select>
    <input type="hidden" name="page" value="smrt_stats_table">
    <?php
        $SMRTMerchantIdsTable->display(); 
        echo '</form><iframe id="exportIframe" src="" style="display:none;visibility:hidden;"></iframe></div>'; 
}


/**
 * Show merchants with no data table
 */
function smrt_merchant_data_list(){
    global $wpdb;

    $SMRTMerchantDataTable = new SMRTMerchantDataTable();
    $SMRTMerchantDataTable->prepare_items(); 

    echo '</pre><div class="wrap"><h2>Merchants With No Data</h2>'; 
?>
  <form method="post">
    <input type="button" id="export-merchants-no-data" value="Export" onclick="MerchantDataReport.export();" class="button button-primary button-large" />
    <select name="member_level" onchange="this.form.submit()">
        <option value="">Show All Merchant Types</option>
        <?php
            // Get membership types
            $membership_levels = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'groups_group');
            foreach ($membership_levels as $cur_level) {
                // Echo membership levels
                if ($cur_level->group_id === $_SESSION['member_level']) {
                    echo '<option value="'.$cur_level->group_id.'" selected>'.$cur_level->name.'</option>';
                } else {
                    echo '<option value="'.$cur_level->group_id.'">'.$cur_level->name.'</option>';
                }
            }
        ?>
    </select>
    <select name="per_page" onchange="this.form.submit()">
        <?php
            $per_page_options = array(10, 25, 50, 100);
            foreach ($per_page_options as $cur_option) {
                if ($cur_option == $_SESSION['smrt_per_page']) {
                    echo '<option value="'.$cur_option.'" selected>'.$cur_option.' Items Per Page</option>';
                } else {
                    echo '<option value="'.$cur_option.'">'.$cur_option.' Items Per Page</option>';
                }
            }  
        ?>
        <option value="all">Show All Merchants</option>
    </select>
    <input type="hidden" name="page" value="smrt_stats_table">
<!--
    <br/><br/><br/>
    <em>This report can only be exported due to the large amount of data it processes.</em>
-->
    <?php
        $SMRTMerchantDataTable->display(); 
        echo '</form><iframe id="exportIframe" src="" style="display:none;visibility:hidden;"></iframe></div>'; 
}


/**
 * Show merchant rewards table
 */
function smrt_merchant_rewards_list(){
    global $wpdb;

    $SMRTMerchantRewardsTable = new SMRTMerchantRewardsTable();
    $SMRTMerchantRewardsTable->prepare_items(); 

    echo '</pre><div class="wrap"><h2>Merchant Reward Points</h2>'; 
?>
  <form method="post">
    <input type="button" id="export-merchant-rewards" value="Export" onclick="MerchantRewardsReport.export();" class="button button-primary button-large" />
    <select name="per_page" onchange="this.form.submit()">
        <?php
            $per_page_options = array(10, 25, 50, 100);
            foreach ($per_page_options as $cur_option) {
                if ($cur_option == $_SESSION['smrt_per_page']) {
                    echo '<option value="'.$cur_option.'" selected>'.$cur_option.' Items Per Page</option>';
                } else {
                    echo '<option value="'.$cur_option.'">'.$cur_option.' Items Per Page</option>';
                }
            }  
        ?>
        <option value="all">Show All Merchants</option>
    </select>
    <input type="hidden" name="page" value="smrt_stats_table">
    <?php
        $SMRTMerchantRewardsTable->display(); 
        echo '</form><iframe id="exportIframe" src="" style="display:none;visibility:hidden;"></iframe></div>'; 
}

/**
 * Export merchant IDs to CSV function
 */
function export_merchant_ids() {
    // Get global vars
    global $wpdb;

    // Get transactions
    $get_transactional_data = smrt_get_merchant_data();

    // Define exports folder
    $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
	
	// Set start date, end date, and file name
	$filename = "Merchant_IDs_".date('Y-m-d_H-i-s').".csv";
	$file_uri = $folder.$filename;
	
	// Set output file
	$output = fopen($file_uri,'w+');		
	
	// Add header line to CSV
	fputcsv($output, array('User ID', 'Merchant IDs', 'Date Registered'));
	
	// Add each row to CSV
	foreach($get_transactional_data as $merchant_data) {
    	// Get user's date registered
    	$get_user = get_user_by('id', $merchant_data['user_id']);
        $date_registered = $get_user->user_registered;

    	 // Put data in CSV
	    fputcsv( $output, array('user_id' => $merchant_data['user_id'], 'merchant_ids' => implode(', ', $merchant_data['merchant_ids']), 'date_registered' => $date_registered) );
	}

    // Close CSV file
	fclose($output);

    // Echo success
	echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );

    die();
}
add_action( 'wp_ajax_export_merchant_ids', 'export_merchant_ids' );


/**
 * Export merchants with no data to CSV function
 */
function export_merchant_data() {
    // Get global vars
    global $wpdb;

    // Get transactions
    $get_transactional_data = smrt_get_merchant_data('smrt_merchant_data_list');

    // Define exports folder
    $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
	
	// Set start date, end date, and file name
	$filename = "Merchants_No_Data_".date('Y-m-d_H-i-s').".csv";
	$file_uri = $folder.$filename;
	
	// Set output file
	$output = fopen($file_uri,'w+');		
	
	// Add header line to CSV
	fputcsv($output, array('Merchant ID', 'User IDs Using Merchant ID'));
	
	// Add each row to CSV
	foreach($get_transactional_data as $merchant_data) {
    	// Set vars
    	$merchant_users = (count($merchant_data['users']) > 0) ? implode(', ', $merchant_data['users']) : '--';
	    
	    // Put data in CSV
	    fputcsv( $output, array('merchant_id' => $merchant_data['merchant_id'], 'user_ids_using_merchant_id' => $merchant_users ) );
	}

    // Close CSV file
	fclose($output);

    // Echo success
	echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );

    die();
}
add_action( 'wp_ajax_export_merchant_data', 'export_merchant_data' );


/**
 * Export merchant reward points to CSV
 */
function export_merchant_reward_points() {
    // Get global vars
    global $wpdb;

    // Get transactions
    $get_transactional_data = smrt_get_merchant_data('smrt_merchant_rewards_list');

    // Define exports folder
    $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
	
	// Set start date, end date, and file name
	$filename = "Merchants_Rewards_".date('Y-m-d_H-i-s').".csv";
	$file_uri = $folder.$filename;
	
	// Set output file
	$output = fopen($file_uri,'w+');		
	
	// Add header line to CSV
	fputcsv($output, array('Merchant ID', 'Reward Points', 'User IDs Using Merchant ID'));
	
	// Add each row to CSV
	foreach($get_transactional_data as $merchant_data) {
    	// Set vars
    	$merchant_reward_points = (isset($merchant_data['reward_points']) && trim($merchant_data['reward_points']) !== '') ? $merchant_data['reward_points'] : '0';
    	$merchant_users = (count($merchant_data['users']) > 0) ? implode(', ', $merchant_data['users']) : '--';
	    
	    // Put data in CSV
	    fputcsv( $output, array($merchant_data['merchant_id'], $merchant_reward_points, $merchant_users ) );
	}

    // Close CSV file
	fclose($output);

    // Echo success
	echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );

    die();
}
add_action( 'wp_ajax_export_merchant_reward_points', 'export_merchant_reward_points' );


/**
 * Search object
 */
function smrt_search_object($needle = null, $haystack = null, $obj_key = null) {
    foreach ($haystack as $cur_bale) {
        if ($needle === $cur_bale->$obj_key) {
            return true;
        }
    }
    return false;
}
?>