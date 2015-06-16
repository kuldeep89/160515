	<div class="top-news">
		
		<?php if( isset($obj_entry_collection) && $obj_entry_collection->size() > 0 ) : ?>
			
			<?php
			
				$arr_entries	= $obj_entry_collection->get('arr_collection');
				$arr_colors		= array('blue', 'green', 'red', 'yellow');
				$i			 	= 0;
				
			?>
			
			<?php foreach( $arr_entries as $obj_entry ) : ?>
			
				<a href="<?php echo site_url('academy/entry/'.$obj_entry->get('id')); ?>" class="btn <?php echo $arr_colors[$i]; $i++;?>">
					<span><?php echo $obj_entry->get('title'); ?></span>
					<em>Posted on: <?php echo date('F d, Y', $obj_entry->get('schedule_post')); ?></em>
					<em>
						<i class="icon-user"></i> 
						<span style=" display: inline-block; font-size: 13px;">Author: <?php echo $this->users_lib->user($obj_entry->get('author_id'))->get_full_name(); ?></span>
					</em>
					<i class="icon-book top-news-icon"></i>                             
				</a>
			
			<?php endforeach; ?>
			
		<?php else: ?>
			
			<p style="color: #777;">No related articles.</p>
		
		<?php endif; ?>

	</div>