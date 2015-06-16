<?php


	class Permissions_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
			echo ('<h2>Permissions Tests</h2>
			The following test have been ran on the Permissions Model through the controller method permissions_tests/run_tests <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('permissions_model');

			////////////////
			// * UNIT TEST: Add Component Test, This test tests whether a a component as been added to the database.
			///////////////
			$test = $this->permissions_model->add_component( array('component_id'=>'102') );
			echo $this->unit->run($test, 'is_null', 'Unit Test add_component()', 'This test tests whether a a component as been added to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_component()', 'This test tests whether a a component as been added to the database.');

			////////////////
			// * UNIT TEST: Update Component Test, This test tests whether the component has been updated.
			///////////////
			$test = $this->permissions_model->update_component( 102, array('component_id'=>'103') );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_component()', 'This test tests whether the component has been updated.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_component()', 'This test tests whether the component has been updated.');
			
			////////////////
			// * UNIT TEST: Remove Component Test, This test tests whether the added and then updated component was removed from the database.
			///////////////
			$test = $this->permissions_model->remove_component( 103 );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_component()', 'This test tests whether the added and then updated component was removed from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test remove_component()', 'This test tests whether the added and then updated component was removed from the database.');
				
			////////////////
			// * UNIT TEST: Add Module Test, This test tests whether a module had been added to the database.
			///////////////
			$test = $this->permissions_model->add_module( array('module_id'=>'102') );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_module()', 'This test tests whether a module had been added to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_module()', 'This test tests whether a module had been added to the database.');
			
			////////////////
			// * UNIT TEST: Update Module Test, This test tests whether a module had been updated to the database.
			///////////////
			$test = $this->permissions_model->update_module( 102, array('module_id'=>'103') );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_module()', 'This test tests whether a module had been updated to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_module()', 'This test tests whether a module had been updated to the database.');
			
			////////////////
			// * UNIT TEST: Remove Module Test, This test tests whether a module had been removed to the database.
			///////////////
			$test = $this->permissions_model->remove_module( 103 );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_module()', 'This test tests whether a module had been removed to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test remove_module()', 'This test tests whether a module had been removed to the database.');
			
			////////////////
			// * UNIT TEST: Add Permissions Test, This test tests whether a permission as been added to the database.
			///////////////
			$test = $this->permissions_model->add_permission( array('permission_id'=>'102') );
			echo $this->unit->run($test, 'is_null', 'Unit Test add_permission()', 'This test tests whether a permission as been added to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_permission()', 'This test tests whether a permission as been added to the database.');
			
			////////////////
			// * UNIT TEST: Update Permission Test, This test tests whether a permission as been updated to the database.
			///////////////
			$test = $this->permissions_model->update_permission( 102, array('permission_id'=>'103') );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_permission()', 'This test tests whether a permission as been updated to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_permission()', 'This test tests whether a permission as been updated to the database.');
			
			////////////////
			// * UNIT TEST: Remove Permission Test, This test tests whether a permission as been removed to the database.
			///////////////
			$test = $this->permissions_model->remove_permission( 103 );
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_permission()', 'This test tests whether a permission as been removed to the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test remove_permission()', 'This test tests whether a permission as been removed to the database.');
			
			////////////////
			// * UNIT TEST: Get All Permissions Test, This test tests whether all permission have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_all_permissions();
			echo $this->unit->run($test, 'is_object', 'Unit Test get_all_permissions()', 'This test tests whether all permission have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_all_permissions()', 'This test tests whether all permission have been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Component Test, This test tests whether a component has been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_component( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_component()', 'This test tests whether a component has been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_component()', 'This test tests whether a component has been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Components Test, This test tests whether a components has been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_components( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_components()', 'This test tests whether a components has been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_components()', 'This test tests whether a components has been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Group Test, This test tests whether a group has been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_group( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_group()', 'This test tests whether a group has been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_group()', 'This test tests whether a group has been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Group Permissions Test, This test tests whether a group of permissions has been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_group_permissions(1);
			echo $this->unit->run($test, 'is_array', 'Unit Test get_group_permissions()', 'This test tests whether a group of permissions has been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_group_permissions()', 'This test tests whether a group of permissions has been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get User Permissions Test, This test tests whether a user permissions have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_user_permissions( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_user_permissions()', 'This test tests whether a user permissions have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_user_permissions()', 'This test tests whether a user permissions have been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Module Test, This test tests whether a module have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_module( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_module()', 'This test tests whether a module have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_module()', 'This test tests whether a module have been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Modules Test, This test tests whether a modules have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_modules();
			echo $this->unit->run($test, 'is_array', 'Unit Test get_modules()', 'This test tests whether a modules have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_modules()', 'This test tests whether a modules have been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Permissions Test, This test tests whether permissions have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_permissions( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_permissions()', 'This test tests whether permissions have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_permissions()', 'This test tests whether permissions have been pulled from the database.');
			
			////////////////
			// * UNIT TEST: Get Permission Test, This test tests whether permission have been pulled from the database.
			///////////////
			$test = $this->permissions_model->get_permission(1);
			echo $this->unit->run($test, 'is_array', 'Unit Test get_permission()', 'This test tests whether permission have been pulled from the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_permission()', 'This test tests whether permission have been pulled from the database.');
						
			////////////////
			// * UNIT TEST: Update Group Permissions Test, This test tests whether a group of permissions have been updated in the database.
			///////////////
		/*	$test = $this->permissions_model->update_group_permissions( 1, array() );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_group_permissions()', 'This test tests whether a group of permissions have been updated in the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_group_permissions()', 'This test tests whether a group of permissions have been updated in the database.');
			
			////////////////
			// * UNIT TEST: Update User Permissions Test, This test tests whether user permissions have been updated in the database.
			///////////////
			$test = $this->permissions_model->update_user_permissions( 1, array() );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_user_permissions()', 'This test tests whether user permissions have been updated in the database.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_user_permissions()', 'This test tests whether user permissions have been updated in the database.');
			*/
		}   
		
	}