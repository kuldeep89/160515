<?php 

	//**
	// Accounts Model
	// Author: Thomas Melvin
	// Date: Aug 19, 2013
	//**
	
	require_once dirname(__FILE__).'/classes/collections/Account_collection.php';
	
	class Accounts_model extends CI_Model {
		
		/**
		* Get Accounts
		*
		* This method will return a listing of accounts.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	obj_account_collection
		*
		*/
		public function get_accounts() {
		
			//**
			// Retrieve accounts
			//**
			$obj_query				= $this->db->get('accounts');
			$obj_account_collection	= new Account_collection_class();
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_account_collection->add(new Account_class($arr_row));
				}
				
			}
			
			return $obj_account_collection;
			
		}
		
	}

?>