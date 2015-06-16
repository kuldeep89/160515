<?php  


class Myaccount_tests extends CI_Controller {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
			
			echo ('<h2>My Account Model Tests</h2>
			The following tests have been ran on the My Account Model through the controller method myaccount_tests/run_tests <br /><br />');
			
		
			$this->load->library('unit_test');
			$this->load->model('myaccount_model');		
			
			////////////////
			// * UNIT TEST: Test, This test tests whether a spisific users data has been retrived from the database.
			///////////////
			$obj_user = $this->myaccount_model->get_user_data( 1 );
			echo $this->unit->run($obj_user, 'is_object', 'Unit Test get_user_data()', 'This test tests whether a specific users data has been retrieved from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->myaccount_model->get_user_data( 1 ) == true, 'is_true', 'Component Test get_user_data()', 'This test tests whether a specific users data has been retrieved from the database.');
			
			////////////////
			// * UNIT TEST: Test, This test tests whether .
			///////////////
	/*		$first_name = $obj_user->get('username');
			$obj_user->set('username', 'test');
			$test = $this->myaccount_model->update_user( $obj_user );
			$obj_user = $this->myaccount_model->get_user_data( 1 );
			echo $this->unit->run($obj_user->get('username'), 'is_null', 'Unit Test update_user()', 'This test tests whether the specific user has been updated.');
			$obj_user->set('username', $first_name);
			$this->myaccount_model->update_user( $obj_user );
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->myaccount_model->update_user( $obj_user ) == true, 'is_true', 'Component Test update_user()', 'This test tests whether the specific user has been updated.');
	*/
	}
}
