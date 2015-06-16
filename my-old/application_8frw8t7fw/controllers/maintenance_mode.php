<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance_mode extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		// Check if site is in maintanance mode
		$this->load->model('core_model');
		if($this->core_model->get_config('maintenance_mode') === "true") :
			$this->load->view('backend/pages/maintenance_mode');
		else :
			redirect('auth');
		endif;
	}
}