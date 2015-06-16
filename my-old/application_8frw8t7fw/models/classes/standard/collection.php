<?php
	
	/**
	* Standard Collection
	* Author: Thomas Melvin
	* Date: 12/10/2012
	*
	*/
	
	class Standard_collection {
		
		protected $arr_collection;
		protected $arr_collection_ids;
		
		protected $CI; //CodeIgniter Reference
		/**
		* Constructor
		*
		* Default Constructor
		*
		* @access	public
		* @param	void
		* @return	void
		*
		*/
		public function __construct($arr_row = FALSE) {
			
			$this->CI	=& get_instance();
			$this->arr_collection		= array();
			
			//
			// If object data is sent, build the object.
			//	
			if( $arr_row !== FALSE ) {
				$this->build($arr_row);
			}
			
		}
		
		/**
		* Add
		*
		* Adds a item to the collection.
		*
		* @access	public
		* @param	obj_item
		* @return	void
		*
		*/
		public function add( $object ) {

			if( !isset($this->arr_collection[$object->get('id')]) ) {
				$this->arr_collection[$object->get('id')]	= $object;
			}
			
		}
		
		/**
		* Build
		*
		* Builds the object instance with passed array data.
		*
		* @access	public
		* @param	array
		* @return	void
		*
		*/
		public function build( $arr_row ) {
			
			foreach( $arr_row as $property => $value ) {
				$this->set($property, $value);
			}
			
		}
		
		/**
		* Setters
		*
		* @access	public
		* @param	value
		* @return	void
		*
		*/
		public function set( $property, $value ) {
			
			$class	= get_class($this);
			
			if( property_exists($class, $property) ) {
				
				if( method_exists($class, 'set_'.$property) ) {
					
					//
					// Call custom setter.
					//
					$this->{'set_'.$property}($value);
					
				}
				else {
					
					//
					// Set the value.
					//
					$this->{$property}	= $value;
					
				}
				
			}
			
		}
		
		/**
		* Getters
		*
		* @access	public
		* @param	void
		* @return	mixed
		*
		*/
		public function get( $property, $arr_parameters = FALSE ) {
			
			if( property_exists(get_class($this), $property) ) {
				
				if( $arr_parameters !== FALSE ) {
					return $this->{'get_'.$property}($arr_parameters);
				}
				
				return $this->{$property};
			}
			
		}
		
		/*******************************
		**	size
		**
		**	Description:
		**	This method returns the number of objects
		**  that are stored in the collection.
		**
		**	@param:		void
		**	@return:	int
		**
		**/
		public function size() {
			return count($this->arr_collection);
		}
		
	}

?>