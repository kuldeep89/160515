<?php
	
	/*
		Plugin Name: More By Author
		Plugin URI: http://www.paypromedia.com/wp/plugins/more-by-author
		Description: This plugin will include more articles by the current author.
		Author: PayProMedia
		Version: 1.0.0
		Author URI: http://paypromedia.com
	*/
	
	require_once dirname(__FILE__).'/more-by-author-model.php';
	
	class More_by_author extends WP_Widget {
		
		////////////////
		// Class Default Constructor
		////////////////
		public function __construct() {
			
			////////////////
			// Create Widget Options
			////////////////
			$arr_widget_options	= array(
				'classname'		=> 'More_by_author',	//CSS classname.
				'description'	=> 'Displays more articles by author.'	//Description in plugins.
			);
			
			////////////////
			// Instantiate widget options.
			////////////////
			$this->WP_Widget('More_by_author', 'More By Author', $arr_widget_options);
			
		}
		
		/*******************************
		**	Form
		**
		**	Description:
		**	This method is the form for the widget.
		**
		**	@param:		$arr_instance
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function form( $arr_instance ) {
			
			$arr_instance	= wp_parse_args( (array) $arr_instance, array('title' => '', 'count' => '') );
			$title			= $arr_instance['title'];
			$count			= $arr_instance['count'];
			
			echo '<p><label for="'.$this->get_field_id('title').'">Title: <input class="widefat" id="'.
				 $this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.attribute_escape($title).'"></label></p>'.
				 '<p><label for="'.$this->get_field_id('count').'">Count: <input class="widefat" id="'.$this->get_field_id('count').'"'.
				 ' name="'.$this->get_field_name('count').'" type="text" value="'.attribute_escape($count).'"></label></p>';
			
		}
		
		/*******************************
		**	Update
		**
		**	Description:
		**	This method is the update for the widget (from form).
		**
		**	@param:		$new_instance
		**	@param:		$old_instance
		**	@return:	$arr_instance
		**
		**  Author: Thomas Melvin
		**
		**/
		public function update( $arr_new_instance, $arr_old_instance ) {
		
			$arr_instance			= $arr_old_instance;
			$arr_instance['title']	= $arr_new_instance['title'];
			$arr_instance['count']	= $arr_new_instance['count'];
			
			return $arr_instance;
		
		}
		
		/*******************************
		**	Widget
		**
		**	Description:
		**	This method is the widget behavior.
		**
		**	@param:		$arr_args
		**  @param:     $arr_instance
		**	@return:	void
		**
		**  Author: Thomas Melvin
		**
		**/
		public function widget( $arr_args, $arr_instance ) {
			
			//Get widget fields.
			$title	= empty($arr_instance['title'])? ' ':apply_filters('widget_title', $arr_instance['title']);
			$count	= empty($arr_instance['count'])? 3:$arr_instance['count'];

			////////////////
			// Get more articles by the current user.
			////////////////
			$obj_widget_model	= new More_by_author_model();
			
			global $post;
			
			$arr_posts			= $obj_widget_model->get_articles_by_author($post->post_author, $count);
			
			////////////////
			// We got them, now lets display them.
			////////////////
			extract($arr_args, EXTR_SKIP);
			
			if( count($arr_posts) > 0 ) {
			
				//Wordpress stuff.
				echo $before_widget;
				
				//echo '<section id="more_articles_by_author">';
				
				//Output title, if set.
				echo (empty($title))? '':$before_title.$title.$after_title;
				echo '<ul>';
				foreach( $arr_posts as $obj_post ) {
						//echo '<div class="sidebar_article">';
							//echo '<a href="'.$obj_post->guid.'">';
							//	echo get_the_post_thumbnail($obj_post->ID, 'thumbnail', array('class'=> 'sidebar_featured_image img-responsive'));
							//echo '</a>';
							//echo '<a href="'.$obj_post->guid.'">'.$obj_post->post_title.'</a>';
						//echo '</div>';
						echo '<li>'.
								'<a href="'.$obj_post->guid.'">'.$obj_post->post_title.'</a>'.
							'</li>';
				}
				echo '</ul>';
				
				//echo '</section>';
				
				//More wordpress stuff.
				echo $after_widget;
			
			}
			
		}
		
	}

	////////////////
	// Register widget
	////////////////
	add_action( 'widgets_init', create_function('', 'return register_widget("More_by_author");') );
	
?>