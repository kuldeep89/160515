<?php

	/**
	*
	* academy_model Tests
	* Author: Enrique Marrufo
	* Date: 13 September 2013
	*
	**/


	class Academy_tests extends CI_Controller {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
			

			echo ('<h2>Academy Model Tests</h2>
			The following tests have been ran on the Academy Model through the controller method academy_tests/run_tests <br /><br />');
			
		
			$this->load->library('unit_test');
			$this->load->model('academy_model');			
			
			////////////////
			// * UNIT TEST: Add Category Test, This test tests whether a category has been inserted to the database.
			//         $cat_id is set to the integer representing the category that was added by the add_category() test. 
			///////////////
			$cat_id = $this->academy_model->add_category( 'name', $description = FALSE, $color = FALSE, $icon = FALSE ); 
			echo $this->unit->run($cat_id, 'is_int', 'Unit Test add_category()', 'This test tests whether a category has been inserted to the database.');			
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_categories($cat_id) == true, 'is_true', 'Component Test add_category()', 'This test tests whether a category has been added to the database.'); 
			
			////////////////
			// * UNIT TEST: Remove Category Test, This test tests whether a category has been removed from the database.
			//         $cat_id is passed to the remove_category() which was created by the add_category test then removes the category 
			//         from the database as well as it's associations.           
			///////////////  
			$test = $this->academy_model->remove_category( $cat_id );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_category()', 'This test tests whether a category has been removed from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_categories($cat_id) == false, 'is_false', 'Component Test remove_category()', 'This test tests whether a category has been removed from the database.'); 
			
			////////////////
			// * UNIT TEST: Add Entry Test, This test tests whether an entry has been inserted to the database.
			//         $obj_entry is using the get_entry() to pull entry #59 then using the set_academy_entry_id() reassigns the id to #1 to $obj_entry
			///////////////
			$obj_entry = $this->academy_model->get_entry( 59 );
			$obj_entry->set_academy_entry_id( 1 );
			$test = $this->academy_model->add_entry( $obj_entry );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_entry()', 'This test tests whether an entry has been inserted to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_entry()', 'This test tests whether an entry has been inserted to the database.'); 
			
			////////////////
			// * UNIT TEST: Remove Entry Test, This test tests whether an entry has been removed from the database 
			//	       as well as it's associations with categories and tags.
			///////////////
			$test = $this->academy_model->remove_entry( 1 ); //** Needs permission to run from academy_model line 651
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_entry()', 'This test tests whether an entry has been removed from the database.');
			//////////////////
			// * COMPONENT TEST: ** Cant work out this component test **
			/////////////////
			echo $this->unit->run(!$test == false, 'is_false', 'Component Test remove_entry()', 'This test tests whether an entry has been inserted to the database.'); 
			
			////////////////
			// * UNIT TEST: Add Tag Test, This test tests whether a tag has been inserted to the database.
			///////////////
			$tag_id = $this->academy_model->add_tag( 'Name', $description = FALSE );
			echo $this->unit->run($tag_id, 'is_int', 'Unit Test add_tag()', 'This test tests whether a tag has been inserted to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_tags( $tag_id ) == true, 'is_true', 'Component Test add_tag()', 'This test tests whether a tag has been inserted to the database.');
			
			////////////////
			// * UNIT TEST: Remove Tag Test, This test tests whether a tag has been removed from the database.
			///////////////
			$test = $this->academy_model->remove_tag( $tag_id );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_tag()', 'This test tests whether a tag has been removed from the database.'); 
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_tags( $tag_id ) == false, 'is_false', 'Component Test remove_tag()', 'This test tests whether a tag has been removed from the database.');
			
			////////////////
			// * UNIT TEST: Get Categories Test, This test tests whether all categories are returned in an array.
			///////////////
			$arr_categories = $this->academy_model->get_categories( $force_reload = TRUE );
			echo $this->unit->run($arr_categories, 'is_array', 'Unit Test get_categories()', 'This test tests whether all categories are returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_categories( $force_reload = TRUE ) == true, 'is_true', 'Component Test get_categories()', 'This test tests whether all categories are returned in an array.');
			
			////////////////
			// * UNIT TEST: Get Entries Test, This test tests whether multiple entries are returned in an array.
			///////////////
			$test = $this->academy_model->get_entries( $limit = 999, $force_reload = FALSE, $all_entries = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entries', 'This test tests whether entries are returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////	
			echo $this->unit->run($this->academy_model->get_entries( $limit = 999, $force_reload = FALSE, $all_entries = FALSE ) == true, 'is_true', 'Component Test get_entries()', 'This test tests whether entries are returned in an array.');
					
			////////////////
			// * UNIT TEST: Get Single Entry, This test tests whether a single entry object is returned.
			///////////////
			$test = $this->academy_model->get_entry( 25 );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entry()', 'This test tests whether a single entry object is returned.'); 
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_entry( 25 ) == true, 'is_true', 'Component Test get_entry()', 'This test tests whether a single entry object is returned.');
						
			////////////////
			// * UNIT TEST: Get Entries Like, This test tests whether an entries containing the provided string are returned.
			///////////////
			$test = $this->academy_model->get_entries_like( 'Marketing' );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_entries_like()', 'This test tests whether entry objects containing the provided string are returned.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_entries_like( 'Marketing' ) == true, 'is_true', 'Component Test get_entries_like()', 'This test tests whether entry objects containing the provided string are returned.');
						
			////////////////
			// * UNIT TEST: Get Entries Tags Test, This test tests whether entry tags returned in an array.'
			///////////////
			$arr_tags = $this->academy_model->get_entry_tags( 25 );
			echo $this->unit->run($arr_tags, 'is_array', 'Unit Test get_entry_tags()', 'This test tests whether entry tags returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_entry_tags( 25 ) == true, 'is_true', 'Component Test get_entry_tags()', 'This test tests whether entry tags returned in an array.');
						
			////////////////
			// * UNIT TEST: Get Entry Categories, This test tests whether entry categories are returned in an array.
			///////////////
			$test = $this->academy_model->get_entry_categories( 25 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_entry_categories()', 'This test tests whether entry categories are returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_entry_categories( 25 ) == true, 'is_true', 'Component Test get_entry_categories()', 'This test tests whether entry categories are returned in an array.');
						
			////////////////
			// * UNIT TEST: Get Categorized Entries Test, This test tests whether categorized entries are returned in an object.
			///////////////
			$test = $this->academy_model->get_categorized_entries( $arr_categories, $omit_entry_id = FALSE, $limit = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_categorized_entries()', 'This test tests whether categorized entries are returned in an object.'); 
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_categorized_entries( $arr_categories, $omit_entry_id = FALSE, $limit = FALSE ) == true, 'is_true', 'Component Test get_categorized_entries()', 'This test tests whether categorized entries are returned in an object.');
			
			////////////////
			// * UNIT TEST: This method returns an array with the index being the tag ID and the value being how many entries are in that category.
			///////////////
			$test = $this->academy_model->get_num_entries_in_categories( $arr_categories );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_num_entries_in_categories()', 'This test tests whether get num entries in categories returns an array.');
		    //////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_num_entries_in_categories( $arr_categories ) == true, 'is_true', 'Component Test get_num_entries_in_categories()', 'This test tests whether get num entries in categories returns an array.');
			
			////////////////
			// * UNIT TEST: Get Number Entries in Tags Test, This test tests whether get num entries in tags returns an array.
			///////////////
			$test = $this->academy_model->get_num_entries_in_tags( $arr_tags );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_num_entries_in_tags()', 'This test tests whether get num entries in tags returns an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_num_entries_in_tags( $arr_tags ) == true, 'is_true', 'Component Test get_num_entries_in_tags()', 'This test tests whether get num entries in tags returns an array.');
			
			////////////////
			// * UNIT TEST: Get Tagged Entries Test, This test tests whether tagged entires are returned as objects.
			///////////////
			$test = $this->academy_model->get_tagged_entries( $tag_id, $omit_entry_id = FALSE );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_tagged_entries()', 'This test tests whether tagged entires are returned as objects.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_tagged_entries( $tag_id, $omit_entry_id = FALSE ) == true, 'is_true', 'Component Test get_tagged_entries()', 'This test tests whether tagged entires are returned as objects.');
			
			////////////////
			// * UNIT TEST: Get Tags Test, This test tests whether tags are returned as objects.
			///////////////
			$test = $this->academy_model->get_tags( $force_reload = TRUE );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_tags()', 'This test tests whether tags are returned as objects.');			
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_tags( $force_reload = TRUE ) == true, 'is_true', 'Component Test get_tags()', 'This test tests whether tags are returned as objects.');
			
			////////////////
			// * UNIT TEST: Update Category Test, This test tests whether a category has been updated.
			///////////////
			$test = $this->academy_model->update_category(array('academy_entry_category_id'=>102,'name'=>'My Category Name'));
			echo $this->unit->run($test, 'is_null', 'Unit Test update_category()', 'This test tests whether a category has been updated.');
		    //////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_categories( 102 ) == true, 'is_true', 'Component Test update_category()', 'This test tests whether a category has been updated.');
			
			////////////////
			// * UNIT TEST: Update Tag Test, This test tests whether a tag has been updated.
			///////////////
			$test = $this->academy_model->update_tag(array('academy_entry_tag_id'=>1,'name'=>'My New Tag'));
			echo $this->unit->run($test, 'is_null', 'Unit Test update_tag()', 'This test tests whether a tag has been updated.');
		    //////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->academy_model->get_tags( 1 ) == true, 'is_true', 'Component Test update_tag()', 'This test tests whether a tag has been updated.');
			
			////////////////
			// * UNIT TEST: Update Entry Test, This test tests whether an entry has been updated.
			///////////////
			$test = $this->academy_model->update_entry( $obj_entry );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_entry()', 'This test tests whether an entry has been updated.');
			//////////////////
			// * COMPONENT TEST: ** Cant work out this component test **
			/////////////////
			echo $this->unit->run(!$this->academy_model->update_entry( $obj_entry ) == true, 'is_true', 'Component Test update_entry()', 'This test tests whether an entry has been updated.'); 
		}
	}