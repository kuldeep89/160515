<?php
	
	/**
	* Module Listing Page
	* Author: Thomas Melvin
	* Date: 8/9/13
	* Notes: 
	* This is a list of modules on the system.
	*
	*/	
	
	//$arr_css[]				= '';
	//$arr_header['arr_css']	= $arr_css;

	$this->load->view('backend/includes/header');
	
?>
<div class="row-fluid">
	<div class="span12">
	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption"><i class="icon-reorder"></i>Module Listing</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
			<div class="portlet-body form">
				<div class="btn-group">
                  <a href="<?php echo site_url('permissions/create-module'); ?>" id="sample_editable_1_new" class="btn green">
                  Add New <i class="icon-plus"></i>
                  </a>
               </div>
				<?php if( isset($arr_modules) ) : ?>
	
						<table class="table table-striped table-bordered table-hover dataTable" id="users-table">
						   <thead>
						      <tr>
						         <th style="width:8px; display:none;"><input type="checkbox" class="group-checkable" data-set="#users-table .checkboxes" /></th>
						         <th>Module Name</th>
						         <th>Module Description</th>
						         <th class="hidden-480">Module ID</th>
						         <th class="hidden-480">Components</th>
						         <th class="hidden-480">Actions</th>
						      </tr>
						   </thead>
						   <tbody>
						   
							   <?php if( count($arr_modules) > 0 ) : ?>
							   		
							   		<?php foreach( $arr_modules as $arr_module ) : ?>
							   				
						   				<tr class="odd gradeX">
						   					<td style="display:none;"><input type="checkbox" class="checkboxes" value="<?php echo $arr_module['module_id']; ?>" /></td>
											<td><?php echo ucwords($arr_module['module_name']); ?></td>
											<td><?php echo $arr_module['module_description']; ?></td>
											<td><?php echo $arr_module['module_id']; ?></td>
											<td><a href="<?php echo site_url('permissions/view-components/'.$arr_module['module_id']); ?>">View Components</a></td>
											<td><span class="label label-success"><a href="<?php echo site_url('permissions/edit-module/'.$arr_module['module_id']); ?>">Edit</a> | <a href="<?php echo site_url('permissions/remove-module/'.$arr_module['module_id']); ?>" class="confirm-delete">Delete</a></span></td>
										</tr>
							   		
							   		<?php endforeach; ?>
						    
								<?php else : ?>
									
									<tr class="odd gradeX">
										<td colspan="5">There are currently no modules to display.</td>
									</tr>  
									
								<?php endif; ?>
								
						   </tbody>
						</table>
						
						<!-- END EXAMPLE TABLE PORTLET-->
					
				<?php else: ?>
				
					There are no modules!
					
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>


<?php

	$this->load->view('backend/includes/footer');