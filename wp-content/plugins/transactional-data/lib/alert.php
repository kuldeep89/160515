<?php
	
	require_once dirname(__FILE__).'/Mailgun/Mailgun.php';
	require_once dirname(__FILE__).'/alert-model.php';
	
	/**
	 * Required to set timezone. Cron jobs are based on FT Wayne Timezone.
	 */
    date_default_timezone_set('America/Fort_Wayne');
	
	/**
	 * Class to send out transactional emails.
	 */
	class batch_alert {
		
		//Class model.
		private $obj_model;
		
		// Default Constructor
		public function __construct() {
			$this->obj_model	= new alert_model();
		}
		
		/*******************************
		**	Transactional Mailer
		**
		**	Description:
		**	This method will send out emails for transactional history on a clients account.
		**
		**  @param:		$timeframe
		**  @param:		$type (daily, weekly, monthly)
		**  @param:     $send_to_merchants
		**	@return:	status
		**
		**/
		
		public function test($test = null) {
			echo $test;
		}
		
	}
?>