                  
                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                           <thead>
                              <tr>
                                 <th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
                                 <th>FAQ</th>
                                 <th class="hidden-480">Response</th>
                                 <th class="hidden-480">Created By</th>
                                 <th class="hidden-480">Created Date</th>
                                 <th class="hidden-480">Action</th>
                              </tr>
                           </thead>
                           <tbody>
                           
							   <?php if( isset($obj_entry_collection) && $obj_entry_collection->size() > 0 ) : ?>
							   		
							   		<?php
							   			$arr_entries	= $obj_entry_collection->get('arr_collection');
							   		?>
							   		
							   		<?php foreach( $arr_entries as $obj_entry ) : ?>
							   		
							   				<tr class="odd gradeX">
												<td><input type="checkbox" class="checkboxes" value="<?php echo $obj_entry->get('id'); ?>" /></td>
												<td><a href="<?php echo site_url('faq/entry/'.$obj_entry->get('id')); ?>"><?php echo substr($obj_entry->get('faq'), 0, 75); ?></a></td>
												<td class="hidden-480"><?php echo substr($obj_entry->get('response'), 0, 75); ?></td>
												<td class="hidden-480"><?php echo $this->users_lib->user($obj_entry->get('created_by'))->get_full_name(); ?></td>
												<td class="center hidden-480"><?php echo date('l, F jS Y', $obj_entry->get('schedule_post')); ?></td>
												<td><span class="label label-success"><a href="<?php echo site_url('faq/entry/'.$obj_entry->get('id')); ?>">Edit</a> | <a href="<?php echo site_url('faq/remove-entry/'.$obj_entry->get('id')); ?>" class="confirm-delete">Delete</a></span></td>
											</tr>
							   		
							   		<?php endforeach; ?>
                            
								<?php else : ?>
									
									<tr class="odd gradeX">
										<td colspan="6">There are currently no FAQ entries.</td>
									</tr>  
									
								<?php endif; ?>
								
                           </tbody>
                        </table>
                    
                  <!-- END EXAMPLE TABLE PORTLET-->