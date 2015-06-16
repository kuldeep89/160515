<?php
/*
Template Name: Bio
Author: Isaiah Arnold
*/
?>

<?php get_header(); ?>


	<?php $row = 1; ?>
	<?php if( have_rows('bio') ): ?>
	<?php the_content(); ?>
	
	
	
		<ul id="bio_body">
			
			<?php the_field('bio_top'); ?>
			<?php while( have_rows('bio') ): the_row(); ?>
			<hr>
			<?php
			$facebook	= get_sub_field('facebook_url');
			$twitter	= get_sub_field('twitter_url'); 	
			$linkedin	= get_sub_field('linked_in');
			$googleplus	= get_sub_field('google_plus');
			
			?>
			<?php if($row & 1){ ?>
			
			<div class="col-md-2">
			<?php $image = get_sub_field('employee_image');
			
			echo '<img class="employee_image" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
			
			?>
			</div> 
			<?php  }
			else{	?>
			<div class="col-md-2 mobile">
			<?php $image = get_sub_field('employee_image');
			echo '<img class="employee_image" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
			?>
			</div> 
			<?php } ?>
			<div class="col-md-10 bio_content">
			
			
			<?php
			if($row & 1){ ?>	
			<div class="employee_title">	
			<div class="bio_name"><?php the_sub_field('employee_name'); ?> </div>
			<div class="bio_job"><?php the_sub_field('employee_job'); ?></div>
			
			<br>  
			
			<ul class="social_icons_employee">
				<?php if($facebook){ ?>
				<li><a href="https://<?php echo the_sub_field('facebook_url'); ?>" target="_blank" data-original-title="Facebook" class="facebook_employee"></a></li>
				<?php } if($twitter){ ?>
				<li><a href="<?php echo the_sub_field('twitter_url'); ?>" target="_blank" data-original-title="twitter" class="twitter_employee"></a></li>
				<?php } if($googleplus){ ?>
				<li><a href="https://<?php echo the_sub_field('google_plus'); ?>" target="_blank" data-original-title="google_plus" class="googleplus_employee"></a></li>
				<?php } if($linkedin){ ?>
				<li><a href="https://<?php echo the_sub_field('linked_in'); ?>" target="_blank" data-original-title="linked_in" class="linkedin_employee"></a></li>
				<?php }?>
			</ul>
			
			<br>
			</div>
			<?php }
			else { ?>
			<div class="employee_title_right">	
			<div class="bio_name"><?php the_sub_field('employee_name'); ?> </div>
			<div class="bio_job"><?php the_sub_field('employee_job'); ?></div>
			
			<br>  
			
			<ul class="social_icons_employee social_right">
				<?php if($facebook){ ?>
				<li><a href="https://<?php echo the_sub_field('facebook_url'); ?>" target="_blank" data-original-title="Facebook" class="facebook_employee"></a></li>
				<?php } if($twitter){ ?>
				<li><a href="<?php echo the_sub_field('twitter_url'); ?>" target="_blank" data-original-title="twitter" class="twitter_employee"></a></li>
				<?php } if($googleplus){ ?>
				<li><a href="https://<?php echo the_sub_field('google_plus'); ?>" target="_blank" data-original-title="google_plus" class="googleplus_employee"></a></li>
				<?php } if($linkedin){ ?>
				<li><a href="https://<?php echo the_sub_field('linked_in'); ?>" target="_blank" data-original-title="linked_in" class="linkedin_employee"></a></li>
				<?php }?>
		
			</ul>
			
			<br>
			</div>
			<?php } ?>
			
			<div class="bio_text"> <?php the_sub_field('biography'); ?></div>
			</div>
			<?php 
			if ($row % 2 == 0) {
			
			?>
			<div class="col-md-2 desktop">
			<?php $image = get_sub_field('employee_image');
			echo '<img class="employee_right" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
			?>
			</div>
			<?php } ?>
			
			<div class="clearfix"></div>
			<?php $row = $row + 1;  ?>
			<?php endwhile; ?>
		
		</ul>
	
	<?php endif; ?>
	

<?php get_footer(); ?>
