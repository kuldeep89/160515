<?php
	
	/**
	* Permissions Model
	* Author: Thomas Melvin
	* Date: 9 August 2013
	* Notes:
	* This model updates and retrieves permissions from the database.
	*
	*/
	
	require_once dirname(__FILE__).'/classes/collections/Component_collection.php';
	require_once dirname(__FILE__).'/classes/collections/Module_collection.php';
	require_once dirname(__FILE__).'/classes/objects/Module_class.php';
	require_once dirname(__FILE__).'/classes/objects/Component_class.php';
	
	class Permissions_model extends CI_Model {
		
		/*******************************
		**	Add Component
		**
		**	Description:
		**	This method adds a component to the database.
		**
		**	@param:		$arr_component
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function add_component( $arr_component ) {
		
			////////////////
			// Insert into database.
			////////////////
			$this->db->insert('perms_components', $arr_component);
		
		}
		
		/**
		* Add Module
		*
		* This method adds a permissions module to the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	arr_module
		* @return	module_id <int>
		*
		*/
		public function add_module($arr_module) {
		
			//**
			// Add module to system.
			//**
			$this->db->insert('perms_modules', $arr_module);
			
			return $this->db->insert_id();
		
		}
		
		/*******************************
		**	Add Permission
		**
		**	Description:
		**	This method adds a permission to the system.
		**
		**	@param:		$arr_permission
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function add_permission( $arr_permission ) {
		
			////////////////
			// Insert permission into the database
			////////////////
			$this->db->insert('perms_permissions', $arr_permission);
		
		}
		
		/*******************************
		**	Get All Permission
		**
		**	Description:
		**	This method returns all the permissions in
		**  the database.
		**
		**	@param:		void
		**	@return:	obj_collection
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_all_permissions() {
		
			////////////////
			// Retrieve all permissions
			////////////////
			$obj_modules	= new Module_collection_class();
			
			$this->db->join('perms_components', 'perms_components.module_id = perms_modules.module_id', 'left');
			$this->db->join('perms_permissions', 'perms_permissions.component_id = perms_components.component_id', 'left');
			
			$obj_query		= $this->db->get('perms_modules');
			
			if( $obj_query->num_rows() > 0 ) {
				$obj_modules->add_array($obj_query->result_array());
			}
			
			return $obj_modules;
		
		}
		
		/**
		* Get Component
		*
		* This method will return a component that matches
		* the component_id passed to it.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	component_id
		* @return	void
		*
		*/
		public function get_component( $component_id ) {
		
			//**
			// Return Component
			//**
			$this->db->where('component_id', $component_id);
			$arr_component	= $this->db->get('perms_components')->result_array();
			
			return array_pop($arr_component);
		
		}
		
		/*******************************
		**	Get Components
		**
		**	Description:
		**	This method will retrieve components
		**  from the database that are associated with
		**  the module passed to it.
		**
		**	@param:		module_id <int>
		**	@return:	array
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_components( $module_id ) {
			
			$this->db->where('module_id', $module_id);
			$obj_query	= $this->db->get('perms_components');
			
			return $obj_query->result_array();
			
		}
		
		/**
		* Get Group
		*
		* This method returns a group based on the passed group_id
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	group_id
		* @return	array
		*
		*/
		public function get_group( $group_id ) {
		
			$this->db->where('id', $group_id);
			$arr_group	= $this->db->get('groups')->result_array();
			
			return array_pop($arr_group);
		
		}
		
		/*******************************
		**	Get Group Permissions
		**
		**	Description:
		**	This method will return an array listing of the group permissions.
		**
		**	@param:		$group_id
		**	@return:	array
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_group_permissions($group_id) {
		
			$this->db->where('perms_groups_permissions.group_id', $group_id);
			$obj_query		= $this->db->get('perms_groups_permissions');
			
			return $obj_query->result_array();
		
		}
		
		/**
		* Get Permissions
		*
		* This method returns a collection of permissions from the database.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	user_id
		* @return	array
		*
		*/
		public function get_user_permissions( $user_id ) {
		
			
			$this->db->where('perms_users_permissions.user_id', $user_id);
			$obj_query		= $this->db->get('perms_users_permissions');
			
			return $obj_query->result_array();
		
		}
		
		/**
		* Get Module
		*
		* This method returns a module that matches the passed id.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	module_id
		* @return	array
		*
		*/
		public function get_module( $module_id ) {
		
			$this->db->where('module_id', $module_id);
			$arr_module	= $this->db->get('perms_modules')->result_array();
			
			return array_pop($arr_module);
		
		}
		
		/**
		* Get Modules
		*
		* Return modules from database.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	array
		*
		*/
		public function get_modules() {
		
			$obj_query	= $this->db->get('perms_modules');
			return $obj_query->result_array();
			
		}
		
		/**
		* Get Permissions
		*
		* This method returns a listing of permissions that are 
		* associated with the component passed to it.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	$component_id <int>
		* @return	obj_collection
		*
		*/
		public function get_permissions( $component_id ) {
		
			$this->db->join('perms_components', 'perms_components.component_id = perms_permissions.component_id', 'left');
			$this->db->where('perms_permissions.component_id', $component_id);
			$obj_query	= $this->db->get('perms_permissions');
			
			return $obj_query->result_array();
			
		}
		
		/**
		* Get Permission
		*
		* This method returns the passed permissions.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	permission_id
		* @return	array_permission
		*
		*/
		public function get_permission($permission_id) {
		
			$this->db->where('permission_id', $permission_id);
			$arr_permission	= $this->db->get('perms_permissions')->result_array();
			
			return array_pop($arr_permission);
		
		}
		
		/**
		* Rebuild Members Permissions In Group
		*
		* It rebuilds the members permissions that are associated with the passed group.
		* This method is called when a group-level permission change has taken place, and now
		* we need to rebuild all of the members permissions and update the database.
		*
		* Psuedo Code:
		* 1. Get Users in Group
		* 2. Loop through users and get all the groups that user is in.
		* 3. Get the permissions from all of the groups the user is in.
		* 4. Collect those permissions in $arr_updated_permissions
		* 5. Remove user's existing permissions that are not flagged with override
		* 6. Get the user's remaining permissions (flaggged with override)
		* 7. Remove the permissions flagged with override from the $arr_updated_permissions
		* 8. Add $arr_updated_permissions to the user's permissions.
		* 9. This continues for every user that is in the group associated with the passed group id.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	group_id
		* @return	void
		*
		*/
		public function rebuild_members_permissions_in_group( $group_id ) {
		
			//**
			// Get a listing of users in the passed group.
			//**
			$this->load->model('users_model');
			$obj_user_collection	= $this->users_model->get_group_members($group_id);
			
			//Loop through and update each user.
			foreach( $obj_user_collection->get('arr_collection') as $obj_user ) {
				
				//Store updated permissions.
				$arr_updated_permissions	= array();
					
				//Get user's groups.
				$arr_groups	= $this->users_model->get_users_groups($obj_user->get('id'));
				
				//Get groups permissions.
				foreach( $arr_groups as $arr_group ) {
					
					$arr_permissions	= $this->users_model->get_group_permissions($arr_group['group_id']);
					
					//**
					// Build a listing of group permissions.
					//**
					foreach( $arr_permissions as $arr_permission ) {
						$arr_updated_permissions[$arr_permission['permission_id']]	= $arr_permission['value'];
					}
					
				}
				
				//**
				// Now remove the user's current permission (excluding the ones that are marked with overwrite.
				//**
				$this->db->where('user_id', $obj_user->get('id'));
				$this->db->where('override', 0);
				$this->db->delete('perms_users_permissions');
				
				//**
				// Now retrieve the remaining user permissions.
				//**
				$arr_user_permissions	= $this->get_user_permissions($obj_user->get('id'));
				
				//**
				// Loop through these permissions and remove them from $arr_updated_permissions
				// because we do not want these permissions to be altered.
				//**
				foreach( $arr_user_permissions as $arr_permission ) {
					unset($arr_updated_permissions[$arr_permission['permission_id']]);
				}
				
				//**
				// Now the permissions are prepared for the user to be updated.
				//**
				$this->rebuild_user_permissions($obj_user->get('id'), $arr_updated_permissions);
				
			}
			
			//Update current user's permissions.
			$this->security_lib->user_login($this->users_model->get_user_by_identity($this->current_user->get('id'), 'id'));
		
		}
		
		/**
		* Rebuild User Permissions
		*
		* This method just takes an incoming permissions array and updates the user associated with 
		* the passed ID with the permissions.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	$user_id
		* @param    $arr_permissions
		* @return	void
		*
		*/
		public function rebuild_user_permissions($user_id, $arr_permissions) {
		
			//**
			// Insert Permissions.
			//**
			$arr_data	= array();
			
			foreach( $arr_permissions as $permission_id => $value ) {
				$arr_data[]	= array('permission_id' => $permission_id, 'value' => $value, 'override' => 0, 'user_id' => $user_id);
			}
			
			$this->db->where('user_id', $user_id);
			$this->db->insert_batch('perms_users_permissions', $arr_data);
		
		}
		
		/*******************************
		**	Remove Component
		**
		**	Description:
		**	This method will remove a component from the system.
		**
		**	@param:		$component_id<int>
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function remove_component( $component_id ) {
		
			////////////////
			// Remove from db
			////////////////
			$this->db->where('component_id', $component_id);
			$this->db->delete('perms_components');
		
		}
		
		/*******************************
		**	Remove Module
		**
		**	Description:
		**	This method removes a module from the system.
		**
		**	@param:		$module_id
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function remove_module( $module_id ) {
		
			////////////////
			// Remove Module from System
			////////////////
			$this->db->where('module_id', $module_id);
			$this->db->delete('perms_components');
			$this->db->where('module_id', $module_id);
			$this->db->delete('perms_modules');
		
		}
		
		/**
		* Remove Permission
		*
		* This will remove the permission from the database, and then remove
		* the users that have permission rules associated with it.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	permission_id
		* @return	void
		*
		*/
		public function remove_permission( $permission_id ) {
		
			$this->db->where('permission_id', $permission_id);
			$this->db->delete('perms_permissions');
			
			$this->db->where('permission_id', $permission_id);
			$this->db->delete('perms_users_permissions');
		
		}
		
		/**
		* Update Component
		*
		* This method will update the values of the passed component.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	component_id
		* @param    array <fields>
		* @return	void
		*
		*/
		public function update_component($component_id, $arr_data) {
		
			//**
			// Update database.
			//**
			$this->db->where('component_id', $component_id);
			$this->db->update('perms_components', $arr_data);
		
		}
		
		/**
		* Update module
		*
		* This method will update the values of the passed module.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	module_id
		* @param    array <fields>
		* @return	void
		*
		*/
		public function update_module($module_id, $arr_data) {
		
			//**
			// Update database.
			//**
			$this->db->where('module_id', $module_id);
			$this->db->update('perms_modules', $arr_data);
		
		}
		
		/**
		* Update permission
		*
		* This method will update the values of the passed permission.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	module_id
		* @param    array <fields>
		* @return	void
		*
		*/
		public function update_permission($permission_id, $arr_data) {
		
			//**
			// Update database.
			//**
			$this->db->where('permission_id', $permission_id);
			$this->db->update('perms_permissions', $arr_data);
		
		}
		
		/*******************************
		**	Update Group Permissions
		**
		**	Description:
		**	This method will remove a group's permissions and then add them again.
		**
		**	@param:		group_id
		**
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_group_permissions($group_id, $arr_group_permissions) {
		
			////////////////
			// Remove Exisitng permissions
			////////////////
			$this->db->where('group_id', $group_id);
			$this->db->delete('perms_groups_permissions');
			
			////////////////
			// Loop through permissions and add them.
			////////////////
			foreach( $arr_group_permissions as $perm_id => $arr_permission ) {
				
				$arr_ids	= explode('_', $perm_id);
				$arr_data	= array('group_id' => $group_id, 'permission_id' => $arr_ids[2], 'value' => 1);
				
				$this->db->insert('perms_groups_permissions', $arr_data);
				
			}
			
			//**
			// Now Update Members associated with those groups.
			//**
			$this->rebuild_members_permissions_in_group($group_id);
		
		}
		
		/*******************************
		**	Update User Permissions
		**
		**	Description:
		**	This method will remove and add the passed
		**  permissions to the database.
		**
		**	@param:		$user_id
		**	@return:	$arr_permissions (array[1_1_1]=>'on' format)
		**
		**  Author: Thomas Melvin
		**
		**/
		// passing post to this method was actually a bad idea and doesn't allow for much utility of the method.
		public function update_user_permissions( $user_id, $arr_post ) {
			
			$arr_user_permissions	= $arr_post['permissions'];
			$arr_overrides			= (isset($arr_post['overrides']))? $arr_post['overrides']:array();
			
			////////////////
			// Remove Existing Permissions
			////////////////
			$this->db->where('user_id', $user_id);
			$this->db->delete('perms_users_permissions');
			
			//**
			// Check for overrides on non-set checkboxes.
			//**
			if( count($arr_overrides) > 0 ) {
				
				foreach( $arr_overrides as $permission_id => $override ) {
					
					if( !isset($arr_user_permissions[$permission_id]) ) {
						$arr_user_permissions[$permission_id] = 0;
					}
					
				}
				
			}
			
			////////////////
			// Now Loops through permissions and add them to the database.
			////////////////
			foreach( $arr_user_permissions as $perm_id => $value ) {
				
				$override	= (isset($arr_overrides[$perm_id]))? 1:0;
				$arr_perm	= array('user_id' => $user_id, 'permission_id' => $perm_id, 'value' => $value, 'override' => $override);
				
				$this->db->insert('perms_users_permissions', $arr_perm);
				
			}
		
		}
		
	}
	
?>