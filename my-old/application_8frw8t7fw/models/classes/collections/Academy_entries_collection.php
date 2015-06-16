<?php
	
	/**
	* Academy Entries Collection
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This collection will store entries retrieved from
	* the database and methods and data members associated
	* with Academy Entries.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/collection.php';
	
	class Academy_entries_collection extends Standard_collection {
		
		////////////////
		// Optinally Generated Fields
		////////////////
		protected $category;
		protected $category_id;
		protected $arr_tags;
		protected $color;
		protected $icon;
		protected $code;			// Keaton: added to Frankenstein customizable dashboard with eval()'s
		
		/*******************************
		**	Filter Out
		**
		**	Description:
		**	This method returns an array of entries that
		**  don't have ID's contained in the exception array passed.
		**
		**	@param:		$arr_exceptions
		**	@return:	$arr_entry_objects
		**
		**/
		public function filter_out( $exceptions ) {
			
			////////////////
			// Make sure it's an array.
			////////////////
			if( !is_array($exceptions) ) {
				$arr_exceptions[] = $exceptions;
			}
			else {
				$arr_exceptions = $exceptions;
			}
			$arr_collection	= $this->arr_collection;
			
			////////////////
			// Filters out undesires entries.
			////////////////
			$obj_entry_collection	= new Academy_entries_collection();
			
			foreach( $arr_collection as $id => $obj_entry ) {
				
				if( !in_array($id, $arr_exceptions) ) {
					$obj_entry_collection->add($obj_entry);
				}
				
			}
			
			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Get Related
		**
		**	Description:
		**	This method returns a collection of entries
		**  that are related to the categories passed to it.
		**
		**	@param:		$arr_categories
		**	@return:	$obj_entry_collection
		**
		**/
		public function get_categorized( $category_ids ) {
			
			$arr_categories	= array();
			
			if( !is_array($category_ids) ) {
				$arr_categories	= array($category_ids);
			}
			else {
				$arr_categories	= $category_ids;	
			}
			
			$arr_collection	 	= $this->get('arr_collection');
			$obj_new_collection	= new Academy_entries_collection();
			
			$arr_category_ids	= $this->get_category_ids();
		
			foreach( $arr_collection as $obj_entry ) {
				
				$arr_entry_categories	= $obj_entry->get_category_ids();
				
				foreach( $arr_categories as $arr_category ) {
					
					if( in_array($arr_category, $arr_entry_categories) ) {
						$obj_new_collection->add($obj_entry);
					}
					
				}
				
			}
						
			return $obj_new_collection;
			
		}
		
		/*******************************
		**	Get Category IDs
		**
		**	Description:
		**	This method returns a array listing of all the
		**  category ID's contained in the entry listing.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_category_ids() {
			
			$arr_collection	= $this->get('arr_collection');
			$arr_return		= array();
			
			foreach( $arr_collection as $obj_entry ) {
				
				$arr_tmp_cats	= $obj_entry->get_categories();
				
				foreach( $arr_tmp_cats as $arr_cat ) {
					$arr_return[$arr_cat['academy_entry_category_id']]	= $arr_cat['academy_entry_category_id'];
				}
				
			}
			
			return $arr_return;
			
		}
		
		/*******************************
		**	Get Columns
		**
		**	Description:
		**	This method returns a 2d array of entry objects.
		**
		**	@param:		columns
		**	@return:	array
		**
		**/
		public function get_columns( $columns ) {
			
			////////////////
			// Prepare Columns Array
			////////////////
			$arr_columns	= array();
			
			for( $x = 0; $x < $columns; $x++ ) {
				$arr_columns[$x]	= array();
			}
			
			$arr_collections			= $this->get_categories();
			$obj_cat_entry_collection	= null;
			
			////////////////
			// Sort Columns Evenly
			////////////////
			$smallest = 0;
			 
			while( ($obj_cat_entry_collection = $this->get_largest($arr_collections)) !== FALSE ) {
				
				foreach( $arr_columns as $key => $arr_column ) {
					
					if( $this->column_size($arr_column) <= $this->column_size($arr_columns[$smallest]) ) {
						$smallest = $key;
					}
					
				}
				
				$arr_columns[$smallest][]	= $obj_cat_entry_collection;
				
			}
			
			return $arr_columns;
			
		}
		
		/*******************************
		**	Column Size
		**
		**	Description:
		**	This returns this size of a column in the
		**  entry feed columns.
		**
		**	@param:		array
		**	@return:	int
		**
		**/
		private function column_size( $arr_column ) {
			
			$size	= 0;
			
			foreach( $arr_column as $obj_entry_collection ) {
				$size += $obj_entry_collection->size();
			}

			return $size;
			
		}
		
		/*******************************
		**	Get Categories
		**
		**	Description:
		**	Returns an array of collections that associate
		**  entries to categories.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_categories() {
			
			$this->CI->load->model('academy_model');
			
			$arr_categories		= $this->CI->academy_model->get_categories();
			$arr_category_ids	= $this->get_category_ids();
			
			$arr_category_collections	= array();
			
			////////////////
			// Build collections based on categories.
			////////////////
			foreach( $arr_category_ids as $category_id ) {
				
				$arr_category_collections[$category_id] = $this->get_categorized($category_id);
				$arr_category_collections[$category_id]->set('category', $arr_categories[$category_id]['name']);
				$arr_category_collections[$category_id]->set('category_id', $arr_categories[$category_id]['academy_entry_category_id']);
				
			}
			
			return $arr_category_collections;
			
		}
		
		/*******************************
		**	Get Largest
		**
		**	Description:
		**	Returns the largest collection based on entries in category.
		**
		**	@param:		$arr_collections
		**	@return:	obj_collection
		**
		**/
		private function get_largest( &$arr_collections ) {
			
			$largest	= FALSE;
			
			if( is_array($arr_collections) && count($arr_collections) > 0 ) {
				
				foreach( $arr_collections as $key => $obj_collection ) {
					
					if( $largest === FALSE ) {
						$largest = $key;
					}
					
					if( $obj_collection->size() >= $arr_collections[$largest]->size() ) {
						$largest = $key;
					}
					
				}

			}
			
			if( $largest !== FALSE ) {
				
				$rtn	= $arr_collections[$largest];
				unset($arr_collections[$largest]);
				
				return $rtn;
				
			}
			else {
				return FALSE;
			}
			
		}
		
		/*******************************
		**	Get Tags
		**
		**	Description:
		**	This method returns the tags associated with the
		**  articles collected in it.
		**
		**	@param:		void
		**	@return:	$arr_tags
		**
		**/
		public function get_tags() {
			
			if( isset($this->arr_tags) ) {
				return $this->arr_tags;
			}
			
			$arr_collection	= $this->arr_collection;
			$arr_rtn		= array();
			
			foreach( $arr_collection as $obj_entry ) {
				
				$arr_tags	= $obj_entry->get_tags();
				
				foreach( $arr_tags as $arr_tag ) {
					$arr_rtn[$arr_tag['academy_entry_tag_id']]	= $arr_tag['name'];
				}
				
			}
			
			$this->arr_tags	= $arr_rtn;
			
			return $arr_rtn;
			
		}
		
		/*******************************
		**	Get Arr Collection
		**
		**	Description:
		**	This method overwrites the default
		**  behavior of the get('arr_collection')
		**
		**	@param:		$limit <int>
		**  @param:		$order_by
		**	@return:	arr_collection
		**
		**/
		public function get_arr_collection( $arr_parameters ) {
			
			////////////////
			// Set parameters
			////////////////
			$limit		= FALSE;
			$order_by	= FALSE;
			
			if( isset($arr_parameters['limit']) ) {
				$limit		= $arr_parameters['limit'];
			}
			else if( isset($arr_parameters['order_by']) ) {
				$order_by	= $arr_parameters['order_by'];
			}
			
			$arr_order_by	= $order_by;
			
			if( $order_by !== FALSE ) {

				if( !is_array($order_by) ) {
					$arr_order_by	= $order_by;
				}
				
			}
			
			if( $limit === FALSE ) {
				return $this->arr_collection;
			}
			
			////////////////
			// Limit the post
			////////////////
			$arr_collection	= $this->arr_collection;
			$arr_rtn		= array();
			$x				= 0;
			
			foreach( $arr_collection as $obj_entry ) {
				
				$x++;
				
				$arr_rtn[$obj_entry->get('id')]	= $obj_entry;
				
				if( $x >= $limit ) {
					break;
				}
				
			}
			
			return $arr_rtn;
			
		}
		
		/*******************************
		**	Get Meta
		**
		**	Description:
		**	This method will return meta data for this category, such as
		**  color and icon of this category.
		**
		**	@param:		void
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_meta() {
		
			////////////////
			// Return meta data from database.
			////////////////
			//Check if category id is set.
			if( !isset($this->category_id) ) {
				
				//Try to get the category id, based off entries in the arr_collection?
				$this->color = 'light-grey';
				$this->icon	= 'icon-bookmark';
				
			}
			
			$arr_category	= $this->CI->academy_model->get_category($this->category_id);
			
			$this->color	= $arr_category['color'];
			$this->icon		= $arr_category['icon'];
			
		}
		
	}