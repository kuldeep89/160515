<?php
	
	/**
	* Accounts
	* Author: Thomas Melvin
	* Date: 17 July 2013
	* Notes:
	* This controller handles the management of the accounts
	* added to the system.
	*
	*/
	
	class Accounts extends MY_Controller {
		
		/**
		* Create Account
		*
		* This method will present a form to add an account
		* to the systme.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function add_account() {
		
			//**
			// Load Form.
			//**
			$this->load->view('backend/pages/accounts/add-account');
		
		}
		
		/**
		* Index
		*
		* This method will show the accounts on the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function index() {
			$this->listing();
		}
		
		/**
		* Listing
		*
		* This method will print out all the accounts
		* that have been added to the system.
		*
		* Author: Thomas Melvin
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function listing() {
			
			
			
			//**
			// Retrieve all accounts.
			//**
			$this->load->model('accounts_model');
			
			$obj_accounts	= $this->accounts_model->get_accounts();
			
			//**
			// Build Page Array
			//**
			$arr_page['obj_account_collection']	= $obj_accounts;
			
			//**
			// Load View
			//**
			$this->load->view('backend/pages/accounts/listing', $arr_page);
		
		}
		
	}
		
?>