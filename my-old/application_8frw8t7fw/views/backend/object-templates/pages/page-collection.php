
                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                           <thead>
                              <tr>
                                 <th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
                                 <th>Title</th>
                                 <th class="hidden-480">Description</th>
                                 <th class="hidden-480">Author</th>
                                 <th class="hidden-480">Created Date</th>
                                 <th class="hidden-480">Action</th>
                              </tr>
                           </thead>
                           <tbody>

							   <?php if( isset($obj_page_collection) && $obj_page_collection->size() > 0 ) : ?>

							   		<?php
							   			$arr_pages	= $obj_page_collection->get('arr_collection');
							   		?>

							   		<?php foreach( $arr_pages as $obj_page ) : ?>

							   				<tr class="odd gradeX">
												<td><input type="checkbox" class="checkboxes" value="<?php echo $obj_page->get('id'); ?>" /></td>
												<td><a href="<?php echo site_url('pages/page/'.$obj_page->get('id')); ?>"><?php echo $obj_page->get('name'); ?></a></td>
												<td class="hidden-480"><?php echo substr($obj_page->get('description'), 0, 75); ?></td>
												<td class="hidden-480"><?php echo $this->users_lib->user($obj_page->get('created_by'))->get_full_name(); ?></td>
												<td class="center hidden-480"><?php echo date('j M Y', $obj_page->get('created_date')); ?></td>
												<td><span class="label label-success"><a href="<?php echo site_url('pages/page/'.$obj_page->get('id')); ?>">Edit</a> | <a style="color: #fff !important;" onclick="return confirm_delete();" href="<?php echo site_url('pages/remove-page/'.$obj_page->get('id')); ?>">Delete</a></span></td>
											</tr>

							   		<?php endforeach; ?>

								<?php else : ?>

									<tr class="odd gradeX">
										<td colspan="6">There are currently no pages.</td>
									</tr>

								<?php endif; ?>

                           </tbody>
                        </table>

                  <!-- END EXAMPLE TABLE PORTLET-->