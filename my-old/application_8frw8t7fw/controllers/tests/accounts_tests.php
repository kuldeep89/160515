<?php


	class Accounts_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
		echo ('<h2>Account Model Test</h2>
			The following test have been ran on the Accounts Model through the controller method account_test/run_test <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('accounts_model');	
			
			////////////////
			// * UNIT TEST: Get Accounts Test, This test tests whether the accounts have been retrieved and returned as objects.
			///////////////
			$test = $this->accounts_model->get_accounts();
			echo $this->unit->run($test, 'is_object', 'Unit Test get_accounts()', 'This test tests whether the accounts have been retrieved and returned as objects.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->accounts_model->get_accounts() == true, 'is_true', 'Component Test get_accounts()', 'This test tests whether the accounts have been retrieved and returned as objects.');

	}
}