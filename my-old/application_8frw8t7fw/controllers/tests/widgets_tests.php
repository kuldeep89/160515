<?php


	class Widgets_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
			echo ('<h2>Widgets Tests</h2>
			The following test have been ran on the Widgets Model through the controller method widgets_tests/run_tests <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('widgets_model');

			////////////////
			// * TEST: Get Category Test, This test tests whether a category has been returned depending on the cat_id.
			///////////////
			$test = $this->widgets_model->get_category( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_category()', 'This test tests whether a category has been returned depending on the cat_id.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_category()', 'This test tests whether a category has been returned depending on the cat_id.');
			
			////////////////
			// * TEST: Get Quotes Test, This test tests whether a supplied quote codes are queried and returned.
			///////////////
			$test = $this->widgets_model->get_quotes( array('Microsoft'=>'MSFT') );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_quotes()', 'This test tests whether a supplied quote codes are queried and returned.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_quotes()', 'This test tests whether a supplied quote codes are queried and returned.');
			
			////////////////
			// * TEST: Get Tweets Test, This test tests whether a tweets have been returned in an array.
			///////////////
			$test = $this->widgets_model->get_tweets( array('follow_type'=>'hashtag', 'who_to_follow'=>'MyBizPerks') );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_tweets()', 'This test tests whether a tweets have been returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run(!$test == true, 'is_true', 'Component Test get_tweets()', 'This test tests whether a tweets have been returned in an array.');

		}		
	}