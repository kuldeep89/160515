<?php
/**
 * Accept POST request from mailgun
 */
function mr_add_log_item() {
    // Ignore POST data for other domains
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && stripos($_SERVER['HTTP_USER_AGENT'], 'mailgun') !== false) {
        // Include wp database
        global $wpdb;

        // Table to query columns from
        $table_name = $wpdb->prefix.'mailgun_stats';

        // Replace '-' from POST var keys and replace with '_' so they match up with db fields
        foreach ($_POST as $key => $value) {
            // Remove old POST key
            unset($_POST[$key]);

            // Replace dashes with underscores
            $key = str_replace('-', '_', strtolower($key));

            // Set new key
            $_POST[$key] = $value;
        }

        // Assign data to db query
        $db_data = array();
        foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
            if (isset($_POST[$column_name]) && trim($_POST[$column_name]) !== '') {
                $db_data[$column_name] = $_POST[$column_name];
            }
        }

        // Insert event into database
        $wpdb->insert( $table_name, $db_data );

        // Echo success response
        http_response_code(200);

        die();
    } else {
        // Echo 404 response
        http_response_code(404);
    }
}
add_action('wp_ajax_mes_add_log_item', 'mr_add_log_item');
add_action('wp_ajax_nopriv_mes_add_log_item', 'mr_add_log_item');


/**
 * Get email logs
 */
function mr_get_mail_logs() {
    global $wpdb;

    // Array to store logs in
    $return_logs = array();

    // Select specific log types
    $select_type = '';
    if (isset($_SESSION['log_type']) && trim($_SESSION['log_type']) !== '') {
        $select_type = " WHERE event = '".$_SESSION['log_type']."'";
    }
    
    // Get email logs
    $mail_logs = $wpdb->get_results('SELECT timestamp,event,recipient,device_type,client_os FROM '.$wpdb->prefix.'mailgun_stats'.$select_type.' ORDER BY id,timestamp DESC', ARRAY_A);
    foreach ($mail_logs as $cur_log) {
        $cur_log['timestamp'] = date('F d, Y @ H:i:s', $cur_log['timestamp']);
        $return_logs[] = $cur_log;
    }

    return $return_logs;
}


function smrt_mail_stats_list(){
    global $wpdb;

    $SMRTMailgunStatsTable = new SMRTMailgunStatsTable();
    $SMRTMailgunStatsTable->prepare_items(); 

    echo '</pre><div class="wrap"><h2>Mailgun Stats</h2>'; 
?>
  <form method="post">
    <input type="button" id="export-mailgun-stats" value="Export" onclick="MailgunStatsReport.export();" class="button button-primary button-large" />
    <select name="log_type" onchange="this.form.submit()">
        <option value="">Show All Events</option>
        <?php
            // Set options
            $mes_filter_options = array('Delivered', 'Opened', 'Clicked', 'Complained', 'Bounced', 'Dropped');
            foreach ($mes_filter_options as $key => $value) {
                if (strtolower($value) === $_SESSION['log_type']) {
                    echo '<option value="'.strtolower($value).'" selected>'.$value.'</option>';
                } else {
                    echo '<option value="'.strtolower($value).'">'.$value.'</option>';
                }
            }
        ?>
    </select>
    <select name="per_page" onchange="this.form.submit()">
        <?php
            $per_page_options = array(10, 25, 50, 100);
            foreach ($per_page_options as $cur_option) {
                if ($cur_option == $_SESSION['log_per_page']) {
                    echo '<option value="'.$cur_option.'" selected>'.$cur_option.' Items Per Page</option>';
                } else {
                    echo '<option value="'.$cur_option.'">'.$cur_option.' Items Per Page</option>';
                }
            }  
        ?>
    </select>
    <input type="hidden" name="page" value="mes_stats_table">
    <?php
        $SMRTMailgunStatsTable->display(); 
        echo '</form><iframe id="exportIframe" src="" style="display:none;visibility:hidden;"></iframe></div>'; 
}


/**
 * Export merchants with no data to CSV function
 */
function export_mailgun_stats() {
    // Get global vars
    global $wpdb;

    // Get transactions
    $get_mail_logs = mr_get_mail_logs();

    // Define exports folder
    $folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/transactional-data/export/";
	
	// Set start date, end date, and file name
	$filename = "Mailgun_Logs_".date('Y-m-d_H-i-s').".csv";
	$file_uri = $folder.$filename;
	
	// Set output file
	$output = fopen($file_uri,'w+');		
	
	// Add header line to CSV
	fputcsv($output, array('Time', 'Event', 'Recipient', 'Device Type', 'Device OS'));
	
	// Add each row to CSV
	foreach($get_mail_logs as $log_data) {
    	// Put data in CSV
	    fputcsv( $output, array('timestamp' => $log_data['timestamp'], 'event' => $log_data['event'], 'recipient' => $log_data['recipient'], 'device_type' => $log_data['device_type'], 'device_os' => $log_data['device_os'] ) );
	}

    // Close CSV file
	fclose($output);

    // Echo success
	echo json_encode( array( 'status' => 'success', 'file_name' => $filename ) );

    die();
}
add_action( 'wp_ajax_export_mailgun_stats', 'export_mailgun_stats' );
?>