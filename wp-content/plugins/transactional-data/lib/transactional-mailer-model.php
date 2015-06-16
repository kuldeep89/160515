<?php
	
	/**
	* 
	* Date: 6/13/14
	* Notes: 
	* This model is used for mailing out transactional emails.
	*
	*/	
	
	class Transactional_mailer_model {
		
		/**
		* Get Merchants
		*
		* This function returns a listing of merchants.
		*
		* @access	public
		* @param	void
		* @return	array
		*
		*/
		
		public function get_merchants($send_to_merchants = null) {
			
		    // Pull into scope database object.
			global $mysqli, $table_prefix;

            // Query data.
			$obj_query	= $user_data = $mysqli->query("SELECT ".$table_prefix."usermeta.meta_value,".$table_prefix."users.user_email FROM ".$table_prefix."usermeta,".$table_prefix."users WHERE ".$table_prefix."usermeta.meta_key='ppttd_merchant_info' AND ".$table_prefix."usermeta.user_id=".$table_prefix."users.ID");

			// Check for available merchants.
			$arr_merchants	= array();

			if( $obj_query->num_rows > 0 ) {

				while( $arr_row	= $obj_query->fetch_row() ) {

					$arr_row[0] = unserialize($arr_row[0]);

                    // If send_to_merchant is null, add all merchants
                    if (is_null($send_to_merchants)) {
        				$arr_merchants[]	= $arr_row;
                    } else {
                        if (gettype($arr_row[0]['ppttd_merchant_id']) == 'array') {
                            // Get array of merchant IDs
                            $the_merchant_ids = $arr_row[0]['ppttd_merchant_id'];

                            // Loop through merchant IDs and add if not already in array
                            foreach ($the_merchant_ids as $cur_merchant_id => $cur_merchant_name) {
            					if (in_array($cur_merchant_id, $send_to_merchants)) {
                					// Set the current merchant ID
                					$arr_row[0]['ppttd_merchant_id'] = $cur_merchant_id;
                					$arr_row[0]['ppttd_merchant_name'] = $cur_merchant_name;
                					
                					
                					// Add to merchant array
                					$arr_merchants[]	= $arr_row;
            					}
                            }
                        } else {
                            // Explode list of CSV merchant IDs so we have an array of merchant IDs
                            $the_merchant_ids = explode(',', $arr_row[0]['ppttd_merchant_id']);

                            // Loop through merchant IDs and add if not already in array
                            foreach ($the_merchant_ids as $cur_merchant_id) {
            					if (in_array($cur_merchant_id, $send_to_merchants)) {
                					// Set the current merchant ID
                					$arr_row[0]['ppttd_merchant_id'] = $cur_merchant_id;

                					// Add to merchant array
                					$arr_merchants[]	= $arr_row;
            					}
                            }
                        }
					}
				}

				$arr_merchants[]	= $arr_row[0]['ppttd_enable_alerts'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_duplicate_transaction'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_no_processing'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_chargebacks'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_retrievals'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_batch_above'];
				$arr_merchants[]	= $arr_row[0]['ppttd_alert_batch_below'];
			}

            // Remove any blank merchants
            foreach ($arr_merchants as $key => $cur_merchant) {
                // Merchant string
                if (gettype($cur_merchant) === 'string') {
                    if (trim($cur_merchant) === '') {
                        unset($arr_merchants[$key]);
                        continue;
                    }
                }

                // Merchant array
                if (!isset($cur_merchant[1]) || trim($cur_merchant[1]) === '') {
                    unset($arr_merchants[$key]);
                }
            }

			return $arr_merchants;

		}
		
		/**
		* Get User Transactional Data
		*
		* This function returns an array of transactional data for the passed user,
		* in the passed timeframe.
		*
		* @access	public
		* @param	$merchant_id
		* @param	$timeframe
		* @param	$end_time
		* @return	array
		*
		*/
		public function get_user_mailer_variables( $merchant_id, $timeframe, $end_time, $title) {

			// Pull into scope database object.
			global $mysqli, $table_prefix;

			// Declare/Define variables.
			$sales_volume	= 0;
			$highest_ticket	= 0;

			// Query batches to get latest batch
			$timeframe = ($title === 'Daily') ? date('Y-m-d', strtotime('-1 day', strtotime($timeframe))).' 23:59:59' : $timeframe;
			$end_time = $end_time.' 23:59:59';
			$get_batches = $mysqli->query("SELECT uniq_batch_id FROM ".$table_prefix."ppttd_batchlisting WHERE (merchant_id = '".$merchant_id."' OR merchant_id = '".trim($merchant_id, '0')."') AND batch_date BETWEEN '".$timeframe."' AND '".$end_time."' ORDER BY batch_date DESC");
			if ($get_batches->num_rows == 0){
				return array(array(), 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '0', '0', '0');
			}

			// Get batch ID
			$transaction_query = '';
			while($row = $get_batches->fetch_assoc()) {
				$transaction_query .= "uniq_batch_id = '".trim($row['uniq_batch_id'])."' OR ";
			}
		

			// Query transactions
			$transaction_data = $mysqli->query("SELECT transaction_time,amt FROM ".$table_prefix."ppttd_transactionlisting WHERE ".substr($transaction_query, 0, -4)." ORDER BY transaction_time");

			// Get timeframes
			$all_timeframes 		= array('12AM-05:59AM' => 0.00, '6AM-11:59AM' => 0.00, '12PM-05:59PM' => 0.00, '6PM-11:59PM' => 0.00);
			$all_timeframes_sales 	= array('12AM-05:59AM' => 0.00, '6AM-11:59AM' => 0.00, '12PM-05:59PM' => 0.00, '6PM-11:59PM' => 0.00);

			// Loop through transactions
			if ($transaction_data->num_rows > 0) {
				while( $cur_transaction = $transaction_data->fetch_row() ) {
					
					$transaction_time	= $cur_transaction[0];
					$transaction_amt	= $cur_transaction[1];
					$sales_volume		= floatval($sales_volume)+floatval($transaction_amt);
	
					// Speed up script.
					$timestamp			= strtotime($transaction_time);
					$day				= date('y-m-d',$timestamp);
					
					// Add to correct timeframe
					if ( $timestamp > strtotime($day.'00:00:00') && $timestamp <= strtotime($day.' 05:59:59')) {
	
						$all_timeframes['12AM-05:59AM'] 		= floatval($all_timeframes['12AM-05:59AM'])+floatval($transaction_amt);
						$all_timeframes_sales['12AM-05:59AM'] 	= $all_timeframes_sales['12AM-05:59AM']+1;

					}
					
					if ( $timestamp > strtotime($day.' 06:00:00') && $timestamp <= strtotime($day.' 11:59:59')) {
	
						$all_timeframes['6AM-11:59AM'] 			= floatval($all_timeframes['6AM-11:59AM'])+floatval($transaction_amt);
						$all_timeframes_sales['6AM-11:59AM']	= $all_timeframes_sales['6AM-11:59AM']+1;
					
					}
					
					if ( $timestamp > strtotime($day.' 12:00:00') && $timestamp <= strtotime($day.' 17:59:59')) {
	
						$all_timeframes['12PM-05:59PM']			= floatval($all_timeframes['12PM-05:59PM'])+floatval($transaction_amt);
						$all_timeframes_sales['12PM-05:59PM']	= $all_timeframes_sales['12PM-05:59PM']+1;
					
					}
					
					if ( $timestamp > strtotime($day.' 18:00:00') && $timestamp <= strtotime($day.' 23:59:59')) {
	
						$all_timeframes['6PM-11:59PM']			= floatval($all_timeframes['6PM-11:59PM'])+floatval($transaction_amt);
						$all_timeframes_sales['6PM-11:59PM']	= $all_timeframes_sales['6PM-11:59PM']+1;
					
					}
	
					// Set highest ticket if current transaction is higher
					if ($transaction_amt > $highest_ticket) {
						$highest_ticket = number_format($transaction_amt, 2);
					}
					
				}
				
				// Sort to get best spending timeframes
				arsort($all_timeframes);
				$best_times 		= array_keys($all_timeframes);
				$best_one 			= ($all_timeframes[$best_times[0]] > 0) ? $best_times[0] : 'N/A';
				$best_two 			= ($all_timeframes[$best_times[1]] > 0) ? $best_times[1] : 'N/A';
				$best_one_volume	= ($all_timeframes[$best_times[0]] > 0) ? '$'.number_format($all_timeframes[$best_times[0]], 2) : 'N/A';
				$best_two_volume	= ($all_timeframes[$best_times[1]] > 0) ? '$'.number_format($all_timeframes[$best_times[1]], 2) : 'N/A';
				$best_one_sales		= ($all_timeframes[$best_times[0]] > 0) ? $all_timeframes_sales[$best_times[0]] : 'N/A';
				$best_two_sales		= ($all_timeframes[$best_times[1]] > 0) ? $all_timeframes_sales[$best_times[1]] : 'N/A';
	
				// Set total sales
				$total_sales 	= $transaction_data->num_rows;
				$sales_volume 	= ($sales_volume > 0) ? number_format($sales_volume, 2) : 0.00;
				$highest_ticket	= ($highest_ticket > 0) ? number_format($highest_ticket, 2) : 0.00;
				
				
			} else {
				return array(array(), 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '0', '0', '0');
			}

			// Return data
			return array($best_times, $best_one, $best_two, $best_one_volume, $best_two_volume, $best_one_sales, $best_two_sales, $total_sales, $sales_volume, $highest_ticket);
			
		}
		
	}
	
?>