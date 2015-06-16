<?php
	
	/**
	* Related Articles Widget Model
	* Author: Thomas Melvin
	* Date: 26 March 2014
	* Notes:
	* This is class handles reading/writing data to database.
	*
	*/
	
	class More_by_author_model {
		
		/*******************************
		**	Get Articles by Author
		**
		**	Description:
		**	This method will return an array of articles by this author.
		**  Blank array for no articles.
		**
		**  @param:		<int> author_id
		**	@return:	$arr_posts (array of posts)
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_articles_by_author( $author_id = -1, $count = 3 ) {
			
			////////////////
			// Store original post.
			////////////////
			global $post;
			
			////////////////
			// Get author id.
			////////////////
			$author_id	= $post->post_author;
			$arr_posts	= array();
			
			////////////////
			// Check if author_id is passed.
			////////////////
			if( $author_id == -1 ) {
				$author_id	= $post->post_author;
			}
			
			////////////////
			// Retrieve authors posts.
			////////////////
			$arr_args	= array(
				'author'			=> $author_id,
				'posts_per_page'	=> $count,
				'post__not_in'		=> array($post->ID)
			);
			
			$obj_query	= new WP_Query($arr_args);
			$arr_posts	= $obj_query->get_posts();
			
			////////////////
			// Make sure we have posts.
			////////////////
			if( count($arr_posts) > 0 ) {
				return $arr_posts;
			}
			else {
				return array();
			}
			
			
		}
		
	}
	
?>