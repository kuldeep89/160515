<?php


	class Users_tests extends CI_Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
		
			echo ('<h2>Users Tests</h2>
			The following test have been ran on the Users Model through the controller method users_tests/run_tests <br /><br />');
			
			$this->load->library('unit_test');
			$this->load->model('users_model');

			////////////////
			// * UNIT TEST: Add Moderator Test, This test tests whether a user has been added as a moderator.
			///////////////
		/*	$test = $this->users_model->add_moderator();
			echo $this->unit->run($test, 'is_int', 'Unit Test add_moderator()', 'This test tests whether a user has been added as a moderator.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test add_moderator()', 'This test tests whether a user has been added as a moderator.');

		*/
			////////////////
			// * UNIT TEST: Get Groups Test, This test tests whether a groups are returned as an array.
			///////////////
			$test = $this->users_model->get_groups();
			echo $this->unit->run($test, 'is_array', 'Unit Test get_groups()', 'This test tests whether a groups are returned as an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_groups()', 'This test tests whether a groups are returned as an array.');
			
			////////////////
			// * UNIT TEST: Get Groups Members Test, This test tests whether a groups are returned as an array by group ID.
			///////////////
			$test = $this->users_model->get_group_members( 2 );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_group_members()', 'This test tests whether a groups are returned as an array by group ID.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_group_members()', 'This test tests whether a groups are returned as an array by group ID.');
			
			////////////////
			// * UNIT TEST: Get Group Permissions Test, This test tests whether a group permissions have been returned in an array. 
			///////////////
			$test = $this->users_model->get_group_permissions( 3 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_group_permissions()', 'This test tests whether a group permissions have been returned in an array.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_group_permissions()', 'This test tests whether a group permissions have been returned in an array.');
			
			////////////////
			// * UNIT TEST: Get Users Test, This test tests whether a users have been returned in objects.
			///////////////
			$test = $this->users_model->get_users( $limit = null );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_users()', 'This test tests whether a users have been returned in objects.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_users()', 'This test tests whether a users have been returned in objects.');
			
			////////////////
			// * UNIT TEST: Get Users by ID Test, This test tests whether a users are retuned by ID in to objects.
			///////////////
			$test = $this->users_model->get_users_by_id( 1 );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_users_by_id()', 'This test tests whether a users are retuned by ID in to objects.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_users_by_id()', 'This test tests whether a users are retuned by ID in to objects.');
			
			////////////////
			// * UNIT TEST: Get User by Identity Test, This test tests whether a users are returned by identity.
			///////////////
			$test = $this->users_model->get_user_by_identity( 'admin', $identity_column = 'username' );
			echo $this->unit->run($test, 'is_object', 'Unit Test get_user_by_identity()', 'This test tests whether a users are returned by identity.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_user_by_identity()', 'This test tests whether a users are returned by identity.');
			
			////////////////
			// * UNIT TEST: Is Username Available Test, This test tests whether selected usernames are free.
			///////////////
			$test = $this->users_model->is_username_available( 'admin' );
			echo $this->unit->run($test, 'is_bool', 'Unit Test is_username_available()', 'This test tests whether selected usernames are free.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run(!$test == true, 'is_true', 'Component Test is_username_available()', 'This test tests whether selected usernames are free.');
			
			////////////////
			// * UNIT TEST: Get Users Groups Test, This test tests whether user groups are returned by ID.
			///////////////
			$test = $this->users_model->get_users_groups( 1 );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_users_groups()', 'This test tests whether user groups are returned by ID.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test get_users_groups()', 'This test tests whether user groups are returned by ID.');
			
			////////////////
			// * UNIT TEST: Remove User Test, This test tests whether a user has been removed by user ID.
			///////////////
			$test = $this->users_model->remove_user(00);
			echo $this->unit->run($test, 'is_null', 'Unit Test remove_user()', 'This test tests whether a user has been removed by user ID.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test remove_user()', 'This test tests whether a user has been removed by user ID.');
			
			////////////////
			// * UNIT TEST: Set Users Groups Test, This test tests whether a users groups has been set.
			///////////////
			$test = $this->users_model->set_users_groups(1, array());
			echo $this->unit->run($test, 'is_int', 'Unit Test set_users_groups()', 'This test tests whether a users groups have been set.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test set_users_groups()', 'This test tests whether a users groups have been set.');
			
			////////////////
			// * UNIT TEST: Set Profile Images Test, This test tests whether a profile image has been set.
			///////////////
			$test = $this->users_model->set_profile_image( 1, 111 );
			echo $this->unit->run($test, 'is_null', 'Unit Test set_profile_image()', 'This test tests whether a profile image has been set.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test set_profile_image()', 'This test tests whether a profile image has been set.');
			
			////////////////
			// * UNIT TEST: Update Moderator Test, This test tests whether moderator has been updated.
			///////////////
		/*	$test = $this->users_model->update_moderator( 1, array() );
			echo $this->unit->run($test, 'is_null', 'Unit Test update_moderator()', 'This test tests whether moderator has been updated.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_moderator()', 'This test tests whether moderator has been updated.');
		*/	
			////////////////
			// * UNIT TEST: Update Users Groups Permissions Test, This test tests whether a users groups permission has been updated.
			///////////////
			$test = $this->users_model->update_users_groups_permissions(1, array());
			echo $this->unit->run($test, 'is_null', 'Unit Test update_users_groups_permissions()', 'This test tests whether a users groups permission has been updated.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($test == true, 'is_true', 'Component Test update_users_groups_permissions()', 'This test tests whether a users groups permission has been updated.');
		}
	}

