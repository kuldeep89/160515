<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	
	////////////////
	// Define Constructor?
	////////////////
	public function __construct() {
		parent::__construct();
	}
	
	/*******************************
	**	Auth
	**
	**	Description:
	**	This runs authentiation on login criteria.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function authenticate() {
		
		////////////////
		// Load Required Files
		////////////////
		$this->load->model('users_model');

		$password	= $this->input->post('password');
		$identity	= $this->input->post('username');
		
		if( empty($password) || empty($identity) ) {
			
			$this->notification_lib->add_error('Username and or password fields were empty. Please try again.');
			redirect('auth');
			
		}
		
		////////////////
		// Check for valid authentication.
		////////////////
		$obj_user	= FALSE;
		
		if( ($obj_user = $this->users_model->get_user_by_identity($identity)) !== FALSE ) {
			
			////////////////
			// User is Found. Now check password hashes.
			////////////////
			if( $this->security_lib->check_password($password, $obj_user->get('password')) ) {
				
				////////////////
				// This user is good to login.
				// Now setup session variables.
				////////////////
				$this->security_lib->user_login($obj_user);
				redirect('dashboard');
				
			}
			else {
				
				////////////////
				// Password failed!
				////////////////
				$this->notification_lib->add_error('Login failed: username or password is incorrect.');
				redirect('auth');
					
			}
			
		}
		else {
			
			////////////////
			// This user does not exist: 
			// but don't tell them that! (be generic)
			////////////////
			$this->notification_lib->add_error('Username and or password is incorrect.');
			redirect('auth');
			
		}
		
	}
	
	/*******************************
	**	Login
	**
	**	Description:
	**	This method will log the user into their account.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function index() {
		// Check if site is in maintanance mode, if not, load auth view
		$this->load->model('core_model');
		if($this->core_model->get_config('maintenance_mode') === "true" && $this->security_lib->is_authenticated() == FALSE) :
			if (isset($_GET['do_login']) && $_GET['do_login'] == 'true') {
				$this->load->view('backend/pages/auth/login');
			} else {
				redirect('maintenance_mode');
			}
		else :
			$this->load->view('backend/pages/auth/login');
		endif;
	}
	
	/*******************************
	**	Logout
	**
	**	Description:
	**	This method logs the user out.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function logout() {
		
		$this->session->sess_destroy();
		redirect('auth');
		
	}
	
	/*******************************
	**	Login
	**
	**	Description:
	**	This method supports the old URL structure.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function login() {
		$this->authenticate();
	}
		
}