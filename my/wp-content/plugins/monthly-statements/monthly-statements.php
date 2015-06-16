<?php
/**
 * Plugin Name: Monthly Statements
 * Author: Bobbie Stump
 * Description: Monthly statements for merchants.
 * Version: 0.0.1
*/


/**
 * Runs when plugin is activated
 */
register_activation_hook(__FILE__,'smms_install'); 


/**
 * Runs on plugin deactivation
 */
register_deactivation_hook( __FILE__, 'smms_remove' );


/**
 * Creates new database field(s) associated with plugin
 */
function smms_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;

	// Setup db tables
	$smms_statements = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."smms_monthly_statements` (
        `id` bigint(20) NOT NULL,
        `merchant_id` varchar(16) NOT NULL,
        `month` varchar(2) NOT NULL,
        `year` varchar(4) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	dbDelta( $smms_statements );
}


/**
 * Embed shortcode for merchant statement display
 */
function smms_monthly_statements($merchant_id = null) {
    global $wpdb;

    // Check if AJAX request
    $is_ajax = (isset($_REQUEST['action'])) ? true : false;

    // Pad merchant IDs to 16 characters in length
    $merchant_id = $_SESSION['active_mid']['merchant_id'];
    $statement_year = (isset($_REQUEST['the_statement_year']) && is_numeric($_REQUEST['the_statement_year'])) ? $_REQUEST['the_statement_year'] : date('Y');

	// Check if user is logged in
	if (!is_user_logged_in() || is_null($merchant_id)) {
	    $the_message = '<div class="row-fluid">
    			<div class="span12">
    	            <em>You must have a merchant account set up to view your monthly statements.</em>
    			</div>
    		</div>';

        // Echo if ajax, return if not
		if ($is_ajax == true) {
    		echo json_encode(array('status' => 'error', 'message' => 'You must have a merchant account set to view your monthly statements.'));
        } else {
            return $the_message;
        }
	}

	// Enqueue some scripts/styles in all environments
    wp_enqueue_script('init-statements', '/wp-content/plugins/monthly-statements/js/init_statements.js');

    // Enqueue some scripts/styles only in local environment
	if (stripos($_SERVER['HTTP_HOST'], 'local.') !== false) {
    	wp_enqueue_style('css-styles', '/wp-content/plugins/monthly-statements/css/style-frontend.css');
    }

    // Get merchant statements
	try {
    	$merchant_statements = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."smms_monthly_statements` WHERE merchant_id = '".$merchant_id."' AND year='".$statement_year."' ORDER BY month");
	} catch (Exception $exc) {
    	echo '<tr><td colspan="2"><em>No statements available for time period selected.</td></tr>';
    	die();
	}
	
	// Check if statements or not
    $the_months = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
    if (count($merchant_statements) == 0) {
        $the_merchant_statements = '<tr><td colspan="2"><em>No statements available for time period selected.</td></tr>';
    } else {
        foreach ($merchant_statements as $cur_merchant_statement) {
            $the_link = "/wp-content/plugins/monthly-statements/lib/download.php?the_year=".$cur_merchant_statement->year."&the_month=".str_pad($cur_merchant_statement->month, 2, '0', STR_PAD_LEFT)."&the_merchant_id=".str_pad($cur_merchant_statement->merchant_id, 16, '0', STR_PAD_LEFT);
            if (!isset($the_merchant_statements)) { $the_merchant_statements = ''; }
            $the_merchant_statements .= '<tr>
		                <td>
		                    '.$the_months[intval($cur_merchant_statement->month)].'
		                </td>
		                <td>
		                    <a href="'.$the_link.'" target="_blank">View Statement</a>
		                </td>
		            </tr>';
        }
    }

    // Build years option list
    $year_options = '';
    for ($i=2013;$i<=date('Y');$i++) {
        $year_options = '<option value="'.$i.'">'.$i.'</option>'.$year_options;
    }
	
	// Get user data
	$user_info = wp_get_current_user();
	
	//Modal for opting in for paper statments
	$modal = '<div id="monthly_statement_modal" class="modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="monthly_statement_modal" aria-hidden="false">
				<div class="modal-header">
					<h4><strong>OPT-IN to receive monthly paper statements by entering the following information:</strong></h4>
				</div>
				
				<div class="modal-body">
					<div class="row-fluid">	
					
						<div class="span12" >
							<form id="opt_in_form">
								<label>
									MID
									<input type="text" name="mid" value="'.ltrim($merchant_id, '0').'" required />
								</label>
								<label>
									Email
									<input type="email" name="email" value="'.$user_info->user_email.'" required />
								</label>
								<label>
									Phone
									<input type="text" name="phone" required />
								</label>
								<button type="submit" class="btn green"  id="opt_in_submit">OPT-IN</button> 
							</form>
							<div id="opt_in_response"></div>

							<small style="margin:1rem 0 0 0;">A monthly charge of $1.00 will be added to your Monthly Basic Service Fee.</small>
						</div>
					</div>
				</div>
				
				<div class="modal-footer">
					<button class="btn" type="button" data-dismiss="modal" style="background:#67ACED;">Close</button>
				</div>
			</div>';
	
    // If this is not ajax, echo init
    if ($is_ajax == false) {
        echo '  <script>
            window.onload = function() {
    			Statements.init();
    		};
    	</script>
        <ul class="large-block-grid-4 medium-block-grid-2 small-block-grid-1">
    		<li>
    			<a href="/sales-data/" class="light-blue-button" style="width:200px;">< Back to Sales Data</a>
    		</li>
    	</ul>
    	<ul class="large-block-grid-4 medium-block-grid-2 small-block-grid-1">
    		<li>
    			<select id="the_statement_year" class="statement-select">
    			    '.$year_options.'
    			</select>
    			<div class="col-header">Monthly Statements</div>
    			<div id="statements_container">
    			    <table>'.$the_merchant_statements.'</table>
    			</div>
    		</li>
    		<li>
    			<!-- <div class="col-header">Tax Documents</div>
    			<div class="tax-doc-list">
    			    <a href="#" class="light-blue-button">Tax Document #1</a>
    			    <a href="#" class="light-blue-button">Tax Document #2</a>
    			    <a href="#" class="light-blue-button">Tax Document #3</a>
    			</div> -->
    		</li>
    	</ul>'.
    	'<a href="#monthly_statement_modal" role="button" data-toggle="modal">I want to receive monthly paper statements!</a>'.
    	$modal;
    } else {
        echo $the_merchant_statements;
        die();
    }
}
add_shortcode('monthly_statements', 'smms_monthly_statements');
add_action( 'wp_ajax_smms_monthly_statements', 'smms_monthly_statements' );
add_action( 'wp_ajax_nopriv_smms_monthly_statements', 'smms_monthly_statements' );


/**
 * Get merchant statements
 */
function smms_get_monthly_statements($merchant_id = null, $the_year = null) {
    // Pad merchant ID to 16 characters
    $merchant_id = str_pad($merchant_id->merchant_id, 16, '0', STR_PAD_LEFT);

	// Get statements for selected merchant and year
    for ($i=1;$i<13;$i++) {
        $the_month = str_pad($i, 2, '0', STR_PAD_LEFT);
        $the_year = (is_null($the_year)) ? date('Y') : $the_year;
        $the_cur_statement = statement_exists($the_year.$the_month.'01-'.$merchant_id);
        if ($the_cur_statement !== false) {
            $this_statement = new stdClass();
            $this_statement->month = $the_month;
            $this_statement->url = $the_cur_statement;
            $the_statements[] = $this_statement;
        }
    }

	// Return statement data
	return $the_statements;
}


/**
 * Send Opt-In Email
 */
function smms_opt_in() {
	global $wpdb;
	
	$form_data = array();
	parse_str($_POST['form_data'], $form_data);
	
	$mid = $form_data['mid'];
	$email = $form_data['email'];
	$phone = $form_data['phone'];

	// Set up the headers to send html and attachments in the email
	$headers =	"From: Saltsha <success@saltsha.com>" . "\r\n";
	$headers .=	"Reply-To: Saltsha <success@saltsha.com>" . "\r\n";
	$headers .=	"MIME-Version: 1.0" . "\r\n";
	$headers .=	"X-Mailgun-Native-Send: true" . "\r\n";
	$headers .=	"Content-type: text/html; charset=iso-8859-1" . "\r\n";
	
	//$to = 'curtis.wolfenberger@gmail.com';
	//$to = 'jfarrell@payprotec.com';
	$to = 'support@saltsha.com';
	
	$subject = 'Saltsha - Monthly Paper Statements Opt-In';

	$message = '
		<h1 style="margin:0; padding:0; color:#444;">Monthly Paper Statements Opt-In Request</h1>
		<p style="margin:0; padding:0; color:#444;">A user has submitted this information from the Monthly Paper Statements Opt-In form on Saltsha.</p>
		<hr />
		<br />
		<strong>MID</strong>: '.$mid.' <br />
		<strong>Email</strong>: '.$email.' <br />
		<strong>Phone</strong>: '.$phone.' <br />

	';
	
	$sendOptInMail = @mail($to, $subject, $message, $headers);
	if($sendOptInMail){
		echo json_encode(array("status"=>"success", "message"=>"Your request has been sent."));
	} else {
		echo json_encode(array("status"=>"fail", "message"=>"Something went wrong while submitting the information."));
	}
				
	
	
	die();
}
add_action( 'wp_ajax_smms_opt_in', 'smms_opt_in' );
add_action( 'wp_ajax_nopriv_smms_opt_in', 'smms_opt_in' );






?>