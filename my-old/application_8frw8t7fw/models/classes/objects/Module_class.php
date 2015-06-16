<?php

	/**
	* Module Class
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a Permissions Module.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	require_once dirname(dirname(__FILE__)).'/collections/Component_collection.php';
	
	class Module_class extends Standard_class {
		
		//**
		// Data Members
		//**
		protected $module_id;
		protected $module_name;
		protected $module_description;
		protected $id;
		
		//**
		// Collection
		//**
		public $obj_component_collection;

		//**
		// Construct
		//**
		public function __construct( $arr_rows = FALSE ) {
			$this->obj_component_collection	= new Component_collection_class();
		}
		
		//**
		// Methods
		//**
		
		public function add_array( $arr_rows ) {
			
			$arr_components	= $this->get_components($arr_rows);
			
			foreach( $arr_components as $arr_component ) {
				
				$arr_comp		= $arr_component[0];
				
				$obj_component	= new Component_class();
				
				$obj_component->set('component_id', $arr_comp['component_id']);
				$obj_component->set('component_name', $arr_comp['component_name']);
				$obj_component->set('component_description', $arr_comp['component_description']);
				
				$obj_component->add_permissions($arr_component);
				
				$this->obj_component_collection->add($obj_component);
				
			}
			
		}
		
		/*******************************
		**	Get Components
		**
		**	Description:
		**	This method returns an array broken down
		**  into components.
		**
		**	@param:		$arr_rows
		**	@return:	array
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_components( $arr_rows ) {
		
			////////////////
			// Build array based on component names.
			////////////////
			$arr_components	= array();
			
			foreach( $arr_rows as $arr_row ) {
				
				$arr_components[$arr_row['component_name']][]	= $arr_row;
				$this->module_name	= $arr_row['module_id'];
				$this->set('module_id', $arr_row['module_id']);
				$this->set('module_name', $arr_row['module_name']);
				$this->set('module_description', $arr_row['module_description']);
				
			}
			
			return $arr_components;
		
		}
		
		////////////////
		// Getter/Setters
		////////////////
		public function set_module_id( $val ) {
			
			$this->id = $val;
			$this->module_id = $val;
			
		}
		
	}