<?php
	
	/**
	* My Account Model
	* Author: Bobbie Stump
	* Date: 17 July 2013
	* Notes:
	* This model will work with the database to
	* add/update/remove data associated with a user's account
	*
	*/

	// require_once dirname(__FILE__).'/classes/objects/Page_class.php';
	
	class Myaccount_model extends CI_Model {
	
		/*******************************
		**	Get current user's profile data
		**
		**	Description:
		**	This method gets the logged in user's profile data
		**
		**	@param:		$str_user
		**	@return:	$page_id <int>
		**
		**/

		public function get_user_data( $str_user ) {

			// Specify fields to select
			$this->db->select(array('id','first_name','last_name', 'company', 'phone', 'google_id'));

			// Build where query
			$this->db->where(array('id'=>$str_user));

			// Run query to get user
			return $this->db->get('users', 1);

		}


		/*******************************
		**	Save current user's profile data
		**
		**	Description:
		**	This method updates the logged in user's profile data
		**
		**	@param:		$obj_user
		**	@return:	void
		**
		**/

		public function update_user( $obj_user ) {
			////////////////
			// Updatable Fields
			////////////////
			$arr_fields	= array(
				'first_name',
				'last_name',
				'company',
				'phone',
				'google_id'
			);
			
			////////////////
			// Array to Update DB
			////////////////
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field] = $obj_user->get($field);
			}

			// Save user changes in database
			if ($this->db->where('id', $obj_user->get('id'))) {
				if ($this->db->update('users', $arr_data)) {
					return true;
				} else {
					// Update failed
					return false;
				}
			} else {
				// Update failed
				return false;
			}
		}
	}