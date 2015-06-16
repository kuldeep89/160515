<?php
/**
 * Plugin Name: Merchant Tax Documents
 * Author: Curtis
 * Description: Tax document access for merchants.
 * Version: 0.0.1
*/


/**
 * Runs when plugin is activated
 */
register_activation_hook(__FILE__,'k_install'); 


/**
 * Creates new database field(s) associated with plugin
 */
function k_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
}

/**
 * Embed shortcode for merchant file display
 */
function k_files($merchant_id = null) {
    global $wpdb;
    $is_ajax = false;

    // Check if ajax
    if (isset($_REQUEST['the_merchant_id']) && trim($_REQUEST['the_merchant_id']) != null) {
        $merchant_id = $_REQUEST['the_merchant_id'];
        $cur_merchant_id = $merchant_id;
        $is_ajax = true;
    } else {
        // Get merchant info
        $merchant_info = get_the_author_meta('ppttd_merchant_info', get_current_user_id());

        // Get merchant ID
    	$merchant_id = (isset($merchant_info['ppttd_merchant_id'])) ? $merchant_info['ppttd_merchant_id'] : null;

    	// Show merchant IDs
        $merchant_ids ='';
        if (gettype($merchant_id) == 'array') {
            foreach ($merchant_id as $key => $value) {
                $value = (trim($value) == '') ? $key : $value;
            }
            $cur_merchant_id = array_keys($merchant_id);
            $cur_merchant_id = array_shift($cur_merchant_id);
        } else {
            $merchant_id = explode(',', $merchant_id);
            $cur_merchant_id = $merchant_id[0];
        }
    }

    // Pad merchant IDs to 16 characters in length
    $cur_merchant_id = str_pad($cur_merchant_id, 16, '0', STR_PAD_LEFT);

	// Check if user is logged in
	if (!is_user_logged_in() || is_null($merchant_id)) {
	    $the_message = '<div class="row-fluid">
    			<div class="span12">
    	            <em>You must have a merchant account set to view your 1099-K\'s.</em>
    			</div>
    		</div>';

        // Echo if ajax, return if not
		if ($is_ajax == true) {
    		echo $the_message;
        } else {
            return $the_message;
        }
	}

	// Enqueue scripts/styles
	wp_enqueue_script('data-tables', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js');
	wp_enqueue_style('data-tables-style', '/wp-content/themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.css');
    wp_enqueue_script('init-statements', '/wp-content/plugins/1099k/js/1099k.js');
    wp_enqueue_style('css-styles', '/wp-content/plugins/1099k/css/style.css');
    
	require_once('lib/class.rs_cdn.php');
	require_once('lib/functions.php');
	
	if(check_cdn()){
	    $the_cdn = new RS_CDN();
	    
		$files = $the_cdn->get_cdn_objects();
		$the_files = '';
	    foreach($files as $file){
			$path_parts = pathinfo($file['file_name']);
		    $loop_merchant_id = (isset($merchant_info['ppttd_merchant_id'])) ? $merchant_info['ppttd_merchant_id'] : null;
			
	        if (gettype($loop_merchant_id) == 'array') {
		        
	            foreach ($loop_merchant_id as $key => $value) {
	                $value = (trim($value) == '') ? $key : $value;
				    if( trim($path_parts['filename']) == ltrim( $key, '0') ){
					    $the_link = "/wp-content/plugins/1099k/lib/download.php?the_year=".$path_parts['dirname']."&the_merchant_id=".$path_parts['filename'];
						$the_files .=	'<tr>
											<td>
												'.$path_parts['dirname'].'
											</td>
											<td>
												'.$path_parts['filename'].'
											</td>
											<td>
												'.$value.'
											</td>
											<td>
												<a href="'.$the_link.'" target="_blank">Download</a>
											</td>
										</tr>';
				    }
	            }
	        } else {
	            $loop_merchant_id = explode(',', $loop_merchant_id);
	            $cur_merchant_id = $loop_merchant_id[0];
			    $cur_merchant_name = (trim($cur_merchant_value) == "" || $cur_merchant_id == $cur_merchant_value) ? $cur_merchant_id : $cur_merchant_value;
			    $cur_merchant_name = (strlen($cur_merchant_id) >= 8) ? $cur_merchant_name : $cur_merchant_value;
			    if( trim($path_parts['filename']) == ltrim( $cur_merchant_id, '0') ){
				    $the_link = "/wp-content/plugins/1099k/lib/download.php?the_year=".$path_parts['dirname']."&the_merchant_id=".$path_parts['filename'];
				    $the_files .=	'<tr>
										<td>
											'.$path_parts['dirname'].'
										</td>
										<td>
											'.$path_parts['filename'].'
										</td>
										<td>
											'.$cur_merchant_name.'
										</td>
										<td>
											<a href="'.$the_link.'" target="_blank">Download</a>
										</td>
									</tr>';
			    }
	        }
	    }
	    if( empty($the_files) ){
			$tableHead = '';
		    $the_files = '<tr><td colspan="4"><em>No files available.</td></tr>';
	    } else {
			$tableHead = '<thead>
				            <tr>
								<th width="50px">Year</th>
								<th>MID</th>
								<th>Name</th>
								<th>Download</th>
				            </tr>
				        </thead>';
	    }
	    
	    echo '  <script>
		            window.onload = function() {
		    			Docs.init();
		    		};
		    	</script>
				<div class="row-fluid">
					<div class="span4">
						<a href="/sales-data/" class="light-blue-button" style="width:200px;">< Back to Sales Data</a>
						<div class="col-header">1099-K files</div>
						<div id="container_1099k">
						    <table>
						        '.$tableHead.'
						        <tbody>
						    	'.$the_files.'
						    	</tbody>
						    </table>
						</div>
					</div>
				</div>';
	} else{
		echo 'no';
	}
}
add_shortcode('k_files', 'k_files');
add_action( 'wp_ajax_k_files', 'k_files' );
add_action( 'wp_ajax_nopriv_k_files', 'k_files' );

?>