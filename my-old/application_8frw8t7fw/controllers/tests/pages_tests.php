<?php  


class Pages_tests extends CI_Controller {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
			
			echo ('<h2>Pages Model Tests</h2>
			The following tests have been ran on the Pages Model through the controller method pages_tests/run_tests <br /><br />');
			
		
			$this->load->library('unit_test');
			$this->load->model('pages_model');		
			
			////////////////
			// * UNIT TEST: Add Pages Test, This test tests whether a page has been inserted in to the database.
			///////////////
			$test = $this->pages_model->add_page( array('page_id'=>'102', 'name'=>'test', 'url'=>'test', 'title'=>'test', 'content'=>'test content', 'browser_title'=>'test') );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_page()', 'This test tests whether a page has been inserted in to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_page()', 'This test tests whether a page has been inserted in to the database.');
			
			////////////////
			// * UNIT TEST: Update Page Test, This test tests whether changes to the page have been made and updated.
			///////////////
			$test = $this->pages_model->update_page( 102, array('page_id'=>'103', 'name'=>'test', 'url'=>'test', 'title'=>'test', 'content'=>'test content', 'browser_title'=>'test') );
			echo $this->unit->run($test, 'is_bool', 'Unit Test update_page()', 'This test tests whether changes to the page have been made and updated.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_page()', 'This test tests whether a page has been inserted in to the database.');
			
			////////////////
			// * UNIT TEST: Get Page by ID Test, This test tests whether the correct page is retrieved by the page's requested by ID.
			///////////////
			$test = $this->pages_model->get_page_by_id( 103 );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_page_by_id()', 'This test tests whether the correct page is retrieved by the page\'s requested by ID.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_page_by_id()', 'This test tests whether the correct page is retrieved by the page\'s requested by ID.');
			
			////////////////
			// * UNIT TEST: Get Page by URL Test, This test tests whether the correct page is retrieved by the page requested by URL.
			///////////////
			$test = $this->pages_model->get_page_by_url( 'test' );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_page_by_url()', 'This test tests whether the correct page is retrieved by the page requested by URL.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_page_by_url()', 'This test tests whether the correct page is retrieved by the page requested by URL.');
			
			////////////////
			// * UNIT TEST: Remove Page Test, This test tests whether the correct page is being removed by the page ID.
			///////////////
			$test = $this->pages_model->remove_page( 103 );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_page()', 'This test tests whether the correct page is being removed by the page requested by ID.');
			//////////////////
			// * COMPONENT TEST: ** I had to comment out line 48 from the pages_model **
			/////////////////
			echo $this->unit->run($test == false, 'is_false', 'Component Test remove_page()', 'This test tests whether the correct page is being removed by the page requested by ID.');
			
			////////////////
			// * UNIT TEST: Create Page Reference Test, This test tests whether a page reference has been created and inserted into the database.
			///////////////
			$test = $this->pages_model->create_page_reference( array() );
			echo $this->unit->run($test, 'is_null', 'Unit Test create_page_reference()', 'This test tests whether a page reference has been created and inserted into the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test create_page_reference()', 'This test tests whether a page reference has been created and inserted into the database.');
			
			////////////////
			// * UNIT TEST: Get Pages Like Test, This test tests whether page that contain the search phrase "" have been retrieved from the database.
			///////////////
			$test = $this->pages_model->get_pages_like( 'Look for this.' );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_pages_like()', 'This test tests whether page that contain the search phrase "" have been retrieved from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_pages_like()', 'This test tests whether page that contain the search phrase "" have been retrieved from the database.');
			
			////////////////
			// * UNIT TEST: Get Navigation Test, This test tests whether the correct navigation is retrieved by the navigation requested by ID.
			///////////////
			$test = $this->pages_model->get_navigation( 2 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_navigation()', 'This test tests whether the correct navigation is retrieved by the navigation requested by ID.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_navigation()', 'This test tests whether the correct navigation is retrieved by the navigation requested by ID.');
			
			////////////////
			// * UNIT TEST: Get Navigations Test, This test tests whether the navigations is retrieved. 
			///////////////
			$test = $this->pages_model->get_navigations();
			echo $this->unit->run($test, 'is_array', 'Unit Test get_navigations()', 'This test tests whether the navigations is retrieved.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test ', 'This test tests whether the navigations is retrieved.');
			
			////////////////
			// * UNIT TEST: Get Pages Test, This test tests whether the pages are retrieved and returned.
			///////////////
			$test = $this->pages_model->get_pages();
			echo $this->unit->run($test, 'is_object', 'Unit Test get_pages()', 'This test tests whether the pages are retrieved and returned.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_pages()', 'This test tests whether the pages are retrieved and returned.');
			
			////////////////
			// * UNIT TEST: Update Navigation Test, This test tests whether changes to the navigation have been made and updated.
			///////////////
			$test = $this->pages_model->update_navigation( 2, '{test: test}' );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_navigation()', 'This test tests whether changes to the navigation have been made and updated.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_navigation()', 'This test tests whether changes to the navigation have been made and updated.');
			
		}
	}