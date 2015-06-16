<?php if( isset($arr_groups) && count($arr_groups) > 0 ) : ?>
	
	<?php foreach( $arr_groups as $arr_group ) : ?>
		
		<option <?php 
			
			if( isset($arr_selected_groups) ) {
				echo (in_array($arr_group['id'], $arr_selected_groups))? 'selected="selected"':''; 
			}
			
		?> value="<?php echo $arr_group['id']; ?>"><?php echo ucwords($arr_group['name']); ?></option>
		
	<?php endforeach; ?>
	
<?php endif; ?>