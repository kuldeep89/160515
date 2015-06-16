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
		$arr_js[]	= 'scripts/resource_manage_tags.js';
		
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
		                            Resource Entry Categories
		                        </div>
		                    </div>
		
		                    <div class="portlet-body">
		                    	<div class="btn-group">
	                              <a href="<?php echo site_url('resource/add-category'); ?>" id="sample_editable_1_new" class="btn green">
	                              Add New <i class="icon-plus"></i>
	                              </a>
	                           </div>
		                        <table class="table table-striped table-bordered table-hover" id="tags-table">
									<thead>
									
										<tr>
											<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
											<th class="hidden-480">Category ID</th>
											<th>Category Name</th>
											<th class="hidden-480">Categorized Entries</th>
											<th class="hidden-480">Action</th>
											<th >View Page</th>
										</tr>
										
									</thead>
		                            <tbody>
		                            	
		                            	<?php if( isset($arr_categories) && count($arr_categories) > 0 ) : ?>

									   		<?php foreach( $arr_categories as $arr_category ) : ?>
									   			
									   			
									   				<tr class="odd gradeX">
									   				
									   					<td><input type="checkbox" class="checkboxes" value="<?php echo $arr_category['resource_entry_category_id']; ?>" /></td>
									   					<td><?php echo $arr_category['resource_entry_category_id']; ?></td>
														<td><?php echo $arr_category['name']; ?></td>
														<td><?php echo $arr_num_in_categories[$arr_category['resource_entry_category_id']]; ?></td>
														<td><span class="label label-success"><a href="<?php echo site_url('resource/edit-category/'.$arr_category['resource_entry_category_id']); ?>">Edit</a> | <a href="<?php echo site_url('resource/remove-category/'.$arr_category['resource_entry_category_id']); ?>" class="confirm-delete">Delete</a></span></td>
														<td><a href="<?php echo site_url('resource/category/'.$arr_category['resource_entry_category_id']); ?>">View Tagged Entries</a></td>
													</tr>
									   		
									   		<?php endforeach; ?>
		                            
										<?php else : ?>
											
											<tr class="odd gradeX">
												<td colspan="6">There are currently no resource entry tags.</td>
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
		
		