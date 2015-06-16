<?php

	/**
	* Navigation Library
	* Author: Thomas Melvin
	* Date: 12 July 2013
	* Notes:
	* This library will be used to translate the 
	* JSON navigation into arrays used to output the pages
	* in various navigations.
	*
	*/
	class Navigation_lib {
		
		protected $arr_pages;
		protected $CI;
		
		public function __construct() {
			
			////////////////
			// Get CI Instance
			////////////////
			$this->CI	=& get_instance();
			
		}
		
		public function json_to_array( $json, $obj_page_collection = FALSE ) {
			
			if( empty($json) ) {
				return array();
			}
			
			////////////////
			// Get JSON Object
			////////////////
			$obj_json		= json_decode($json, TRUE);
			
			return $obj_json;
			
		}
		
		/*******************************
		**	Print Edit Navigation
		**
		**	Description:
		**	This prints out the listin for configuring the navigation in
		**  the navigation manager.
		**
		**	@param:		$arr_order
		**  @param:		$obj_page_collection
		**	@return:	void
		**
		**/
		public function print_edit_navigation( $arr_order, $obj_pages ) {
			
			$this->arr_pages	= $obj_pages->get('arr_collection');
			
			foreach( $arr_order as $id => $arr_children ) {
				$this->print_page($arr_children);
			}
			
		}
		
		public function print_page( $arr_page ) {
			
			if( isset($arr_page['children']) ) {
				
				echo '<li class="dd-item" data-id="'.$arr_page['id'].'">';
					echo '<div class="dd-handle">'.$this->arr_pages[$arr_page['id']]->get('name').'</div>';
					echo '<ol class="dd-list">';
						
						foreach( $arr_page['children'] as $arr_child ) {
							$this->print_page($arr_child);
						}
						
					echo '</ol>';
				echo '</li>';
				
			}
			else {
					
				echo '<li class="dd-item" data-id="'.$arr_page['id'].'">';
					echo '<div class="dd-handle">'.$this->arr_pages[$arr_page['id']]->get('name').'</div>';
				echo '</li>';
				
			}
			
		}
		
		/*******************************
		**	Print User Navigation
		**
		**	Description:
		**	This method retrieves and prints the
		**  the user naviation associated with the 
		**  navigation ID passed to it.
		**
		**	@param:		nav_id
		**	@return:	void
		**
		**/
		public function print_user_navigation( $nav_id = 1 ) {
			
			$this->CI->load->model('pages_model');
			$obj_pages				= $this->CI->pages_model->get_pages();
			$arr_user_navigation	= $this->CI->pages_model->get_navigation($nav_id);
			$this->arr_pages		= $obj_pages->get('arr_collection');
			$arr_user_navigation	= $this->json_to_array($arr_user_navigation['navigation']);
			
			foreach( $arr_user_navigation as $arr_page ) {
				$this->print_navigation($arr_page);
			}
			
		}
		
		/*******************************
		**	Print Navigation
		**
		**	Description:
		**	This method prints out the left sidebar navigation
		**  for the array that was passed to it.
		**
		**	@param:		$arr_nav
		**	@return:	void
		**
		**/
		public function print_navigation( $arr_page ) {
			
			$url		= ( (strstr($this->arr_pages[$arr_page['id']]->get('url'), '/')) === FALSE )? strtolower($this->CI->router->class):strtolower($this->CI->router->class.'/'.$this->CI->router->method);
			$url		= trim($url);
			$page_url	= trim(strtolower($this->arr_pages[$arr_page['id']]->get('url')));
			$open		= ( $url == $page_url )? 'class="open active"':'';
			
			if( isset($arr_page['children']) && count($arr_page['children']) > 0 || $this->arr_pages[$arr_page['id']]->get('name') == 'Academy' ) {
				
				echo '<li '.$open.'><a href="javascript:;"><i class="icon-'.$this->arr_pages[$arr_page['id']]->get('icon').'"></i><span class="title">'.$this->arr_pages[$arr_page['id']]->get('name').'</span></a>';
				echo '<ul class="sub-menu">';
					
					
					if( isset($arr_page['children']) ) {

						foreach( $arr_page['children'] as $arr_child ) {
						
							if( isset($arr_child['children']) ) {
								$this->print_navigation($arr_child);
							}
							else {
								
								$open	= ( ($this->arr_pages[$arr_page['id']]->get('name') == 'Academy') && $this->CI->uri->segment(3) == FALSE )? 'class="open active"':'';
								echo '<li '.$open.'><a href="'.site_url($this->arr_pages[$arr_child['id']]->get('url')).'">'.$this->arr_pages[$arr_child['id']]->get('name').'</a></li>';
								
							}
							
						}
						
					}
						
					if( $this->arr_pages[$arr_page['id']]->get('name') == 'Academy' ) {
						
						$this->print_academy_categories();
						
					}
					
				echo '</ul>';
				echo '</li>';
				
			}
			else {
				
				echo '<li '.$open.'><a href="'.site_url($this->arr_pages[$arr_page['id']]->get('url')).'"><i class="icon-'.$this->arr_pages[$arr_page['id']]->get('icon').'"></i><span class="title">'.$this->arr_pages[$arr_page['id']]->get('name').'</span></a></li>';
				
			}
			
		}
		
		/*******************************
		**	Print Academy Categories
		**
		**	Description:
		**	This method prints ou the academy categories.
		**
		**	@param:		void
		**	@return:	void
		**
		**/
		private function print_academy_categories() {
			
			$this->CI->load->model('academy_model');
			$arr_categories	= $this->CI->academy_model->get_categories();
			
			foreach( $arr_categories as $arr_category ) {

				$open	= ( $this->CI->uri->segment(3) == $arr_category['academy_entry_category_id'] )? 'class="active"':'';
				echo '<li '.$open.'><a href="'.site_url('academy/category/'.$arr_category['academy_entry_category_id']).'">'.$arr_category['name'].'</a></li>';
				
			}
			
		}
		
	}
