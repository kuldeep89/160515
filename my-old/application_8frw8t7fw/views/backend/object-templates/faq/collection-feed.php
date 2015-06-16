		<div class="row-fluid">
		   <div class="span12">
		   
		      <div class="span3">
		         <ul class="ver-inline-menu tabbable margin-bottom-10">
		         	<?php
		         		$first = TRUE;
		         	?>
		         	<?php if( isset($arr_categories) && count($arr_categories) ) : ?>
		         		
		         		<?php foreach( $arr_categories as $arr_category ) : ?>
		         		
				            <li class="<?php echo ($first)? 'active':''; ?>">
				               <a href="#tab_<?php echo $arr_category['faq_entry_category_id']; ?>" data-toggle="tab">
				               <i class="icon-briefcase"></i> 
				               <?php echo $arr_category['name']; ?>
				               </a> 
				               <span class="after"></span>                                    
				            </li>
				            
				            <?php
				            	$first = FALSE;
				            ?>
				            
						<?php endforeach; ?>
                     		
                     <?php else: ?>
                     	
                   		<li>
                   			<a href="#" data-toggle="tab"><i class="icon-info-sign"></i> No Categories</a></li>
                   		</li>
                     	
                     <?php endif; ?>

		         </ul>
		      </div>
		      
		      <div class="span9">
		         <div class="tab-content">
		         	
		         	<?php if( isset($arr_categories) && count($arr_categories) ) : ?>
                     		<?php
                     			$first = TRUE;
                     		?>
                     		<?php foreach( $arr_categories as $outerkey => $arr_category ) : ?>
                     			
					 			<?php
					 				$obj_category_collection	= $obj_faq_collection->get_categorized($arr_category['faq_entry_category_id']);
					 			?>

					            <div class="tab-pane <?php echo ($first)? 'active':''; ?>" id="tab_<?php echo $arr_category['faq_entry_category_id']; ?>">
					            	<?php
					            		$first	= FALSE;
					            	?>
					               <div class="accordion in collapse" id="accordion<?php echo $arr_category['faq_entry_category_id']; ?>" style="height: auto;">
					               	
					               		<?php foreach( $obj_category_collection->get('arr_collection') as $key => $obj_faq ) : ?>
					               
						                  <div class="accordion-group">
						                     <div class="accordion-heading">
						                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion<?php echo $arr_category['faq_entry_category_id']; ?>" href="#collapse_<?php echo $key.$outerkey; ?>">
						                        <?php echo $obj_faq->get('faq'); ?>
						                        </a>
						                     </div>
						                     <div id="collapse_<?php echo $key.$outerkey; ?>" class="accordion-body collapse">
						                        <div class="accordion-inner">
						                          <?php echo $obj_faq->get('response'); ?>
						                        </div>
						                     </div>
						                  </div>
										  
										  <?php endforeach; ?>
										  
					               </div>
					            </div>
								
							<?php endforeach; ?>
							
					<?php endif; ?>
								
		         </div>
		      </div>

		   </div>
		</div>