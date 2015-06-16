<?php
    // CSV format:
    // MID,DBA Name,Equipment,Received Date,Phone,Email Address

    // Require WordPress schtuff
    require_once('../../../../wp-blog-header.php');

    // Require MailGun schtuff
    require_once '../lib/Mailgun/Mailgun.php';

    // Get content of file
    $users = file_get_contents('user-import.csv');
    
    // Failed users
    $failed_merchants = array();

    // Import CSV
    if (($handle = fopen("user-import.csv", "r")) !== FALSE) {
        while (($cur_user = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $user_dba = trim($cur_user[1]);
            $user_login = (trim($cur_user[0]) !== '') ? trim($cur_user[0], '0') : null;
            $user_pass = random_password();
            $user_email = strtolower(trim($cur_user[3]));
            $sales_rep_id = (is_null($cur_user[2]) || trim($cur_user[2]) === '') ? '' : $cur_user[2];

            if (!is_null($user_login)) {
                if (!username_exists($user_login) && !email_exists($user_email)) {
                    // Set default merchant data
                    $merchant_data = array(
                        'ppttd_merchant_id' => array(
                            str_pad($user_login, 16, '0', STR_PAD_LEFT) => $user_dba
                        ),
                        'ppttd_sales_rep_id' => $sales_rep_id,
                        'ppttd_daily_transaction_report' => 'on',
                        'ppttd_weekly_transaction_report' => 'on',
                        'ppttd_monthly_transaction_report' => 'on',
                        'ppttd_week_starts_on' => 'Monday'
                    );

                    // Create user
                    $user_id = wp_create_user($user_login, $user_pass, $user_email);

                    // Check if user was created successfully
                    if (is_int($user_id)) {
                        // Update name
                        $update_user_info = wp_update_user(array('ID' => $user_id, 'display_name' => $user_dba, 'first_name' => $user_dba));

                        // Update merchant meta
                        $update_user_meta = update_user_meta($user_id, 'ppttd_merchant_info', $merchant_data);
                        $update_user_company_select_meta = update_user_meta($user_id, 'company_select', 'Pilothouse'); // Pilothouse or PayProTec

                        // Check if meta was added successfully
                        if ($update_user_meta !== false && $update_user_company_select_meta !== false) {
                            // Insert user into "Tier 1 Merchants" group
                            $add_user_group = $wpdb->insert($wpdb->prefix.'groups_user_group', array('user_id' => $user_id, 'group_id' => 7));
                            if ($add_user_group === false) {
                                echo 'Error adding user <strong>'.$user_login.' ('.$user_id.')</strong> to Tier 1 Merchant group.'."\n";
                                if (!in_array($user_login, $failed_merchants)) {
                                    $failed_merchants[] = implode(',', $cur_user);
                                }
                            }

                            // Remove merchant from group 0, not sure why this is added by WP, but it is
                            $wpdb->delete($wpdb->prefix.'groups_user_group', array('user_id' => $user_id, 'group_id' => 0));

                            // Send out  mailer.
                            $headers 	= "From: Saltsha <success@saltsha.com>\r\n";
                            $headers 	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
                            $headers 	.= "MIME-Version: 1.0\r\n";
                            $headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                            $headers 	.= "X-Mailgun-Native-Send: true\r\n";

                            // Get template
                            $message = file_get_contents('../template/welcome-template.html');

                            // Replace user/pass placeholders in email
                            $message = str_replace('[PPTTD_USER]', $user_login, $message);
                            $message = str_replace('[PPTTD_PASS]', $user_pass, $message);

                            // Send email
                            try {
        	                	mail($user_email, 'Saltsha', $message, $headers);
        	                } catch (Exception $exc) {
        	                	echo 'Error sending welcome email to user <strong>'.$user_login.' ('.$user_id.')</strong>.'."\n";
        	                }
                        } else {
                            echo 'Error adding merchant info for user <strong>'.$user_login.' ('.$user_id.')</strong>.'."\n";
                            if (!in_array($user_login, $failed_merchants)) {
                                $failed_merchants[] = implode(',', $cur_user);
                            }
                        }
                    } else {
                        echo 'Error adding user <strong>'.$user_login.'</strong>.'."\n";
                        if (!in_array($user_login, $failed_merchants)) {
                            $failed_merchants[] = implode(',', $cur_user);
                        }
                    }
                } else {
                    echo 'User <strong>'.$user_login.'</strong> already exists.'."\n";
                    if (!in_array($user_login, $failed_merchants)) {
                        $failed_merchants[] = implode(',', $cur_user);
                    }
                }
            }
            echo "\n\n";
        }
        fclose($handle);
    }

    // Send out  mailer.
    $fe_headers 	= "From: Saltsha <success@saltsha.com>\r\n";
    $fe_headers 	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
    $fe_headers 	.= "MIME-Version: 1.0\r\n";
    $fe_headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $fe_headers 	.= "X-Mailgun-Native-Send: true\r\n";

    // Set failed merchants message
    $fe_message = implode("<br/>", $failed_merchants);

    // Send failed import email
    try {
    	mail('jfarrell@payprotec.com', 'Failed Merchant Imports', $fe_message, $fe_headers);
    } catch (Exception $exc) {
    	echo "Error sending failed merchants.\n\n".implode("\n", $failed_merchants);
    }

    // Generate random password
    function random_password() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
?>