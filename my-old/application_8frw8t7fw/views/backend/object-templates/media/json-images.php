<?php $first = TRUE; ?>
<?php if( isset($arr_images) && count($arr_images) ) : ?>
	
	[
	
	<?php foreach( $arr_images as $arr_image ) : ?>
		
		<?php echo (!$first)? ',':''; ?>
		<?php $first = FALSE; ?>
		
		{
			"thumb": "<?php echo $arr_image['thumb']; ?>",
			"image": "<?php echo $arr_image['name']; ?>"
		}
		
	<?php endforeach; ?>
	
	]
	
<?php endif; ?>