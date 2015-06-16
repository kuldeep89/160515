<?php
    // Require db info file, include globals
	require_once(dirname(dirname(__DIR__)).'/lib/database.php');

    // Add merchant IDs
    $merchantless_transactions = $mysqli->query("SELECT DISTINCT uniq_batch_id FROM wp_ppttd_transactionlisting WHERE merchant_id=0000000000000000");
    
    foreach ($merchantless_transactions as $cur_trans) {
        $get_merchant = $mysqli->query("SELECT merchant_id FROM wp_ppttd_batchlisting WHERE uniq_batch_id='$cur_trans[uniq_batch_id]' LIMIT 1");
        $merchant_info = mysqli_fetch_assoc($get_merchant);

        // Update transactions with correct merchant ID
        $mysqli->query("UPDATE wp_ppttd_transactionlisting SET merchant_id=".$merchant_info['merchant_id']." WHERE uniq_batch_id='$cur_trans[uniq_batch_id]'");
    }
?>