<?php
	
	// Require this file so we can access $wpdb
	require('../../../wp-blog-header.php'); 
	$loop = new WP_Query( array( 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id' ) );
	
	$arr_posts	= $loop->posts;
	
	foreach( $arr_posts as $obj_post ) {
		
		
		
		if( preg_match('/^<a href=/', $obj_post->post_content) ) {
			
			echo '<hr>';
			echo '<h1>'.$obj_post->post_title.'</h1>';
			echo $obj_post->post_content;
			
		}

	}
	
?>