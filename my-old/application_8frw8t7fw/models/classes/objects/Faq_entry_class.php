<?php

	/**
	* Faq Entry Class
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a Faq Entry.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class Faq_entry_class extends Standard_class {
		
		////////////////
		// Object Data Members
		////////////////
		protected $id;
		protected $faq_entry_id;
		protected $created_by;
		protected $faq;
		protected $response;
		protected $date_created;

		////////////////
		// Generated Data Members
		////////////////
		protected $arr_tags;
		protected $arr_categories;
		protected $arr_update_tags;
		protected $arr_update_categories;

		/*******************************
		**	Get Categories
		**
		**	Description:
		**	This method returns a array of categories that are
		**  associated to it.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_categories( $force_reload = FALSE ) {
			
			//Is this already loaded?
			if( isset($this->arr_categories) && !$force_reload ) {
				return $this->arr_categories;
			}
			
			$this->arr_categories	= $this->CI->faq_model->get_entry_categories($this->id);
			
			return $this->arr_categories;
			
		}
		
		public function get_category_ids() {
			
			$arr_categories	= $this->get_categories();
			$arr_ids		= array();
			
			foreach( $arr_categories as $arr_category ) {
				$arr_ids[$arr_category['faq_entry_category_id']] = $arr_category['faq_entry_category_id'];
			}
			
			return $arr_ids;
			
		}
		
		/*******************************
		**	Get Category Names
		**
		**	Description:
		**	This method returns a listing of just category names, no category_id.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_category_names() {
			
			////////////////
			// Get the tags.
			////////////////
			$arr_all_categories	= $this->get_categories();
			
			////////////////
			// Build array with just names.
			////////////////
			$arr_names	= array();
			
			foreach( $arr_all_categories as $arr_category ) {
				$arr_names[]	= $arr_category['name'];
			}
			
			return $arr_names;
			
		}
		
		/*******************************
		**	Get Snippet
		**
		**	Description:
		**	This method trims a string down to the set number of characters. -Keaton
		**
		**	@param:		string
		**	@return:	string
		**
		**/
		public function get_snippet( $length = 300 ) {
			
			$snippet = $this->get('content');
			
			if (strlen($snippet)>$length) {								// if entry length is longer than passed $length...
				$snippet = trim(strip_tags(substr($snippet, 0, $length))).'...';	// truncates down to $length, trims off white space, adds ellipsis
			}
			
			return $snippet;
		}
		
		/*******************************
		**	Get Tags
		**
		**	Description:
		**	This method get's the classes tags from the database.
		**
		**	@param:		void
		**	@return:	obj_tag_collection
		**
		**/
		public function get_tags( $force_reload = FALSE ) {
			
			//Is this this already loaded?
			if( isset($this->arr_tags) && !$force_reload ) {
				return $this->arr_tags;
			}
			
			$this->arr_tags	= $this->CI->faq_model->get_entry_tags($this->id);
			
			return $this->arr_tags;
			
		}
		
		/*******************************
		**	Get Tag Names
		**
		**	Description:
		**	This method returns a listing of just tag names, no tag_id.
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_tag_names() {
			
			////////////////
			// Get the tags.
			////////////////
			$arr_all_tags	= $this->get_tags();
			
			////////////////
			// Build array with just names.
			////////////////
			$arr_names	= array();
			
			foreach( $arr_all_tags as $arr_tag ) {
				$arr_names[]	= $arr_tag['name'];
			}
			
			return $arr_names;
			
		}
		
		/*******************************
		**	Save
		**
		**	Description:
		**	This method updates the object's changes
		**  to the database.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function save() {
			$this->CI->faq_model->update_entry($this);
		}
		
		/*******************************
		**	Save Categories
		**
		**	Description:
		**	This method will save the categories
		**  that have been marked as add to the table.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function save_categories() {

			if( count($this->arr_update_categories) > 0 ) {
				
				////////////////
				// Remove all categories from this entry.
				////////////////
				$this->CI->db->delete('faq_entry_categories_assoc', 'faq_entry_id = '.$this->get('id'));
				
				////////////////
				// Get All Categories
				////////////////
				$arr_categories	= $this->CI->faq_model->get_categories();
				$arr_names		= array();
				
				////////////////
				// Get Names
				////////////////
				foreach( $arr_categories as $arr_category ) {
					$arr_names[$arr_category['faq_entry_category_id']]	= $arr_category['name'];
				}
				
				$arr_category_ids	= array_flip($arr_names);
				
				foreach( $this->arr_update_categories as $category ) {
				
					$category	= trim($category);
					
					if( empty($category) ) {
						continue; //Go to next loop iteration.
					}
					
					////////////////
					// Check to see if category exists.
					////////////////
					if( in_array($category, $arr_names) ) {
						$this->CI->db->insert('faq_entry_categories_assoc', array('faq_entry_id' => $this->get('id'), 'faq_entry_category_id' => $arr_category_ids[$category]));
					}
					else {
						
						////////////////
						// Add this category to the system first.
						////////////////
						$this->CI->db->insert('faq_entry_categories_assoc', array('faq_entry_id' => $this->get('id'), 'faq_entry_category_id' => $this->CI->faq_model->add_category($category)));
						
					}
					
				}
					
			}
			
		}
		
		/*******************************
		**	Save Tags
		**
		**	Description:
		**	This method will save tags that have been marked
		**  as add to the database.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		public function save_tags() {
			
			if( count($this->arr_update_tags) > 0 ) {
				
				////////////////
				// Remove all tags from this entry.
				////////////////
				$this->CI->db->delete('faq_entry_tags_assoc', 'faq_entry_id = '.$this->get('id'));
				
				////////////////
				// Get All Tags
				////////////////
				$arr_tags	= $this->CI->faq_model->get_tags();
				$arr_names	= array();
				
				////////////////
				// Get Names
				////////////////
				foreach( $arr_tags as $arr_tag ) {
					$arr_names[$arr_tag['faq_entry_tag_id']]	= $arr_tag['name'];
				}
				
				$arr_tag_ids	= array_flip($arr_names);
				
				foreach( $this->arr_update_tags as $tag ) {
					$tag	= trim($tag);
					if( empty($tag) ) {
						continue; //Go to next loop iteration.
					}
					
					////////////////
					// Check to see if tag exists.
					////////////////
					if( in_array($tag, $arr_names) ) {
						$this->CI->db->insert('faq_entry_tags_assoc', array('faq_entry_id' => $this->get('id'), 'faq_entry_tag_id' => $arr_tag_ids[$tag]));
					}
					else {
						
						////////////////
						// Add this tag to the system first.
						////////////////
						$this->CI->db->insert('faq_entry_tags_assoc', array('faq_entry_id' => $this->get('id'), 'faq_entry_tag_id' => $this->CI->faq_model->add_tag($tag)));
						
					}
					
				}
					
			}
			
		}
		
		/*******************************
		**	Set Content
		**
		**	Description:
		**	This overrites the default functionality
		**  and filters out unwanted characters.
		**
		**	@param:		$content
		**	@return:	void
		**
		**/
		public function set_content( $content ) {
			
			// First, replace UTF-8 characters.
			$content = str_replace(
				array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
				array("'", "'", '"', '"', '-', '--', '...'),
				$content);
				
			// Next, replace their Windows-1252 equivalents.
			$content = str_replace(
				array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
				array("'", "'", '"', '"', '-', '--', '...'),
				$content);
			
			$this->content	= $content;
			
		}
		
		/*******************************
		**	Set Faq Entry ID
		**
		**	Description:
		**	This method overrites the default
		**  setter to set the $id attributes for collections.
		**
		**	@param:		$id
		**	@return:	void
		**
		**/
		public function set_faq_entry_id( $id ) {
		
			$this->faq_entry_id	= $id;
			$this->id				= $id;
			
		}
		
		/*******************************
		**	Set Categories
		**
		**	Description:
		**	This method sets new categories for the entry.
		**
		**	@param:		array
		**	@return:	void
		**
		**/
		public function set_categories( $arr_categories ) {
			$this->arr_update_categories	= $arr_categories;
		}
		
		/*******************************
		**	Set Tags
		**
		**	Description:
		**	This method sets new tags for the entry.
		**
		**	@param:		array
		**	@return:	void
		**
		**/
		public function set_tags( $arr_tags ) {
			$this->arr_update_tags	= $arr_tags;
		}
		
		/*******************************
		**	Update Fields
		**
		**	Description:
		**	This method updates the entries' fields with the passed
		**  associative array values.
		**
		**	@param:		array
		**	@return:	void
		**
		**/
		public function update_fields( $arr_data ) {
			
			foreach( $arr_data as $field => $value ) {
				$this->set($field, $value);
			}
			
		}
		
	}
		
?>