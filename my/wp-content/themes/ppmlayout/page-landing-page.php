<?php
/**
 * The default template for displaying content for Landing Pages
 * Template Name: Banner Landing Page
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
 
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-breadcrumb-heading">
	<h3 class="page-title">
		<?php the_title(); ?>
		<small>
			<?php if( function_exists('the_field') ) {the_field('page_subtitle');} ?>
		</small>
	</h3>
	<ul class="breadcrumb">
		<li>
			<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
		</li>	
	</ul>
</div>
 <div class="container-page">
	<div class="row-fluid">
		<div class="span12 responsive">
			<?php 
				
				$banner_image = get_field('banner_image');
				if( !empty($banner_image) ){
					echo '<img class="banner_image" src="'.$banner_image['url'].'" alt="'.$banner_image['alt'].'" />';
				}
				
			?>
			<div class="page_content">
				<?php 
					the_content(); 
				?>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span8">
			<h4>Related Articles</h4>
			
			<?php

				if( have_rows('related_articles') ):
					echo '<ul class="related_articles">';
					
					    while ( have_rows('related_articles') ) : the_row();
							$href = get_sub_field('href');
							$link_text = get_sub_field('link_text');
							$date_posted = get_sub_field('date_posted');
							echo	'<li>'.
										'<a href="'.$href.'" target="_blank">'.$link_text.'</a>'.
										'<small>Posted on: '.$date_posted.'</small>'.
									'</li>';
					
					    endwhile;
					    
				    echo '</ul>';
				endif;
			
			?>
		</div>
		<div class="span4">
			<?php 
				$cta_image = get_field('sidebar_cta_image');
				$cta_link = get_field('sidebar_cta_link');
				if( !empty($cta_image) ): 
			?>
				<a href="<?php echo $cta_link; ?>" target="_blank" class="sidebar_cta">
					<img src="<?php echo $cta_image['url']; ?>" alt="<?php echo $cta_image['alt']; ?>" />
				</a>
			<?php endif; ?>
		</div>             
	</div>
 </div>
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>

		