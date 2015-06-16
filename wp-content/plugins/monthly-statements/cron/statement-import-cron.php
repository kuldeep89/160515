<?php
/**
 * Set timezone
 */
date_default_timezone_set('America/Indianapolis');


/**
 * Define global vars
 */
$current_hostname = gethostname();
global $upload_dir; $upload_dir = (stripos($current_hostname, 'saltsha.com') !== false && is_dir('/home/nabsftp/')) ? '/home/nabsftp/' : dirname(getcwd()).'/uploads/';
global $num_checks; $num_checks=0;
global $max_checks; $max_checks=10;
global $wait_time; $wait_time=60;
global $mysqli;
global $table_prefix;
global $cdn_instance;


/**
 * Start checking for files
 */
check_for_files();


/**
 * Check for files
 */
function check_for_files() {
	// Include CDN libraries
	require_once(dirname(__DIR__).'/lib/class.rs_cdn.php');
	require_once(dirname(__DIR__).'/lib/functions.php');
	require_once(dirname(__DIR__).'/lib/php-opencloud-1.5.10/lib/php-opencloud.php');

    // Get globals
	global $num_checks;
	global $max_checks;
	global $wait_time;
	global $upload_dir;
	global $cdn_instance;

    // Check SFTP folder permissions
    if ($upload_dir === '/home/nabsftp/') {
        $folder_owner = posix_getpwuid(fileowner('/home/nabsftp'));
        if ($folder_owner['name'] !== 'nabsftp') {
            // Set owner to nabsftp
            shell_exec("chown -R nabsftp:nabsftp /home/nabsftp/");
        }
    }

    // Make sure CDN instance is set
    $cdn_instance = (is_null($cdn_instance) || !isset($cdn_instance)) ? new RS_CDN() : $cdn_instance;

	// Check if user is logged in via SFTP, if so, wait a few seconds and check again
	$check_sftp = shell_exec("ps -ef | grep 'nabsftp@internal-sftp'");
	if (stripos($check_sftp, 'sshd') !== false && stripos($check_sftp, 'nabsftp@internal-sftp') !== false) {
	        $num_checks++;
			
			// If we've reached max number of checks, cancel and wait until next cron runs
			if ($num_checks >= $max_checks) {
				exit();
			}

			// Wait 5 seconds and try again
	        $check_sftp = "";
	        sleep($wait_time);

			// Check for files again
	        check_for_files();
	} else {
			// If file is locked, exit script, it's already running
			$td_lock_file = fopen('/tmp/smmslock.txt', 'w+');
			if(!flock($td_lock_file, LOCK_EX | LOCK_NB)) {
				exit();
			}

	        // Check for files
            $num_tgz_files = count(get_files_of_type('tgz', 'statement'));
            $num_pdf_files = count(get_files_of_type('pdf', 'statement'));

	        // If there are files, import them
	        if ($num_tgz_files > 0 || $num_pdf_files > 0) {
				import_statements();
	        }
	}
}


/**
 * Import data
 */
function import_statements() {
    // Include globals
    global $mysqli;
    global $cdn_instance;
    global $table_prefix;
    global $upload_dir;
    $send_to_merchants = array();

    // Require db info file, include globals
	require_once(dirname(__DIR__).'/lib/database.php');

	// Import data variables
	$current_hostname = gethostname();
	$file_data 			= array();
	$did_upload 		= array();
	$directory_to_scan 	= scandir($upload_dir);

	// Set file arrays
	$unlink_files = array();

    // Get tgz archives, decompress and delete
    $cur_tgz_files = get_files_of_type('tgz', 'statement');
    if ($cur_tgz_files > 0) {
        foreach ($cur_tgz_files as $the_tgz_file) {
            if (untar_file($the_tgz_file) === true) {
                @unlink($the_tgz_file);
            }
        }
    }

    // Get PDF files, decompress and delete
    $cur_pdf_files = get_files_of_type('pdf', 'statement');
    if ($cur_pdf_files > 0) {
        // PDF files exist, add date of file(s) to array
        foreach ($cur_pdf_files as $cur_pdf_file) {
            // Get file name parts so we can rename file with padded 16 digit merchant ID
            $cur_pdf_file_basename = basename($cur_pdf_file);
            $cur_pdf_file_parts = explode('-', basename($cur_pdf_file));

            // Set new file name with merchant ID padded to 16 digits with zeros
            $padded_file_name = $cur_pdf_file_parts[0].'-'.str_pad($cur_pdf_file_parts[1], 16, '0', STR_PAD_LEFT).'-'.$cur_pdf_file_parts[2];
            $padded_file_name = str_replace($cur_pdf_file_basename, $padded_file_name, $cur_pdf_file);

            // Try to rename file, if it fails, continue to next file
            if (rename($cur_pdf_file, $padded_file_name) === false) {
                notify_admin('bstump@paypromedia.com', 'PDF_RENAME_ERROR_001 ('.$cur_pdf_file.').');
        		log_error("PDF_RENAME_ERROR_001 (".$cur_pdf_file."): ", "Unable to rename file (".$cur_pdf_file.").");
                continue;
            }

            // Set current PDF file name to the new one
            $cur_pdf_file = $padded_file_name;

            // Get current file name
            $cur_file_name = pathinfo($cur_pdf_file, PATHINFO_BASENAME);
            $the_file_date = substr($cur_file_name, 0, 6);

            // Get file data
            $file_data = explode('-', pathinfo($cur_pdf_file, PATHINFO_BASENAME));
            $the_merchant_id = $file_data[1];
            $the_month = substr($file_data[0], 4, 2);
            $the_year = substr($file_data[0], 0, 4);

            // Try to upload file to the CDN
        	if (try_to_upload($cur_pdf_file) == true) {
        	    // Add to array of merchants to send notifications to
        	    $send_to_merchants[] = $the_merchant_id;

                // Upload successful, remove PDF from server
                @unlink($cur_pdf_file);

                // Add file data to database
                send_query("INSERT IGNORE INTO ".$table_prefix."smms_monthly_statements (merchant_id,month,year) VALUES ('".$the_merchant_id."','".$the_month."','".$the_year."')");
    		}
        }

        // Send out statement notification emails
/*
        require_once dirname(__DIR__).'/lib/statement-mailer.php';
    	$obj_mailer	= new Statement_mailer();
    	$obj_mailer->send_update('statement', $the_month, $the_year, $send_to_merchants);
*/
    }

	// Close MySQL connection
	$mysqli->close();
}


/**
 * Try to upload file
 */
function try_to_upload($file_to_upload = null) {
    // Just return true if localhost
    $hostname = gethostname();
    if (stripos($hostname, 'local') !== false || stripos($hostname, 'sbcglobal') !== false) {
        echo "Local testing, not uploading file. To change modify 'statement-import-cron.php' file.\n\n";
        return true;
    }

    global $cdn_instance;

    // If null, return false
    if (is_null($cdn_instance) || is_null($file_to_upload)) {
        return false;
    }

    // Try to upload the file to the CDN
    $num_tries = 0;
    while ($num_tries < 2) {
        // If this is the third try, exit and return false
        if ($num_tries == 2) {
            return false;
        }

        // Try to upload, three tries max
    	try {
    		$cdn_instance->upload_file($file_to_upload);
    		return true;
    	} catch (Exception $exc) {
    		// Upload unsuccessful, break from loop
    		notify_admin('bstump@paypromedia.com', 'CDN_UPLOAD_ERROR_001 ('.$file_to_upload.'): '.$exc);
    		log_error("CDN_BACKUP_ERROR_001 (".$file_to_upload."): ", $exc);
    		return false;
    	}

        // Increment number of tries
        $num_tries++;
    }
}


/**
 * Get files of certain type
 */
function get_files_of_type($file_ext = null, $file_name_search = null) {
    global $upload_dir;
    global $cdn_instance;

    $return_files = array();

    // If null, return empty array
    if ($file_ext == null) {
        return $return_files;
    }

    // Scan directory
    $directory_to_scan 	= scandir($upload_dir);

    // Loop through directory
    foreach ($directory_to_scan as $cur_file) {
        // Get path info for file
        $file_path_info = pathinfo($cur_file);

        // Search file name and/or extension
        if (is_null($file_name_search)) {
            // Search extension only
            if (is_readable($upload_dir.$cur_file) && $file_path_info['extension'] == $file_ext) {
                $return_files[] = $upload_dir.$cur_file;
            }
        } else {
            // Build search string(s)
            $search_regex = '/^(?=.*';
            if (is_array($file_name_search)) {
                // Multiple search strings
                $search_regex .= '';
                $search_regex .= implode(')(?=.*', $file_name_search);
            } else {
                // Single search string
                $search_regex .= $file_name_search;
            }
            $search_regex .= ')/i';

            // Search extension AND file name
            if (is_readable($upload_dir.$cur_file) && isset($file_path_info['extension']) && $file_path_info['extension'] == $file_ext && preg_match($search_regex, $file_path_info['filename']) > 0) {
                $return_files[] = $upload_dir.$cur_file;
            }
        }
    }

    // Return list of files
    return $return_files;
}


/**
 * Untar tarball
 */
function untar_file($file_to_untar) {
    global $upload_dir;
    global $cdn_instance;

    // Untar file
    $do_untar = shell_exec("tar -zxvf ".$file_to_untar." -C ".$upload_dir." 2>&1; echo $?");

    // If error, return false
    if (stripos($do_untar, 'error') !== false) {
	    notify_admin('bstump@paypromedia.com', 'UNTAR_FILE_ERROR_001 ('.$file_to_untar.'): '.$do_untar);
	    log_error("UNTAR_FILE_ERROR_001 (".$file_to_untar.")", $do_untar);
        return false;
    }

    // Successful untar
    return true;
}


/**
 * Run MySQL query
 */
function send_query($the_query) {
    global $mysqli;
    global $cdn_instance;

    // If db connection is no good, fail
	if ($mysqli === false) {
		notify_admin('bstump@paypromedia.com', 'DB_QUERY_ERROR_001: Database connection not established.');
		log_error("DB_QUERY_ERROR_001", 'Database connection not established.');
		return false;
	}

    // Try query three times before failing
	for ($failures = 0; $failures < 3; $failures++) {
		try {
			$query_result = $mysqli->query($the_query);
			if (isset($mysqli->error) && trim($mysqli->error) != '') {
				notify_admin('bstump@paypromedia.com', 'DB_QUERY_ERROR_002: '.$mysqli->error);
				log_error("DB_QUERY_ERROR_002", $mysqli->error);
			}
			return $query_result;
		} catch(Exception $exc) {
			if ($failures == 3) {
				notify_admin('bstump@paypromedia.com', 'DB_QUERY_ERROR_003: '.$exc);
				log_error("DB_QUERY_ERROR_003", $exc);
				return false;
			}
		}
	}
}


/**
 * Notify function
 */
function notify_admin($admin_email, $message) {
    global $cdn_instance;

	// Set admin email
	$admin_email = (isset($admin_email)) ? $admin_email : 'webmaster@saltsha.com';

	// Add headers and send email
	$headers = "From: Saltsha <success@saltsha.com>\r\n";
	$headers .= "Reply-To: Saltsha <success@saltsha.com>\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$headers .= "X-Mailgun-Native-Send: true\r\n";
	mail($admin_email, 'Saltsha Statement Import Cron Failure', $message, $headers);
}


/**
 * Log an error
 */
function log_error($error_code, $error_text) {
    global $mysqli;

    $mysqli->query("INSERT INTO wp_ppttd_log (timestamp,error_code,error_text) VALUES ('".time()."','".addslashes($error_code)."','".addslashes($error_text)."')");
}
?>