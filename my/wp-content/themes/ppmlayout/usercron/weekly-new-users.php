<?php
//cron job - first day of every week at 2am - 0 2 * * 2

include('functions.php');
/**
*	Weekly New Merchant Accounts
**/

//Selects the most recent change
$frows_query = $mysqli->query( "
	SELECT u.ID, GROUP_CONCAT(um.meta_value SEPARATOR ' ') as name, u.user_email, u.user_registered, gg.name
	FROM wp_users u 
		LEFT JOIN ".$wp_groups_user_group." gu ON u.ID = gu.user_id 
		LEFT JOIN ".$wp_groups_group." gg ON gu.group_id = gg.group_id 
		LEFT JOIN ".$wp_usermeta." um ON u.ID = um.user_id
	WHERE 
		(gu.group_id = 7 OR gu.group_id = 8)
                AND (um.meta_key = 'first_name' OR um.meta_key = 'last_name')
		AND u.user_registered > timestampadd(day, -7, now())
	GROUP BY u.ID" );

// Make sure query was successful before we do the deed
if($frows_query){
	$theFile = $_SERVER['DOCUMENT_ROOT'] . "/New_Users_" . date("Y-m-d") . ".csv";
	$df = fopen($theFile, 'w');
	$colHeaders = array(
		0 => 'User ID',
		1 => 'Full Name',
		3 => 'Email',
		4 => 'Date Registered',
		5 => 'Group'
	);
	fputcsv($df, $colHeaders);

	while($frows_result = mysqli_fetch_assoc($frows_query)) {
		//echo print_r($frows_result, true);
		//Outpu a new row to the csv file for each row
		fputcsv($df, $frows_result);
	}
	fclose($df);
	
	$fileatt_type = "text/csv";
	
	$file_size = filesize($theFile);
	$handle = fopen($theFile, "r");
	$content = fread($handle, $file_size);
	fclose($handle);
	
	//$to = $admin_email;
	$to_users = array('tchin@paypromedia.com','jfarrell@paypromedia.com','nmanahan@paypromedia.com','pkent@paypromedia.com');
	$subject = 'Saltsha Weekly Report - New Merchant Accounts';
	
	$content = chunk_split(base64_encode($content));
	
	$message = "<html>
	<head>
	<title>Saltsha Weekly Report - New Merchant Accounts</title>
	</head>
	<body><table><tr><td><h4>Weekly New Merchant Accounts</h4></td></tr><tr><td><p style='color:#444444;'>New Tier 1 Merchant and Tier 0 Merchant accounts.</p></td></tr></table></body></html>";

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
?>
