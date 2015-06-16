<?php

	/**
	* Permission Class
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a Permissions Module.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class Permission_class extends Standard_class {
		
		//**
		// Data Members
		//**
		protected $permission_id;
		protected $compontent_id;
		protected $permission_name;
		protected $permission_description;
		protected $id;
		
		//**
		// Methods
		//**
		
		/*******************************
		**	Is Set
		**
		**	Description:
		**	This method takes in a array listing of
		**  user permissoins and sees if any of those permissions match
		**  this permission.
		**
		**	@param:		arr_user_permissions
		**	@return:	string
		**
		**  Author: Thomas Melvin
		**
		**/
		public function is_set($arr_user_permissions) {
		
			foreach( $arr_user_permissions as $user_permission ) {
			
				if( $user_permission['permission_id'] == $this->id ) {
					
					if( $user_permission['value'] == 1 ) {
						return 'checked="checked"';
					}
					
				}
				
			}
			
			return '';
		
		}
		
		/*******************************
		**	Is Overridded
		**
		**	Description:
		**	Checks to see if the permission is set to override
		**
		**	@param:		arr_user_permissions
		**	@return:	string
		**
		**  Author: Thomas Melvin
		**
		**/
		public function is_overrided($arr_user_permissions) {
		
			foreach( $arr_user_permissions as $user_permission ) {
				
				if( $user_permission['permission_id'] == $this->id ) {
				
					if( $user_permission['override'] == '1' ) {
						return 'checked="checked"';	
					}
					
				}
				
			}
			
			return '';
		
		}
		
		public function set_permission_id( $val ) {
			
			$this->id = $val;
			$this->permissions_id = $val;
			
		}
		
	}