<?php
	
	/**
	* Module Collection
	* Author: Thomas Melvin
	* Date: 9 August 2013
	* Notes:
	* This collection will store modules retrieved from
	* the database and methods and data members associated
	* with Permissions Module.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/collection.php';
	
	class Module_collection_class extends Standard_collection {	
		
		/*******************************
		**	Add Array
		**
		**	Description:
		**	This method will add an entire array to the collection.
		**
		**	@param:		$arr_rows
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function add_array( $arr_rows ) {
		
			////////////////
			// Loop through and add modules
			////////////////
			if( count($arr_rows) > 0 ) {
				
				$arr_modules	= $this->get_modules($arr_rows);

				foreach( $arr_modules as $module_name => $arr_components ) {
				
					$obj_module		= new Module_class();
					$obj_module->set('module_name', $module_name);
					$obj_module->add_array($arr_components);
					$this->add($obj_module);
					
				}
				
			}
		
		}
		
		/*******************************
		**	Get Modules
		**
		**	Description:
		**	This method gets modules that are in the passed rows
		**  and returns an array with the first index of module name.
		**  $array[<module name>]
		**
		**	@param:		$arr_rows
		**	@return:	array
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_modules( $arr_rows ) {
		
			////////////////
			// Build 2d array with first
			// index module name.
			////////////////
			$arr_modules	= array();
			
			foreach( $arr_rows as $arr_row ) {
				$arr_modules[$arr_row['module_name']][]	= $arr_row;
			}
			
			return $arr_modules;
		
		}
		
	}