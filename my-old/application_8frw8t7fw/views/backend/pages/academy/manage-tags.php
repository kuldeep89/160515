<?php

/**
 * Entries
 * Author: Thomas Melvin
 * Date: 26th June 2013
 * Notes:
 * This template will display the passed blog_entry_collection.
 *
 */

?>

	<?php
		
		
		////////////////
		// JS Files
		////////////////
		$arr_js[]	= 'scripts/index.js';
		$arr_js[]	= 'plugins/select2/select2.min.js';
		$arr_js[]	= 'plugins/data-tables/jquery.dataTables.js';
		$arr_js[]	= 'plugins/data-tables/DT_bootstrap.js';
		$arr_js[]	= 'scripts/academy_manage_tags.js';
		
		////////////////
		// Build Head/Footer Arrays
		////////////////
		$arr_footer 	= array('arr_js' => $arr_js);
		

		
		$this->load->view('backend/includes/header');
	?>

		<div class="row-fluid">
		    <div class="span12">
		        <div class="row-fluid">
		            <div class="span12">
		                <!-- BEGIN EXAMPLE TABLE PORTLET-->
		
		                <div class="portlet box light-grey">
		                    <div class="portlet-title">
		                        <div class="caption">
		                            Academy Entry Tags
		                        </div>
		                    </div>
		
		                    <div class="portlet-body">
		                    	<div class="btn-group">
	                              <a href="<?php echo site_url('academy/add-tag'); ?>" id="sample_editable_1_new" class="btn green">
	                              Add New <i class="icon-plus"></i>
	                              </a>
	                           </div>
		                        <table class="table table-striped table-bordered table-hover" id="tags-table">
									<thead>
									
										<tr>
											<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
											<th class="hidden-480">Tag ID</th>
											<th>Tag Name</th>
											<th class="hidden-480">Tagged Entries</th>
											<th class="hidden-480">Action</th>
											<th >View Page</th>
										</tr>
										
									</thead>
		                            <tbody>
		                            	
		                            	<?php if( isset($arr_tags) && count($arr_tags) > 0 ) : ?>

									   		<?php foreach( $arr_tags as $arr_tag ) : ?>
									   			
									   			
									   				<tr class="odd gradeX">
									   				
									   					<td><input type="checkbox" class="checkboxes" value="<?php echo $arr_tag['academy_entry_tag_id']; ?>" /></td>
									   					<td><?php echo $arr_tag['academy_entry_tag_id']; ?></td>
														<td><?php echo $arr_tag['name']; ?></td>
														<td><?php echo $arr_num_in_tags[$arr_tag['academy_entry_tag_id']]; ?></td>
														<td><span class="label label-success"><a href="<?php echo site_url('academy/edit-tag/'.$arr_tag['academy_entry_tag_id']); ?>">Edit</a> | <a href="<?php echo site_url('academy/remove-tag/'.$arr_tag['academy_entry_tag_id']); ?>" class="confirm-delete">Delete</a></span></td>
														<td><a href="<?php echo site_url('academy/tag/'.$arr_tag['academy_entry_tag_id']); ?>">View Tagged Entries</a></td>
													</tr>
									   		
									   		<?php endforeach; ?>
		                            
										<?php else : ?>
											
											<tr class="odd gradeX">
												<td colspan="6">There are currently no Academy entry tags.</td>
											</tr>  
											
										<?php endif; ?>
		                            	
		                            </tbody>
		                        </table>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div><!-- END PAGE CONTAINER-->
		</div><!-- END PAGE -->
    
    <?php
		$this->load->view('backend/includes/footer', $arr_footer);
		
		