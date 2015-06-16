<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends MY_Controller {
	
	/*******************************
	**	Add Page
	**
	**	Description:
	**	This method adds a page to the database.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function add_page() {
		
		$this->security_lib->permissions_required(12);
		
		$this->load->model('users_model');
		$this->load->helper('template_helper');
		
		////////////////
		// Get Listing of Authors
		////////////////
		$obj_author_collection	= $this->users_model->get_group_members(3);
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['obj_author_collection']	= $obj_author_collection;
		$arr_page['arr_templates']			= get_templates();
		
		$this->load->view('backend/pages/pages/page', $arr_page);
		
	}
	
	/*******************************
	**	Add Page Reference
	**
	**	Description:
	**	This method presents a form to add a page reference.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function add_page_reference() {
		
		$this->security_lib->permissions_required(12);
		
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$this->load->view('backend/pages/pages/add-page-reference', $arr_page);
		
	}
	
	/*******************************
	**	Create Page
	**
	**	Description:
	**	This method creates a page by adding it to the database.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function create_page() {
		
		$this->security_lib->permissions_required(12);
		
		// Get posted JSON data
		$page_data = json_decode($_POST["json"], true);
		
		////////////////
		// Define Expected Fields
		////////////////
		$arr_fields	= array('title', 'name', 'content', 'browser_title', 'keywords', 'description', 'template', 'url');
		$arr_data	= array();
		
		////////////////
		// Populate Fields
		////////////////
		foreach( $arr_fields as $field ) {
			if (isset($page_data[$field])) {
				$arr_data[$field]	= $page_data[$field];
			} else {
				echo '{"status":"error","statusmsg":"Unknown field \''.$field.'\'"}';
				die;
			}
		}
		
		$arr_data['created_date']	= time();
		$arr_data['created_by']		= $this->current_user->get('id');
		
		$this->load->model('pages_model');
		
		$page_id	= $this->pages_model->add_page($arr_data);
		
		if ($page_id) {
			echo '{"status":"success","statusmsg":"Page created successfully!", "page_id":"'.$page_id.'"}';
		} else {
			echo '{"status":"error","statusmsg":"Page was not created successfully, please try again."}';
		}
		
	}
	
	/*******************************
	**	Create Page Reference
	**
	**	Description:
	**	This method creates a page reference.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function create_page_reference() {
		
		$this->security_lib->permissions_required(12);
		
		$arr_data['name']	= $this->input->post('name');
		$arr_data['url']	= $this->input->post('url');
		
		$this->load->model('pages_model');
		
		$this->pages_model->create_page_reference($arr_data);
		
		$this->notification_lib->add_success('Page reference has been successfully added to the system.');
		
		
		redirect('pages');
	
			
	}


	/*******************************
	**	Get breadcrumbs
	**
	**	Description:
	**	This method receives a request for breadcrumbs
	**  and returns the breadcrumb trail
	**
	**	@param:		void
	**	@return:	breadcrumbs
	**
	**	Author: Bobbie Stump
	**
	**/
	
	public function get_breadcrumbs( $arr_params ) {

		$method	= $arr_params['method'];
	
		$breadcrumbs[]	= array('title'=> 'Pages', 'url' => site_url('pages'));

		switch( $method ) {
			case 'page':
				$breadcrumbs[]	= array('title'=> $arr_params['item_name'], 'url' => site_url('pages/page/'.$arr_params['item_id']));
				return $breadcrumbs;
			case 'add_page':
				$breadcrumbs[]	= array('title'=> 'Create Page', 'url' => site_url('pages/'.str_replace("_","-",$method)));
				return $breadcrumbs;
			case 'add_page_reference':
				$breadcrumbs[]	= array('title'=> 'Create Page Reference', 'url' => site_url('pages/'.str_replace("_","-",$method)));
				return $breadcrumbs;
			case 'navigations':
				$breadcrumbs[] = array('title'=> 'Navigation Management', 'url' => site_url('pages/'.str_replace("_","-",$method)));
			default:
				return $breadcrumbs;
				break;
			
		}
	}

	/*******************************
	**	Index
	**
	**	Description:
	**	This page displays the pages that have been
	**  added to the website.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function index() {
		
		$this->load->model('pages_model');
		
		$obj_pages		= $this->pages_model->get_pages();
		$arr_collection	= $obj_pages->get('arr_collection');
		
		$obj_pages->get_navigation(1);
		
		foreach( $arr_collection as $obj_page ) {
			$this->users_lib->add_user_to_retrieve($obj_page->get('created_by'));
		}
		
		$this->users_lib->retrieve_users();
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['obj_page_collection']	= $obj_pages;
		
		$this->load->view('backend/pages/pages/page-listing', $arr_page);
		
	}
	
	/*******************************
	**	Navigation
	**
	**	Description:
	**	This page displays and changes navigations that have been created to the website.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function navigation( $nav_id ) {
		
		$this->load->model('pages_model');
		$this->load->library('navigation_lib');
		
		$obj_pages			= $this->pages_model->get_pages();
		$arr_navigation		= $this->pages_model->get_navigation($nav_id);
		
		////////////////
		// Parse Pages
		////////////////
		$arr_order	= $this->navigation_lib->json_to_array($arr_navigation['navigation'], $obj_pages);
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['obj_pages']	= $obj_pages;
		$arr_page['nav_id']		= $nav_id;
		$arr_page['arr_order']	= $arr_order;
		
		$this->load->view('backend/pages/pages/navigation', $arr_page);
		
	}
	
	/*******************************
	**	Navigations
	**
	**	Description:
	**	This method displays a list of all the navigations
	**  available on the system.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function navigations() {
		
		$this->load->model('pages_model');
		$this->load->model('users_model');
		
		$arr_navigations	= $this->pages_model->get_navigations();
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
		$arr_page['arr_navigations']	= $arr_navigations;
		
		$this->load->view('backend/pages/pages/navigations', $arr_page);
		
	}
	
	/*******************************
	**	Page
	**
	**	Description:
	**	This method retrieves and displays a page to the user.
	**
	**	@param:		$page_id OR $page_name5
	**	@return:	void
	**
	**/
	public function page( $page ) {
		
		$this->load->model('pages_model');
		$this->load->helper('template_helper');
		$this->load->model('users_model');
		
		////////////////
		// Identify if $page is ID or name.
		////////////////
		$obj_pages	= null;
		
		if( is_numeric($page) ) {
			$obj_page	= $this->pages_model->get_page_by_id($page);	
		}
		else {
			$obj_page	= $this->pages_model->get_page_by_url($page);
		}
		
		if( $obj_page === FALSE ) {
			show_404();
		}
		
		$obj_author_collection	= $this->users_model->get_group_members(3);
		// print_r($obj_page); die;
		
		////////////////
		// Set Template
		////////////////
		$template	= $obj_page->get('template');
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method, 'item_id' => $obj_page->get('id'), 'item_name' => $obj_page->get('title')));
		$arr_page['obj_author_collection']	= $obj_author_collection;
		$arr_page['obj_page']			= $obj_page;
		$arr_page['selected_template']	= $template;
		$arr_page['arr_templates']		= get_templates();
		
		////////////////
		// Load Template
		////////////////
		$this->load->view('backend/pages/pages/templates/'.$obj_page->get('template'), $arr_page);
		//$this->load->view('backend/object-templates/pages/page', $arr_page);

	}
	
	/*******************************
	**	Remove Page
	**
	**	Description:
	**	This method removes a page from the system.
	**
	**	@param:		$page_id <int>
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function remove_page( $page_id ) {
		
		$this->security_lib->permissions_required(14);
		
		if( !is_numeric($page_id) ) {
			
			$this->notification_lib->add_error('Invalid page ID, please try again.');
			redirect('pages');
			
		}
		
		$this->load->model('pages_model');
		
		$this->pages_model->remove_page($page_id);
		$this->notification_lib->add_success('Page removed successfully!');
		redirect('pages');
		
	}
	
	/*******************************
	**	Update Navigation
	**
	**	Description:
	**	This method updates a navigation's pages and ordering.
	**
	**	@param:		nav_id
	**	@return:	void
	**
	**/
	public function update_navigation( $navigation_id ) {
		
		////////////////
		// Make sure nav_id is numeric.
		////////////////
		if( !is_numeric($navigation_id) ) {
			
			echo 'Invalid navigation ID, navigation ID must be numeric.';
			return 0;
			
		}
		
		$this->load->model('pages_model');
		
		$this->pages_model->update_navigation($navigation_id, $this->input->post('JSON_nav'));
		
		echo 'UPDATED';
		
	}
	
	/*******************************
	**	Update Page
	**
	**	Description:
	**	This method will update an existing page's attributes.
	**
	**	@param:		void
	**	@return:	void
	**
	**  Author: Thomas Melvin
	**
	**/
	public function update_page() {
	
		$this->security_lib->permissions_required(13);
	
		// Get posted JSON data
		$page_data = json_decode($_POST["json"], true);

		// Get page ID
		$page_id	= $page_data['page_id'];

		if( $page_id === FALSE || !is_numeric($page_id) ) {
			
			echo '{"status":"error","statusmsg":"Invalid page ID, please try again."}';
			die;
		}
		
		////////////////
		// Define Expected Fields
		////////////////
		$arr_fields	= array('page_id', 'title', 'name', 'content', 'browser_title', 'keywords', 'description', 'template', 'url');
		$arr_data	= array();
		
		////////////////
		// Populate Fields
		////////////////
		foreach( $arr_fields as $field ) {
			if (isset($page_data[$field])) {
				$arr_data[$field]	= $page_data[$field];
			} else {
				echo '{"status":"error","statusmsg":"Unknown field \''.$field.'\'"}';
				die;
			}
		}
		
		// Load pages model
		$this->load->model('pages_model');
		
		if ($this->pages_model->update_page($page_id, $arr_data)) {
			echo '{"status":"success","statusmsg":"Page updated successfully!"}';
		} else {
			echo '{"status":"error","statusmsg":"Page update was not successful, please try again."}';
		}
	}

}