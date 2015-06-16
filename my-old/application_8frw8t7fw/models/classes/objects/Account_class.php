<?php

	/**
	* Account Class
	* Author: Thomas Melvin
	* Date: 9 August 2013
	* Notes:
	* This object will store the attributes and methods associated
	* with an Account.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/collections/Permission_collection.php';
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class Account_class extends Standard_class {
		
		//**
		// Data Members
		//**
		protected $account_id;
		protected $name;
		protected $organization;
		protected $type;
		protected $owner_id;
		protected $creation_date;
		protected $created_by;
		
		//**
		// Methods
		//**
	
		public function set_account_id( $val ) {
			
			$this->id			= $val;
			$this->account_id	= $val;
			
		}
		
	}