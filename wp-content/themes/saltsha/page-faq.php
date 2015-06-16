<?php
/*
Template Name: FAQ
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
	<div class="large-12 columns">
		
			<?php
				if( have_rows('faq') ):
					
					echo '<ul class="large-block-grid-2">';
				 	// loop through the rows of data
				    while ( have_rows('faq') ) : the_row();
				
				        // display a sub field value
				        $faq_title = get_sub_field('faq_title');
				        $faq_content = get_sub_field('faq_content');
						
						echo '<li class="faq_block">';
							echo "<h5>$faq_title<h5>";
							echo "<p>$faq_content</p>";
						echo '</li>';
						
				    endwhile;
				
					echo '</ul>';
				else :
				
				    // no rows found
				
				endif;
			?>
	</div>
</div>
		
<?php get_footer(); ?>