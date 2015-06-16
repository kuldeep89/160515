<?php

	/**
	* Academy Page Class
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This object will store the attributes and mehtods associated
	* with a Page.
	*
	*/
	
	require_once dirname(dirname(__FILE__)).'/standard/class.php';
	
	class Page_class extends Standard_class {
		
		////////////////
		// DB Data Members
		////////////////
		protected $id;
		protected $page_id;
		protected $url;
		protected $name;
		protected $title;
		protected $content;
		protected $browser_title;
		protected $keywords;
		protected $description;
		protected $created_by;
		protected $created_date;
		protected $template;
		protected $icon;
		
		////////////////
		// Custom Setter to Define id
		////////////////
		public function set_page_id( $val ) {
		
			$this->id		= $val;
			$this->page_id	= $val;
			
		}
		
	}
		
?>