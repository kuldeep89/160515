<?php
	
	/**
	* Related Articles Widget Model
	* Author: Thomas Melvin
	* Date: 26 March 2014
	* Notes:
	* This is class handles reading/writing data to database.
	*
	*/
	
	class Related_articles_model {
		
		/*******************************
		**	Get Related Articles
		**
		**	Description:
		**	This method will use parameters of the post object
		**  to return a passed number of related articles.
		**
		**  @param:		<optional> $number_of_posts	(number of related posts)
		**	@return:	$arr_posts (array of posts)
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_related_articles( $num_posts = 3 ) {
			
			////////////////
			// Store original post.
			////////////////
			global $post;
			
			////////////////
			// Get the tags of the current
			// post.
			////////////////
			$arr_tags		= wp_get_post_tags($post->ID);
			$arr_tag_ids	= array();
			$arr_posts		= array();
			
			////////////////
			// Check if tags were available.
			////////////////
			if( $arr_tags ) {
				
				foreach( $arr_tags as $obj_tag ) {
					
					//Store tag ids in array.
					$arr_tag_ids[]	= $obj_tag->term_id;
					
				}
				
				////////////////
				// Specify DB arguments.
				////////////////
				$arr_args	= array(
					'tag__in'			=> $arr_tag_ids,
					'post__not_in'		=> array($post->ID),
					'posts_per_page'	=> $num_posts,
					'ignore_sticky_posts'	=> 1
				);
				
				$obj_query	= new wp_query($arr_args);
				$arr_posts	= $obj_query->get_posts();
				
			}
			
			////////////////
			// Declare array to store cateogry posts.
			////////////////
			$arr_cat_posts	= array();
			
			if( count($arr_posts) < $num_posts ) {
				
				////////////////
				// Tags yield zero posts
				// try getting by category.
				////////////////
				
				////////////////
				// Get categories
				////////////////
				$arr_category_ids		= wp_get_post_categories($post->ID);

				////////////////
				// Build DB arguments.
				////////////////
				$arr_args	= array(
					'category__in'			=> $arr_category_ids,
					'post__not_in'		=> array($post->ID),
					'posts_per_page'	=> $num_posts,
					'ignore_sticky_posts'	=> 1
				);
				
				////////////////
				// Fetch Articles form DB
				////////////////
				$obj_query		= new wp_query($arr_args);
				$arr_cat_posts	= $obj_query->get_posts();
				
			}
			
			////////////////
			// Combine array and trim extras.
			////////////////
			if( count($arr_cat_posts) > 0 ) {
				$arr_posts	= array_merge($arr_posts, $arr_cat_posts);
			}
			
			if( count($arr_posts) > $num_posts ) {
				$arr_posts	= array_splice($arr_posts, 0, $num_posts);
			}
			
			return $arr_posts;
			
		}
		
	}
	
?>