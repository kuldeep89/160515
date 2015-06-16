<?php
	
	/**
	* FAQ Model
	* Author: Thomas Melvin
	* Date: 15 July 2013
	* Notes:
	* This model will work with the database to
	* add/update/remove data associated with the FAQs.
	*
	*/
	
	require_once dirname(__FILE__).'/classes/collections/Faq_entries_collection.php';
	require_once dirname(__FILE__).'/classes/objects/Faq_entry_class.php';
	
	class Faq_model extends CI_Model {
		
		////////////////
		// Cached Data Members
		////////////////
		protected $obj_entry_collection;
		protected $arr_all_tags;
		protected $arr_all_categories;
		
		public function __construct() {
			parent::__construct();
		}
		
		/*******************************
		**	Add Category
		**
		**	Description:
		**	Adds a category to the system and returns it's insert ID.
		**
		**	@param:		category <string>
		**	@return:	boolean
		**
		**/
		public function add_category( $category ) {
			
			$category = ucwords(trim($category));
			
			if( empty($category) ) {
				return FALSE;
			}
			
			$this->db->insert('faq_entry_categories', array('name' => $category));
			return $this->db->insert_id();
			
		}
		
		/*******************************
		**	Add Entry
		**
		**	Description:
		**	Add's an entry to the database and returns the entry_id
		**
		**	@param:		$obj_entry
		**	@return:	$entry_id <int>
		**
		**/
		public function add_entry( $obj_entry ) {
			
			$arr_fields = array('faq', 'response', 'date_created', 'created_by');
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field]	= $obj_entry->get($field);
			}
			
			$this->db->insert('faq_entries', $arr_data);
			
			return $this->db->insert_id();
			
		}
		
		/*******************************
		**	Add Tag
		**
		**	Description:
		**	This method adds a tag to the DB
		**
		**	@param:		tag <string>
		**	@return:	int
		**
		**/
		public function add_tag( $tag ) {
			
			$tag	= ucwords(trim($tag));
			
			if( empty($tag) ) {
				return FALSE;
			}
			
			$this->db->insert('faq_entry_tags', array('name' => $tag));
			return $this->db->insert_id();	
			
		}
		
		/*******************************
		**	Get Categories
		**
		**	Description:
		**	This method returns all categories added to the system.
		**  *Defualts to caching, use force_reload boolean parameters to overwrite.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_categories( $force_reload = FALSE ) {
			
			if( $force_reload !== TRUE && isset($this->arr_all_categories) ) {
				return $this->arr_all_categories;
			}
			
			$obj_query	= $this->db->get('faq_entry_categories');
			$arr_rows	= $obj_query->result_array();
			
			foreach( $arr_rows as $arr_row ) {
				$this->arr_all_categories[$arr_row['faq_entry_category_id']]	= $arr_row;
			}
			
			return $this->arr_all_categories;
			
		}
		
		/*******************************
		**	Get Entries
		**
		**	Description:
		**	This method returns ALL entries in the 
		**  Faq Entries table.
		**  *Defualts to caching, use force_reload boolean parameters to overwrite.
		** 
		**	@param:		boolean
		**	@return:	obj_entry_collection
		**
		**/
		public function get_entries( $force_reload = FALSE ) {
			
			if( $force_reload !== TRUE && isset($this->obj_entry_collection) ) {
				return $this->obj_entry_collection;
			}
			
			$this->db->order_by('date_created','DESC');				// orders entries by scheduled post date, most recent first
			
			$obj_query				= $this->db->get('faq_entries');
			$obj_entry_collection	= new Faq_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					
					$obj_entry_collection->add(new Faq_entry_class($arr_row));
					
					//Add author as a retrievable users to print out name/info.
					$this->users_lib->add_user_to_retrieve($arr_row['created_by']);
					
				}
				
			}
			
			////////////////
			// Cache Entries
			////////////////
			$this->obj_entry_collection	= $obj_entry_collection;
			
			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Get Entry
		**
		**	Description:
		**	This method returns a specific entry that matches
		**  the entry id that was passed to it.
		**
		**	@param:		$entry_id
		**	@return:	$obj_entry:FALSE
		**
		**/
		public function get_entry( $entry_id ) {
			
			////////////////
			// Check Data
			////////////////
			if( !is_numeric($entry_id) || $entry_id < 0 ) {
				return FALSE;
			}
			
			////////////////
			// Get Entry
			////////////////
			$this->db->where('faq_entry_id', $entry_id);
			$obj_query	= $this->db->get('faq_entries');
			
			$obj_entry	= FALSE;
			
			if( $obj_query->num_rows() > 0 ) {
			
				$arr_rows	= $obj_query->result_array();
				$obj_entry	= new Faq_entry_class(array_pop($arr_rows));
				
			}
			
			return $obj_entry;
			
		}
		
		/*******************************
		**	Get Entry('s) Tags
		**
		**	Description:
		**	This method returns an array listing of entry tags
		**  for the entry associated with the passed ID.
		**
		**	@param:		$entry_id
		**	@return:	array
		**
		**/
		public function get_entry_tags( $entry_id ) {
			
			////////////////
			// Retreive the list from the table.
			////////////////
			$this->db->where('faq_entry_id', $entry_id);
			$this->db->join('faq_entry_tags', 'faq_entry_tags.faq_entry_tag_id = faq_entry_tags_assoc.faq_entry_tag_id', 'left');
			
			$obj_query	= $this->db->get('faq_entry_tags_assoc');

			if( $obj_query->num_rows() > 0 ) {
				return $obj_query->result_array();
			}
			else {
				return array();
			}
			
		}
		
		/*******************************
		**	Get Entry('s) Categories
		**
		**	Description:
		**	This method returns an array listing of entry categories
		**  for the entry associated with the passed ID.
		**
		**	@param:		$entry_id
		**	@return:	array
		**
		**/
		public function get_entry_categories( $entry_id ) {
			
			////////////////
			// Retreive the list from the table.
			////////////////
			$this->db->where('faq_entry_id', $entry_id);
			$this->db->join('faq_entry_categories', 'faq_entry_categories.faq_entry_category_id = faq_entry_categories_assoc.faq_entry_category_id', 'left');
			
			$obj_query	= $this->db->get('faq_entry_categories_assoc');
			
			if( $obj_query->num_rows() > 0 ) {
				return $obj_query->result_array();
			}
			else {
				return array();
			}
			
		}
		
		/*******************************
		**	Get Related Entries
		**
		**	Description:
		**	This method returns entries that are similar to the one passed.
		**  If none are found then the most recent entries are returned.
		**
		**	@param:		$arr_categories
		**	@return:	$obj_entry_collection
		**
		**/
		public function get_categorized_entries( $arr_categories, $omit_entry_id = FALSE, $limit = FALSE ) {
			
			////////////////
			// Make sure it's an array
			////////////////
			if( !is_array($arr_categories) ) {
				$arr_categories	= array($arr_categories);
			}
			
			if( $limit === FALSE ) {
				$limit = 999;
			}
			
			$this->db->distinct('faq_entry_categories_assoc.faq_entry_id');
			$this->db->select('faq_entries.*');
			
			////////////////
			// Now get 5 of the most recent entries that are in these categories.
			////////////////
			$first	= TRUE;
			$where	= '';
			
			////////////////
			// Build Query Manually
			////////////////
			if( count($arr_categories) > 0 ) {
				
				foreach( $arr_categories as $arr_category ) {
				
					$category_id	= (is_array($arr_category))? $arr_category['faq_entry_category_id']:$arr_category;
					
					if( $first ) {
						
						$where	= '(faq_entry_categories_assoc.faq_entry_category_id = '.$category_id;
						$first = FALSE;
						
					}
					else {
						$where .= ' OR faq_entry_categories_assoc.faq_entry_category_id = '.$category_id;
					}
					
				}
				
				$where .= ')';
				
				$this->db->where($where);	
				
			}
			
			if( $omit_entry_id !== FALSE ) {
				$this->db->where('faq_entry_categories_assoc.faq_entry_id != '.$omit_entry_id);
			}
			
			$this->db->join('faq_entries', 'faq_entry_categories_assoc.faq_entry_id = faq_entries.faq_entry_id');
			$obj_query	= $this->db->get('faq_entry_categories_assoc', $limit, 0);
			
			$obj_entry_collection	= new Faq_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					$obj_entry_collection->add(new Faq_entry_class($arr_row));	
				}
				
			}

			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Get Tagged Entries
		**
		**	Description:
		**	This method returns entries that have
		**  been tagged with the passed tag.
		**
		**	@param:		$arr_tag_name
		**	@return:	obj_entry_collection
		**
		**/
		public function get_tagged_entries( $tag, $omit_entry_id = FALSE ) {
			
			////////////////
			// Make it an Array Form
			////////////////
			$arr_tags	= FALSE;
			
			if( !is_array($tag) ) {
				$arr_tags[]	= $tag;
			}
			else {
				$arr_tags	= $tag;
			}
			
			////////////////
			// Get Entries
			////////////////
			$this->db->distinct('faq_entry_tags_assoc.faq_entry_id');
			$this->db->select('faq_entries.*');
			
			////////////////
			// Now get 5 of the most recent entries that are in these tags.
			////////////////
			$first	= TRUE;
			$where	= '';
			
			////////////////
			// Build Query Manually
			////////////////
			if( count($arr_tags) > 0 ) {
				
				foreach( $arr_tags as $tag ) {
					
					if( $first ) {
					
						$where	= '(faq_entry_tags_assoc.faq_entry_tag_id = '.$tag;	
						$first	= FALSE;
						
					}
					else {
						$where .= ' OR faq_entry_tags_assoc.faq_entry_tag_id = '.$tag;
					}
					
				}
				
				$where .= ')';
				
				$this->db->where($where);	
				
			}
			
			if( $omit_entry_id !== FALSE ) {
				$this->db->where('faq_entry_tags_assoc.faq_entry_id != '.$omit_entry_id);
			}
			
			$this->db->join('faq_entries', 'faq_entry_tags_assoc.faq_entry_id = faq_entries.faq_entry_id');
			$obj_query	= $this->db->get('faq_entry_tags_assoc');
			
			$obj_entry_collection	= new Faq_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					$obj_entry_collection->add(new Faq_entry_class($arr_row));	
				}
				
			}
			
			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Get Tags
		**
		**	Description:
		**	This method returns all tags added to the system.
		**  *Defualts to caching, use force_reload boolean parameters to overwrite.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_tags( $force_reload = FALSE ) {
			
			if( $force_reload !== TRUE && isset($this->arr_all_tags) ) {
				return $this->arr_all_tags;
			}
			
			$obj_query			= $this->db->get('faq_entry_tags');
			$this->arr_all_tags	= $obj_query->result_array();
			
			return $this->arr_all_tags;
			
		}
		
		/*******************************
		**	Get Entries Like
		**
		**	Description:
		**	This method returns entries matching the passed string
		**
		**	@param:		$search <string>
		**	@return:	obj_collection
		**
		**/
		public function get_entries_like( $search ) {
			
			$this->db->like('faq', $search);
			$this->db->or_like('response', $search);
			
			$obj_query				= $this->db->get('faq_entries', 10);
			$obj_entry_collection	= new Faq_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					
					$obj_entry_collection->add(new Faq_entry_class($arr_row));
					
					//Add author as a retrievable users to print out name/info.
					$this->users_lib->add_user_to_retrieve($arr_row['created_by']);
					
				}
				
			}
			
			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Remove Entry
		**
		**	Description:
		**	Removes entry from the database as well as it's
		**  associations with categories and tags.
		**
		**	@param:		entry_id
		**	@return:	void
		**
		**/
		public function remove_entry( $entry_id ) {
			
			$this->db->where('faq_entry_id', $entry_id);
			$this->db->delete('faq_entries');
			$this->db->where('faq_entry_id', $entry_id);
			$this->db->delete('faq_entry_categories_assoc');
			$this->db->where('faq_entry_id', $entry_id);
			$this->db->delete('faq_entry_tags_assoc');
			
		}
		
		/*******************************
		**	Update Entry
		**
		**	Description:
		**	This method updates the entry passed in the database.
		**
		**	@param:		$obj_entry
		**	@return:	void
		**
		**/
		public function update_entry( $obj_entry ) {
			
			////////////////
			// Updatable Fields
			////////////////
			$arr_fields	= array(
				'faq',
				'response'
			);
			
			////////////////
			// Array to Update DB
			////////////////
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field] = $obj_entry->get($field);
			}
			
			////////////////
			// Save Tags/Categories
			////////////////
			$obj_entry->save_tags();
			$obj_entry->save_categories();
			
			////////////////
			// Now Insert into DB
			////////////////
			$this->db->update('faq_entries', $arr_data, 'faq_entry_id = '.$obj_entry->get('id'));
			
		}
		
	}