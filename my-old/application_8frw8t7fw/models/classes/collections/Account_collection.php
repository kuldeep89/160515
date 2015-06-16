<?php
	
	/**
	* Component Collection
	* Author: Thomas Melvin
	* Date: 9 August 2013
	* Notes:
	* This collection will store compontents retrieved from
	* the database and methods and data members associated
	* with Permissions Module.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/collection.php';
	
	class Account_collection_class extends Standard_collection {
		
		public function add_permissions( $arr_row ) {
			$this->add(new Permission_class($arr_row));
		}
		
	}