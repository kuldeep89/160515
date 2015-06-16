                  
                        <table class="table table-striped table-bordered table-hover dataTable" id="sample_1">
                           <thead>
                              <tr>
                              <?php // Keaton: hidden checkboxes ; ?>
                                 <th style="width:8px; display:none;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
                                 <th>Title</th>
                                 <th class="hidden-480">Description</th>
                                 <th class="hidden-480">Author</th>
                                 <th class="hidden-480">Schedule Date</th>
                                 <th class="hidden-480">Action</th>
                              </tr>
                           </thead>
                           <tbody>
                           
							   <?php if( isset($obj_entry_collection) && $obj_entry_collection->size() > 0 ) : ?>
							   		
							   		<?php
							   			$arr_entries	= $obj_entry_collection->get('arr_collection');
							   		?>
							   		
							   		<?php foreach( $arr_entries as $obj_entry ) : ?>
							   			
							   			<?php if( $obj_entry->get('initial') != 1 || $obj_entry->get('author_id') == $this->current_user->get('id') || $obj_entry->get('created_by') == $this->current_user->get('id') ) : ?>
							   			
							   				<tr class="odd gradeX">
							   				<?php // Keaton: hidden checkboxes ; ?>
												<td style="display:none;"><input type="checkbox" class="checkboxes" value="<?php echo $obj_entry->get('id'); ?>" /></td>
												<td><a href="<?php echo site_url('academy/entry/'.$obj_entry->get('id')); ?>"><?php echo $obj_entry->get('title'); ?></a>
												
													<?php
														
														////////////////
														// Print Tag to notify the author/creator of this article to update it!
														////////////////
														if( $obj_entry->get('initial') == '1' ) {
															echo '<span class="label label-warning">Article needs updated!</span>';
														}
														
													?>
													
												</td>
												<td class="hidden-480"><?php echo substr($obj_entry->get('description'), 0, 75); ?></td>
												<td class="hidden-480"><?php echo $this->users_lib->user($obj_entry->get('author_id'))->get_full_name(); ?></td>
												<td class="center hidden-480"><?php echo date('l, F jS Y', $obj_entry->get('schedule_post')); ?></td>
												<td><span class="label label-success"><a href="<?php echo site_url('academy/entry/'.$obj_entry->get('id')); ?>">Edit</a> | <a href="<?php echo site_url('academy/remove-entry/'.$obj_entry->get('id')); ?>" class="confirm-delete">Delete</a></span></td>
											</tr>
											
										<?php endif; ?>
							   		
							   		<?php endforeach; ?>
                            
								<?php else : ?>
									
									<tr class="odd gradeX">
										<td colspan="6">There are currently no Academy entries.</td>
									</tr>  
									
								<?php endif; ?>
								
                           </tbody>
                        </table>
                    
                  <!-- END EXAMPLE TABLE PORTLET-->