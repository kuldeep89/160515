<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>

<!-- BEGIN PAGE CONTENT-->
<div class="row-fluid">
   <div class="span12">
	   <div class="prev_next"><?php previous_post_link('%link', '<&nbsp;&nbsp;Previous') ?> |  <?php next_post_link('%link', 'Next&nbsp;&nbsp;>') ?></div>
   </div>
</div><!-- end row-fluid -->
<div class="row-fluid">
	<div class="span9">
		<header class="row-fluid downloadable_resource_header">
			<div class="span12">
				<h1><?php the_title(); ?></h1>
				<div id="resources_rating">
					<?php 
						$instance = array(
						'enabled'				=> '2',
						'displaystyle'			=> 'grey',
						'displayaverage'		=> '1',
						'averageratingtext'		=> '',
						'displaytotalratings'	=> '1',
						'displaybreakdown'		=> '0' );
						if (function_exists('display_average_rating')) display_average_rating($instance); 
					?>
				</div>
			</div>
		</header>
		<!--end header-->
		<section class="resources_content">
			<figure>
				<?php the_post_thumbnail('full'); ?>
			</figure>
			<article>
				<?php show_saltsha_content(); ?>
			</article>
		</section>
	</div> <!-- end span9 / body content -->
	<aside class="span3 resources_sidebar">
		<a class="resources_visit_website" href="<?php the_field('website'); ?>" target="_BLANK">Visit Website</a>
		<div class="resources_sidebar_sections light_border_bottom">
			<h4>Categories</h4>
			<ul>
				<?php
					// Get categories
					$res_categories = wp_get_post_terms( get_the_ID(), 'resource-categories' );
					if (!isset($res_categories->errors) && count($res_categories) > 0) {
						foreach ($res_categories as $cur_category) {
				   		echo '<li><a href="'.site_url('/resource-categories/'.$cur_category->slug.'/').'" title="View all posts in '.$cur_category->name.'" rel="category tag">'.$cur_category->name.'</a></li>';
						}
					}
				?>
			</ul>
		</div>
		<div class="resources_sidebar_sections light_border_bottom">
			<h4>Pricing</h4>
			<ul>
				<li><?php the_field('pricing'); ?></li>
			</ul>
		</div>
		<div class="resources_sidebar_sections">
			<h4>Price Model</h4>
			<ul>
				<li><?php echo implode('</li><li>', get_field('price_model')); ?></li>
			</ul>
		</div>
	</aside> <!-- end span3 sidebar-->
</div><!-- end row-fluid -->
<!-- END PAGE CONTENT-->

