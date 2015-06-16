<?php


	class Faq_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
			echo ('<h2>FAQ Model Tests</h2>
			The following test have been ran on the FAQ Model through the controller method faq_tests/run_tests <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('faq_model');
				
			////////////////
			// * UNIT TEST: Add Category Test, This test tests whether a FAQ category has been added to the faq related database.
			///////////////
			$test = $this->faq_model->add_category( 'Test_Cat' );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_category()', 'This test tests whether a FAQ category has been added to the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->add_category( 'Test_Cat' ) == true, 'is_true', 'Component Test add_category()', 'This test tests whether a FAQ category has been added to the faq related database.'); 
			
			////////////////
			// * UNIT TEST: Add Entry Test, This test tests whether a FAQ entry has been added to the faq related database.
			///////////////
		/*	$obj_entry = $this->faq_model->get_entry( 17 );
			$obj_entry->set_faq_entry_id( 102 );
			$test = $this->faq_model->add_entry( $obj_enrty );
			echo $this->unit->run($test, 'is_int', 'Add Entry Test', 'This test tests whether a FAQ entry has been added to the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test ', 'notes');
		*/	
		
			////////////////
			// * UNIT TEST: Add Tag Test, This test tests whether a faq tag has been added to the faq related datbase.
			///////////////
			$test = $this->faq_model->add_tag( 'Test_Tag' );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_tag()', 'This test tests whether a faq tag has been added to the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->add_tag( 'Test_Tag' ) == true, 'is_true', 'Component Test add_tag()', 'This test tests whether a faq tag has been added to the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Categories Test, This test tests whether the faq categories have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_categories( $force_reload = FALSE );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_categories()', 'This test tests whether the faq categories have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_categories( $force_reload = FALSE ) == true, 'is_true', 'Component Test get_categories()', 'This test tests whether the faq categories have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Entries Test, This test tests whether the faq entries have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_entries( $force_reload = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entries()', 'This test tests whether the faq entries have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_entries( $force_reload = FALSE ) == true, 'is_true', 'Component Test get_entries()', 'This test tests whether the faq entries have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Entry Test, This test tests whether a faq entry has been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_entry( 17 );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entry()', 'This test tests whether a faq entry has been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_entry( 17 ) == true, 'is_true', 'Component Test get_entry()', 'This test tests whether a faq entry has been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Entry Tags Test, This test tests whether faq entry tags have been retrieved from the faq related datbase.
			///////////////
			$test = $this->faq_model->get_entry_tags( 17 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_entry_tags()', 'This test tests whether faq entry tags have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_entry_tags()', 'This test tests whether faq entry tags have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Entry Categories Test, This test tests whether the faq entry categories have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_entry_categories( 17 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_entry_categories()', 'This test tests whether the faq entry categories have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_entry_categories( 17 ) == true, 'is_true', 'Component Test get_entry_categories()', 'This test tests whether the faq entry categories have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Categorized Entries Test, This test tests whether the faq categorized entries have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_categorized_entries( array(), $omit_entry_id = FALSE, $limit = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_categorized_entries()', 'This test tests whether the faq categorized entries have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_categorized_entries( array(), $omit_entry_id = FALSE, $limit = FALSE ) == true, 'is_true', 'Component Test get_categorized_entries()', 'This test tests whether the faq categorized entries have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Tagged Entries Test, This test tests whether the faq tagged entries have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_tagged_entries( array(), $omit_entry_id = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_tagged_entries()', 'This test tests whether the faq tagged entries have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_tagged_entries( array(), $omit_entry_id = FALSE ) == true, 'is_true', 'Component Test get_tagged_entries()', 'This test tests whether the faq tagged entries have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Tags Test, This test tests whether the faq tags have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_tags( $force_reload = FALSE );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_tags()', 'This test tests whether the faq tags have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_tags( $force_reload = FALSE ) == true, 'is_true', 'Component Test get_tags()', 'This test tests whether the faq tags have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Get Entries Like Test, This test tests whether the faq entries containing the provided string have been retrieved from the faq related database.
			///////////////
			$test = $this->faq_model->get_entries_like( 'Looking for this' );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entries_like()', 'This test tests whether the faq entries containing the provided string have been retrieved from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->get_entries_like( 'Looking for this' ) == true, 'is_true', 'Component Test get_entries_like()', 'This test tests whether the faq entries containing the provided string have been retrieved from the faq related database.');
			
			////////////////
			// * UNIT TEST: Remove Entry Test, This test tests whether faq entry has been removed from the faq related database.
			///////////////
			$test = $this->faq_model->remove_entry( 102 );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_entry()', 'This test tests whether faq entry has been removed from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->faq_model->remove_entry( 102 ) == !true, 'is_true', 'Component Test remove_entry()', 'notes');
			
			////////////////
			// * UNIT TEST: Update Entry Test, This test tests whether faq entry has been updated from the faq related database.
			///////////////
		/*	$test = $this->faq_model->update_entry( $obj_entry );
			echo $this->unit->run($test, 'is_null', 'Update Entry Test', 'This test tests whether faq entry has been updated from the faq related database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run( == true, 'is_true', 'Component Test ', 'notes');
		*/	
	}
}