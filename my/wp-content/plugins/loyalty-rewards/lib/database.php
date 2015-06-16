<?php

	// Get current working directory
	$current_dir = getcwd();
	$current_hostname = gethostname();

	// Check which environment we are in
	if (stripos($current_hostname, 'saltsha.com') !== false && is_dir('/home/nabsftp/')) {
		// Production
		$db_prefix = 'saltshac_live';
		$db_host = 'dbs1.saltsha.com';
	} else {
		// Dev environment(s)
		if (stripos($current_dir, '/home/saltshac/public_html/stage') !== false) {
			$db_prefix = 'saltshac_stage';
			$db_host = 'localhost';
		} elseif (stripos($current_dir, '/home/saltshac/public_html/qa') !== false) {
			$db_prefix = 'saltshac_qa';
			$db_host = 'localhost';
		} elseif (stripos($current_hostname, 'local.') !== false || stripos($current_hostname, '.local') !== false || stripos($current_hostname, 'sbcglobal.') !== false) {
			$db_prefix = 'saltshac_dev';
			// $db_host = 'localhost';
			$db_host = '172.1.2.5';
		} else {
			$db_prefix = 'saltshac_dev';
			$db_host = 'lw1.paypromedia.com';
		}
	}
	
	/*
	 * Define Database Credientials
	 */
	define('DB_NAME', $db_prefix.'_my');
	define('DB_USER', 'saltshac_Tlc0hWJ');
	define('DB_PASSWORD', 'R,UN~v.l2p+iBQ9zud');
	define('DB_HOST', $db_host);
	
	$table_prefix = 'wp_';
	
	// Setup db connection
	try {
    	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	} catch (Exception $exc) {
    	$mysqli = false;
	}
	
	// Connect to db, error out if it breaks
    if ($mysqli->connect_errno || $mysqli === false) {
		echo 'DB_CONNECT_ERROR: '.$mysqli->connect_errno.'<br/>';
		@$mysqli->close();
		$mysqli = false;
		exit;
	}
?>