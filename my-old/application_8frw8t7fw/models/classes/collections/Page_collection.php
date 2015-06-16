<?php
	
	/**
	* Academy Pages Collection
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This collection will store pages retrieved from
	* the database and methods and data members associated
	* with pages.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/collection.php';
	
	class Page_collection_class extends Standard_collection {
		
		/*******************************
		**	Get Navigation
		**
		**	Description:
		**	This method returns a navigation that has been
		**  established within the database.
		**
		**	@param:		navigation_id <int>
		**	@return:	array
		**
		**/
		public function get_navigation( $navigation_id ) {
			
			////////////////
			// Get the Navigation Hierarchy
			////////////////
			$this->CI->load->model('pages_model');
			$arr_pages_in_nav	= $this->CI->pages_model->get_navigation($navigation_id);
			
			////////////////
			// Now Organize an Array of Pages
			////////////////
			$arr_pages		= $this->arr_collection;
			$arr_navigation	= array();
			
			return $arr_pages_in_nav;
			
		}
		
	}