<?php

	/*
	* Author: Thomas Melvin... and Keaton - kinda. yay!
	*/

	$first = TRUE;
	
?><?php if( isset($obj_entries) && $obj_entries->size() > 0 ) : ?>[<?php foreach( $obj_entries->get('arr_collection') as $obj_entry ) : ?><?php echo ($first)? '':','; ?>["<?php echo str_replace('"', '', $obj_entry->get('title')); ?>", "<?php echo $obj_entry->get('id'); ?>"]<?php $first = FALSE; ?><?php endforeach; ?>]<?php endif; ?>