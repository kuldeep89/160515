<?php
	
	/**
	* Academy Entries Collection
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This collection will store entries retrieved from
	* the database and methods and data members associated
	* with Academy Entries.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/collection.php';
	
	class User_collection_class extends Standard_collection {
		
		/*******************************
		**	Get User
		**
		**	Description:
		**	This method returns a user by the passed ID.
		**
		**	@param:		$user_id
		**	@return:	$obj_user
		**
		**/
		public function get_user( $user_id ) {
			return $this->arr_collection[$user_id];
		}	
		
	}