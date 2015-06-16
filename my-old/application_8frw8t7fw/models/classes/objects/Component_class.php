<?php

	/**
	* Component Class
	* Author: Thomas Melvin
	* Date: 9 August 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a Permissions Module.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/collections/Permission_collection.php';
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class Component_class extends Standard_class {
		
		//**
		// Data Members
		//**
		protected $component_id;
		protected $component_name;
		protected $component_description;
		protected $id;
		
		//**
		// Collection
		//**
		public $obj_permissions_collection;
		
		//**
		// Constructor
		//**
		public function __construct() {
			$this->obj_permissions_collection	= new Permissions_collection_class();
		}
		
		//**
		// Methods
		//**
		
		public function add_permissions( $arr_rows ) {
			
			foreach( $arr_rows as $arr_row ) {
				$this->set('component_name', $arr_row['component_name']);
				$this->obj_permissions_collection->add(new Permission_class($arr_row));
			}
			
		}
		
		public function set_component_id( $val ) {
			
			$this->id			= $val;
			$this->module_id	= $val;
			
		}
		
	}