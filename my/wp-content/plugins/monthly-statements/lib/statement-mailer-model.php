<?php
	
	/**
	* 
	* Date: 09/18/14
	* Notes: 
	* This model is used for mailing out statement and tax document notifications.
	*
	*/	
	
	class Statement_mailer_model {

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
			}

			return $arr_merchants;

		}

	}
	
?>