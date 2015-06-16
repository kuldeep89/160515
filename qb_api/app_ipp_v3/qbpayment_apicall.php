<?php
/**
 * @package QuickBooks implementation
 * @description: Quick Book Payment Api calls
 * @date: 5june2015: 
 * @author: Chetu Inc
 */
 
// Check if this is a saltsha domain
if(preg_match('/^(?:.+\.)?saltsha\.com$/', $_SERVER['HTTP_ORIGIN']) !== TRUE) {
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
}

require_once dirname(__FILE__) . '/config.php';
global $wpdb;
$table = 'wp_ppttd_cashtransactions';

$PaymentService = new QuickBooks_IPP_Service_Payment();     /* calling payment query method and write query for calling payment api*/

$merchant_id = (isset($_SESSION['active_mid']['merchant_id']) && trim($_SESSION['active_mid']['merchant_id']) !== '') ? $_SESSION['active_mid']['merchant_id'] : null;    // Get merchant ID

// fetch last transaction_time from table wp_ppttd_cashtransactions
$last_transaction_time = $wpdb->get_results( "SELECT DATE_FORMAT(transaction_time, '%Y-%m-%d') as transaction_time FROM wp_ppttd_cashtransactions WHERE merchant_id = '".$merchant_id."' ORDER BY transaction_time DESC LIMIT 1" );	
// select which api query needed
if(count($last_transaction_time)>0) {
	//  pull data on the basis of date
	$transact_time = $last_transaction_time[0]->transaction_time.'T00:00:00'; 
	$list_payment_data = $PaymentService->query($Context, $realm, "select * from Payment Where Metadata.LastUpdatedTime>='".$transact_time."' Order By Metadata.LastUpdatedTime");
} else { 
	// pull all api data 
	$list_payment_data = $PaymentService->query($Context, $realm, "select * from Payment");
}

/*** find cash data and insert into table wp_ppttd_cashtransactions  ***/
$pull_record = 0;
foreach ($list_payment_data as $payment) {	
	// get payment method type
	$payment_id = (int) trim( $payment->getId(), '{-}' );
	$payment_method_type = (int) trim($payment->getPaymentMethodRef(), '{-}');
	$customer_ref = trim($payment->getCustomerRef(), '{-}');
	$transaction_time = $payment->getMetaData()->getLastUpdatedTime();
	
	// check if record already exists in table or not
	$result_set = $wpdb->get_results("SELECT * FROM wp_ppttd_cashtransactions WHERE qb_payment_id = '$payment_id'");

	if ( ( count($result_set) == 0 ) && ( $payment_method_type == 1 ) ) {	//$payment_method_type : 1 denoted Cash
		// insert new record
		$get_cash_payments = array(
								'qb_payment_id' 	=>	$payment_id,
								'qb_customer_id' 	=>	$customer_ref,
								'transaction_time'	=>	$transaction_time, 
								'amt'				=>	$payment->getTotalAmt(),
								'card_type'			=>	'Cash',
								'qb_reference'		=>	$payment->getPaymentRefNum(),
								'merchant_id'		=>	$merchant_id
								);
								
		$wpdb->insert( $table, $get_cash_payments);
		$pull_record ++;
	}	
	 	
}
echo json_encode( array( 'pull_record' => $pull_record ) ); 


?>