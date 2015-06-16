<?php
	
	/**
	* Add Member
	* Author: Thomas Melvin
	* Date: 1 August 2013
	* Notes:
	* This view presents a form to add a user to the system.
	*
	*/	
	
	$arr_css[]				= 'plugins/select2/select2_metro.css';
	$arr_header['arr_css']	= $arr_css;

	$this->load->view('backend/includes/header', $arr_header);
	
?>

        <div class="tab-pane">
           <div class="portlet box blue">
              <div class="portlet-title">
                 <div class="caption"><i class="icon-reorder"></i>Add Moderator</div>
                 <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                 </div>
              </div>
              <div class="portlet-body form">
                 <!-- BEGIN FORM-->
                <form id="add-moderator" action="<?php echo site_url('permissions/update-group-permissions'); ?>" class="form-horizontal form-bordered form-row-stripped" method="post">
               	 <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
               	 	<?php if( isset($obj_all_permissions) ) : ?>
					
						<?php foreach( $obj_all_permissions->get('arr_collection') as $obj_module ) : ?>
						
							<h2 class="form-section"><?php echo ucwords($obj_module->get('module_name')); ?></h2>
							<div class="row-fluid">
								
								<?php foreach( $obj_module->get('obj_component_collection')->get('arr_collection') as $obj_component ) : ?>
								
									<?php if( $obj_component->get('obj_permissions_collection')->size() > 0 ) : ?>
									
									<div class="span2">
									
										<h4 class="form-section"><?php echo $obj_component->get('component_name'); ?></h4>
										<ul class="unstyled well">
										
										<?php foreach( $obj_component->get('obj_permissions_collection')->get('arr_collection') as $obj_permission ) : ?>
										
											<li>
												<label>
												
													<i data-original-title="<?php echo ucwords($obj_module->get('module_name')).' > '.ucwords($obj_component->get('component_name')).' > '.$obj_permission->get('permission_name'); ?>" data-trigger="hover" data-content="<?php echo $obj_permission->get('permission_description'); ?>" data-placement="bottom" class="icon-info-sign icon popovers"></i>
													
													<input <?php echo $obj_permission->is_set($arr_group_permissions); ?> type="checkbox" name="<?php echo $obj_module->get('module_id').'_'.$obj_component->get('id').'_'.$obj_permission->get('id'); ?>" /><?php echo $obj_permission->get('permission_name'); ?>
												
												</label>
											</li>
										
										<?php endforeach; ?>
										</ul>
									
									</div>
									
									<?php endif; ?>
									
								<?php endforeach; ?>
							
							</div>
							
						<?php endforeach; ?>
					
					<?php else: ?>
						
						<h4 class="form-section">No Permissions</h4>
						
					<?php endif; ?>
               	
					<div class="form-actions">
					<button type="submit" onclick="$('#myaccount-update-form').submit()" class="btn blue"><i class="icon-ok"></i> Save</button>
					<a href="<?php echo site_url('users'); ?>" class="btn yellow"><i class="icon-ban-circle"></i> Cancel</a>
				</div>
				</form>
				
                 <!-- END FORM--> 
              </div>
           </div>
        </div>



<?php

	$this->load->view('backend/includes/footer');