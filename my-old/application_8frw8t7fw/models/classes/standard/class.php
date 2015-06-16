<?php
	
	/**
	* Standard Class
	* Author: Thomas Melvin
	* Date: 12/10/2012
	*
	*/
	
	class Standard_class {
		
		protected $CI;
		
		/**
		* Default Constructor
		*
		* Optional arr_row from database to build the class instance.
		*
		* @access	public
		* @param	arr_row:void
		* @return	void
		*
		*/
		public function __construct( $arr_row = FALSE ) {
			
			//
			// If object data is sent, build the object.
			//	
			if( $arr_row !== FALSE ) {
				$this->build($arr_row);
			}
			
			$this->CI	=& get_instance();
			
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
		public function get( $property ) {
			
			if( property_exists(get_class($this), $property) ) {
				return $this->{$property};
			}
			
		}
		
	}

?>