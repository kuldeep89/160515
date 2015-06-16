<?php

// Import script
function import_price_overrides() {
	
	global $wpdb;
	
	function in_array_r($needle, $haystack, $strict = false) {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
	
	    return false;
	}
	
	$directory = dirname( __FILE__ );
	$file = $directory.'/so.csv';
	if( file_exists($file) ) {
		
		$csv = array_map("str_getcsv", file($file,FILE_SKIP_EMPTY_LINES));
		$keys = array_shift($csv);
		
		foreach ($csv as $i=>$row) {
		    $csv[$i] = array_combine($keys, $row);
		    //echo print_r($csv[$i], true);
			//echo $csv[$i]['mid'].' '.$csv[$i]['override'].'<br />';
			
		}

	} else {
		$responseVar =	array(
		                    'status'=>'failed',
		                    'message'=>'no file'
	                    );
		echo json_encode($responseVar);
		die();
	}
	
	if( count($csv)>1 ){
		$users = get_users();
		foreach ( $users as $user ) {
			$user_id = $user->ID;
				//Get merchant data
				$merchant_info = get_the_author_meta('ppttd_merchant_info', $user_id);
				//Get merchant IDs
				$merchant_id = $merchant_info['ppttd_merchant_id'];
				//Get price overrides for mid
				$price_overrides = (isset($merchant_info['ppttd_price_override'])) ? $merchant_info['ppttd_price_override'] : array();
				
				//check for multiple MIDs
				if( isset($merchant_id) && !empty($merchant_id) ){
					if( is_array($merchant_id) ){
						foreach( $merchant_id as $mid => $name ){
							if( in_array_r($mid, $csv) ){
								foreach($csv as $csv_row){
									
									//echo print_r($price, true).' <br />';
									$csv_mid = str_pad($csv_row['mid'], 16, '0', STR_PAD_LEFT);
									
									if($mid==$csv_mid){
										
										//echo '<pre>'.print_r($price_overrides, true).'</pre>';
										
										if( count($price_overrides) > 0 ){
											
											foreach($price_overrides as $p_mid=>$p_override){
												
												$p_mid = str_pad($p_mid, 16, '0', STR_PAD_LEFT);
												if($csv_mid==$p_mid){
													
													$replace_price = array($p_mid => $csv_row['override']);
													$new_price_overrides = array_replace($price_overrides, $replace_price);
													
												}
												
											}
												
											$update_user = update_user_meta($user_id, 'ppttd_price_override', $new_price_overrides);
											if( !is_wp_error($update_user) ){
												/*
$message	 =	$user_id.' - updated - 1 - '.print_r($new_price_overrides, true).' </br>';
												$responseVar =	array(
												                    'status'=>'success',
												                    'message'=>$message
											                    );
*/
											} else {
												$message	 =	$update_user->get_error_message();
												$responseVar =	array(
												                    'status'=>'failed',
												                    'message'=>$message
											                    );
												echo json_encode($responseVar);
											}
											                    
											
										} else {
											
											$new_price_overrides = array($mid => $csv_row['override']);
		
											$update_user = update_user_meta($user_id, 'ppttd_price_override', $new_price_overrides);
											if( !is_wp_error($update_user) ){
												/*
$message	 =	$user_id.' - updated - 2 - '.print_r($new_price_overrides, true).' </br>';
												$responseVar =	array(
												                    'status'=>'success',
												                    'message'=>$message
											                    );
*/
											} else {
												$message	 =	$update_user->get_error_message();
												$responseVar =	array(
												                    'status'=>'failed',
												                    'message'=>$message
											                    );
												echo json_encode($responseVar);
											}
											
											
										}
										
									}
									
								}
							}
						}
					} else {
						if( in_array_r($merchant_id, $csv) ){
							foreach($csv as $csv_row){
								
								//echo print_r($price, true).' <br />';
								$csv_mid = str_pad($csv_row['mid'], 16, '0', STR_PAD_LEFT);
								
								if($merchant_id==$csv_mid){
									
									//echo $merchant_id.' - '.$csv_mid.': '.$csv_row['override'].'<br />';
									//echo '<pre>'.print_r($price_overrides, true).'</pre>';
									
									
									if( count($price_overrides) > 0 ){
										
										foreach($price_overrides as $p_mid=>$p_override){
											
											$p_mid = str_pad($p_mid, 16, '0', STR_PAD_LEFT);
											if($csv_mid==$p_mid){
												
												$replace_price = array($merchant_id => $csv_row['override']);
												$new_price_overrides = array_replace($price_overrides, $replace_price);
												
											}
											
										}
										
										$update_user = update_user_meta($user_id, 'ppttd_price_override', $new_price_overrides);
										if( !is_wp_error($update_user) ){
											$message	 =	$user_id.' - updated - 3 - '.print_r($new_price_overrides, true).' </br>';
											$responseVar =	array(
											                    'status'=>'success',
											                    'message'=>$message
										                    );
										} else {
											$message	 =	$update_user->get_error_message();
											$responseVar =	array(
											                    'status'=>'failed',
											                    'message'=>$message
										                    );
										}
										
										echo json_encode($responseVar);
										
									} else {
										
										$new_price_overrides = array($merchant_id => $csv_row['override']);
										
										$update_user = add_user_meta($user_id, 'ppttd_price_override', $new_price_overrides);
										
										if( !is_wp_error($update_user) ){
											/*
$message	 =	$user_id.' - added - 4 - '.print_r($new_price_overrides, true).' </br>';
											$responseVar =	array(
											                    'status'=>'success',
											                    'message'=>$message
										                    );
*/
										} else {
											$message	 =	$update_user->get_error_message();
											$responseVar =	array(
											                    'status'=>'failed',
											                    'message'=>$message
										                    );
											echo json_encode($responseVar);
										}
										
										
									}
								}
								
							}
						}
					}
				}
				
			
		}
		
		
		$responseVar =	array(
		                    'status'=>'success',
		                    'message'=>'Done.'
	                    );
		echo json_encode($responseVar);

	}
	die();
}
add_action( 'wp_ajax_import_price_overrides', 'import_price_overrides' );
add_action( 'wp_ajax_nopriv_import_price_overrides', 'import_price_overrides' );

?>