<?php
//cron job - first day of every month at 2am - 0 2 1 * *

include('functions.php');
/**
*	Monthly Tier 0 Merchant who upgraded to Tier 1 Merchant
**/
$rows_query = $mysqli->query("
	SELECT u1.usr_id, u1.date, g.name
	FROM ".$wp_user_changes." u1 
		LEFT JOIN ".$wp_user_changes." u2 ON (u1.usr_id = u2.usr_id AND u1.id < u2.id) 
		LEFT JOIN ".$wp_groups_group." g ON (u1.grp_id = g.group_id)
	WHERE u2.id IS NULL 
		AND (u1.grp_id = '7' OR u1.grp_id='8') 
		AND u1.date > timestampadd(day, -7, now())
	ORDER BY u1.grp_id, u1.date;" );
	
// Make sure query was successful before we do the deed
if($rows_query){
	$theFile = $_SERVER['DOCUMENT_ROOT'] . "/User_Changes_" . date("Y-m-d") . ".csv";
	$df = fopen($theFile, 'w');
	$colHeaders = array(
		0 => 'User ID',
		1 => 'Date Modified',
		2 => 'Tier',
		3 => 'Merchant ID'
	);
	fputcsv($df, $colHeaders);
	//print_r($colHeaders); echo '<br />'; 
	while($rows_result = mysqli_fetch_assoc($rows_query)) {
		//Get user's merchant id from meta info
		$usr = $rows_result['usr_id'];
		$meta = get_user_meta( $usr, 'ppttd_merchant_info', true );
		if(isset($meta['ppttd_merchant_id']) && !is_null($meta['ppttd_merchant_id']) && trim($meta['ppttd_merchant_id']) != ''){
			array_push($rows_result, $meta['ppttd_merchant_id']);
		}
		//Output a new row to the csv file for each row
		fputcsv($df, $rows_result);
		//echo print_r($rows_result, true);
	}
	fclose($df);
	
	$cdnSettings = array(
		"container" => "saltsha_csv_backups"
	);
	
	if( $csv_cdn = new CFCDN_CDN($cdnSettings) ){
		if( $csv_cdn->upload_file($theFile) ){
			$fileatt_type = "text/csv";
			
			$file_size = filesize($theFile);
			$handle = fopen($theFile, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			
			//$to = $admin_email;
			$to_users = array('tchin@paypromedia.com','jfarrell@paypromedia.com','nmanahan@paypromedia.com','pkent@paypromedia.com');
			$subject = 'Saltsha Monthly Report - Tier 0 and 1 merchant account changes';
			
			$content = chunk_split(base64_encode($content));
			
			$message = "<html>
			<head>
			<title>Saltsha Monthly Report - Tier 0 and 1 merchant account changes</title>
			</head>
			<body><table><tr><td><h4>Monthly User Change Report</h4></td></tr><tr><td><p style='color:#444444;'>Tier 0 Merchants who upgraded to Tier 1 and Tier 1 Merchants who downgraded to Tier 0</p></td></tr></table></body></html>";
			
			foreach ($to_users as $to) {
    			$uid = md5(uniqid(time()));
    			
    			$header = "From: Saltsha <success@saltsha.com>\r\n";
    			$header .= "MIME-Version: 1.0\r\n";
    			$header	.= "X-Mailgun-Native-Send: true\r\n";
    			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    			$header .= "This is a multi-part message in MIME format.\r\n";
    			$header .= "--".$uid."\r\n";
    			$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
    			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    			$header .= $message."\r\n\r\n";
    			$header .= "--".$uid."\r\n";
    			$header .= "Content-Type: text/csv; name=\"".$theFile."\"\r\n";
    			$header .= "Content-Transfer-Encoding: base64\r\n";
    			$header .= "Content-Disposition: attachment; filename=\"".$theFile."\"\r\n\r\n";
    			$header .= $content."\r\n\r\n";
    			$header .= "--".$uid."--";
    			
    			$mailSent = mail($to, $subject, $message, $header);
    			if($mailSent){
    				echo 'Sent to '.$to.".\n";
    			}
            }

            // Delete the file
            unlink($theFile);
		}
	}
}
