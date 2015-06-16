<?php
	
	/*
		Plugin Name: Related Articles Widget
		Plugin URI: http://www.paypromedia.com/wp/plugins/related-articles-widget
		Description: This plugin creates a Related Articles Widget.
		Author: PayProMedia
		Version: 1.0.0
		Author URI: http://paypromedia.com
	*/
	
	require_once dirname(__FILE__).'/related-articles-model.php';
	
	class Related_articles_widget extends WP_Widget {
		
		////////////////
		// Class Default Constructor
		////////////////
		public function __construct() {
			
			////////////////
			// Create Widget Options
			////////////////
			$arr_widget_options	= array(
				'classname'		=> 'Related_articles_widget',
				'description'	=> 'Displays related articles.'
			);
			
			////////////////
			// Instantiate widget options.
			////////////////
			$this->WP_Widget('Related_articles_widget', 'Related Posts', $arr_widget_options);
			
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
				 '<p><label for="'.$this->get_field_id('count').'">Count: <input class="widefat" id="'.
				 $this->get_field_id('count').'" name="'.$this->get_field_name('count').'" type="text" value="'.attribute_escape($count).'"></label></p>';
			
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
			
			//Get widget data.
			$title	= empty($arr_instance['title'])? ' ':apply_filters('widget_title', $arr_instance['title']);
			$count	= empty($arr_instance['count'])? 3:$arr_instance['count'];
			
			////////////////
			// Retrieve list of related articles.
			////////////////
			$obj_model	= new Related_articles_model();
			$arr_posts	= $obj_model->get_related_articles($count);
			
			////////////////
			// Make sure we actually have some.
			////////////////
			if( count($arr_posts) > 0 ) {

				////////////////
				// We got them, now lets display them.
				////////////////
				extract($arr_args, EXTR_SKIP);
				
				//Wordpress stuff.
				echo $before_widget;
				
				//Output title, if set.
					echo (empty($title))? '':$before_title.$title.$after_title;
					echo '<ul class="related_posts">';
						foreach( $arr_posts as $obj_post ) {
							echo '<li><a href="'.$obj_post->guid.'">'.$obj_post->post_title.'</a></li>';
						}
					echo '</ul';
				//More wordpress stuff.
				echo $after_widget;

			}
			
		}
		
	}

	////////////////
	// Register widget
	////////////////
	add_action( 'widgets_init', create_function('', 'return register_widget("Related_articles_widget");') );
	
?>