<?php

	/**
	* Academy User Class
	* Author: Thomas Melvin
	* Date: 1 July 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a User.
	*
	*/
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class User_class extends Standard_class {
		
		////////////////
		// Object Data Members
		////////////////
		protected $id;
		protected $ip_address;
		protected $username;
		protected $email;
		protected $created_on;
		protected $last_login;
		protected $active;
		protected $first_name;
		protected $last_name;
		protected $company;
		protected $address;
		protected $city;
		protected $state;
		protected $zip;
		protected $phone;
		protected $google_id;
		protected $profile_image;
		protected $account;
		protected $password;
		
		////////////////
		// Profile Image
		////////////////
		protected $name;
		protected $size;
		protected $height;
		protected $width;
		protected $type;
		protected $date_created;
		protected $created_by;
				
		////////////////
		// Generated Data Members
		////////////////
		protected $arr_groups;
		protected $arr_permissions;
		
		////////////////
		// METHODS
		////////////////
		/*******************************
		**	Get Name
		**
		**	Description:
		**	This method returns the first and last name of the user.
		**
		**	@param:		void
		**	@return:	string
		**
		**/
		public function get_full_name() {
			return $this->get('first_name').' '.$this->get('last_name');
		}
		
		/**
		* Get Groups
		*
		* This method will reteieve the user's groups.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	arr_groups
		*
		*/
		public function get_groups() {
		
			//**
			// Get Groups
			//**
			$this->CI->load->model('users_model');
			
			$this->arr_groups	= $this->CI->users_model->get_users_groups($this->id);
			
			return $this->arr_groups;
		
		}
		
		/*******************************
		**	Update user data
		**
		**	Description:
		**	This method updates the user with post data
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function update_from_post() {
		
			$this->set('id', $this->CI->input->post('id'));
			$this->set('first_name', $this->CI->input->post('first_name'));
			$this->set('last_name', $this->CI->input->post('last_name'));
			$this->set('company', $this->CI->input->post('company'));
			$this->set('address', $this->CI->input->post('address'));
			$this->set('city', $this->CI->input->post('city'));
			$this->set('state', $this->CI->input->post('state'));
			$this->set('zip', $this->CI->input->post('zip'));
			$this->set('phone', $this->CI->input->post('phone'));
			$this->set('google_id', $this->CI->input->post('google_id'));
			$this->save();
			
		}

		/*******************************
		**	Save user changes
		**
		**	Description:
		**	This method returns true if update is successful,
		**  it returns fales if update fails
		**
		**	@param:		void
		**	@return:	boolean
		**
		**/
		public function save() {
			return $this->CI->myaccount_model->update_user($this);
		}
				
	}
	
?>