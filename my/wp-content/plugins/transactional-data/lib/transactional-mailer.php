<?php
	
	require_once dirname(__FILE__).'/Mailgun/Mailgun.php';
	require_once dirname(__FILE__).'/transactional-mailer-model.php';

    global $hostname; $hostname = gethostname();

	/**
	 * Required to set timezone. Cron jobs are based on FT Wayne Timezone.
	 */
    date_default_timezone_set('America/Fort_Wayne');
	
	/**
	 * Class to send out transactional emails.
	 */
	class Transactional_mailer {
		
		//Class model.
		private $obj_model;
		
		// Default Constructor
		public function __construct() {
			$this->obj_model	= new Transactional_mailer_model();
		}
		
		/*******************************
		**	Transactional Mailer
		**
		**	Description:
		**	This method will send out emails for transactional history on a clients account.
		**
		**  @param:		$timeframe
		**  @param:		$type (daily, weekly, monthly)
		**  @param:     $send_to_merchants
		**	@return:	status
		**
		**/
		public function send_update( $timeframe, $type, $send_to_merchants = null) {
			// Pull into scope database object.
			global $mysqli, $table_prefix;

			// Merchant data storage array
			$arr_all_merchant_data = array();

			// Get end date for timeframe
			$end_time = (!is_null($send_to_merchants)) ? $timeframe : date('Y-m-d', strtotime("-2 days"));

			// Declare/Define variables.
			$sales_volume	= 0;
			$highest_ticket	= 0;

			// Define email template
			$title		= 'Daily';
			$template	= 'email-daily.html';

			switch( $type ) {

				case 'weekly':

					$title		= 'Weekly';
					$template	= 'email-weekly.html';
	
				break;
				case 'monthly':

					$title		= 'Monthly';
					$template	= 'email-monthly.html';

				break;

			}

			// Get Merchants
			$arr_merchants	= $this->obj_model->get_merchants($send_to_merchants);

			// Process merchants.
			if( count($arr_merchants) > 0 ) {

				foreach( $arr_merchants as $arr_merchant ) {

					//Extract member data.
					$arr_merchant_data = $arr_merchant[0];
					$merchant_email		= $arr_merchant[1];
					$merchant_id		= $arr_merchant_data['ppttd_merchant_id'];
					$merchant_name		= (isset($arr_merchant_data['ppttd_merchant_name'])) ? $arr_merchant_data['ppttd_merchant_name'] : null;

					//Instantiate a lot of variables.
					$best_times	= $best_one = $best_two = $best_one_volume = $best_two_volume = $best_one_sales = $best_two_sales = $sales_volume = $highest_ticket = 0;

					// Make sure merchant ID isn't blank, they want to receive this type of transactional report, and transactional reporting is set to on.
					if (trim($merchant_id) != '' && isset($arr_merchant_data['ppttd_'.$type.'_transaction_report']) && $arr_merchant_data['ppttd_'.$type.'_transaction_report'] == 'on') {

						// Check if sending out weekly
						if ($type == 'weekly') {
						    if ($arr_merchant_data['ppttd_week_starts_on'] == date('l', strtotime('-1 day'))) {
								// Get merchant data
								if (!isset($arr_all_merchant_data[$merchant_id])) {
									if (!array_key_exists($merchant_id, $arr_all_merchant_data)) {
										$arr_all_merchant_data[$merchant_id] = $this->obj_model->get_user_mailer_variables($merchant_id, $timeframe, $end_time, $title);
									}
								}

								// Set merchant data
								$merchant_data_to_send		= $arr_all_merchant_data[$merchant_id];
								$merchant_data_to_send[]	= $timeframe;
								$merchant_data_to_send[]	= $end_time;
								$merchant_data_to_send[]	= (!is_null($merchant_name)) ? $merchant_name : $merchant_id;
								$merchant_data_to_send[]	= $merchant_email;
								$merchant_data_to_send[]	= $template;
								$merchant_data_to_send[]	= $type;

								// Send out mailer if sales volume is greater than $0
								if ($merchant_data_to_send[8] > 0) {
								    $this->send_mailer($merchant_data_to_send);
								}
							}
						} else {
							// Get merchant data
							if (!isset($arr_all_merchant_data[$merchant_id])) {
								if (!array_key_exists($merchant_id, $arr_all_merchant_data)) {
									$arr_all_merchant_data[$merchant_id] = $this->obj_model->get_user_mailer_variables($merchant_id, $timeframe, $end_time, $title);
								}
							}

							// Set merchant data
							$merchant_data_to_send		= $arr_all_merchant_data[$merchant_id];
							$merchant_data_to_send[]	= $timeframe;
							$merchant_data_to_send[]	= $end_time;
							$merchant_data_to_send[]	= (!is_null($merchant_name)) ? $merchant_name : $merchant_id;
							$merchant_data_to_send[]	= $merchant_email;
							$merchant_data_to_send[]	= $template;
							$merchant_data_to_send[]	= $type;

                            // Set alert data
							$alert_data		= $arr_all_merchant_data[$merchant_id];
							$alert_data[]	= $timeframe;
							$alert_data[]	= $end_time;
							$alert_data[]	= (!is_null($merchant_name)) ? $merchant_name : $merchant_id;
							$alert_data[]	= $merchant_email;
							$alert_data[]	= $template;
							$alert_data[]	= $type;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_enable_alerts'])) ? $arr_merchant_data['ppttd_enable_alerts'] : null ;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_alert_chargebacks'])) ? $arr_merchant_data['ppttd_alert_chargebacks'] : null ;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_alert_retrievals'])) ? $arr_merchant_data['ppttd_alert_retrievals'] : null ;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_alert_no_processing'])) ? $arr_merchant_data['ppttd_alert_no_processing'] : null ;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_alert_batch_above'])) ? $arr_merchant_data['ppttd_alert_batch_above'] : null ;
							$alert_data[]	= (isset($arr_merchant_data['ppttd_alert_batch_below'])) ? $arr_merchant_data['ppttd_alert_batch_below'] : null ;
							$alert_data[]   = $merchant_id;

                            // Check if we should send alerts
							if(isset($alert_data[16]) && $alert_data[16] == "on"){
								$this->send_alerts($alert_data);
							}

							// Send out mailer if sales volume is greater than $0
							if ($merchant_data_to_send[8] > 0) {
							    $this->send_mailer($merchant_data_to_send);
                            }
						}
					}
				}
			}
		}

		/**
		* Send Mailer
		*
		* This method sends out the mailer passed to it.
		*
		* @access	public
		* @param	$arr_variables
		* @return	boolean
		*
		*/
		public function send_mailer( $arr_variables ) {
    		global $mysqli;
    		global $hostname;

            list($best_times, $best_one, $best_two, $best_one_volume, $best_two_volume, $best_one_sales, $best_two_sales, $total_sales, $sales_volume, $highest_ticket, $timeframe, $end_time, $merchant_id, $to, $template, $type) = $arr_variables;

			// Send out  mailer.
			$subject	= '';
			$headers 	= "From: Saltsha <success@saltsha.com>\r\n";
			$headers 	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
			$headers 	.= "MIME-Version: 1.0\r\n";
			$headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$headers 	.= "X-Mailgun-Native-Send: true\r\n";

			// Set email message
			$message = file_get_contents(dirname(__DIR__).'/template/'.$template);

			// Handle Header based on the type of mailer
			switch( $type ) {
			
				case 'daily':
					$subject = date('F d, Y', strtotime($end_time)).' '.ucwords($type).' Summary for '.$merchant_id;
					$message = str_replace('[PPTTD_HEADER]', date('F d, Y', strtotime($end_time)).'<br/>'.ucwords($type).' Summary<br/>for '.$merchant_id, $message);
				break;
				case 'weekly':
					$subject = date('F d, Y', strtotime($timeframe)).' - '.date('F d, Y', strtotime($end_time)).' '.ucwords($type).' Summary for '.$merchant_id;
					$message = str_replace('[PPTTD_HEADER]', date('F d, Y', strtotime($timeframe)).' - '.date('F d, Y', strtotime($end_time)).'<br/>'.ucwords($type).' Summary<br/>for '.$merchant_id, $message);
				break;
				case 'monthly':
					$subject = date('F d, Y', strtotime($timeframe)).' - '.date('F d, Y', strtotime($end_time)).' '.ucwords($type).' Summary for '.$merchant_id;
					$message = str_replace('[PPTTD_HEADER]', date('F d, Y', strtotime($timeframe)).' - '.date('F d, Y', strtotime($end_time)).'<br/>'.ucwords($type).' Summary<br/>for '.$merchant_id, $message);
				break;
				
			}

            // Define search array
            $str_search = array('[PPTTD_COMPANY_NAME]','[PPTTD_DATE]','[RECAP_TYPE]','[PPTTD_SALES_VOLUME]','[PPTTD_TOTAL_SALES]','[PPTTD_HIGHEST_VOL_1_TIME]','[PPTTD_HIGHEST_VOL_2_TIME]','[PPTTD_HIGHEST_VOL_1_VOLUME]','[PPTTD_HIGHEST_VOL_2_VOLUME]','[PPTTD_HIGHEST_VOL_1_SALES]','[PPTTD_HIGHEST_VOL_2_SALES]','[PPTTD_HIGHEST_TICKET]','[PPTTD_MERCHANT_ID]');

            // Define replace array
            $str_replace = array($merchant_id, date('F d, Y', strtotime($timeframe)),ucwords($type),'$'.$sales_volume, $total_sales, $best_one, $best_two, $best_one_volume, $best_two_volume, $best_one_sales, $best_two_sales, '$'.$highest_ticket, $merchant_id);

            // Search and replace content in email template
            $message = str_replace($str_search, $str_replace, $message);

            // If local host, send to test email(s)
            // $test_emails = array('cwolfenberger@212mediastudios.com', 'bstump@212mediastudios.com', 'bobbie.stump@gmail.com');
            $test_emails = array('bobbie.stump@gmail.com');

            // Prepare $to email address
            $to = trim($to);

            // Check for producton / local testing
            if (stripos($hostname, 'saltsha.com') !== false) {
			    if (gets_email($to)) {
		            // Adds additional email addresses to the $to variable if any exist. Comma separated emails.
					$user_ID = get_user_from_email($to);
					$get_addtl_emails = $mysqli->query("SELECT * FROM wp_usermeta WHERE meta_key='merchant_emails' AND user_id='$user_ID'");
					if ($get_addtl_emails->num_rows > 0) {
						$to_emails = $get_addtl_emails->fetch_row();
						if(!empty($to_emails[3])){
							$to = $to.', '.$to_emails[3];
						}
					}
            		mail($to, $subject, $message, $headers);
			    } else {
    			    return false;
			    }
            } else {
    			// Send out email if in test group
    			if (in_array($to, $test_emails)) {
    			    if (gets_email($to)) {
			            // Adds additional email addresses to the $to variable if any exist. Comma separated emails
				        $user_ID = get_user_from_email($to);
						$get_addtl_emails = $mysqli->query("SELECT * FROM wp_usermeta WHERE meta_key='merchant_emails' AND user_id='$user_ID'");
						if ($get_addtl_emails->num_rows > 0) {
							$to_emails = $get_addtl_emails->fetch_row();
							if(!empty($to_emails[3])){
								$to = $to.', '.$to_emails[3];
							}
						}
                		mail($to, $subject, $message, $headers);
    			    } else {
	    			    return false;
    			    }
                }
            }

			return true;
		}

		/**
		* Alert Styling
		*
		* This styles the alert for passing to an email.
		*
		* @access	public
		* @param	$alert_text
		* @return	content
		*
		*/
		public function style_alert( $alert_text ){
			return "<div style='background:#76EF7C; color:white; padding: 15px 25px 15px 25px; margin:25px 25px; font-family:Helvetica, Arial, sans-serif; font-size:18px; border-radius:5px; border-bottom:3px solid rgb(83,191,89);'>".$alert_text."</div>";
		}

		/**
		* Alerts
		*
		* This method Updates the wp_ppttd_alerts table and sends an alert to the user.
		*
		* @access	private
		* @param	$alert_vars
		* @return	boolean
		*
		*/
		private function send_alerts( $alert_vars ) {
			global $mysqli;

            list($best_times, $best_one, $best_two, $best_one_volume, $best_two_volume, $best_one_sales, $best_two_sales, $total_sales, $sales_volume, $highest_ticket, $timeframe, $end_time, $merchant_name, $to, $template, $type, $ppttd_enable_alerts, $ppttd_alert_chargebacks, $ppttd_alert_retrievals, $ppttd_alert_no_processing, $ppttd_alert_batch_above, $ppttd_alert_batch_below, $merchant_id) = $alert_vars;
						
			$alert_message = "";
			$mid_text = "For ".$merchant_id;

            // Get user ID from email address
			$user_ID = get_user_from_email($to);

            // Send low batch alert
			if( isset($total_sales) && $total_sales != 0 && $ppttd_alert_batch_below != 0 && $ppttd_alert_batch_below !== null && trim($ppttd_alert_batch_below) != '' && $ppttd_alert_batch_below > $total_sales ) {
				$alert_message .= $this->style_alert("Your recent batch ".$mid_text." of $".$total_sales." was below $".$ppttd_alert_batch_below);
				$alert_text = "Your recent batch ".$mid_text." of $".$total_sales." was below $".$ppttd_alert_batch_below;
				$alert_type = 'batch_below';

				ppttd_insert_alert($user_ID, $alert_type, $alert_text, $merchant_id);
			}

            // Send high batch alert
			if( isset($total_sales) && $total_sales != 0 && $ppttd_alert_batch_above != 0 && $ppttd_alert_batch_above !== null && $ppttd_alert_batch_above != '' && $ppttd_alert_batch_above != 0 && $ppttd_alert_batch_above < $total_sales ) {
				$alert_message .= $this->style_alert("Your recent batch ".$mid_text." of $".$total_sales." was above $".$ppttd_alert_batch_above);
				$alert_text = "Your recent batch ".$mid_text." of $".$total_sales." was above $".$ppttd_alert_batch_above;
				$alert_type = 'batch_above';

				ppttd_insert_alert($user_ID, $alert_type, $alert_text, $merchant_id);
			}

            // Send no recent processing batch
			if( $ppttd_alert_no_processing !== null && strtotime($end_time) < strtotime($ppttd_alert_no_processing.' days ago') ){
				$alert_message .= $this->style_alert($merchant_id." - It's been ".$ppttd_alert_no_processing." days since your last transaction.");
				$alert_text = $merchant_id." - It's been ".$ppttd_alert_no_processing." days since your last transaction.";
				$alert_type = 'days_since';

				ppttd_insert_alert($user_ID, $alert_type, $alert_text, $merchant_id);
			}
			
			
			if($alert_message!==""){
				// Send out  mailer.
				$subject	= '';
				$headers	= "From: Saltsha <success@saltsha.com>\r\n";
				$headers	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
				$headers	.= "MIME-Version: 1.0\r\n";
				$headers	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				$headers	.= "X-Mailgun-Native-Send: true\r\n";
	
				// Set email message
				$message = file_get_contents(dirname(__DIR__).'/template/email-alert.html');
	
				$subject = 'Saltsha Alert';
				$message = str_replace('[PPTTD_HEADER]', date('F d, Y', strtotime($end_time)).'<br/>'.ucwords($type).' Saltsha Alert', $message);
	
	            // Define search array
	            $str_search = array('[PPTTD_COMPANY_NAME]','[PPTTD_DATE]','[RECAP_TYPE]','[PPTTD_ALERT]','[PPTTD_MERCHANT_ID]');
	
	            // Define replace array
	            $str_replace = array($merchant_name, date('F d, Y', strtotime($timeframe)),ucwords($type),$alert_message, $merchant_name);
	
	            // Search and replace content in email template
	            $message = str_replace($str_search, $str_replace, $message);
                // echo 'Sending alert to "'.$to.'"...'."\n";
	            // If local host, send to test ep0s)
	            // $test_emails = array('cwolfenberger@212mediastudios.com', 'bstump@212mediastudios.com', 'bobbie.stump@gmail.com');
	            $test_emails = array('bobbie.stump@gmail.com');
	            $hostname = gethostname();
	            
	            if (stripos($hostname, 'saltsha.com') !== false) {
	                if (gets_email($to)) {
			            /*
				         * Adds additional email addresses to the $to variable if any exist. Comma separated emails.
				         */
						$get_addtl_emails = $mysqli->query("SELECT * FROM wp_usermeta WHERE meta_key='merchant_emails' AND user_id='$user_ID'");
						if ($get_addtl_emails->num_rows > 0) {
							$to_emails = $get_addtl_emails->fetch_row();
							if(!empty($to_emails[3])){
								$to = $to.', '.$to_emails[3];
							}
						}
                		mail($to, $subject, $message, $headers);
    			    } else {
	    			    return false;
    			    }
                } else {
	    			// Send out email
	    			$to = trim($to);
	    			if (in_array($to, $test_emails)) {
	    			    if (gets_email($to)) {
				            // Adds additional email addresses to the $to variable if any exist. Comma separated emails.
							$get_addtl_emails = $mysqli->query("SELECT * FROM wp_usermeta WHERE meta_key='merchant_emails' AND user_id='$user_ID'");
							if ($get_addtl_emails->num_rows > 0) {
								$to_emails = $get_addtl_emails->fetch_row();
								if(!empty($to_emails[3])){
									$to = $to.', '.$to_emails[3];
								}
							}

                            // Send email
		    			    mail($to, $subject, $message, $headers);
	    			    } else {
		    			    return false;
	    			    }
	                }
	            }
				return true;
			} else {
	            return false;
			}
		}

	}

    // Get user id from merchant
    function get_user_from_email($email_address = null) {
        global $mysqli;

        if (is_null($email_address)) {
            return 0;
        } else {
            $get_user_id = $mysqli->query("SELECT ID FROM wp_users WHERE user_email='$email_address' LIMIT 1");
            if ($get_user_id->num_rows > 0) {
                $row = $get_user_id->fetch_row();
                return $row[0];
            }
        }

        // Default return 0
        return 0;
    }
    
    
    /**
    * Checks if the user should receive emails based on their Tier.
    * Tier 0 should not receive emails.
    */
	function gets_email($email_address = null){
        global $mysqli;

		if (is_null($email_address)) {
            return FALSE;
        } else {
	        
	        $user_ID = get_user_from_email($email_address);
	        if ($user_ID == 0) {
            	return FALSE;
            } else {
		        $get_user_group = $mysqli->query("SELECT * FROM wp_groups_user_group WHERE user_id='$user_ID' AND (group_id='7' OR group_id='6' OR group_id='9') LIMIT 1");
		        if ($get_user_group->num_rows > 0) {
			        return TRUE;
		        } else {
			        return FALSE;
		        }
	        }
        }
        return FALSE;
	}
	
	
    
    /**
    * Gets the company tied to the user's account
    */
	function get_company( $user_ID = null ){
		if( $user_ID != null ){
			global $mysqli;
			$company_select = $mysqli->query("SELECT * FROM wp_usermeta WHERE meta_key='company_select' AND user_id='$user_ID'");
			$get_company = $company_select->fetch_row();
			
			if( isset($get_company[3]) && trim($get_company[3]) == 'Pilothouse' ) {
				$company_select_name = 'Pilothouse';
				$company_select_link = 'http://pilothousepayments.com/';
				$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/pilothouse.png';
			} elseif( isset($get_company[3]) && trim($get_company[3]) === 'SuperiorProcessing' ) {
				$company_select_name = 'Superior Processing';
				$company_select_link = 'http://www.superiorprocessingsolutions.com/';
				$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/superiorps-logo.png';
			} else {
				$company_select_name = 'PayProTec';
				$company_select_link = 'http://payprotec.com';
				$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt.png';
			}
			
			return array('company_select_name'=> $company_select_name,'company_select_link'=> $company_select_link,'company_select_logo'=> $company_select_logo);
		}
	}

    /**
    * This function inserts a row into the wp_ppttd_batch_alerts table when an alert is triggered.
    */
    function ppttd_insert_alert($user_id, $alert_type, $alert_text, $alert_mid = null) {
        // Get the global database object
        global $mysqli;

        $mysqli->query("INSERT INTO wp_ppttd_batch_alerts (user_id, alert_type, alert_text, `read`, alert_mid) VALUES ('".$user_id."', '".$alert_type."', '".$alert_text."', '0', '".$alert_mid."')");
    }
?>