<?php
    exit;

    // $500 Processed = 1 Point

    // Global vars
    global $mysqli;

    // Require db info file, include globals
	require_once(dirname(__DIR__).'/lib/database.php');

    $start_time = microtime(true);

    // Get merchants from batch listing table
    $batch_merchants_query = $mysqli->query("SELECT DISTINCT merchant_id FROM wp_ppttd_batchlisting");
    while($row = mysqli_fetch_array($batch_merchants_query)) {
        $cur_merchant_id = str_pad(trim($row['merchant_id'], '0'), 16, '0', STR_PAD_LEFT);   

        // Query for batch total for last 6 months
        $cur_merchant_batches = $mysqli->query("SELECT SUM(total_volume) FROM wp_ppttd_batchlisting WHERE (merchant_id='$cur_merchant_id' OR merchant_id='".trim($cur_merchant_id, '0')."') AND batch_date BETWEEN '".date('Y-m-d', strtotime('-6 months'))."' AND '".date('Y-m-d')."'");

        // Retrieve and echo batch total
        $cur_batch_total = mysqli_fetch_array($cur_merchant_batches);
        $cur_batch_total = preg_replace("/[^0-9.]/", "", $cur_batch_total[0]);
        $cur_batch_total = (isset($cur_batch_total) && trim($cur_batch_total) !== '') ? $cur_batch_total : 0;

        // Total points
        $total_points = round($cur_batch_total/500);

        // Add points
        $mysqli->query("INSERT INTO wp_ppttd_reward_points (merchant_id,points) VALUES ('".$cur_merchant_id."',".$total_points.")");

        // Echo success
        echo $cur_merchant_id.' ($'.number_format($cur_batch_total, 2).' - '.$total_points." Points)\n";
    }

    $end_time = microtime(true);
    echo 'Script took '.number_format($end_time-$start_time, 10).' seconds to complete.';
?>