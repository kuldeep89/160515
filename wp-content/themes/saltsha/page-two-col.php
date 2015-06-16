<?php
/*
Template Name: Two col page
*/

get_header(); ?>

<div class="row top-row">
  <div class="small-12 large-12" role="main">

		<?php while (have_posts()) : the_post(); ?>
					<?php the_content(); ?>
		<?php endwhile; ?>

	</div>
</div>
<div class="row">
	<div class="small-12 medium-6 large-5 columns">
  
    <?php $posts = get_posts ( array( 'category_name' => 'faq', 'posts_per_page' => 5, 'order' => 'ASC' ) ) ?> 
    <?php if ($posts) : ?>
    <?php foreach ($posts as $post) : setup_postdata ($post); ?>    
        <div class="post-item">
          <strong><?php the_title(); ?></strong>
          <?php the_content(); ?>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?> 

	</div>
  
  <div class="small-12 medium-6 large-5 columns">
    <?php $posts = get_posts ( array( 'category_name' => 'faq', 'posts_per_page' => 5, 'offset' => 5, 'order' => 'ASC' ) ); ?> 
    <?php if ($posts) : ?>
    <?php foreach ($posts as $post) : setup_postdata ($post); ?>    
        <div class="post-item">
          <strong><?php the_title(); ?></strong>
          <?php the_content(); ?>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?> 
  </div>
</div>
		
<?php get_footer(); ?>