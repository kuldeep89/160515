<?php

	/**
	* JS Entry Tags
	* Author: Thomas Melvin
	* Date: 28 June 2013
	* Notes:
	* This page generates the JS needed to
	* populate the tag element choices and initilizes the 
	* select2 jquery plugin.
	*
	*/
	
	$first	= TRUE;
		
?>
<?php if( isset($arr_select_categories) && count($arr_select_categories) > 0 ) : ?>

	<script type="text/javascript">
	
		 $(function() {
		 
			$("#select-categories").select2({
			
				tags: [		       
	
				<?php foreach( $arr_select_categories as $arr_tag ) : ?>
					
					<?php echo ($first === TRUE)? '': ', '; ?>
					
					"<?php echo $arr_tag['name']; ?>"
					
					<?php $first = FALSE; ?>
					
				<?php endforeach; ?>
				
				]
				
			});
			
		});
		
	</script>
	
<?php endif; ?>