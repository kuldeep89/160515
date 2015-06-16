<?php
	
	/**
	* Users Library
	* Author: Thomas Melvin
	* Date: 1 July 2013
	* Notes:
	* This library contains methods associated
	* with managing users on the site.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/models/classes/collections/User_collection.php';
	require_once dirname(dirname(__FILE__)).'/models/classes/objects/User_class.php';
	
	class Users_lib {
		
		protected $obj_user_collection;
		protected $arr_retrieve_users;
		protected $obj_current_user;
		
		////////////////
		// Generated Data Members
		////////////////
		protected $CI;
		
		public function __construct() {

			$this->CI	=& get_instance();
			$this->obj_user_collection	= new User_collection_class();

		}
		
		/*******************************
		**	Add Collection
		**
		**	Description:
		**	This method adds a collection of users
		**  to be stored in this collection.
		**
		**	@param:		obj_user_collection
		**	@return:	void
		**
		**/
		public function add_collection( $obj_collection ) {
			
			////////////////
			// Get Members and then add them to this collection.
			////////////////
			$arr_collection	 = $obj_collection->get('arr_collection');
			
			foreach( $arr_collection as $obj_user ) {
				$this->obj_user_collection->add($obj_user);
			}
			
		}
		
		/*******************************
		**	Add User to Retrieve
		**
		**	Description:
		**	This
		**
		**	@param:		$user_id
		**	@return:	void
		**
		**/
		public function add_user_to_retrieve( $user_id ) {
		

			$arr_collection				= $this->obj_user_collection->get('arr_collection');
			
			////////////////
			// Only add id if it's not alreayd loaded.
			////////////////
			if( !isset($arr_collection[$user_id]) ) {
				$this->arr_retrieve_users[$user_id]	= $user_id;
			}
			
		}
		
		/*******************************
		**	Reload Current User
		**
		**	Description:
		**	This method reloads the user from the database.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function reload_current_user() {
			
			////////////////
			// Assume User is not loaded.
			////////////////
			$user_id	= $this->CI->current_user->get('id');
			
			$this->CI->load->model('users_model');
			
			$obj_collection	= $this->CI->users_model->get_users_by_id($user_id);
			$obj_user		= array_pop($obj_collection->get('arr_collection'));
			
			$this->set_current_user($obj_user);
			
		}
		
		/*******************************
		**	Retrieve Users
		**
		**	Description:
		**	This method retrieves users that have been added
		**  by the add_user_to_retrieve method from the database.
		**
		**	@param:		void
		**	@return:	obj_user_collection
		**
		**/
		public function retrieve_users() {
			
			$obj_user_collection	= null;
			$this->CI->load->model('users_model');
			
			if( isset($this->arr_retrieve_users) && count($this->arr_retrieve_users) > 0 ) {
			
				$obj_user_collection	= $this->CI->users_model->get_users_by_id($this->arr_retrieve_users);
				$this->add_collection($obj_user_collection);
				
			}
			
			return $this->obj_user_collection;
			
		}
		
		/*******************************
		**	Set Current User
		**
		**	Description:
		**	This method will set the current user,
		**  either from session (when $obj_user === FALSE) or by
		**  from the passed obj_user.
		**
		**	@param:		$obj_user:FALSE
		**	@return:	void
		**
		**/
		public function set_current_user( $obj_user = FALSE ) {
			
			$arr_fields	= array('id', 'ip_address', 'username', 'email', 'created_on', 'last_login', 'active', 'first_name', 'last_name', 'company', 'phone', 'profile_image', 'name', 'size', 'height', 'width', 'type', 'date_created');
			
			if( $obj_user !== FALSE ) {
				
				foreach( $arr_fields as $field ) {
					$this->CI->session->set_userdata($field, $obj_user->get($field));
				}
				
				$this->obj_current_user	= $obj_user;
				
			}
			else {
				
				$this->obj_current_user	= new User_class();
				
				foreach( $arr_fields as $field ) {
					$this->obj_current_user->set($field, $this->CI->session->userdata($field));
				}
				
			}
			
		}
		
		/*******************************
		**	Current User
		**
		**	Description:
		**	This is the currently logged in user.
		**
		**	@param:		void
		**	@return:	obj_user
		**
		**/
		public function current_user() {
			return $this->obj_current_user;
		}
		
		/*******************************
		**	User
		**
		**	Description:
		**	This method returns the user object of the passed 
		**  user ID, assuming it's been loaded.
		**
		**	@param:		$user_id <int>
		**	@return:	obj_user
		**
		**/
		public function user( $user_id ) {
			return $this->obj_user_collection->get_user($user_id);
		}
		
		/**
		* Get Users
		*
		* This method returns obj_user_collection
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	obj_collection
		*
		*/
		public function get_users() {
		
			return $this->obj_user_collection;
		
		}
				
	}
	
?>