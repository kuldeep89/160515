<?php
/**
 * The default template for displaying content for the support page
 * Template Name: Support
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
<!-- END PAGE TITLE & BREADCRUMB-->
 <!-- BEGIN PAGE CONTAINER-->
 <div class="container-fluid">
    <div id="dashboard">
       <div class="row-fluid">
          <div class="span12 responsive">
	          <center style="margin-top:20px;"><strong>Have a quick question? Visit the <a href="/faqs">Frequently Asked Questions</a> page to see if it's already been answered. If not, feel free to call, email or LiveChat below!</strong></center>
          </div>
       </div>
       <div class="row-fluid">
          <div class="span12 responsive">
			<div class="support_box">
				<?php if( get_field('call_image') ): ?>
				<img class="alignnone size-full" alt="icon-phone" src="<?php the_field('call_image'); ?>" width="125" height="125" />
				<?php endif; ?>
				<p><?php the_field('phone_before'); ?>  <a href="tel:<?php the_field('phone_number_link'); ?>"><?php the_field('phone_number_display'); ?></a></p>
			</div>
			<div class="support_box">
				<?php if( get_field('email_image') ): ?>
				<img class="alignnone size-full" alt="icon-imac" src="<?php the_field('email_image'); ?>" width="125" height="125" />
				<?php endif; ?>
				<p><?php the_field('email_before'); ?> <a href="mailto:support@saltsha.com"><?php the_field('email_address'); ?></a></p>
			</div>
			<div class="support_box">
				<?php if( get_field('address_image') ): ?>
				<img class="alignnone size-full" alt="icon-gift" src="<?php the_field('address_image'); ?>" width="125" height="125" />
				<?php endif; ?>
				<p><?php the_field('address_before'); ?> <?php the_field('address'); ?></p>
			</div>
	      </div>                  
       </div>
       <div class="clearfix"></div>
    </div>
 </div>
 <!-- END PAGE CONTAINER-->    
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>

		