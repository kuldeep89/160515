<?php 

require('../../../wp-blog-header.php'); 

$ppp = 8;
$post_count = wp_count_posts('resource')->publish;

$target = $_POST['target'];
$pagination = $_POST['page'];

// Returns query based on action taken on archive-resource.php
switch ($target) {
	case 'f-featured':
		query_posts(
			array (	'posts_per_page' => 4,
			        'post_type' => 'resource',
			        'resource-categories' => 'Featured'
			 	  )
		);
		break;
	case 'f-newest':
		query_posts(
			array (	'posts_per_page' => 4,
	 				'post_type' => 'resource',
	 				'orderby' => 'post_date'
			 	  )
		);
		break;
	case 'f-rating':
		query_posts(
			array (	'posts_per_page' => 4,
	 				'post_type' => 'resource',
	 				'orderby' => 'meta_value_num',
	 				'meta_key' => 'crfp-average-rating'
			 	  )
		);
		break;
	case 'alphabetical':
		query_posts(
			array (	'posts_per_page' => $ppp,
	 				'post_type' => 'resource',
	 				'orderby' => 'title',
	 				'order' => 'ASC',
	 				'offset' => $pagination
			 	  )
		);
		break;
	case 'newest':
		query_posts(
			array (	'posts_per_page' => $ppp,
	 				'post_type' => 'resource',
	 				'orderby' => 'post_date',
	 				'offset' => $pagination
			 	  )
		);
		break;
	case 'rating':
		query_posts(
			array (	'posts_per_page' => $ppp,
	 				'post_type' => 'resource',
	 				'orderby' => 'meta_value_num',
	 				'meta_key' => 'crfp-average-rating',
	 				'offset' => $pagination
			 	  )
		);
		break;
	default:
		query_posts(
			array (	'posts_per_page' => $ppp,
	 				'post_type' => 'resource',
	 				'offset' => $pagination
			 	  )
		);
		break;
	
}

// Returns template contents instead of echoing it out so that it can be passed as a variable.
function load_template_part($template_name, $part_name=null) {
    ob_start();
    get_template_part($template_name, $part_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

$html = '';

while ( have_posts() ) : the_post();
	$html .= load_template_part( 'content', 'resource' ); 
endwhile;

$prev = '';
$next = '';

// Passes variable that's used to disable buttons if no more resources are available in the clicked direction.
if ($pagination - $ppp < 0) {
	$prev = 'inactive';
}
if ($pagination + $ppp >= $post_count ) {
	$next = 'inactive';
}

$data = json_encode(array('next'=>$next, 'prev'=>$prev));
$response = array('html'=>$html, 'data'=>$data);
echo json_encode($response);

?>
