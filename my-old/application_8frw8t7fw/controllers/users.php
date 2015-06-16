<?php
	
	/**
	* Members Controller
	* Author: Thomas Melvin
	* Date: 31 July 2013
	* Notes:
	* This controller handles managing users on the 
	* system.
	*
	*/
	
	class Users extends MY_Controller {
		
		/**
		* Add Member
		*
		* This method provides a way for an administrator to add 
		* a member to the website.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function add_member() {
		
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(29);
		
			//**
			// Build Page Array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));

			//**
			// Load View
			//**
			$this->load->view('backend/pages/users/add-member', $arr_page);
		
		}
		
		/**
		* Add Moderator
		*
		* This method provides a way for an administrator to add 
		* a moderator to the website.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function add_moderator() {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(25);
			
			//**
			// Get the Groups.
			//**
			$this->load->model('users_model');
			
			$arr_groups	= $this->users_model->get_groups();
			
			//**
			// Build Page Array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_groups']	= $arr_groups;
		
			//**
			// Load View
			//**
			$this->load->view('backend/pages/users/add-moderator', $arr_page);
		
		}
		
		/*******************************
		**	Get breadcrumbs
		**
		**	Description:
		**	This method receives a request for breadcrumbs
		**  and returns the breadcrumb trail
		**
		**	@param:		void
		**	@return:	breadcrumbs
		**
		**	Author: Bobbie Stump
		**
		**/
		
		public function get_breadcrumbs( $arr_params ) {
	
			$method	= $arr_params['method'];
		
			$breadcrumbs[]	= array('title'=> 'Users', 'url' => site_url('users'));
	
			switch( $method ) {
				case 'moderators':
					$breadcrumbs[]	= array('title'=> 'View Moderators', 'url' => site_url('users/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				case 'add_moderator':
					$breadcrumbs[]	= array('title'=> 'Add Moderator', 'url' => site_url('users/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				case 'members':
					$breadcrumbs[]	= array('title'=> 'View Members', 'url' => site_url('users/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				case 'add_member':
					$breadcrumbs[]	= array('title'=> 'Add Member', 'url' => site_url('users/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				default:
					// $breadcrumbs[]	= array('title'=> $arr_params['item_name'], 'url' => site_url('faq/entry/'.$arr_params['item_id']));
					return $breadcrumbs;
					break;
			}
		}
		
		/**
		* Insert Moderator
		*
		* This method inserts a moderate into the system after
		* it validates a few things.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function insert_moderator() {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(15);
			
			$this->load->model('users_model');
			
			//**
			// Get Expected Data
			//**
			$arr_fields	= array('username', 'password', 'password_confirm', 'first_name', 'last_name', 'company', 'address', 'city', 'state', 'zip', 'phone', 'email', 'arr_groups', 'google_id');
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field]	= $this->input->post($field);
			}
			
			//**
			// Username present?
			//**
			if( empty($arr_data['username']) ) {
			
				$this->notification_lib->add_error('Account must have a username, please enter a username and try again.');
				redirect('users/add-moderator');
				die();
				
			}
			
			//**
			// Is the username unique?
			//**
			if( !$this->users_model->is_username_available($arr_data['username']) ) {
				
				$this->notification_lib->add_error('Username already in use, please try another username.');
				redirect('users/add-moderator');
				die();
				
			}
			
			//**
			// Password Present?
			//**
			if( empty($arr_data['password']) ) {
				
				$this->notification_lib->add_error('Password field was empty, please enter a password');
				redirect('users/add-moderator');
				die();
				
			}
			
			//**
			// Passwords match?
			//**
			if( $arr_data['password'] != $arr_data['password_confirm'] ) {
				
				$this->notification_lib->add_error('Passwords do not match. Please try typing the account\'s password again.');
				redirect('users/add-moderator');
				die();
				
			}
			
			//**
			// Insert User
			//**
			$user_id	= $this->users_model->add_moderator($arr_data);
			
			redirect('users/moderator/'.$user_id);
			
		}
		
		/**
		* Members
		*
		* This method prints out all members that are on the website.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function members() {
		
			//**
			// Get All Members
			//**
			//**
			// Retreive listing of moderators.
			//**
			$this->load->model('users_model');
			$this->db->where('account', '2');
			$obj_collection	= $this->users_model->get_users();
			
			//**
			// Build Page Array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_user_collection']	= $obj_collection;
			
			//**
			// Load View
			//**
			$this->load->view('backend/pages/users/member-listing', $arr_page);
			
		
		}
		
		/*******************************
		**	Moderator
		**
		**	Description:
		**	This method displays the user to the screen.
		**
		**	@param:		user_id
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function moderator( $user_id = FALSE ) {
			
			////////////////
			// Default to Current User
			////////////////
			if( $user_id === FALSE ) {
				$user_id	= $this->current_user->get('id');
			}
			
			////////////////
			// Make sure passed id is numeric.
			////////////////
			if( !is_numeric($user_id) ) {
				
				$this->notification_lib->add_error('Invalid user ID, please try again.');
				redirect('users');
				
			}
			
			////////////////
			// Get User form Database
			////////////////
			$this->load->model('users_model');

			$obj_collection	= $this->users_model->get_users_by_id($user_id);
			$obj_user		= array_pop($obj_collection->get('arr_collection'));
			
			//**
			// Get the Groups.
			//**			
			$arr_groups			= $this->users_model->get_groups();
			$arr_user_groups	= $obj_user->get_groups();
			$arr_selected		= array();
			
			if( count($arr_user_groups) > 0 ) {
				
				foreach( $arr_user_groups as $arr_group ) {
					$arr_selected[]	= $arr_group['group_id']; 
				}
				
			}
			
			////////////////
			// Get User Permissions
			////////////////
			$this->load->model('permissions_model');
			$arr_user_permissions	= $this->permissions_model->get_user_permissions($user_id);
			$obj_all_permissions	= $this->permissions_model->get_all_permissions();
			
			//**
			// Build Page Array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_groups']				= $arr_groups;
			$arr_page['obj_user']				= $obj_user;
			$arr_page['arr_selected_groups']	= $arr_selected;
			$arr_page['arr_user_permissions']	= $arr_user_permissions;
			$arr_page['obj_all_permissions']	= $obj_all_permissions;

			$this->load->view('backend/pages/users/edit-moderator', $arr_page);
			
		}
		
		/**
		* Moderators
		*
		* This function lists out all moderators.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function moderators() {
		
			//**
			// Retreive listing of moderators.
			//**
			$this->load->model('users_model');
			$this->db->where('account', '1');
			$obj_collection	= $this->users_model->get_users();
			
			//**
			// Build Page Array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_user_collection']	= $obj_collection;
			
			//**
			// Load View
			//**
			$this->load->view('backend/pages/users/moderator-listing', $arr_page);
			
		}
		
		/**
		* Remove User
		*
		* This method removes a user from the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	moderator_id
		* @return	void
		*
		*/
		public function remove_moderator($moderator_id) {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(27);
			
			//**
			// Remove User from system.
			//**
			$this->load->model('users_model');
			$this->users_model->remove_user($moderator_id);
			
			$this->notification_lib->add_success('User has been removed successfully.');
			redirect('users/moderators');
			
		}		


		//**
		// OLDER CODE FOLLOWS, REPLACE or MOVE ABOVE THIS LINE.
		//**



		
		/*******************************
		**	Add Member
		**
		**	Description:
		**	This method adds a user to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function add_user() {
			//**
			// Build page array
			//**
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));

			//**
			// Load view
			//**
			$this->load->view('backend/pages/users/add-user', $arr_page);
			
		}
		
		/*******************************
		**	Index
		**
		**	Description:
		**	This method prints out a list of users.
		**
		**	@param:		void
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function index() {
			
			$this->load->model('users_model');
			$obj_collection	= $this->users_model->get_users();
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_user_collection']	= $obj_collection;
			
			$this->load->view('backend/pages/users/user-listing', $arr_page);
			
		}
		
		/*******************************
		**	Update Moderator
		**
		**	Description:
		**	This method will update a user from data passed via a form
		**  POST.
		**
		**	@param:		void
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_moderator( $ajax = FALSE ) {
			

			if( !$ajax ) {
				
				////////////////
				// Authorization
				////////////////
				$this->security_lib->permissions_required(26);
				
			}
			else {
				
				if( !$this->security_lib->accessible(26) ) {
					die('You do not have permissions to update users.');
				}
				
			}
			
			////////////////
			// Define Expected Fields
			////////////////
			$arr_data	= array();
			
			if( $ajax === FALSE ) {
			
				$arr_fields	= array('first_name', 'id', 'last_name', 'company', 'address', 'city', 'state', 'zip', 'phone', 'google_id', 'password', 'password_confirm', 'arr_groups');
				
				foreach( $arr_fields as $field ) {
					
					if( $this->input->post($field) ) {
						$arr_data[$field]	= $this->input->post($field);
					}
					
				}
			
			}
			else {
				$arr_data	= json_decode($this->input->post('json'), true);
			}
			
			////////////////
			// Get the User ID
			////////////////
			$user_id	= $arr_data['id'];
			
			if( $user_id === FALSE ) {
							
				if( $ajax === FALSE ) {
				
					$this->notification_lib->add_error('Invalid user ID, please try again.');
					redirect('users/moderators');
					
				}
				else {
					
					echo 'Invalid user ID, please try again.';
					die();
					
				}
				
			}
			
			////////////////
			// Check Password Update
			////////////////
			if( !empty($arr_data['password']) && $arr_data['password'] != $arr_data['password_confirm'] ) {
				
				$this->notification_lib->add_error('');
				redirect('users/moderators/'.$user_id);
				
			}
			
			////////////////
			// Now Update the Moderator.
			////////////////
			$this->load->model('users_model');
			$this->users_model->update_moderator($user_id, $arr_data);
			
			////////////////
			// Reload Permissions/User Settings
			////////////////
			$obj_user	= $this->users_model->get_user_by_identity($this->current_user->get('username'));
			$this->security_lib->user_login($obj_user);
			
			////////////////
			// Set Notification
			////////////////
			if( $ajax === FALSE ) {
			
				$this->notification_lib->add_success('Moderator\'s settings have been updated successfully.');
				redirect('users/moderator/'.$user_id);
				
			}
			else {
				die('UPDATED');
			}
			
			
		}
		
		/*******************************
		**	Update Member
		**
		**	Description:
		**	This method updates a user.
		**
		**	@param:		void
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_user( $ajax = FALSE ) {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(30);
			
			$arr_user	= array();
			
			if( $ajax === FALSE ) {
			
				$arr_fields	= array('id', 'first_name', 'last_name', 'phone', 'company', 'address', 'city', 'state', 'zip', 'email');
				
				foreach( $arr_fields as $field ) {
				
					$value	= $this->input->post($field);
					
					if( isset($value) ) {
						$arr_user[$field] = $value;
					}
					
				}
				
			}
			else {
				$arr_user = (array) json_decode($this->input->post('json'));
			}
			
			$user_id	= $arr_user['id'];
			
			$this->load->model('users_model');
			$this->users_model->update_user($user_id, $arr_user);
			
			
			if( $ajax === FALSE ) {
				$this->notification_lib->add_success('User updated successfully!');
				redirect('users/user/'.$user_id);
			}
			else {
				echo 'UPDATED';
			}
			
		}

	}
