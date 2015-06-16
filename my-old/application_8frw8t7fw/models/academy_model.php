<?php
	
	/**
	* Academy Model
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This model will work with the database to
	* add/update/remove data associated with the academy model.
	*
	*/
	
	require_once dirname(__FILE__).'/classes/collections/Academy_entries_collection.php';
	require_once dirname(__FILE__).'/classes/objects/Academy_entry_class.php';
	
	class Academy_model extends CI_Model {
		
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
		public function add_category( $name, $description = FALSE, $color = FALSE, $icon = FALSE ) {
			
			$name = ucwords(trim($name));
			
			if( empty($name) ) {
				return FALSE;
			}
			
			////////////////
			// Build Insert Data
			////////////////
			$arr_fields	= array('name', 'description', 'color', 'icon');
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				
				if( $$field !== FALSE ) {
					$arr_data[$field]	= $$field;
				}
				
			}
			
			$this->db->insert('academy_entry_categories', $arr_data);
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
			
			$arr_fields = array('title', 'content', 'author_id', 'date_created', 'created_by', 'schedule_post');
			$arr_data	= array();
			
			////////////////
			// Implemented to fix the "Could not save entry, please try again."
			////////////////
			$entry_id	= $obj_entry->get('academy_entry_id');
			
			if( isset($entry_id) && $entry_id !== FALSE ) {
				$arr_fields[]	= 'academy_entry_id';
			}
			
			foreach( $arr_fields as $field ) {
				$arr_data[$field]	= $obj_entry->get($field);
			}
			
			////////////////
			// Set Published State
			////////////////
			$arr_data['published']	= 0;
			
			$this->db->insert('academy_entries', $arr_data);
			
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
		public function add_tag( $name, $description = FALSE ) {
			
			$name	= ucwords(trim($name));
			
			if( empty($name) ) {
				return FALSE;
			}
			
			////////////////
			// Build Insert Data
			////////////////
			$arr_fields	= array('name', 'description');
			$arr_data	= array();
			
			foreach( $arr_fields as $field ) {
				
				if( $$field !== FALSE ) {
					$arr_data[$field]	= $$field;
				}
				
			}
			
			$this->db->insert('academy_entry_tags', $arr_data);
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
			
			$obj_query	= $this->db->get('academy_entry_categories');
			$arr_rows	= $obj_query->result_array();
			
			foreach( $arr_rows as $arr_row ) {
				$this->arr_all_categories[$arr_row['academy_entry_category_id']]	= $arr_row;
			}
			
			return $this->arr_all_categories;
			
		}
		
		/*******************************
		**	Get Category
		**
		**	Description:
		**	This method will return the category data that is retreived.
		**  Ex. Color, icon...
		**
		**	@param:		category_id
		**	@return:	array:FALSE
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_category( $category_id ) {
		
			////////////////
			// Retrieve category meta.
			////////////////
			$this->db->where('academy_entry_category_id', $category_id);
			$obj_query	= $this->db->get('academy_entry_categories');
			
			if( $obj_query->num_rows() > 0 ) {
				return array_pop($obj_query->result_array());
			}
			
			return FALSE;
		
		}
		
		/*******************************
		**	Get Entries
		**
		**	Description:
		**	This method returns ALL entries in the 
		**  Academy Entries table.
		**  *Defualts to caching, use force_reload boolean parameters to overwrite.
		** 
		**	@param:		boolean
		**	@return:	obj_entry_collection
		**
		**/
		public function get_entries( $limit = 999, $force_reload = FALSE, $all_entries = FALSE ) {
			
			if( $force_reload !== TRUE && isset($this->obj_entry_collection) ) {
				return $this->obj_entry_collection;
			}
			
			$this->db->order_by('schedule_post','DESC');				// orders entries by scheduled post date, most recent first
			
			if ( $all_entries == FALSE ) {								// excludes entries that aren't scheduled to be posted yet
				
				$date = getdate();
				$this->db->where('schedule_post <',$date[0]);
				$this->db->where('published', 1);
				
			}
			$obj_query				= $this->db->get('academy_entries', $limit, 0);
			
			$obj_entry_collection	= new Academy_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					
					$obj_entry_collection->add(new Academy_entry_class($arr_row));
					
					//Add author as a retrievable users to print out name/info.
					$this->users_lib->add_user_to_retrieve($arr_row['author_id']);
					
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
			$this->db->where('academy_entry_id', $entry_id);
			
			$obj_query	= $this->db->get('academy_entries');
			
			$obj_entry	= FALSE;
			
			if( $obj_query->num_rows() > 0 ) {
			
				$arr_rows	= $obj_query->result_array();
				$obj_entry	= new Academy_entry_class(array_pop($arr_rows));
				
			}
			
			// echo '<pre>'.print_r($obj_entry, true);
			
			return $obj_entry;
			
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
			
			$this->db->like('title', $search);
			$this->db->or_like('description', $search);
			$this->db->or_like('content', $search);
			
			$obj_query				= $this->db->get('academy_entries', 10);
			$obj_entry_collection	= new Academy_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					
					$obj_entry_collection->add(new Academy_entry_class($arr_row));
					
					//Add author as a retrievable users to print out name/info.
					$this->users_lib->add_user_to_retrieve($arr_row['author_id']);
					
				}
				
			}
			
			return $obj_entry_collection;
			
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
			$this->db->where('academy_entry_id', $entry_id);
			$this->db->join('academy_entry_tags', 'academy_entry_tags.academy_entry_tag_id = academy_entry_tags_assoc.academy_entry_tag_id', 'left');
			
			$obj_query	= $this->db->get('academy_entry_tags_assoc');

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
			$this->db->where('academy_entry_id', $entry_id);
			$this->db->join('academy_entry_categories', 'academy_entry_categories.academy_entry_category_id = academy_entry_categories_assoc.academy_entry_category_id', 'left');
			
			$obj_query	= $this->db->get('academy_entry_categories_assoc');
			
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
			
			$this->db->distinct('academy_entry_categories_assoc.academy_entry_id');
			$this->db->select('academy_entries.*');
			$this->db->where('academy_entries.published', 1);
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
				
					$category_id	= (is_array($arr_category))? $arr_category['academy_entry_category_id']:$arr_category;
					
					if( $first ) {
						
						$where	= '(academy_entry_categories_assoc.academy_entry_category_id = '.$category_id;
						$first = FALSE;
						
					}
					else {
						$where .= ' OR academy_entry_categories_assoc.academy_entry_category_id = '.$category_id;
					}
					
				}
				
				$where .= ')';
				
				$this->db->where($where);	
				
			}
			
			if( $omit_entry_id !== FALSE ) {
				$this->db->where('academy_entry_categories_assoc.academy_entry_id != '.$omit_entry_id);
			}
			
			$this->db->join('academy_entries', 'academy_entry_categories_assoc.academy_entry_id = academy_entries.academy_entry_id');
			$obj_query	= $this->db->get('academy_entry_categories_assoc', $limit, 0);
			
			$obj_entry_collection	= new Academy_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					$obj_entry_collection->add(new Academy_entry_class($arr_row));
					$this->users_lib->add_user_to_retrieve($arr_row['author_id']);
				}
				
			}

			return $obj_entry_collection;
			
		}
		
		/*******************************
		**	Get Number of Entries in Categories
		**
		**	Description:
		**	This method returns an array with the index being
		**  the tag ID and the value being how many entries are
		**  in that category.
		**
		**	@param:		$arr_categories
		**	@return:	array
		**
		**/
		public function get_num_entries_in_categories( $arr_categories ) {
			
			if( !is_array($arr_categories) ) {
				$arr_categories	= array(array('academy_entry_category_id' => $arr_categories));
			}
			
			$arr_rtn	= array();
			
			foreach( $arr_categories as $arr_category ) {
				
				$this->db->where('academy_entry_category_id', $arr_category['academy_entry_category_id']);
				$arr_rtn[$arr_category['academy_entry_category_id']]	= $this->db->count_all_results('academy_entry_categories_assoc');
				
			}
			
			return $arr_rtn;
			
		}
		
		/*******************************
		**	Get Number of Entries in Tags
		**
		**	Description:
		**	This method returns an array with the index being
		**  the tag ID and the value being how many entries are
		**  in that tag.
		**
		**	@param:		$arr_tags
		**	@return:	array
		**
		**/
		public function get_num_entries_in_tags( $arr_tags ) {
			
			$arr_rtn	= array();
			
			foreach( $arr_tags as $tag ) {
				
				$this->db->where('academy_entry_tag_id', $tag['academy_entry_tag_id']);
				$arr_rtn[$tag['academy_entry_tag_id']]	= $this->db->count_all_results('academy_entry_tags_assoc');
				
			}
			
			return $arr_rtn;
			
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
			
			$this->db->distinct('academy_entry_tags_assoc.academy_entry_id');
			$this->db->select('academy_entries.*');
			$this->db->where('academy_entries.published', 1);
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
					
						$where	= '(academy_entry_tags_assoc.academy_entry_tag_id = '.$tag;	
						$first	= FALSE;
						
					}
					else {
						$where .= ' OR academy_entry_tags_assoc.academy_entry_tag_id = '.$tag;
					}
					
				}
				
				$where .= ')';
				
				$this->db->where($where);	
				
			}
			
			if( $omit_entry_id !== FALSE ) {
				$this->db->where('academy_entry_tags_assoc.academy_entry_id != '.$omit_entry_id);
			}
			
			$this->db->join('academy_entries', 'academy_entry_tags_assoc.academy_entry_id = academy_entries.academy_entry_id');
			$obj_query	= $this->db->get('academy_entry_tags_assoc');
			
			$obj_entry_collection	= new Academy_entries_collection();
			
			if( $obj_query->num_rows() > 0 ) {
				
				$arr_rows	= $obj_query->result_array();
				
				foreach($arr_rows as $arr_row ) {
					$obj_entry_collection->add(new Academy_entry_class($arr_row));	
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
			
			$obj_query			= $this->db->get('academy_entry_tags');
			$this->arr_all_tags	= $obj_query->result_array();
			
			return $this->arr_all_tags;
			
		}
		
		/*******************************
		**	Remove Category
		**
		**	Description:
		**	This method removes a category from the database.
		**
		**	@param:		$category_id
		**	@return:	void
		**
		**/
		public function remove_category( $category_id ) {
			
			$this->db->where('academy_entry_category_id', $category_id);
			$this->db->delete('academy_entry_categories');
			$this->db->where('academy_entry_category_id', $category_id);
			$this->db->delete('academy_entry_categories_assoc');
			
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
			
			if( $this->security_lib->accessible(3) ) {
			
				$this->db->where('academy_entry_id', $entry_id);
				$this->db->delete('academy_entries');
				$this->db->where('academy_entry_id', $entry_id);
				$this->db->delete('academy_entry_categories_assoc');
				$this->db->where('academy_entry_id', $entry_id);
				$this->db->delete('academy_entry_tags_assoc');
			
			}
			
		}
		
		/*******************************
		**	Remove Tag
		**
		**	Description:
		**	This method removes a tag from the database.
		**
		**	@param:		$tag_id
		**	@return:	void
		**
		**/
		public function remove_tag( $tag_id ) {
			
			$this->db->where('academy_entry_tag_id', $tag_id);
			$this->db->delete('academy_entry_tags');
			$this->db->where('academy_entry_tag_id', $tag_id);
			$this->db->delete('academy_entry_tags_assoc');
			
		}
		
		/*******************************
		**	Update Category
		**
		**	Description:
		**	This method updates the academy category.
		**
		**	@param:		$arr_category
		**	@return:	void
		**
		**/
		public function update_category($arr_category) {
		
			$this->db->where('academy_entry_category_id', $arr_category['academy_entry_category_id']);
		
			//Remove id from array.
			unset($arr_category['academy_entry_category_id']);
			
			//Update table.
			$this->db->update('academy_entry_categories', $arr_category);
			
		}
		
		/*******************************
		**	Update Tag
		**
		**	Description:
		**	This method updates the academy tag.
		**
		**	@param:		$arr_tag
		**	@return:	void
		**
		**/
		public function update_tag($arr_tag) {
		
			$this->db->where('academy_entry_tag_id', $arr_tag['academy_entry_tag_id']);
		
			//Remove id from array.
			unset($arr_tag['academy_entry_tag_id']);
			
			//Update table.
			$this->db->update('academy_entry_tags', $arr_tag);
			
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
				'author_id',
				'title',
				'content',
				'description',
				'keywords',
				'schedule_post',
				'schedule_remove',
				'last_user_modified',
				'last_modified',
				'browser_title',
				'initial',
				'featured_image',
				'published'
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
			$this->db->update('academy_entries', $arr_data, 'academy_entry_id = '.$obj_entry->get('id'));
			
		}

	}