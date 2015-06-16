<?php
	
	/**
	* My Account Controller
	* Author: Bobbie Stump
	* Date: 17 July, 2013
	* Notes:
	* This controller handles operations of the my account module.
	*
	*/
	
	class myaccount extends MY_Controller {
		
		/*******************************
		**	Default Constructor
		********************************/
		public function __construct() {
			parent::__construct();
		}
		
		/*******************************
		**	Index
		**
		**	Description:
		**	Default My Account page, shows recent academy entries.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function index() {
		
			// Load model(s) we need
			$this->load->model('myaccount_model');
			$this->load->model('ion_auth_model');
			$this->load->model('users_model');

			// Get current logged in user's data
			$this->db->where('id', $this->ion_auth->user()->row()->id);
			$obj_user_collection = $this->users_model->get_users(1);
			$obj_user = array_pop($obj_user_collection->get('arr_collection'));

			// Build page array
			$arr_page['obj_user'] = $obj_user;

			// Load initial view
			$this->load->view('backend/pages/myaccount/myaccount', $arr_page);
			
		}

		/*******************************
		**	Update
		**
		**	Description:
		**	Updates user's information
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function update() {
		
			// Load model(s) we need
			$this->load->model('myaccount_model');
			$this->load->model('ion_auth_model');
			$this->load->model('users_model');

			// Decode JSON object to array
			$arr	= (array) json_decode($this->input->post('json'));

			// Create $_POST fields fore each JSON field
			foreach ($arr as $key=>$value) {
				$_POST[$key] = $value;
			}

			// Get current logged in user's data
			$this->db->where('id', $_POST['id']);
			
			$obj_user_collection	= $this->users_model->get_users(1);
			$obj_user				= array_pop($obj_user_collection->get('arr_collection'));

			// Send user data to be udpated
			$obj_user->update_from_post();

			// Save user changes in database
			if ($obj_user->save()) {
				echo 'UPDATED';
			} else {
				echo 'FAILED';
			}

		}
		
	}
	
?>