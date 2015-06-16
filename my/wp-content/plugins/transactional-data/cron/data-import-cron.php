<?php
/**
 * Require MailGun library
 */
require_once(dirname(__DIR__).'/lib/Mailgun/Mailgun.php');


/**
 * Set timezone
 */
date_default_timezone_set('America/Indianapolis');


/**
 * Define global vars
 */
global $ppttd_debug; $ppttd_debug = false;
$current_hostname = gethostname();
global $upload_dir; $upload_dir = (stripos($current_hostname, 'saltsha.com') !== false && is_dir('/home/nabsftp/')) ? '/home/nabsftp/' : dirname(getcwd()).'/uploads/';
global $num_checks; $num_checks=0;
global $max_checks; $max_checks=10;
global $wait_time; $wait_time=60;
global $mysqli;
global $table_prefix;
global $cron_errors; $cron_errors = array();


/**
 * Start checking for files
 */
check_for_files();


/**
 * Check for files
 */
function check_for_files() {
    // Get globals
	global $num_checks;
	global $max_checks;
	global $wait_time;
	global $upload_dir;
	global $ppttd_debug;

    // Check SFTP folder permissions
    if ($upload_dir === '/home/nabsftp/') {
        $folder_owner = posix_getpwuid(fileowner('/home/nabsftp'));
        if ($folder_owner['name'] !== 'nabsftp') {
            // Set owner to nabsftp
            shell_exec("chown -R nabsftp:nabsftp /home/nabsftp/");
        }
    }

	// Check if user is logged in via SFTP and that it's not a failed session
	$check_sftp = shell_exec("ps -ef | grep 'nabsftp@internal-sftp'");
    $check_sftp = explode("\n", $check_sftp);
    $current_sessions = array();
    foreach ($check_sftp as $key => $cur_sftp) {
            $current_cmd = array_filter(explode(" ", $cur_sftp));
            if (in_array('nabsftp', $current_cmd)) {
                    $current_cmd = array_values($current_cmd);
                    $current_sessions[] = array_values($current_cmd);
                    if (stripos($current_cmd[3], date('Md')) === false) {
                            // Get date of process
                            $pid_month = substr($current_cmd[3], 0, 3);
                            $pid_day = substr($current_cmd[3], -2, 2);
                            $pid_date = strtotime(date('Y').'-'.$pid_month.'-'.$pid_day.' '.$current_cmd[5]);

                            // If date is more than 10 hours, kill SFTP session
                            $time_diff = (time()-$pid_date);
                            if ($time_diff > 36000) {
                                    shell_exec("kill -9 $current_cmd[1]");
                            }
                    }
            }
    }

    // SFTP sessions cleared, good to go
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
		if ($upload_dir === '/home/nabsftp/') {
    		$td_lock_file = fopen('/tmp/tdlock.txt', 'w+');
            if(!flock($td_lock_file, LOCK_EX | LOCK_NB)) {
                log_error("TMP_LOCK_001", "There was and issue getting a temporary lock.");
                exit;
		    }
        }

        // Check for files
        $num_gz_files = count(get_files_of_type('gz'));

        // If there are files, import them
        if ($num_gz_files > 0) {
			import_data();
        }
	}
}


/**
 * Import data
 */
function import_data() {
    // Include globals
    global $mysqli;
    global $table_prefix;
    global $upload_dir;
    global $ppttd_debug;
    $send_to_merchants = array();
    $points_update_failed = array();
    $send_email_date = date('Y-m-d', strtotime("-2 days"));

    // Require db info file, include globals
	require_once(dirname(__DIR__).'/lib/database.php');

	// Include CDN libraries
	require_once(dirname(__DIR__).'/lib/class.rs_cdn.php');
	require_once(dirname(__DIR__).'/lib/php-opencloud-1.5.10/lib/php-opencloud.php');

	// Import data variables
	$current_hostname = gethostname();
	$file_data 			= array();
	$directory_to_scan 	= scandir($upload_dir);

    // Turn off debug if on production server
    if (stripos($current_hostname, 'saltsha.com') !== false) {
        $ppttd_debug = false;
    }

	// Set data timeframe
	$daily_date 		= date('Y-m-d', strtotime("-1 day"));

	// Create ZIP archive
	$zip = new ZipArchive();
	$zip_file_name = ($ppttd_debug === true) ? $upload_dir.'TEST-'.$daily_date."_".date('H.i.s')."-backup.zip" : $upload_dir.$daily_date."_".date('H.i.s')."-backup.zip";

	// Open ZIP archive
	if (!$zip->open($zip_file_name, ZipArchive::CREATE)) {
		log_error("ZIP_ARCHIVE_ERROR_001", "There was an error while creating zip archive.");
		exit;
	}

	// Loop through all folders and files to get transaction data
	$unlink_files = array();

    // Array to store files to parse
    $files_to_parse = array();

    // Get files to sort and store to array, transactions should be inserted first
	foreach ($directory_to_scan as $cur_file) {
		unset($column_names);

		$cur_file = $upload_dir.$cur_file;
		$pathinfo = pathinfo($cur_file);

        // Sort files so batch listing is last (so we can get the right transaction count for updated batches)
		if (is_file($cur_file) && $pathinfo['extension'] == 'gz') {
		    $files_to_parse[] = $cur_file;
		}
    }

    // Reverse sort files so we parse transactions first
    rsort($files_to_parse);

    // Parse files and insert into db
    foreach ($files_to_parse as $cur_file) {
        // Monitor if query fails
        $failed_queries = array();

        // Check if chargebacks, retrievals or settlement file
        $is_crs_file = false;
        if (stripos($cur_file, 'chargeback') !== false || stripos($cur_file, 'retrieval') !== false || stripos($cur_file, 'settlement') !== false) {
            $is_crs_file = true;
        }

        // Get file info
        $pathinfo = pathinfo($cur_file);

        // Define table name and SQL string
		$table_name = trim(str_replace(range(0,9), '', $pathinfo['basename']));
		$table_name = strstr($table_name, '.', true);
		$table_name = trim($table_name, '-');

		if (stripos($table_name, '_') !== false) {
			$table_name = explode('_', $table_name);
			$table_name = $table_name[0];
		}

        // Get transaction date
        $trans_date =  preg_replace("/^(\d{4})(\d{2})(\d{2})$/", "$1-$2-$3", substr($pathinfo['basename'], 0, 8));
        $send_email_date = $trans_date;

        // Check file type
        if ($is_crs_file) {
            // Set data provider
            $data_provider = null;

            // Query number of transactions
            $num_trans = try_to_query("SELECT processor FROM ".$table_prefix."ppttd_".$table_name." WHERE date_imported LIKE '%".$trans_date."%' LIMIT 1");
        } else {
            // Set data provider
            preg_match('/_(.*?)\./', $pathinfo['basename'], $data_provider);
            $data_provider = $data_provider[1];

            // Query number of transactions
            $num_trans = try_to_query("SELECT uniq_batch_id FROM ".$table_prefix."ppttd_".$table_name." WHERE date_imported LIKE '%".$trans_date."%' AND data_provider = '".$data_provider."' LIMIT 1");
        }

        // If error, move to next file
        if (!is_numeric(str_replace('-', '', $trans_date)) || ($num_trans !== false && $num_trans->num_rows > 0)) {
            $failed_queries[] = 'true';
            if (!is_numeric(str_replace('-', '', $trans_date))) {
                log_error("IMPORT_WARN_001", 'Transactional data file for date '.$trans_date.' and provider '.strtoupper($data_provider).' in table '.$table_name.' had no date.');   
            } else {
                log_error("IMPORT_WARN_002", 'Transactional data for date '.$trans_date.' and provider '.strtoupper($data_provider).' in table '.$table_name.' has already been imported.');
            }
			continue;
        }

        // Get column names
        $col_names = array();
        $column_names = '';
        $columns_query = try_to_query("SHOW COLUMNS FROM ".$table_prefix."ppttd_".$table_name);
        if ($columns_query === false) {
			// Query unsuccessful
			$failed_queries[] = 'true';
            log_error("COLUMN_QUERY_ERROR_001", 'COLUMN_QUERY_ERROR_001: Failed getting columns for \''.$table_prefix."ppttd_".$table_name.'\'.');
			exit;
        } else {
            while ($row = $columns_query->fetch_object()){
                if ($row->Field != 'id' && $row->Field != 'is_read' && $row->Field != 'has_duplicates') {
                    $col_names[] = $row->Field;
                }
            }
            $column_names = implode(',', $col_names);
        }

        // Create file to encode
		$file_to_upload = str_replace(array('.txt.gz', '.gz'), '.txt', $cur_file);

		// Decompress GZ file to txt file
		decompress_gz_file($cur_file, $file_to_upload);

		// Read uncompressed file line-by-line, run MySQL query every 500 lines
		$rhf = fopen($file_to_upload, "r");

		if ($rhf) {
			$i=0;

            // Setup new MySQL query
            $sql_query = 'INSERT INTO '.$table_prefix.'ppttd_'.$table_name.' ('.$column_names.') VALUES ';

			while (($line = fgets($rhf)) !== false) {
                // Replace tabs with commas
                if ($is_crs_file) {
                    // Replace commas
                    $line = str_replace(',', ' - ', $line);

                    // Parse tab separated values to get data for line
                    $line = explode('	', $line);

                    // Remove "id" field
                    unset($line[1]);

                    // Create CSV line
                    $line = implode(',', $line);
                }

			    // Remove commas from inside double quotes
				preg_match('/\"(.*?)\"/', $line, $matches);
				foreach ($matches as $cur_match) {
					$new_text = str_replace(',', ' - ', $cur_match);
					$new_text = str_replace('"', '', $new_text);
					$line = str_replace($cur_match, $new_text, $line);
				}

                // Add merchant ID if not already in array
                if ($table_name == 'batchlisting') {
                    $cur_line_data = explode(',',$line);
                    if (!in_array($cur_line_data[1], $send_to_merchants)) {
                        $cur_mid = str_pad($cur_line_data[1], 16, '0', STR_PAD_LEFT);
                        $send_to_merchants[] = $cur_mid;
                        $cur_points = round($cur_line_data[4]/500); // 1 point per $500 processed

                        // Update points in database
                        $points_query_result = try_to_query("INSERT INTO wp_ppttd_reward_points (merchant_id,points) VALUES ('".$cur_mid."',".$cur_points.") ON DUPLICATE KEY UPDATE points=points+VALUES(points)");

                        // Add to failed merchant update array
    					if (!$points_query_result) {
        					$points_update_failed[] = array('merchant_id' => $cur_mid, 'points' => $cur_points);
        				}
                    }
                }

				// Add to SQL query
				if (($i % 500 == 0 || feof($rhf)) && $i != 0) {
					$line = trim(addslashes($line));
					if ($is_crs_file) {
    					$line .= ','.$trans_date;
    				} else {
        				$line .= ','.$trans_date.','.$data_provider;
    				}
					$line = str_replace(',', '\',\'', $line);
					$line = '(\''.$line.'\')';
					$sql_query .= $line;

                    // If batch listing, check if additional transactions for batch and update total
                    if ($table_name == 'batchlisting') {
                        $sql_query .= " ON DUPLICATE KEY UPDATE total_volume=total_volume+VALUES(total_volume),total_trans=total_trans+VALUES(total_trans),total_purch_amt=total_purch_amt+VALUES(total_purch_amt),total_purch_trans=total_purch_trans+VALUES(total_purch_trans),date_imported=CONCAT(date_imported,',','".$trans_date."');";
                    } else {
                        $sql_query .= ';';
                    }

                    // Check if debug
                    if ($ppttd_debug == false) {
                        // Run MySQL query
                        $cur_query_result = try_to_query($sql_query);
    					if ($cur_query_result === false) {
    						// Query unsuccessful
    						$failed_queries[] = 'true';
    					}
    				}

					// Setup new MySQL query
					$sql_query = 'INSERT INTO '.$table_prefix.'ppttd_'.$table_name.' ('.$column_names.') VALUES ';
				} else {
    				if ($i != 0) {
    					$line = trim(addslashes($line));
    
                        // Add transaction date and data provider if batch or transactions
                        if ($is_crs_file) {
                            $line .= ','.$trans_date;
                        } else {
                            $line .= ','.$trans_date.','.$data_provider;
                        }
    
    					$line = str_replace(',', '\',\'', $line);
    					$line = '(\''.$line.'\'),';
    					$sql_query .= $line;
    				}
				}
				$i++;

			}

			// Prepare data, run last query
			$sql_query = trim($sql_query, ',');
			
			// Check for SQL query with no values
			if ($sql_query === 'INSERT INTO '.$table_prefix.'ppttd_'.$table_name.' ('.$column_names.') VALUES ') {
    			// No values, remove file, it's blank
    			@unlink($cur_file);
    			@unlink($file_to_upload);

                // Move to next file
    			continue;
			}

            // If batch listing, check if additional transactions for batch and update total
            if ($table_name == 'batchlisting') {
                $sql_query .= " ON DUPLICATE KEY UPDATE total_volume=total_volume+VALUES(total_volume),total_trans=total_trans+VALUES(total_trans),total_purch_amt=total_purch_amt+VALUES(total_purch_amt),total_purch_trans=total_purch_trans+VALUES(total_purch_trans),date_imported=CONCAT(date_imported,',','".$trans_date."');";
            } else {
                $sql_query .= ';';
            }

			// Check if debug
			if ($ppttd_debug == false) {
    			// Run MySQL query
    			if (try_to_query($sql_query) === false) {
    				// Query unsuccessful
    				$failed_queries[] = 'true';
    			}
            }
		}

		// Set name of encoded file
		$encoded_file = $file_to_upload.'.enc';

		// If successful encrypting file, add encrypted file to archive
		if (try_to_encrypt($file_to_upload, $encoded_file) == true) {
			if ($zip->addFile($encoded_file)) {
				// Remove .gz file ONLY if imported successfully
				if (!in_array('true', $failed_queries)) {
                    @unlink($cur_file);
                }

                // Remove .txt and .enc files
                @unlink($file_to_upload);
                array_push($unlink_files, $file_to_upload.'.enc');
			} else {
				// Adding file to archive unsuccessful
				log_error("ZIP_ARCHVIE_ERROR_002", 'Failed adding file \''.basename($encoded_file).'\' to archive.');
			}
		}
	}

    // Send failed merchant updates
    if (count($points_update_failed) > 0) {
		if ($ppttd_debug) {
    		echo 'Failures: <pre>'.print_r($points_update_failed, true).'</pre>';
        } else {
    		log_error("POINTS_UPDATE_ERROR_001", "Failed updating points for these users:\n\n".print_r($points_update_failed, true));
        }
    }

    // Get date to send email for
    $get_date = pathinfo($files_to_parse[0]);
    try {
        $send_email_date =  preg_replace("/^(\d{4})(\d{2})(\d{2})$/", "$1-$2-$3", substr($get_date['basename'], 0, 8));
    } catch (Exception $exc) {
        $send_email_date = $send_email_date;
    }

    // Send out email(s)
    if (count($send_to_merchants) > 0) {
        require_once dirname(__DIR__).'/lib/transactional-mailer.php';
        try {
        	// New mailer
        	$obj_mailer	= new Transactional_mailer();
        
            // Send update
            try {
                $obj_mailer->send_update($send_email_date, 'daily', $send_to_merchants);
            } catch (Exception $exc) {
                log_error("TRANS_MAIL_ERROR_001", 'Failed sending transactional email ('.$exc.').');
            }
        } catch (Exception $exc) {
        	log_error("TRANS_MAIL_ERROR_002", 'Failed creating new transactional mailer instance ('.$exc.').');
        }
    }

    // Close archive
    if ($zip->close()) {
        // Remove any files left
        foreach ($unlink_files as $cur_file_to_remove) {
            @unlink($cur_file_to_remove);
        }
    } else {
        log_error("ZIP_ARCHVIE_ERROR_003", 'Unable to close zip archive \''.basename($zip_file_name).'\'.');
    }

	// Check if valid zip file
	$check_zip = new ZipArchive();
    if ($check_zip->open($zip_file_name, ZipArchive::ER_READ)) {
        // Close archive from checking zip
        if (!$check_zip->close()) {
            log_error("ZIP_ARCHVIE_ERROR_005", 'Unable to close zip archive verification file \''.basename($zip_file_name).'\'.');
        }
    } else {
        log_error("ZIP_ARCHVIE_ERROR_004", 'Unable to verify zip archive \''.basename($zip_file_name).'\'.');
	}

	// If archive exists and size is greater than 0 bytes, upload it
	if (file_exists($zip_file_name) && filesize($zip_file_name) !== false && filesize($zip_file_name) > 0) {
		// Upload to CDN
		$ppttd_cdn = new RS_CDN();

		// Try to upload the zip archive to the CDN
		$upload_file = $ppttd_cdn->upload_file($zip_file_name);
		if ($upload_file) {
			// Add ZIP archive to be removed
			@unlink($zip_file_name);
		} else {
			// Upload unsuccessful, break from loop
			log_error("CDN_BACKUP_ERROR_001", 'Failed to backup file "'.$upload_file.'".');
		}
    } else {
        // Delete invalid ZIP file
        @unlink($zip_file_name);
    }

    // Add merchant IDs to transactions that don't have them
    $merchantless_transactions = $mysqli->query("SELECT DISTINCT uniq_batch_id FROM wp_ppttd_transactionlisting WHERE merchant_id=0000000000000000");
    foreach ($merchantless_transactions as $cur_trans) {
        // Get merchant information
        $get_merchant = $mysqli->query("SELECT merchant_id FROM wp_ppttd_batchlisting WHERE uniq_batch_id='$cur_trans[uniq_batch_id]' LIMIT 1");
        $merchant_info = mysqli_fetch_assoc($get_merchant);

        // Update transactions for batch with merchant ID
        $mysqli->query("UPDATE wp_ppttd_transactionlisting SET merchant_id=".$merchant_info['merchant_id']." WHERE uniq_batch_id='$cur_trans[uniq_batch_id]'");
    }

	// Close MySQL connection
	$mysqli->close();

    // Notify administrator, if any errors
    notify_admin('bobbie.stump@gmail.com');
}


/**
 * Decompress GZ file
 */
function decompress_gz_file($file_to_read, $file_to_write) {
	try {
    	$file = @gzopen($file_to_read, 'rb');
        if ($file) {
            // While not end of file, write data to plain text file
            while (!gzeof($file)) {
                $cur_data = gzread($file, 10485760);
                $fp = @fopen($file_to_write, 'a');
                fwrite($fp, $cur_data);
                fclose($fp);
            }
            gzclose($file);
        }
        return true;
	} catch (Exception $exc) {
	    log_error("DECOMPRESS_GZ_FILE_ERROR_001", 'Failed to decompress file "'.$file_to_read.'" ('.$exc.')');
    	return false;
	}
}


/**
 * Encrypt plain text file
 */
function try_to_encrypt($file_to_upload, $encoded_file) {
	for ($failures = 0; $failures < 3; $failures++) {
		try {
			// Decrypt encrypted file: See 'decrypt-files.php'
			exec("openssl enc -aes-256-cbc -e -in ".$file_to_upload." -out ".$encoded_file." -k ".escapeshellarg('V|c!tgG0S.q3v44T2<1l&a8AB'));
			return true;
		} catch (Exception $exc) {
			if ($failures == 3) {
				log_error("ENCRYPTION_ERROR_001", 'Failed to encrypt file "'.$file_to_upload.'" ('.$exc.').');
				return false;
			}
		}
	}
}


/**
 * Run MySQL query
 */
function try_to_query($the_query) {
    global $mysqli;

    // If db connection is no good, fail
	if ($mysqli === false) {
		log_error("DB_QUERY_ERROR_001", 'Database connection not established.');
		return false;
	}

    // Try query three times before failing
	for ($failures = 0; $failures < 3; $failures++) {
		try {
			$query_result = $mysqli->query($the_query);
			if (isset($mysqli->error) && trim($mysqli->error) != '') {
				log_error("DB_QUERY_ERROR_002", 'MySQL query error ('.$mysqli->error.'). QUERY('.$the_query.')');
			}
			return $query_result;
		} catch(Exception $exc) {
			if ($failures == 3) {
				log_error("DB_QUERY_ERROR_002", 'MySQL query error ('.$exc.'). QUERY('.$the_query.')');
				return false;
			}
		}
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
 * Notify function
 */
function notify_admin($admin_email) {
    global $cron_errors;

    // If no cron errors don't send email
    if (count($cron_errors) == 0) {
        return;
    }

	// Set admin email
	$admin_email = (isset($admin_email)) ? $admin_email : 'webmaster@saltsha.com';

	// Add headers and send email
	$headers = "From: Saltsha <success@saltsha.com>\r\n";
	$headers .= "Reply-To: Saltsha <success@saltsha.com>\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$headers .= "X-Mailgun-Native-Send: true\r\n";

    // Compile errors into message
    $message = implode("\n\n\n", $cron_errors);

    // Try to notify administrator
    try {
        mail($admin_email, 'Saltsha Import Cron Failure', $message, $headers);
    } catch (Exception $exc) {
        echo 'Saltsha Import Cron Failure: '.$message;
    }
}


/**
 * Log an error
 */
function log_error($error_code, $error_text) {
    global $ppttd_debug;
    global $mysqli;
    global $cron_errors;

    // Set error
    $the_error = $error_text.' ('.$error_code.')';
    $cron_errors[] = $the_error;
    
    if ($ppttd_debug) {
        echo "ERROR: ".$the_error."\n\n";
    }

    $mysqli->query("INSERT INTO wp_ppttd_log (timestamp,error_code,error_text) VALUES ('".time()."','".addslashes($error_code)."','".addslashes($error_text)."')");
}
?>
