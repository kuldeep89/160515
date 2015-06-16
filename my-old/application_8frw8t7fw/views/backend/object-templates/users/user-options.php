<?php

	$user_id	= -1;
	
	if( isset($selected_user) ) {
		$user_id	= $selected_user;
	}
	
?>
<?php if( isset($obj_user_collection) && count($obj_user_collection->size() > 0 ) ) : ?>

	<?php foreach( $obj_user_collection->get('arr_collection') as $obj_user ) : ?>
		
			<option value="<?php echo $obj_user->get('id'); ?>" <?php echo ( $obj_user->get('id') == $user_id )? 'selected':''; ?>><?php echo $obj_user->get_full_name(); ?></option>
		
	<?php endforeach; ?>

<?php endif; ?>