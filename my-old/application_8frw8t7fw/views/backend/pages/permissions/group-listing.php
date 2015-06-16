<?php
	
	/**
	* Group Listing Page
	* Author: Thomas Melvin
	* Date: 8/9/13
	* Notes: 
	* This is a listing of groups for the user to select to edit
	* it's permissions.
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
				<div class="caption"><i class="icon-reorder"></i>Group Listing</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
			<div class="portlet-body form">

				<?php if( isset($arr_groups) ) : ?>
	
						<table class="table table-striped table-bordered table-hover dataTable" id="users-table">
						   <thead>
						      <tr>
						         <th style="width:8px; display:none;"><input type="checkbox" class="group-checkable" data-set="#users-table .checkboxes" /></th>
						         <th>Group Name</th>
						         <th>Group Description</th>
						         <th class="hidden-480">Group ID</th>
						         <th class="hidden-480">Permissions</th>
						         <th class="hidden-480">Actions</th>
						      </tr>
						   </thead>
						   <tbody>
						   
							   <?php if( count($arr_groups) > 0 ) : ?>
							   		
							   		<?php foreach( $arr_groups as $arr_group ) : ?>
							   				
						   				<tr class="odd gradeX">
						   					<td style="display:none;"><input type="checkbox" class="checkboxes" value="<?php echo $arr_group['id']; ?>" /></td>
											<td><?php echo ucwords($arr_group['name']); ?></td>
											<td><?php echo $arr_group['description']; ?></td>
											<td><?php echo $arr_group['id']; ?></td>
											<td>View Components</td>
											<td><a href="<?php echo site_url('permissions/edit-group-permissions/'.$arr_group['id']); ?>">Edit Permissions</a></td>
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