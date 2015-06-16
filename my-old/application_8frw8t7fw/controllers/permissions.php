<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions extends MY_Controller {
	
	/*******************************
	**	Add Component
	**
	**	Description:
	**	This method adds a new component to a module.
	**
	**	@param:		$module_id<int>
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function add_component( $module_id = FALSE ) {

		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);

		////////////////
		// Check to see if module id is legit
		////////////////
		if( $module_id === FALSE ) {
			
			$this->notification_lib->add_error('Module ID is missing, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($module_id) ) {
		
			$this->notification_lib->add_error('Module ID is invalid, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Retrieve posted fields.
		////////////////
		$arr_fields	= array('component_name', 'component_description', 'module_id');
		$arr_data	= array();
		
		foreach( $arr_fields as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		$arr_data['module_id']	= $module_id;
		
		////////////////
		// Add component to database.
		////////////////
		$this->load->model('permissions_model');
		
		$this->permissions_model->add_component($arr_data);
		
		////////////////
		// Set Notification and Redirect
		////////////////
		$this->notification_lib->add_success('Added component to module.');
		redirect('permissions/view-components/'.$module_id);
		
	}
	
	/**
	* Add Module
	*
	* This module processes a incoming post
	* module and sends it to the database.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function add_module() {
	
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);
	
		//**
		// Get Module from Post
		//**
		$arr_data	= array();
		$arr_fields	= array('module_name', 'module_description');
		
		foreach( $arr_fields as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		//**
		// Make module name lowercase.
		//**
		$arr_data['module_name']	= strtolower($arr_data['module_name']);
		
		//**
		// Insert into database.
		//**
		$this->load->model('permissions_model');
		$this->permissions_model->add_module($arr_data);
		
		$this->notification_lib->add_success('Module added successfully to the system.');
		redirect('permissions/view-modules');
		
	}
	
	/*******************************
	**	Add Permission
	**
	**	Description:
	**	This method will add a permission to the system 
	**  that has been posted from the create-permission page.
	**
	**	@param:		$component_id
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function add_permission( $component_id ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);
		
		////////////////
		// Check if component ID is legit.
		////////////////
		if( $component_id === FALSE ) {
			
			$this->notification_lib->add_error('Component ID not sent, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($component_id) ) {
			
			$this->notification_lib->add_error('Component ID is not numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Get Posted permission.
		////////////////
		$arr_fields	= array('permission_name', 'permission_description');
		$arr_data	= array();
		
		foreach( $arr_fields as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		$arr_data['component_id']	= $component_id;
		
		////////////////
		// Insert into database.
		////////////////
		$this->load->model('permissions_model');
		$this->permissions_model->add_permission($arr_data);
		
		////////////////
		// Notify and redirect.
		////////////////
		$this->notification_lib->add_success('Permission added successfully.');
		redirect('permissions/view-permissions/'.$component_id);
	
	}
	
	/*******************************
	**	Create Component
	**
	**	Description:
	**	This method displays a form to the user to create a component
	**  for a specific module, that has been passed to the controller.
	**
	**	@param:		module_id <int>
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function create_component( $module_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);
		
		////////////////
		// Make sure module ID is valid.
		////////////////
		if( $module_id === FALSE ) {
			
			$this->notification_lib->add_error('Module ID not sent, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($module_id) ) {
			
			$this->notification_lib->add_error('Module ID is not numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Build page array.
		////////////////
		$arr_page['module_id']	= $module_id;
		
		////////////////
		// Load View For Creating Component
		////////////////
		$this->load->view('backend/pages/permissions/create-component', $arr_page);
	
	}
	
	/**
	* Create Module
	*
	* This method present a form to the administrator
	* to add a module to the system.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function create_module() {
	
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);
	
		//**
		// Build page array
		//**
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		
		//**
		// Load the view.
		//**
		$this->load->view('backend/pages/permissions/create-module', $arr_page);
	
	}
	
	/*******************************
	**	Create Permission
	**
	**	Description:
	**	This method present a form for the user to create
	**  a permission associated with the passed component_id
	**
	**	@param:		component_id <int>
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function create_permission( $component_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(36);
		
		////////////////
		// Check if component ID is legit.
		////////////////
		if( $component_id === FALSE ) {
			
			$this->notification_lib->add_error('Component ID not sent, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($component_id) ) {
			
			$this->notification_lib->add_error('Component ID is not numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['component_id']	= $component_id;
		$arr_page['breadcrumbs']	= $this->get_breadcrumbs(array('component_id' => $component_id));
		
		////////////////
		// Load View
		////////////////
		$this->load->view('backend/pages/permissions/create-permission', $arr_page);
	
	}
	
	/**
	* Edit Component
	*
	* This method will display a form to edit a component.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	$component_id
	* @return	void
	*
	*/
	public function edit_component( $component_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Make sure component ID is valid.
		//**
		if( $component_id === FALSE ) {
			
			$this->notification_lib->add_error('Component ID was not sent, please try again.');
			redirect('permissions/modules');
			
		}
		
		if( !is_numeric($component_id) ) {
			
			$this->notification_lib->add_error('Invalid ID passed, component id must be numeric.');
			redirect('permissions/modules');
			
		}
		
		//**
		// Retrieve component from database.
		//**
		$this->load->model('permissions_model');
		
		$arr_component	= $this->permissions_model->get_component($component_id);
		
		//**
		// Build Page Array
		//**
		$arr_page['arr_component']	= $arr_component;
		$arr_page['breadcrumbs']	= $this->get_breadcrumbs(array('component_id'=>$component_id));
		
		//**
		// Load View
		//**
		$this->load->view('backend/pages/permissions/edit-component', $arr_page);
	
	}
	
	/**
	* Edit Group Permissions
	*
	* This method retrieves group permissions from the database.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function edit_group_permissions( $group_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Retrieve Permissions
		//**
		$this->load->model('permissions_model');
		
		$obj_all_permissions	= $this->permissions_model->get_all_permissions();
		$arr_group_perms	= $this->permissions_model->get_group_permissions($group_id);
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['obj_all_permissions']	= $obj_all_permissions;
		$arr_page['arr_group_permissions']	= $arr_group_perms;
		$arr_page['group_id']				= $group_id;
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('group_id'=>$group_id));
		
		////////////////
		// Load View
		////////////////
		$this->load->view('backend/pages/permissions/group-permissions', $arr_page);
	
	}
	
	/**
	* Edit Module
	*
	* This method will present a for to edit a module's attributes.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	module_id
	* @return	void
	*
	*/
	public function edit_module( $module_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Validate module_id
		//**
		if( $module_id === FALSE ) {
		
			$this->notification_lib->add_error('Invalid module ID, please try again.');
			redirect('permissions/view-modules');
		
		}
		
		if( !is_numeric($module_id) ) {
			
			$this->notification_lib->add_error('Module ID must be numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		//**
		// Get Module
		//**
		$this->load->model('permissions_model');
		$arr_module	= $this->permissions_model->get_module($module_id);
		
		//**
		// Build Page Array
		//**
		$arr_page['arr_module']	= $arr_module;
		
		//**
		// Load View
		//**
		$this->load->view('backend/pages/permissions/edit-module', $arr_page);
		
	}
	
	/**
	* Edit Permission
	*
	* This method will present a for to edit a permission's attributes.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	module_id
	* @return	void
	*
	*/
	public function edit_permission( $permission_id = FALSE ) {
	
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
	
		//**
		// Validate module_id
		//**
		if( $permission_id === FALSE ) {
		
			$this->notification_lib->add_error('Invalid permission ID, please try again.');
			redirect('permissions/view-modules');
		
		}
		
		if( !is_numeric($permission_id) ) {
			
			$this->notification_lib->add_error('Permission ID must be numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		//**
		// Get permission
		//**
		$this->load->model('permissions_model');
		$arr_permission	= $this->permissions_model->get_permission($permission_id);
		
		//**
		// Build Page Array
		//**
		$arr_page['arr_permission']	= $arr_permission;
		$arr_page['breadcrumbs']	= $this->get_breadcrumbs($arr_permission);
		//**
		// Load View
		//**
		$this->load->view('backend/pages/permissions/edit-permission', $arr_page);
		
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

		if( isset($arr_params['method']) ) 
			$method	= $arr_params['method'];
		else 
			$method	= $this->router->method;
	
		$breadcrumbs[]	= array('title'=> 'Permissions', 'url' => site_url('permissions'));

		switch( $method ) {
			case 'view_modules':
				$breadcrumbs[]	= array('title'=> 'Modules', 'url' => site_url('permissions/'.str_replace("_","-",$method)));
				return $breadcrumbs;
			case 'create_module':
				$breadcrumbs[]	= array('title'=> 'Create Module', 'url' => site_url('permissions/'.str_replace("_","-",$method)));
				return $breadcrumbs;
			case 'groups':
				$breadcrumbs[]	= array('title'=> 'Manage Group Permissions', 'url' => site_url('permissions/'.str_replace("_","-",$method)));
				return $breadcrumbs;
			case 'view_components':
				
				$arr_module	= $this->permissions_model->get_module($arr_params['module_id']);
				
				$breadcrumbs[]	= array('title'=>ucwords($arr_module['module_name']), 'url' => site_url('permissions/view-components/'.$arr_module['module_id']));
				$breadcrumbs[]	= array('title'=>'View Components', 'url'=>'#');
				
				return $breadcrumbs;
				
			break;
			case 'view_permissions':
				
				$arr_component	= $this->permissions_model->get_component($arr_params['component_id']);
				$arr_module		= $this->permissions_model->get_module($arr_component['module_id']);
				$breadcrumbs[]	= array('title'=>ucwords($arr_module['module_name']), 'url' => site_url('permissions/view-components/'.$arr_module['module_id']));
				$breadcrumbs[]	= array('title'=>$arr_component['component_name'], 'url' => site_url('permissions/view-permissions/'.$arr_component['component_id']));
				$breadcrumbs[]	= array('title'=>'View Permissions', 'url'=>'#');
				return $breadcrumbs;
				
			break;
			case 'edit_component':
				
				$arr_component	= $this->permissions_model->get_component($arr_params['component_id']);
				$arr_module		= $this->permissions_model->get_module($arr_component['module_id']);
				$breadcrumbs[]	= array('title'=>ucwords($arr_module['module_name']), 'url' => site_url('permissions/view-components/'.$arr_module['module_id']));
				$breadcrumbs[]	= array('title'=>'Edit Component', 'url'=>'#');
				return $breadcrumbs;
				
			break;
			case 'edit_permission':
				
				$arr_component	= $this->permissions_model->get_component($arr_params['component_id']);
				$arr_module		= $this->permissions_model->get_module($arr_component['module_id']);
				$breadcrumbs[]	= array('title'=>ucwords($arr_module['module_name']), 'url' => site_url('permissions/view-components/'.$arr_module['module_id']));
				$breadcrumbs[]	= array('title'=>$arr_component['component_name'], 'url' => site_url('permissions/view-permissions/'.$arr_component['component_id']));
				$breadcrumbs[]	= array('title'=>'Edit Permission', 'url'=>'#');
				return $breadcrumbs;
			break;
			case 'edit_group_permissions':
				$breadcrumbs	= array();
				$breadcrumbs[]	= array('title' => 'Group Permissions', 'url' => site_url('permissions/groups'));
				$arr_group		= $this->permissions_model->get_group($arr_params['group_id']);
				$breadcrumbs[]	= array('title' => $arr_group['name'], 'url'=>site_url('permissions/edit-group-permissions/'.$arr_group['id']));
				$breadcrumbs[]	= array('title'=>'Edit Group Permissions', 'url'=>'#');
				return $breadcrumbs;
				
			break;
			case 'create_permission':
				
				$this->load->model('permissions_model');
				
				$arr_component	= $this->permissions_model->get_component($arr_params['component_id']);
				$arr_module		= $this->permissions_model->get_module($arr_component['module_id']);
				
				$breadcrumbs[]	= array('title'=>ucwords($arr_module['module_name']), 'url' => site_url('permissions/view-components/'.$arr_module['module_id']));
				$breadcrumbs[]	= array('title'=>ucwords($arr_component['component_name']), 'url' => site_url('permissions/view-permissions/'.$arr_params['component_id']));
				$breadcrumbs[]	= array('title'=>'Add Permission', 'url'=>'#');
				
				return $breadcrumbs;
				
			break;
			default:
				// $breadcrumbs[]	= array('title'=> $arr_params['item_name'], 'url' => site_url('faq/entry/'.$arr_params['item_id']));
				return $breadcrumbs;
				break;
		}
	}
	
	public function index() {
		//**
		// Load Modules from Database.
		//**
		$this->load->model('permissions_model');
		
		$arr_modules	= $this->permissions_model->get_modules();
		
		//**
		// Build Page Array
		//**
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['arr_modules']	= $arr_modules;
		
		//**
		// Load Views
		//**
		$this->load->view('backend/pages/permissions/module-listing', $arr_page);
	}
	
	/**
	* Groups
	*
	* This method will retrieve the groups from the database
	* and then pass them to the permissions groups view.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function groups() {
	
		//**
		// Retrieve Groups
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
		$this->load->view('backend/pages/permissions/group-listing', $arr_page);
		
	}
	
	/*******************************
	**	Remove Component
	**
	**	Description:
	**	This method removes a component from a module.
	**
	**	@param:		component_id
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function remove_component( $module_id = FALSE, $component_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(38);
		
		////////////////
		// Check to see if module id is legit
		////////////////
		if( $module_id === FALSE ) {
			
			$this->notification_lib->add_error('Module ID is missing, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($module_id) ) {
		
			$this->notification_lib->add_error('Module ID is invalid, please try again.');
			redirect('permissions/view-modules');
			
		}
	
		////////////////
		// Check if component ID is legit.
		////////////////
		if( $component_id === FALSE ) {
			
			$this->notification_lib->add_error('Component ID not sent, please try again.');
			redirect('permissions/view-components/'.$module_id);
			
		}
		else if( !is_numeric($component_id) ) {
			
			$this->notification_lib->add_error('Component ID is not numeric, please try again.');
			redirect('permissions/view-components/'.$module_id);
			
		}
		
		////////////////
		// Remove Component from System
		////////////////
		$this->load->model('permissions_model');
		$this->permissions_model->remove_component($component_id);
		
		////////////////
		// Add Notification and Redirect
		////////////////
		$this->notification_lib->add_success('Component removed from module.');
		redirect('permissions/view-components/'.$module_id);
	
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
	public function remove_module($module_id) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(38);
		
		////////////////
		// Check to see if module id is legit
		////////////////
		if( $module_id === FALSE ) {
			
			$this->notification_lib->add_error('Module ID is missing, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($module_id) ) {
		
			$this->notification_lib->add_error('Module ID is invalid, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Remove Module from System
		////////////////
		$this->load->model('permissions_model');
		$this->permissions_model->remove_module($module_id);
		
		////////////////
		// Set notification and redirect.
		////////////////
		$this->notification_lib->add_success('Module removed successfully');
		redirect('permissions/view-modules');
		
	
	}
	
	/*******************************
	**	Remove Permission
	**
	**	Description:
	**	This method removes a permission from the system.
	**
	**	@param:		$permission_id
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function remove_permission( $permission_id = FALSE, $component_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(38);
		
		////////////////
		// Check to see if permission id is legit
		////////////////
		if( $permission_id === FALSE ) {
			
			$this->notification_lib->add_error('Permission ID is missing, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($permission_id) ) {
		
			$this->notification_lib->add_error('Permission ID is invalid, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Remove Permission from System
		////////////////
		$this->load->model('permissions_model');
		$this->permissions_model->remove_permission($permission_id);
		
		////////////////
		// Set notification and redirect.
		////////////////
		$this->notification_lib->add_success('Permission removed successfully');
		
		if( $component_id !== FALSE ) {
			redirect('permissions/view-permissions/'.$component_id);
		}
		redirect('permissions/view-modules');
		
	
	}
	
	/**
	* Update Component
	*
	* This method receives a component in the $_POST and
	* updates the database.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function update_component() {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Retrieve component id.
		//**
		$component_id	= $this->input->post('component_id');
		
		//**
		// Get Expected Fields
		//**
		$arr_expected	= array('component_name', 'component_description');
		$arr_data		= array();
		
		foreach( $arr_expected as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		//**
		// Update Component
		//**
		$this->load->model('permissions_model');
		
		$this->permissions_model->update_component($component_id, $arr_data);
		
		//**
		// Set notification and redirect.
		//**
		$this->notification_lib->add_success('Component updated successfully.');
		redirect('permissions/edit-component/'.$component_id);
	
	}
	
	/**
	* Update Module
	*
	* This method receives a module in the $_POST and
	* updates the database.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function update_module() {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Retrieve module id.
		//**
		$module_id	= $this->input->post('module_id');
		
		//**
		// Get Expected Fields
		//**
		$arr_expected	= array('module_name', 'module_description');
		$arr_data		= array();
		
		foreach( $arr_expected as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		//**
		// Update module.
		//**
		$this->load->model('permissions_model');
		
		$this->permissions_model->update_module($module_id, $arr_data);
		
		//**
		// Set notification and redirect.
		//**
		$this->notification_lib->add_success('Module updated successfully.');
		redirect('permissions/edit-module/'.$module_id);
	
	}
	
	/**
	* Update Permission
	*
	* This method receives a permission in the $_POST and
	* updates the database.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function update_permission() {
	
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(37);
		
		//**
		// Retrieve permission id.
		//**
		$permission_id	= $this->input->post('permission_id');
		
		//**
		// Get Expected Fields
		//**
		$arr_expected	= array('permission_name', 'permission_description');
		$arr_data		= array();
		
		foreach( $arr_expected as $field ) {
			$arr_data[$field]	= $this->input->post($field);
		}
		
		//**
		// Update permission.
		//**
		$this->load->model('permissions_model');
		
		$this->permissions_model->update_permission($permission_id, $arr_data);
		
		//**
		// Set notification and redirect.
		//**
		$this->notification_lib->add_success('Permission updated successfully.');
		redirect('permissions/edit-permission/'.$permission_id);
	
	}
	
	/*******************************
	**	Update User Permissions
	**
	**	Description:
	**	This method will update the user's permissions.
	**
	**	@param:		void
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function update_user_permissions() {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(26);
		
		////////////////
		// Get User ID
		////////////////
		$user_id	= $this->input->post('user_id');
		$referrer	= $this->input->post('referrer');
		
		unset($_POST['user_id']);
		unset($_POST['referrer']);
		
		////////////////
		// Update Database
		////////////////
		$this->load->model('permissions_model');
		
		$this->permissions_model->update_user_permissions($user_id, $_POST);
		$this->notification_lib->add_success('User permissions have been updated successfully.');
		
		////////////////
		// Reload Permissions/User Settings
		////////////////
		$this->load->model('users_model');
		
		$obj_user	= $this->users_model->get_user_by_identity($this->current_user->get('username'));
		$this->security_lib->user_login($obj_user);
		
		redirect('users/'.$referrer.'/'.$user_id);
	
	}
	
	/*******************************
	**	Update Group Permissions
	**
	**	Description:
	**	This method will update the permissions for the passed group_id
	**
	**	@param:		void
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function update_group_permissions() {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(39);
		
		////////////////
		// Get Group ID
		////////////////
		$group_id	= $this->input->post('group_id');
		
		unset($_POST['group_id']);
		
		////////////////
		// Update Database
		////////////////
		$this->load->model('permissions_model');
		$this->permissions_model->update_group_permissions($group_id, $_POST);
		
		$this->notification_lib->add_success('Group permissions have been updated successfully.');
		
		redirect('permissions/edit-group-permissions/'.$group_id);
	
	}
	
	/*******************************
	**	View Components
	**
	**	Description:
	**	This method will retreive components associated with the passed
	**  module ID, then pass them to a view to be displayed.
	**
	**	@param:		module_id <int>
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function view_components( $module_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(40);
		
		////////////////
		// Check if Module ID is valid.
		////////////////
		if( $module_id === FALSE ) {
			
			$this->notification_lib->add_error('Module ID was missing, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		if( !is_numeric($module_id) ) {
			
			$this->notification_lib->add_error('Module ID is not valid, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Retrive Module and Components
		////////////////
		$this->load->model('permissions_model');
		
		$arr_components	= $this->permissions_model->get_components($module_id);
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']	= $this->get_breadcrumbs(array('module_id'=>$module_id));
		$arr_page['arr_components']	= $arr_components;
		$arr_page['module_id']		= $module_id;
		
		////////////////
		// Load View
		////////////////
		$this->load->view('backend/pages/permissions/component-listing', $arr_page);
		
	}
	
	/**
	* View Modules
	*
	* This method retreives modules
	* and passes them to the module listing view.
	*
	* Author: Thomas Melvin
	*
	* @access	public
	* @param	void
	* @return	void
	*
	*/
	public function view_modules() {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(40);
		
		//**
		// Load Modules from Database.
		//**
		$this->load->model('permissions_model');
		
		$arr_modules	= $this->permissions_model->get_modules();
		
		//**
		// Build Page Array
		//**
		$arr_page['breadcrumbs']	= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['arr_modules']	= $arr_modules;
		
		//**
		// Load Views
		//**
		$this->load->view('backend/pages/permissions/module-listing', $arr_page);
	
	}
	
	/*******************************
	**	View Permissions
	**
	**	Description:
	**	This method will display permissions that have been added to a component.
	**
	**	@param:		component_id
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function view_permissions( $component_id = FALSE ) {
		
		////////////////
		// Authorization
		////////////////
		$this->security_lib->permissions_required(40);
		
		////////////////
		// Check if component ID is legit.
		////////////////
		if( $component_id === FALSE ) {
			
			$this->notification_lib->add_error('Component ID not sent, please try again.');
			redirect('permissions/view-modules');
			
		}
		else if( !is_numeric($component_id) ) {
			
			$this->notification_lib->add_error('Component ID is not numeric, please try again.');
			redirect('permissions/view-modules');
			
		}
		
		////////////////
		// Retrieve Permissions from database.
		////////////////
		$this->load->model('permissions_model');
		
		$arr_permissions	= $this->permissions_model->get_permissions($component_id);
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']		= $this->get_breadcrumbs(array('component_id' => $component_id));
		$arr_page['arr_permissions']	= $arr_permissions;
		$arr_page['component_id']		= $component_id;
		
		////////////////
		// Load View
		////////////////
		$this->load->view('backend/pages/permissions/permission-listing', $arr_page);
	
	}
		
}