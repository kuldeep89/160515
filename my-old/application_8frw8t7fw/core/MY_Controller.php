<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	//Default Controller
	function __construct() {
	
		parent::__construct();

		// Check if site is in maintanance mode
		$this->load->model('core_model');
		if($this->core_model->get_config('maintenance_mode') === "true" && $this->security_lib->is_authenticated() == FALSE) :
			if ($this->router->class != 'maintenance_mode')
				redirect('maintenance_mode');
		endif;

		//Security Check
		if ( $this->security_lib->is_authenticated() == FALSE ) {
			
			//redirect them to the login page
			if( $this->router->class == 'dashboard' ) {
				redirect('auth', 'refresh');
			}
			else {
				if($this->core_model->get_config('maintenance_mode') === "true") :
					if ($this->router->class != 'maintenance_mode') :
						redirect('maintenance_mode');
					endif;
				else :
					$this->notification_lib->add_error('You must be logged in to view this page.');
					redirect('auth', 'refresh');
				endif;
			}
			
		}
		else {
				
			$this->security_lib->login_from_session();
			
			////////////////
			// Turn Profiler On For Developers
			////////////////
			$arr_disabled_controllers	= array('media', 'members', 'pages');
			$arr_disabled_pages			= array('update_entry', 'update_member');
			
			if( $this->current_user->get('username') == 'tmelvin' && !in_array($this->router->class, $arr_disabled_controllers) && !in_array($this->router->method, $arr_disabled_pages) ) {
				//$this->output->enable_profiler(TRUE);
			}
			
		}
		
	}
	
	// Get breadcrumbs
	public function get_breadcrumbs() {
		return null;
	}
	
}