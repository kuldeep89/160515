<?php
	
	require_once dirname(__FILE__).'/Mailgun/Mailgun.php';
	require_once dirname(__FILE__).'/statement-mailer-model.php';
	
	/**
	 * Required to set timezone. Cron jobs are based on FT Wayne Timezone.
	 */
    date_default_timezone_set('America/Fort_Wayne');
	
	/**
	 * Class to send out statement and tax document notifications.
	 */
	class Statement_mailer {

		// Class model
		private $obj_model;
		
		// Default Constructor
		public function __construct() {
			$this->obj_model	= new Statement_mailer_model();
		}
		
		/*******************************
		**	Statement Mailer
		**
		**	Description:
		**	This method will send out emails for transactional history on a clients account.
		**
		**  @param:		$email_type
		**  @param:     $statement_month
		**  @param:     $statement_year
		**  @param:     $send_to_merchants
		**	@return:	nothing
		**
		**/
		public function send_update( $email_type, $statement_month, $statement_year, $send_to_merchants = null) {

			// Pull into scope database object.
			global $mysqli, $table_prefix;

			// Merchant data storage array
			$arr_all_merchant_data = array();

            // Set month names array
            $the_months = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");

            // Set statement month
            $statement_month = $the_months[intval($statement_month)];

			// Define email template
			switch( strtolower( $email_type ) ) {

				case 'statement':

					$email_subject		= $statement_month.' Statement Available';
					$email_template	= 'statement.html';

				break;
				case 'taxdocuments':

					$email_subject		= 'Tax Documents Available';
					$email_template	= 'tax-document.html';

				break;

				default:

					$email_subject		= $statement_month.' Statement Available';
					$email_template	= 'statement.html';

			}

			// Get Merchants
			$arr_merchants	= $this->obj_model->get_merchants($send_to_merchants);

			// Process merchants
			if( count($arr_merchants) > 0 ) {

                // Loop through merchants
				foreach( $arr_merchants as $arr_merchant ) {

					//Extract member data.
					$arr_merchant_data = $arr_merchant[0];
					$merchant_email		= $arr_merchant[1];
					$merchant_id		= $arr_merchant_data['ppttd_merchant_id'];
					$merchant_name		= (isset($arr_merchant_data['ppttd_merchant_name'])) ? $arr_merchant_data['ppttd_merchant_name'] : null;

					// Send out mailer
                    $this->send_mailer($statement_month, $statement_year, $merchant_id, $merchant_name, $merchant_email, $email_type, $email_template, $email_subject);
				}
				
			}

		}

		/**
		* Send Mailer
		*
		* This method sends out the mailer passed to it.
		*
		* @access	private
		* @param	$merchant_id
		* @param    $merchant_name
		* @param    $merchant_email
		* @param    $email_type
		* @param    $email_template
		* @param    $email_subject
		* @return	$boolean
		*
		*/
		public function send_mailer( $statement_month, $statement_year, $merchant_id, $merchant_name, $merchant_email, $email_type, $email_template, $email_subject ) {
			global $mysqli;

			// Send out  mailer.
			$headers 	= "From: Saltsha <success@saltsha.com>\r\n";
			$headers 	.= "Reply-To: Saltsha <success@saltsha.com>\r\n";
			$headers 	.= "MIME-Version: 1.0\r\n";
			$headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$headers 	.= "X-Mailgun-Native-Send: true\r\n";

			// Set email message
			$message = file_get_contents(dirname(__DIR__).'/template/'.$email_template);

            // Add date to email
            if ($email_type == 'statement') {
                $message = str_replace(array('[PPTTD_MONTH_YEAR]'), array($statement_month), $message);
            }

			// If local host, send to test email(s)
            $test_emails = array('bstump@paypromedia.com', 'bstump@212mediastudios.com', 'bobbie@thestumps.net');
            $hostname = gethostname();
            if (stripos($hostname, 'local') !== false || stripos($hostname, 'sbcglobal') !== false) {
    			// Send out email
    			$merchant_email = trim($merchant_email);
    			if (in_array($merchant_email, $test_emails)) {
    			    echo 'TEST, SENDING '.$email_type.' to '.$merchant_email."...\n";
    			    try {
                    	mail($merchant_email, $email_subject, $message, $headers);
                    } catch (Exception $exc) {
                    	return false;
                    }
                } else {
                    echo 'TEST, NOT SENDING '.$email_type.' to '.$merchant_email."...\n";
                }
            } else {
                try {
                	mail($merchant_email, $email_subject, $message, $headers);
                } catch (Exception $exc) {
                	return false;
                }
            }

			return true;
		}

	}
?>