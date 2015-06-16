<?php
	
	/**
	* Pages Model
	* Author: Thomas Melvin
	* Date: 5 July 2013
	* Notes:
	* This model will work with the database to
	* add/update/remove data associated with the pages tables.
	*
	*/
	
	require_once dirname(__FILE__).'/classes/collections/Page_collection.php';
	require_once dirname(__FILE__).'/classes/objects/Page_class.php';
	
	class Pages_model extends CI_Model {
		
		/*******************************
		**	Add Page
		**
		**	Description:
		**	This method adds a page to the database.
		**
		**	@param:		$arr_page
		**	@return:	$page_id <int>
		**
		**/
		public function add_page( $arr_page ) {
			
			$this->db->insert('pages', $arr_page);
			return $this->db->insert_id();
			
		}
		
		/*******************************
		**	Create Page Reference
		**
		**	Description:
		**	This mehtod adds a page to the database.
		**
		**	@param:		arr_page_reference
		**	@return:	void
		**
		**/
		public function create_page_reference( $arr_data ) {
		
			$arr_data['page_reference']	= 1;
			$arr_data['created_by']		= $this->current_user->get('id');
			$arr_data['created_date']	= time();
			$this->db->insert('pages', $arr_data);
			
		}
		
		/*******************************
		**	Get Pages Like
		**
		**	Description:
		**	This returns a collection of pages
		**  that match the search criteria.
		**
		**	@param:		$search <string>
		**	@return:	obj_collection
		**
		**/
		public function get_pages_like( $search ) {
			
			$this->db->like('title', $search);
			$this->db->or_like('content', $search);
			$this->db->or_like('description', $search);
			$this->db->or_like('keywords', $search);
			
			$obj_query	= $this->db->get('pages');
			
			$obj_collection	= new Page_collection_class();
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_collection->add(new Page_class($arr_row));
				}
				
			}
			
			return $obj_collection;
			
		}
		
		/*******************************
		**	Get Navigation
		**
		**	Description:
		**	Returns the JSON from the associated nav_id.
		**
		**	@param:		navigation <id>
		**	@return:	array
		**
		**/
		public function get_navigation( $nav_id ) {
			
			////////////////
			// Get Pages Associated with this Navigation
			////////////////
			$this->db->where('navigations.nav_id', $nav_id);
			
			$obj_query			= $this->db->get('navigations');
			$arr_pages_in_nav	= $obj_query->result_array();
			
			return array_pop($arr_pages_in_nav);
			
		}
		
		/*******************************
		**	Get Navigations
		**
		**	Description:
		**	This method returns an array listing of
		**  navigations in the database.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_navigations() {
			
			$obj_query	= $this->db->get('navigations');
			return $obj_query->result_array();
			
		}
		
		/*******************************
		**	Get Pages
		**
		**	Description:
		**	This method returns a listing of the pages that have been added to the system.
		**
		**	@param:		void
		**	@return:	obj_page_collection
		**
		**/
		public function get_pages() {
			
			$obj_query		= $this->db->get('pages');
			$obj_collection	= new Page_collection_class();
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_collection->add(new Page_class($arr_row));
				}
				
			}
			
			return $obj_collection;
			
		}
		
		/*******************************
		**	Get Page By Id
		**
		**	Description:
		**	This method returns a page object from 
		**  the database that matches the passed page id.
		**
		**	@param:		$page_id <int>
		**	@return:	obj_page
		**
		**/
		public function get_page_by_id( $page_id ) {
			
			$this->db->where('page_id', $page_id);
			$obj_query	= $this->db->get('pages');
			
			$obj_page	= FALSE;
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_page	= new Page_class($arr_row);
				}
				
			}
			
			return $obj_page;
			
		}
		
		/*******************************
		**	Get Page By URL
		**
		**	Description:
		**	this method returns a page object
		**  by matching page url.
		**
		**	@param:		$page_url
		**	@return:	obj_page
		**
		**/
		public function get_page_by_url( $page_url ) {
			
			$this->db->like('url', $page_url);
			$obj_query	= $this->db->get('pages');
			
			$obj_page	= FALSE;
			
			if( $obj_query->num_rows() > 0 ) {
				
				foreach( $obj_query->result_array() as $arr_row ) {
					$obj_page	= new Page_class($arr_row);
				}
				
			}
			
			return $obj_page;
			
		}
		
		/*******************************
		**	Remove Page
		**
		**	Description:
		**	This method removes a page from the database.
		**
		**	@param:		page_id <int>
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function remove_page( $page_id ) {
			
			$this->db->where('page_id', $page_id);
			$this->db->delete('pages');
			
		}
		
		/*******************************
		**	Update Navigation
		**
		**	Description:
		**	This method updates a navigation that is associated with the ID passed.
		**
		**	@param:		navigation_id
		**  @param:		navigation <JSON string>
		**	@return:	void
		**
		**/
		public function update_navigation( $navigation_id, $json_navigation ) {
			
			$arr_data	= array('navigation' => $json_navigation);
			
			$this->db->where('nav_id', $navigation_id);
			
			$this->db->update('navigations', $arr_data);
				
		}
		
		/*******************************
		**	Update Page
		**
		**	Description:
		**	This method updates a pages attributes in the database.
		**
		**	@param:		page_id <int>
		**  @param:		arr_data
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update_page( $page_id, $arr_data ) {
			
			if ($this->db->where('page_id', $page_id)) {
				if ($this->db->update('pages', $arr_data)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
			
		}
		
	}