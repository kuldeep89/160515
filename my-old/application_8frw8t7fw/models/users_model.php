<?php
	
	/**
	* Users Model
	* Author: Thomas Melvin
	* Date: 1 July 2013
	* Notes:
	* This model updates and retrieves users from the database.
	*
	*/
	
	require_once dirname(__FILE__).'/classes/collections/User_collection.php';
	require_once dirname(__FILE__).'/classes/objects/User_class.php';
	
	class Users_model extends CI_Model {
		
		/**
		* Add Moderator
		*
		* This method adds a moderator to the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	$arr_moderator <array>
		* @return	$user_id <int>
		*
		*/
		public function add_moderator($arr_user) {
		
			$arr_groups	= $arr_user['arr_groups'];
			
			//**
			// Check Password
			//**
			if( isset($arr_user['password']) ) {
				$arr_user['password']	= $this->security_lib->hash_password($arr_user['password']);
			}
			
			unset($arr_user['password_confirm']);
			unset($arr_user['arr_groups']);
			
			//**
			// 1 Represents "Moderator".
			//**
			$arr_user['account']	= 1;
		
			$this->db->insert('users', $arr_user);
			
			$user_id	= $this->db->insert_id();
			
			//**
			//	Add pre-populated widgets
			//**
			$this->db->insert('widget_data', array('user_id' => $user_id, 'widget_data' => '{"widget_location":{"column":"1","row":"1"},"widget_type":"5","widget_items":{"rss_feed_url":"http:\/\/blog.saltsha.com\/feed","rss_feed_num_articles":"3"}}'));
			$zip_code = (isset($arr_user['zip'])) ? $arr_user['zip'] : '46580';
			$this->db->insert('widget_data', array('user_id' => $user_id, 'widget_data' => '{"widget_location":{"column":"3","row":"1"},"widget_type":"1","widget_items":{"city_or_zip":"'.$zip_code.'"}}'));
			$this->db->insert('widget_data', array('user_id' => $user_id, 'widget_data' => '{"widget_location":{"column":"2","row":"1"},"widget_type":"3","widget_items":{"academy_entry_category_id":"11"}}'));
			$this->db->insert('widget_data', array('user_id' => $user_id, 'widget_data' => '{"widget_location":{"column":"3","row":"2"},"widget_type":"4","widget_items":{"follow_type":"user","who_to_follow":"saltsha"}}'));
			
			$this->set_users_groups($user_id, $arr_groups);
			
			return $user_id;
			
		}
		
		/**
		* Get Groups
		*
		* This method will return an array listing
		* of all the groups added to the site.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	array
		*
		*/
		public function get_groups() {
		
			//**
			// Select groups from DB and return.
			//**
			$obj_query	= $this->db->get('groups');
			
			if( $obj_query->num_rows() > 0 ) {
				return $obj_query->result_array();
			}
			
			return array();
		
		}
		
		/*******************************
		**	Get Group Members
		**
		**	Description:
		**	This method returns a listing of authors that are
		**  stored in the user table.
		**
		**	@param:		void
		**	@return:	obj_user_collection
		**
		**/
		public function get_group_members( $group_ids ) {
			
			////////////////
			// Make sure it's an array.
			////////////////
			if( !is_array($group_ids) ) {
				$arr_member_listing[]	= $group_ids;
			}
			
			$obj_user_collection	= new User_collection_class();
			
			$this->db->join('users', 'users.id = users_groups.user_id', 'left');
			
			foreach( $arr_member_listing as $group_id ) {
				$this->db->or_where('users_groups.group_id = '.$group_id);
			}
			
			$obj_query	= $this->db->get('users_groups');
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_user_collection->add(new User_class($arr_row));
				}
				
			}
			
			return $obj_user_collection;
			
		}
		
		/*******************************
		**	Get Group Permissions
		**
		**	Description:
		**	This method will return an array listing of the group
		**  permissions associated with the passed group id.
		**
		**	@param:		$group_id
		**	@return:	array
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_group_permissions( $group_id ) {
		
			////////////////
			// Retrieve group permissions
			////////////////
			$this->db->where('group_id', $group_id);
			$obj_query	= $this->db->get('perms_groups_permissions');
			
			return $obj_query->result_array();
		
		}
		
		/*******************************
		**	Get Users
		**
		**	Description:
		**	This method returns a collection of users.
		**
		**	@param:		$limit <int>
		**	@return:	obj_user_collection
		**
		**/
		public function get_users( $limit = null ) {

			$obj_query	= $this->db->get('users', $limit);
			
			$obj_user_collection	= new User_collection_class();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach( $arr_rows as $arr_row ) {
					$obj_user_collection->add(new User_class($arr_row));
				}
				
			}
			
			return $obj_user_collection;
			
		}
		
		/*******************************
		**	Get Users By ID
		**
		**	Description:
		**	This method returns a collection of users that
		**  match the id listing passed to it.
		**
		**	@param:		$arr_ids
		**	@return:	obj_user_collection
		**
		**/
		public function get_users_by_id( $arr_ids ) {
			
			if( !is_array($arr_ids) ) {
				$arr_ids	= array($arr_ids);
			}
			
			foreach( $arr_ids as $id ) {
				$this->db->or_where('users.id', $id);
			}
			
			return $this->get_users();
				
		}
		
		/*******************************
		**	Get User By Identity
		**
		**	Description:
		**	This method returns a user that
		**  matches the identity passed.
		**
		**	@param:		identity
		**  @param:		identity database column
		**	@return:	obj_user:FALSE
		**
		**/
		public function get_user_by_identity( $identity, $identity_column = 'username' ) {
			
			$this->db->where($identity_column, $identity);
			$obj_query	= $this->db->get('users');
			
			if( $obj_query->num_rows() > 0 ) {
				
				$obj_user	= new User_class(array_pop($obj_query->result_array()));
				return $obj_user;
				
			}
			else {
				return FALSE;
			}
			
		}
		
		/**
		* Get Users Groups
		*
		* This method will return an array listing of the groups
		* the passed user_id is associated with.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	user_id
		* @return	array
		*
		*/
		public function get_users_groups($user_id) {
		
			//**
			// Get Group Listing
			//**
			$this->db->where('user_id', $user_id);
			$obj_query	= $this->db->get('users_groups');
			
			return $obj_query->result_array();
		
		}
		
		/**
		* Is Username Unique
		*
		* This method checks to see if a username is unique or not.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	username <string>
		* @return	boolean
		*
		*/
		public function is_username_available($username) {
		
			//**
			// Check to see if the username has already been used.
			//**
			$this->db->where('username', $username);
			$obj_query	= $this->db->get('users');
			
			if( $obj_query->num_rows() > 0 ) {
				return FALSE;
			}
			
			return TRUE;
		
		}
		
		/**
		* Remove User
		*
		* This method removes a user from the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	user_id
		* @return	void
		*
		*/
		public function remove_user($user_id) {
		
			//**
			// Remove User from Groups
			//**
			$this->db->where('user_id', $user_id);
			$this->db->delete('users_groups');
			
			$this->db->where('id', $user_id);
			$this->db->delete('users');
			
			$this->db->where('user_id', $user_id);
			$this->db->delete('widget_data');		
		}
		
		/**
		* Set User's Groups
		*
		* This method sets the users groups.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	$user_id
		* @param    $arr_groups
		* @return	void
		*
		*/
		public function set_users_groups($user_id, $arr_groups) {
			
			////////////////
			// First Check to See if there is any change in the 
			////////////////
			$arr_curr_groups	= $this->get_users_groups($user_id);
			$update_required	= FALSE;
			

			////////////////
			// Check if update is required
			////////////////
			foreach( $arr_curr_groups as $arr_group ) {
			
				if( !in_array($arr_group['group_id'], $arr_groups) ) {
					$update_required	= TRUE;
				}
				
			}

			if( !$update_required ) {
				return 0;
			}
			
			//**
			// Remove existing groups.
			//**
			$this->db->where('user_id', $user_id);
			$this->db->delete('users_groups');
			
			if( is_array($arr_groups) && count($arr_groups) > 0 ) {
				
				foreach( $arr_groups as $group_id ) {
					$this->db->insert('users_groups', array('user_id' => $user_id, 'group_id' => $group_id));
				}
				
			}
			
			////////////////
			// Once the user's groups have been updated,
			// it's time to update the users permissions.
			////////////////
			$this->update_users_groups_permissions($user_id, $arr_groups);
			
		}
		
		/*******************************
		**	Set Profile Image
		**
		**	Description:
		**	This method set's the profile image id to the user
		**  that was passed with it.
		**
		**	@param:		$user_id
		**  @param:		$profile_image
		**	@return:	void
		**
		**/
		public function set_profile_image( $user_id, $profile_image ) {			
			// Set new profile image
			$this->db->where('id = '.$user_id);
			$this->db->update('users', array('profile_image' => $profile_image));
			
		}
		
		/*******************************
		**	Get Current Profile Image
		**
		**	Description:
		**	This method set's the profile image id to the user
		**  that was passed with it.
		**
		**	@param:		$user_id
		**  @param:		$profile_image
		**	@return:	void
		**
		**/
		public function get_profile_image( $user_id ) {			
			// Set new profile image
			$this->db->where('id', $user_id);
			$user_data = $this->db->get('users');
			$user = $user_data->result_array();
			return $user[0]['profile_image'];
			
		}
		
		/*******************************
		**	Update Moderator
		**
		**	Description:
		**	This method updates the moderator fields.
		**
		**	@param:		$user_id
		**  @param:		$arr_data
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_moderator( $user_id, $arr_data ) {
			
			////////////////
			// Make sure ID's not set.
			////////////////
			unset($arr_data['id']);
			
			////////////////
			// Extract Groups & Update
			////////////////
			if( isset($arr_data['arr_groups']) ) {
			
				$arr_groups	= $arr_data['arr_groups'];
				$this->set_users_groups($user_id, $arr_groups);
				unset($arr_data['arr_groups']);
				
			}
			
			////////////////
			// Check for Password
			////////////////
			if( !empty($arr_data['password']) && $arr_data['password'] == $arr_data['password_confirm'] ) {
				$arr_data['password']	= $this->security_lib->hash_password($arr_data['password']);
			}
			else {
				unset($arr_data['password']);
			}
			
			////////////////
			// We never want "password_confirm" trying to go
			// into the database.
			////////////////
			unset($arr_data['password_confirm']);
			
			////////////////
			// Update Users Table
			////////////////
			$this->db->where('id', $user_id);
			$this->db->update('users', $arr_data);
			
		}
		
		/*******************************
		**	Update Users Groups Permissions
		**
		**	Description:
		**	This method will update a users permissions based on
		**  what group permissions are contained in the passed group array.
		**
		**	@param:		user_id
		**  @param:     arr_groups
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_users_groups_permissions($user_id, $arr_groups) {
			
			$this->db->where('user_id', $user_id);
			$this->db->delete('perms_users_permissions');
			
			////////////////
			// Get Permissions for the passed groups.
			////////////////
			$arr_permissions	= array();
			
			if( count($arr_groups) > 0 ) {
				
				foreach( $arr_groups as $group_id ) {
					
					$arr_permissions	= $this->get_group_permissions($group_id);
					
					foreach( $arr_permissions as $arr_permission ) {
						$this->db->insert('perms_users_permissions', array('user_id' => $user_id, 'permission_id' => $arr_permission['permission_id'], 'value' => 1));
					}
					
				}
				
			}
		
		}
		
	}
	
?>