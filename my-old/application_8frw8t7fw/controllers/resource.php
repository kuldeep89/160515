<?php
	
	/**
	* resource Controller
	* Author: Thomas Melvin
	* Date: 26 June 2013
	* Notes:
	* This controller handles operations of the resource module.
	*
	*/

	class Resource extends MY_Controller {
		
		/*******************************
		**	Default Constructor
		********************************/
		public function __construct() {
			parent::__construct();
		}
		
		/*******************************
		**	Ajax Articles
		**
		**	Description:
		**	This method will return a JSON array of entry objects.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function ajax_entries() {
			
			$this->load->model('resource_model');
			
			$obj_entries	= $this->resource_model->get_entries();
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['obj_entries']	= $obj_entries;
			
			////////////////
			// Load JSON View
			////////////////
			$this->load->view('backend/object-templates/resource/json-entries', $arr_page);
			
		}
		
		/*******************************
		**	Add Tag
		**
		**	Description:
		**	This method prints a form to add a tag to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function add_tag() {
		
			$this->security_lib->permissions_required(4);
			$this->load->view('backend/pages/resource/add-tag');
		
		}
		
		/*******************************
		**	Add Category
		**
		**	Description:
		**	This method loads a form to add a category.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function add_category() {
		
			$this->security_lib->permissions_required(8);
			$this->load->view('backend/pages/resource/add-category');
		
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
		**	Author: Thomas Melvin
		**
		**/
		public function category( $category_id = FALSE ) {
			
			//$this->output->enable_profiler(TRUE);

			////////////////
			// Validate ID
			////////////////
			if( !is_numeric($category_id) || $category_id === FALSE ) {
				
				$this->notification_lib->add_error('Invalid category ID, please try again.');
				redirect('resource');
				die();
				
			}
			
			////////////////
			// Load Model
			////////////////
			$this->load->model('resource_model');
			$this->load->helper('calculations');
			$arr_categories	= $this->resource_model->get_categories();
			
			$obj_entry_collection	= $this->resource_model->get_categorized_entries($category_id);
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
			$this->load->view('backend/pages/resource/category-listing', $arr_page);
			
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
		**	Author: Thomas Melvin
		**  (This turned out to be not so good of an idea -- probably should have coded it different).
		**/
		public function create_entry() {
			
			$this->security_lib->permissions_required(1);
			
			$this->load->model('resource_model');
			$obj_entry	= new resource_entry_class();
			
			////////////////
			// Build the Entry and Add It
			////////////////
			$obj_entry->set('title', 'Enter Entry Title');
			$obj_entry->set('content', 'Enter the entry content here. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, 
			eget lacinia odio sem nec elit. Aenean lacinia bibendum nulla sed consectetur. Donec id elit non mi porta gravida at eget 
			metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus, nisi erat 
			porttitor ligula, eget lacinia odio sem nec elit. Etiam porta sem malesuada magna mollis euismod.');
			$obj_entry->set('author_id', $this->current_user->get('id'));
			$obj_entry->set('date_created', time());
			$obj_entry->set('schedule_post', time());
			$obj_entry->set('created_by', $this->current_user->get('id'));
			
			$entry_id	= $this->resource_model->add_entry($obj_entry);
			
			////////////////
			// Now redirect the user to the edit page.
			////////////////
			redirect('resource/entry/'.$entry_id);
			
		}
		
		/*******************************
		**	Edit Category
		**
		**	Description:
		**	This method loads a category in a form to edit it.
		**
		**	@param:		$category_id
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function edit_category( $category_id ) {
			
			$this->security_lib->permissions_required(9);
			
			$this->load->model('resource_model');
			
			////////////////
			// Specify what category to get.
			////////////////
			$this->db->where('resource_entry_category_id', $category_id);
			
			$arr_category	= $this->resource_model->get_categories();
			
			if( count($arr_category) < 1 ) {
				
				$this->notification_lib->add_error('Invalid category ID, please try again.');
				redirect('resource/manage-categories');
				die();
				
			}
			
			////////////////
			// Get the one category that should be returned.
			////////////////
			$arr_category	= array_pop($arr_category);
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']	= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_category']	= $arr_category;
			
			////////////////
			// Load View
			////////////////
			$this->load->view('backend/pages/resource/edit-category', $arr_page);
			
		}
		
		/*******************************
		**	Edit Tag
		**
		**	Description:
		**	This method loads a tag into a form to be editted.
		**
		**	@param:		tag_id
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function edit_tag( $tag_id ) {
			
			$this->security_lib->permissions_required(5);
			
			$this->load->model('resource_model');
			
			////////////////
			// Specify what category to get.
			////////////////
			$this->db->where('resource_entry_tag_id', $tag_id);
			
			$arr_tag	= $this->resource_model->get_tags();
			
			if( count($arr_tag) < 1 ) {
				
				$this->notification_lib->add_error('Invalid tag ID, please try again.');
				redirect('resource/manage-tags');
				die();
				
			}
			
			////////////////
			// Get the one category that should be returned.
			////////////////
			$arr_tag	= array_pop($arr_tag);
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_tag']	= $arr_tag;
			
			////////////////
			// Load View
			////////////////
			$this->load->view('backend/pages/resource/edit-tag', $arr_page);
			
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
		
			$breadcrumbs[]	= array('title'=> 'Resource', 'url' => site_url('resource'));

			switch( $method ) {
				
				case 'entry':
					$breadcrumbs[]	= array('title'=> 'Entry', 'url' => site_url('resource'));
					$breadcrumbs[]	= array('title'=> $arr_params['item_name'], 'url' => site_url('resource/entry/'.$arr_params['item_id']));
					return $breadcrumbs;
				break;
				case 'category':
					$breadcrumbs[]	= array('title'=> 'Category', 'url' => site_url('resource'));
					$breadcrumbs[]	= array('title'=> $arr_params['item_name'], 'url' => site_url('resource/category/'.$arr_params['item_id']));
					return $breadcrumbs;
				break;
				case 'listing':
					$breadcrumbs[]	= array('title'=> 'Entries', 'url' => site_url('resource/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				case 'manage_categories':
					$breadcrumbs[]	= array('title'=> 'Manage Entry Categories', 'url' => site_url('resource/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				case 'manage_tags':
					$breadcrumbs[]	= array('title'=> 'Manage Entry Tags', 'url' => site_url('resource/'.str_replace("_","-",$method)));
					return $breadcrumbs;
				default:
					return $breadcrumbs;
					break;
				
			}
		}

		/*******************************
		**	Index
		**
		**	Description:
		**	Default resource page, shows recent resource entries.
		**
		**	@param:		void
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function index() {
			
			//$this->output->enable_profiler(TRUE);
			
			////////////////
			// Get Most Recent Entries
			////////////////
			$this->load->model('resource_model');
			$this->load->helper('calculations');
			
			$obj_entry_collection	= $this->resource_model->get_entries(25);
			$arr_column_collections	= $obj_entry_collection->get_columns(3);
			
			////////////////
			// Retreieve users that have been added by the get_entries call.
			////////////////
			$this->users_lib->retrieve_users();
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_column_collections']	= $arr_column_collections;
			
			$this->load->view('backend/pages/resource/entry-collection-feed', $arr_page);
			
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
		**	Author: Thomas Melvin
		**
		**/
		public function entry( $entry_id ) {
			
			////$this->output->enable_profiler(TRUE);
			
			////////////////
			// Check Data
			////////////////
			if( !is_numeric($entry_id) || $entry_id < 0 ) {
				
				$this->notification_lib->add_error('Invalid entry ID, please try a different ID.');
				redirect('resource');
				
			}
			
			////////////////
			// Load the required model.
			////////////////
			$this->load->model('resource_model');
			$this->load->model('users_model');
			
			////////////////
			// Load the desired entry.
			////////////////
			$obj_entry	= $this->resource_model->get_entry($entry_id);
			
			////////////////
			// Check to See If Entry Exists
			////////////////
			if( $obj_entry === FALSE ) {
				
				$this->notification_lib->add_error('Entry does not exist, this entry may have been removed.');
				redirect('resource');
				
			}
			
			////////////////
			// Get Related Entry
			////////////////
			$obj_related_collection	= $this->resource_model->get_categorized_entries($obj_entry->get_categories(), $obj_entry->get('id'), 4);
			$obj_related_collection	= $obj_related_collection->filter_out($obj_entry->get('id'));
			
			////////////////
			// Build an array of Tags that does not contain any of the tags that
			// this entry already contains.
			////////////////
			$arr_tags		= $this->resource_model->get_tags();
			$arr_categories	= $this->resource_model->get_categories();
			
			////////////////
			// Get Listing of Authors
			////////////////
			$obj_author_collection	= $this->users_model->get_group_members(3);

			$this->users_lib->add_collection($obj_author_collection);
			
			////////////////
			// Get Browser Title
			////////////////
			$browser_title	= $obj_entry->get('browser_title');
			
			////////////////
			// Load Helpers
			////////////////
			$this->load->helper('general_helper');
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method, 'item_id' => $entry_id, 'item_name' => $obj_entry->get('title')));
			$arr_page['obj_entry']				= $obj_entry;
			$arr_page['arr_all_tags']			= $arr_tags;
			$arr_page['arr_select_categories']	= $arr_categories;
			$arr_page['obj_related_collection']	= $obj_related_collection;
			$arr_page['arr_select_tags']		= $arr_tags;
			$arr_page['arr_js_views']			= array('backend/object-templates/resource/js-entry-tags', 'backend/object-templates/resource/js-entry-categories');
			$arr_page['obj_author_collection']	= $obj_author_collection;
			$arr_page['browser_title']			= (isset($browser_title) && !empty($browser_title))? $browser_title.' - MyBizPerks resource':$obj_entry->get('title').' - MyBizPerks resource';
			$arr_page['selected_author']		= $obj_entry->get('author_id');
			
			//**
			// Retrieve users left to retrieve
			//**
			$this->users_lib->retrieve_users();
			
			////////////////
			// Load the View
			////////////////
			$this->load->view('backend/pages/resource/entry', $arr_page);
			
		}
		
		/*******************************
		**	Insert Category
		**
		**	Description:
		**	This method will insert a category into the database.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function insert_category() {
			
			$this->security_lib->permissions_required(8);
			
			$category		= $this->input->post('category');
			$description	= $this->input->post('description');
			$color			= $this->input->post('color');
			$icon			= $this->input->post('icon');
			
			if( empty($category) ) {
				
				$this->notification_lib->add_error('Category was empty, please try again.');
				redirect('resource/add-category');
				
			}
			
			$this->load->model('resource_model');
			
			$this->resource_model->add_category($category, $description, $color, $icon);
			
			$this->notification_lib->add_success('Category created successfully.');
			redirect('resource/manage-categories');
			
		}
		
		/*******************************
		**	Insert Tag
		**
		**	Description:
		**	This methods adds a tag to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function insert_tag() {
			
			$this->security_lib->permissions_required(4);
			
			$tag			= $this->input->post('tag');
			$description	= $this->input->post('description');
			
			if( empty($tag) ) {
				
				$this->notification_lib->add_error('Tag was empty, please try again.');
				redirect('resource/add-tag');
				
			}
			
			$this->load->model('resource_model');
			
			$this->resource_model->add_tag($tag, $description);
			
			$this->notification_lib->add_success('Tag created successfully.');
			redirect('resource/manage-tags');
			
		}
		
		/*******************************
		**	Listing
		**
		**	Description:
		**	This method lists the resource entries that have been
		**  added to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function listing() {
			
			////$this->output->enable_profiler(TRUE);
			
			////////////////
			// Load in the required model, and fetch all entries.
			////////////////
			$this->load->model('resource_model');
			$obj_entry_collection	= $this->resource_model->get_entries(10000, FALSE, TRUE);
			
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
			
			$this->load->view('backend/pages/resource/entries', $arr_page);
			
		}
		
		/*******************************
		**	Remove Category
		**
		**	Description:
		**	This method removes a category from the system.
		**
		**	@param:		$category_id
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function remove_category( $category_id ) {
			
			$this->security_lib->permissions_required(10);
			
			$this->load->model('resource_model');
			
			////////////////
			// Get Total Number of Entries categorized by category.
			////////////////
			$num_entries	= $this->resource_model->get_num_entries_in_categories($category_id);
			
			if( $this->security_lib->permissions_required(10) || $this->security_lib->permissions_required(11) && $num_entries < 1 ) {
			
				$this->resource_model->remove_category($category_id);
				$this->notification_lib->add_success('Category has been removed from the system.');
				
				redirect('resource/manage-categories');
			
			}
			
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
		**	Author: Thomas Melvin
		**
		**/
		public function remove_entry( $entry_id ) {
			
			$this->security_lib->permissions_required(3);
			
			if( !is_numeric($entry_id) ) {
				
				$this->notification_lib->add_error('Invalid entry ID, please try again.');
				redirect('resource');
				
			}
			
			$this->load->model('resource_model');
			$this->resource_model->remove_entry($entry_id);
			
			$this->notification_lib->add_success('Entry has been removed successfully.');
			
			redirect('resource/listing');
			
		}
		
		/*******************************
		**	Remove Tag
		**
		**	Description:
		**	This method removes a tag from the system.
		**
		**	@param:		$tag_id
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function remove_tag( $tag_id ) {
			
			$this->security_lib->permissions_required(3);
			
			$this->load->model('resource_model');
			$this->resource_model->remove_tag($tag_id);
			$this->notification_lib->add_success('Tag has been successfully removed from the system.');
			redirect('resource/manage-tags');
			
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
		**	Author: Thomas Melvin
		**
		**/
		public function tag( $tag_id ) {
			
			$this->load->model('resource_model');
			$this->load->helper('calculations');
			
			$obj_entry_collection	= $this->resource_model->get_tagged_entries($tag_id);
			
			////////////////
			// Build Page Arary
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['obj_entry_collection']	= $obj_entry_collection;
			
			////////////////
			// Load View
			////////////////
			$this->load->view('backend/pages/resource/tag-listing', $arr_page);
			
		}
		
		/*******************************
		**	Manage Categories
		**
		**	Description:
		**	This method allows the users to manage categories added to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function manage_categories() {
			
			$this->load->model('resource_model');
			$arr_categories	= $this->resource_model->get_categories();
			
			$arr_category_entries	= $this->resource_model->get_num_entries_in_categories($arr_categories);
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_categories']			= $arr_categories;
			$arr_page['arr_num_in_categories']	= $arr_category_entries;
			
			$this->load->view('backend/pages/resource/manage-categories', $arr_page);
			
		}
		
		/*******************************
		**	Manage Tags
		**
		**	Description:
		**	This method allows the users to manage tags added to the system.
		**
		**	@param:		void
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function manage_tags() {
			
			$this->load->model('resource_model');
			$arr_tags	= $this->resource_model->get_tags();
			
			$arr_tag_entries	= $this->resource_model->get_num_entries_in_tags($arr_tags);
			
			////////////////
			// Build Page Array
			////////////////
			$arr_page['breadcrumbs']			= $this->get_breadcrumbs(array('method' => $this->router->method));
			$arr_page['arr_tags']			= $arr_tags;
			$arr_page['arr_num_in_tags']	= $arr_tag_entries;
			
			$this->load->view('backend/pages/resource/manage-tags', $arr_page);
			
		}
		
		/*******************************
		**	Update Category
		**
		**	Description:
		**	This method updates the passed category.
		**
		**	@param:		$category_id
		**	@return:	void
		**
		**/
		public function update_category( $category_id ) {
			
			$this->security_lib->permissions_required(9);
			
			if( !is_numeric($category_id) ) {
				
				$this->notification_lib->add_error('Invalid category ID, please try again.');
				redirect('resource/manage-categories');
				
			}
			
			$category	= $this->input->post('category');
			
			if( empty($category) ) {
				
				$this->notification_lib->add_error('Category was empty, please try again.');
				redirect('resource/manage-categories');
				
			}
			
			$this->load->model('resource_model');
			
			////////////////
			// Now Update the category.
			////////////////
			$arr_category['resource_entry_category_id']	= $category_id;
			$arr_category['name']						= $this->input->post('category');
			$arr_category['description']				= $this->input->post('description');
			$arr_category['color']						= $this->input->post('color');
			$arr_category['icon']						= $this->input->post('icon');
			
			$this->resource_model->update_category($arr_category);
			
			$this->notification_lib->add_success('Category updated successfully.');
			redirect('resource/manage-categories');
			
		}
		
		/*******************************
		**	Update Tag
		**
		**	Description:
		**	This method updates the passed tag.
		**
		**	@param:		$tag_id
		**	@return:	void
		**
		**/
		public function update_tag( $tag_id ) {
			
			$this->security_lib->permissions_required(3);
			
			if( !is_numeric($tag_id) ) {
				
				$this->notification_lib->add_error('Invalid tag ID, please try again.');
				redirect('resource/manage-tags');
				
			}
			
			$tag	= $this->input->post('tag');
			
			if( empty($tag) ) {
				
				$this->notification_lib->add_error('Tag was empty, please try again.');
				redirect('resource/manage-tags');
				
			}
			
			$this->load->model('resource_model');
			
			////////////////
			// Now Update the category.
			////////////////
			$arr_tag['resource_entry_tag_id']	= $tag_id;
			$arr_tag['name']					= $this->input->post('tag');
			$arr_tag['description']				= $this->input->post('description');
			
			$this->resource_model->update_tag($arr_tag);
			
			$this->notification_lib->add_success('Tag updated successfully.');
			redirect('resource/manage-tags');
			
		}
		
		/*******************************
		**	Update Entry
		**
		**	Description:
		**	This method receives incoming JSON variable and updates
		**  the resource entry associated with it.
		**
		**	@param:		$entry_id
		**	@return:	void
		**
		**	Author: Thomas Melvin
		**
		**/
		public function update_entry( $entry_id ) {
			
			$this->security_lib->permissions_required(2);
			
			////////////////
			// Decode JSON object to array
			////////////////
			$arr	= (array) json_decode($this->input->post('json'));
			
			////////////////
			// Parse Data
			////////////////
			$arr['schedule_post']	= strtotime($arr['schedule_post']);
			$arr_tags				= explode(',', $arr['tags']);
			$arr_categories			= explode(',', $arr['categories']);
			
			////////////////
			// Get rid of uneccessary fields
			////////////////
			unset($arr['tags']);
			unset($arr['categories']);
			
			////////////////
			// Update that this is not the initial entry.
			////////////////
			$arr['initial']	= '0';
			
			////////////////
			// Build the Entry Object
			////////////////
			$this->load->model('resource_model');
			$obj_entry	= FALSE;
			
			if( isset($arr['entry_id']) && is_numeric($arr['entry_id']) ) {
				$obj_entry	= $this->resource_model->get_entry($arr['entry_id']);
			}
			
			if( $obj_entry === FALSE ) {
				
				//Entry doesn't exist, report.
				echo 'FAILED ID:'.$arr['entry_id'];
				return 1;
				
			}
			
			$obj_entry->set_tags($arr_tags);
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