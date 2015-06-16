<?php
	
	/**
	* Search
	* Author: Thomas Melvin
	* Date: 17 July 2013
	* Notes:
	* This controller handles user searches.
	*
	*/
	
	class Search extends MY_Controller {
		
		public function results() {
			
			$this->load->model('academy_model');
			$this->load->model('faq_model');
			$this->load->model('pages_model');
			
			$arr_page	= array();
			$search		= $this->input->post('search');
			
			if( empty($search) ) {
				$this->notification_lib->add_error('Search string was empty, please try searching again.');
			}
			else {

				$obj_faq_collection		= $this->faq_model->get_entries_like($search);
				$obj_academy_collection	= $this->academy_model->get_entries_like($search);
				$obj_pages_collection	= $this->pages_model->get_pages_like($search);
				
				////////////////
				// Build Page Array
				////////////////
				$arr_page['obj_academy_collection']	= $obj_academy_collection;
				$arr_page['obj_pages_collection']	= $obj_pages_collection;
				$arr_page['obj_faq_collection']		= $obj_faq_collection;
				
			}
			
			$this->load->view('backend/pages/search/search-results', $arr_page);
			
		}
		
	}
		
?>