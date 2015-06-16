<h4><?php the_sub_field('callout_title'); ?></h4>
<p><?php the_sub_field('callout_content'); ?></p>
<?php if(get_sub_field('callout_button_text')): ?>  
	<div class="row">
		<?php if(get_sub_field('use_alt_layout')): ?>
			<div class="calloutLink">
		<?php else: ?>	
		<span>
			<div class="col-sm-7 col-xs-12 button">
		<?php endif; ?>
			<?php if(!get_sub_field('use_external_link')): ?>
				<a href="<?php the_sub_field('callout_link'); ?>">
			<?php else: ?>
				<a href="<?php the_sub_field('external_link'); ?>" target="_blank">
			<?php endif; ?>
				<?php the_sub_field('callout_button_text'); ?>
			</a>
		</div>
		<?php if(!get_sub_field('use_alt_layout')): ?>
		</span>
		<?php endif; ?>
	</div>
<?php endif; ?>

