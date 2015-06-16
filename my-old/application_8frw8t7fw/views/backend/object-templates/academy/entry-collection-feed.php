<div class="row-fluid">
    <div class="span12 news-page">
        <div class="row-fluid">
        	
        	<?php
        		
        		$arr_sizes	= array(5,4,3);
        		$x = -1;
        		
        	?>
        	
        	<?php if( isset($arr_column_collections) && count($arr_column_collections) > 0 ) : ?>
        	
        		<?php foreach( $arr_column_collections as $arr_entry_collections ) : ?>
					<?php
						$x++;
					?>
					<div class="span<?php echo $arr_sizes[$x]; ?>">
						
						<?php foreach( $arr_entry_collections as $obj_entry_collection ) : ?>
							
							<?php
								$arr_tags	= $obj_entry_collection->get_tags();
							?>
						
		        			<!-- Category Title -->
			                <div class="top-news">
			                	<?php
			                		$obj_entry_collection->get_meta();
			                	?>
			                    <a href="<?php echo site_url('academy/category/'.$obj_entry_collection->get('category_id')); ?>" class="btn <?php echo ($obj_entry_collection->get('color'))? $obj_entry_collection->get('color'):category_color($obj_entry_collection->get('category_id')); ?>"><span><?php echo $obj_entry_collection->get('category'); ?></span>
			                    <em><?php echo implode(', ', array_splice($arr_tags, 0, 3)); ?></em> <i class="<?php echo ($obj_entry_collection->get('icon'))? $obj_entry_collection->get('icon'):category_icon($obj_entry_collection->get('category_id')); ?> top-news-icon"></i></a>
			                </div>
			                <!-- End of Category Title -->
		                
			                <?php foreach( $obj_entry_collection->get('arr_collection', array('limit' =>8)) as $obj_entry ) : ?>
			                	
			                	<?php $this->load->view('backend/object-templates/academy/academy-article-listing', array('obj_entry'=>$obj_entry)); ?>
			                	
			                <?php endforeach; ?>
			                
			            <?php endforeach; ?>
					
					</div><!-- End of Span4 -->
					
        		<?php endforeach; ?>
        		
        	<?php endif; ?> 
        
        </div><!-- END PAGE CONTENT-->
    </div><!-- END PAGE CONTAINER-->
</div><!-- END PAGE -->
