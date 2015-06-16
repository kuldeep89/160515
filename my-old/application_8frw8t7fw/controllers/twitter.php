<?

	/**
	* PayProMedia Twitter Widget
	* Author: Enrique Marrufo
	* Date: 26 August 2013
	*
	* Notes: This widget queries Twitter API for selected Twitter Profile and returns a json string. 
	*
	*
	**/

		class Twitter extends CI_Controller {
	
			public function index() {
				
				$this->load->model('widget_model');
				$this->load->view('backend/widgets/twitter');
			}
		}