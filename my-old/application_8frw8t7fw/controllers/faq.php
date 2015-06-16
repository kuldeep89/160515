<?php
	
	/**
	* Faq Controller
	* Author: Thomas Melvin
	* Date: 26 June 2013
	* Notes:
	* This controller handles operations of the faq module.
	*
	*/
	
	class Faq extends MY_Controller {
		
		/*******************************
		**	Default Constructor
		********************************/
		public function __construct() {
			parent::__construct();
		}
		
		/*******************************
		**	Category
		**
		**	Description:
		**	This method displays the entries that are
		**  associated with the category passed.
		**
		**	@param:		category <int>
		**	@return:	void
		**
		**/
		public function category( $category_id = FALSE ) {
			
			////////////////
			// Validate ID
			////////////////
			if( !is_numeric($category_id) || $category_id === FALSE ) {
				
				$this->notification_lib->add_error('Invalid category ID, please try again.');
				redirect('faq');
				die();
				
			}
			
			////////////////
			// Load Model
			////////////////
			$this->load->model('faq_model');
			$this->load->helper('calculations');
			$arr_categories	= $this->faq_model->get_categories();
			
			$obj_entry_collection	= $this->faq_model->get_categorized_entries($category_id);
			$obj_entry_collection->set('category', $arr_categories[$category_id]['name']);
			$obj_entry_collection->set('category_id', $category_id);
			////////////////
			// Build Page Arary
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method, 'item_id' => $category_id, 'item_name' => $arr_categories[$category_id]['name']));
			$arr_page['obj_entry_collection']	= $obj_entry_collection;
			
			////////////////
			// Load View
			////////////////
			$this->load->view('backend/pages/faq/category-listing', $arr_page);
			
		}
		
		/*******************************
		**	Create Entry
		**
		**	Description:
		**	This method creates an entry and then redirects the user
		**  to the entry-editor page to edit it.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function create_entry() {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(15);
			
			$this->load->model('faq_model');
			$obj_entry	= new Faq_entry_class();
			
			////////////////
			// Build the Entry and Add It
			////////////////
			$obj_entry->set('faq', 'Enter the frequently asked question here.');
			$obj_entry->set('response', 'Enter the response to the frequently asked question here.');
			$obj_entry->set('date_created', time());
			$obj_entry->set('created_by', $this->current_user->get('id'));
			
			$entry_id	= $this->faq_model->add_entry($obj_entry);
			
			////////////////
			// Now redirect the user to the edit page.
			////////////////
			redirect('faq/entry/'.$entry_id);
			
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
		
			$breadcrumbs[]	= array('title'=> 'FAQs', 'url' => '');
	
			switch( $method ) {
				
				default:
					return $breadcrumbs;
				break;
				
			}
			
		}

		/*******************************
		**	Index
		**
		**	Description:
		**	Default Faq page, shows recent faq entries.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function index() {
			
			//$this->output->enable_profiler(TRUE);
			
			////////////////
			// Get Most Recent Entries
			////////////////
			$this->load->model('faq_model');
			$this->load->helper('calculations');
			
			$obj_faq_collection	= $this->faq_model->get_entries();
			
			////////////////
			// Retreieve users that have been added by the get_entries call.
			////////////////
			$this->users_lib->retrieve_users();
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_categories']			= $this->faq_model->get_categories();
			$arr_page['obj_faq_collection']		= $obj_faq_collection;
			
			$this->load->view('backend/pages/faq/collection-feed', $arr_page);
			
		}
		
		/*******************************
		**	Entry
		**
		**	Description:
		**	This method displays an entry to the viewer.
		**
		**	@param:		$entry_id
		**	@return:	void
		**
		**/
		public function entry( $entry_id ) {
			
			//$this->output->enable_profiler(TRUE);
			
			////////////////
			// Check Data
			////////////////
			if( !is_numeric($entry_id) || $entry_id < 0 ) {
				
				$this->notification_lib->add_error('Invalid entry ID, please try a different ID.');
				redirect('faq');
				
			}
			
			////////////////
			// Load the required model.
			////////////////
			$this->load->model('faq_model');
			$this->load->model('users_model');
			
			////////////////
			// Load the desired entry.
			////////////////
			$obj_entry	= $this->faq_model->get_entry($entry_id);
			
			////////////////
			// Check to See If Entry Exists
			////////////////
			if( $obj_entry === FALSE ) {
				
				$this->notification_lib->add_error('Entry does not exist, this entry may have been removed.');
				redirect('faq');
				
			}
			
			////////////////
			// Get Related Entry
			////////////////
			$obj_related_collection	= $this->faq_model->get_categorized_entries($obj_entry->get_categories(), $obj_entry->get('id'), 4);
			$obj_related_collection	= $obj_related_collection->filter_out($obj_entry->get('id'));
			
			////////////////
			// Build an array of Tags that does not contain any of the tags that
			// this entry already contains.
			////////////////
			$arr_tags		= $this->faq_model->get_tags();
			$arr_categories	= $this->faq_model->get_categories();
			
			////////////////
			// Get Listing of Authors
			////////////////
			$this->users_lib->retrieve_users();
			
			////////////////
			// Get Browser Title
			////////////////
			$browser_title	= $obj_entry->get('browser_title');
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_entry']				= $obj_entry;
			$arr_page['arr_all_tags']			= $arr_tags;
			$arr_page['arr_select_categories']	= $arr_categories;
			$arr_page['obj_related_collection']	= $obj_related_collection;
			$arr_page['arr_select_tags']		= $arr_tags;
			$arr_page['arr_js_views']			= array('backend/object-templates/faq/js-entry-categories');
			$arr_page['browser_title']			= (isset($browser_title) && !empty($browser_title))? $browser_title.' - MyBizPerks Faq':$obj_entry->get('title').' - MyBizPerks Faq';
			$arr_page['selected_author']		= $obj_entry->get('author_id');
			
			////////////////
			// Load the View
			////////////////
			$this->load->view('backend/pages/faq/entry', $arr_page);
			
		}
		
		/*******************************
		**	Listing
		**
		**	Description:
		**	This method lists the faq entries that have been
		**  added to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function listing() {
			
			////$this->output->enable_profiler(TRUE);
			
			////////////////
			// Load in the required model, and fetch all entries.
			////////////////
			$this->load->model('faq_model');
			$obj_entry_collection	= $this->faq_model->get_entries(10000, FALSE, TRUE);
			
			$arr_collection			= $obj_entry_collection->get('arr_collection');
			
			foreach( $arr_collection as $obj_entry ) {
				$this->users_lib->add_user_to_retrieve($obj_entry->get('author_id'));
			}
			
			$this->users_lib->retrieve_users();
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_entry_collection']	= $obj_entry_collection;
			
			$this->load->view('backend/pages/faq/entries', $arr_page);
			
		}
		
		/*******************************
		**	Remove Entry
		**
		**	Description:
		**	This controller removes the entry, sets a notification
		**  and redirects the user back to the entr listing.
		**
		**	@param:		entry_id <int>
		**	@return:	void
		**
		**/
		public function remove_entry( $entry_id ) {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(17);
			
			if( !is_numeric($entry_id) ) {
				
				$this->notification_lib->add_error('Invalid entry ID, please try again.');
				redirect('faq');
				
			}
			
			$this->load->model('faq_model');
			$this->faq_model->remove_entry($entry_id);
			
			$this->notification_lib->add_success('Entry has been removed successfully.');
			
			redirect('faq');
			
		}
		
		/*******************************
		**	Tag
		**
		**	Description:
		**	This method displays a list of articles that
		**  have been tagged with the passed tag.
		**
		**	@param:		$tag_name
		**	@return:	void
		**
		**/
		public function tag( $tag_id ) {
			
			$this->load->model('faq_model');
			$this->load->helper('calculations');
			
			$obj_entry_collection	= $this->faq_model->get_tagged_entries($tag_id);
			
			////////////////
			// Build Page Arary
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_entry_collection']	= $obj_entry_collection;
			
			////////////////
			// Load View
			////////////////
			$this->load->view('backend/pages/faq/tag-listing', $arr_page);
			
		}
		
		/*******************************
		**	Update Entry
		**
		**	Description:
		**	This method receives incoming JSON variable and updates
		**  the faq entry associated with it.
		**
		**	@param:		$entry_id
		**	@return:	void
		**
		**/
		public function update_entry( $entry_id ) {
			
			////////////////
			// Authorization
			////////////////
			$this->security_lib->permissions_required(16);
			
			////////////////
			// Decode JSON object to array
			////////////////
			$arr	= (array) json_decode($this->input->post('json'));
			
			////////////////
			// Parse Data
			////////////////

			$arr_categories			= explode(',', $arr['categories']);
			
			////////////////
			// Get rid of uneccessary fields
			////////////////
			unset($arr['categories']);
			
			////////////////
			// Build the Entry Object
			////////////////
			$this->load->model('faq_model');
			
			$obj_entry	= FALSE;
			
			if( isset($arr['entry_id']) && is_numeric($arr['entry_id']) ) {
				$obj_entry	= $this->faq_model->get_entry($arr['entry_id']);
			}
			
			if( $obj_entry === FALSE ) {
				
				//Entry doesn't exist, report.
				echo 'FAILED ID:'.$arr['entry_id'];
				return 1;
				
			}

			$obj_entry->set_categories($arr_categories);
			$obj_entry->update_fields($arr);
			
			////////////////
			// Save Entry
			////////////////
			$obj_entry->save();
			
			////////////////
			// Done, Notify them.
			////////////////
			echo 'UPDATED';
			
		}
		
	}
	
?>