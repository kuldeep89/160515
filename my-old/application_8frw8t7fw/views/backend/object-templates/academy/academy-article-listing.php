<?php
	
?>
		<!-- Entry -->
        <div class="news-blocks">
        
            <h3><a href="<?php echo site_url('academy/entry/'.$obj_entry->get('id')); ?>"><?php echo $obj_entry->get('title'); ?></a></h3>

            <div class="news-block-tags">
                <em><?php echo time_since($obj_entry->get('schedule_post')); ?> ago.</em>
            </div>

            <p>
            	<?php $name	= $obj_entry->get('name'); ?>
            	<?php if( !empty($name) && $obj_entry->get('featured_image') != null) : ?>
	        		<img class="news-block-img pull-right" src="<?php echo $obj_entry->get('featured_image'); ?>" alt="Academy Entry Featured Image" />
	        	<?php endif; ?>
            <?php echo $obj_entry->get_snippet(); ?></p>
            <a href="<?php echo site_url('academy/entry/'.$obj_entry->get('id')); ?>" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>
            
        </div>
        <!-- End of Entry -->