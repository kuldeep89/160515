<?php

	//Bobbie was here.

	$arr_js[]	= 'scripts/myaccount.js';
	$arr_js[]	= 'plugins/bootstrap-fileupload/bootstrap-fileupload.js';
	

	$arr_js[]	= 'plugins/gritter/js/jquery.gritter.js';
	$arr_js[]	= 'scripts/ui-general.js';
	$arr_js[]	= 'plugins/jquery.bootpag.min.js';
	$arr_css[]	= 'plugins/bootstrap-fileupload/bootstrap-fileupload.css';
	$this->load->view('backend/includes/header', array('arr_js'=>$arr_js, 'arr_css'=>$arr_css));

?>

	<div class="tabbable tabbable-custom boxless">
     <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-toggle="tab">User Profile</a></li>
        <?php if( $this->security_lib->accessible(37) ) : ?><li><a class="" href="#tab_2" data-toggle="tab">User Permissions</a></li><?php endif; ?>
     </ul>
     <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
           <div class="portlet box blue">
              <div class="portlet-title">
                 <div class="caption"><i class="icon-reorder"></i>User Profile for <?php echo $obj_user->get_full_name(); ?></div>
                 <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                 </div>
              </div>
              <div class="portlet-body form">
              
                 <!-- BEGIN FORM-->
                <form id="myaccount-update-form" class="form-horizontal form-bordered form-row-stripped" action="<?php echo site_url('users/update-moderator'); ?>" method="post">
                
					<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $obj_user->get('id') ?>" />
					
					<div class="control-group">
						<label class="control-label">First Name</label>
						<div class="controls">
							<input type="text" name="first_name" id="first_name" value="<?php echo $obj_user->get('first_name') ?>" class="m-wrap small" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Last Name</label>
						<div class="controls">
							<input type="text" name="last_name" id="last_name" value="<?php echo $obj_user->get('last_name') ?>" class="m-wrap small" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Company</label>
						<div class="controls">
							<input type="text" name="company" id="company" value="<?php echo $obj_user->get('company') ?>" class="m-wrap small" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Address</label>
						<div class="controls">
							<input type="text" name="address" id="address" value="<?php echo $obj_user->get('address') ?>" class="m-wrap" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">City</label>
						<div class="controls">
							<input type="text" name="city" id="city" value="<?php echo $obj_user->get('city') ?>" class="m-wrap" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">State</label>
						<div class="controls">
							<select name="state" id="state"  class="m-wrap">
								<?php
									$enum_values = $this->db->query("SHOW COLUMNS FROM users LIKE 'state'")->row()->Type;
									preg_match_all("/'(.*?)'/", $enum_values, $matches);
									foreach ($matches[1] as $state) {
										if ($state == $obj_user->get('state')) {
											echo '<option value="'.$state.'" selected>'.$state.'</option>';
										} else {
											echo '<option value="'.$state.'">'.$state.'</option>';
										}
									}
								?>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Zip Code</label>
						<div class="controls">
							<input type="text" name="zip" id="zip" value="<?php echo $obj_user->get('zip') ?>" class="m-wrap" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Phone</label>
						<div class="controls">
							<input type="text" name="phone" id="phone" value="<?php echo $obj_user->get('phone'); ?>" class="m-wrap small" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Google ID</label>
						<div class="controls">
							<input type="text" name="google_id" id="google_id" value="<?php echo $obj_user->get('google_id') ?>" class="m-wrap small" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Password</label>
						<div class="controls">
							<input type="password" name="password" id="password"  class="m-wrap" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Confirm Password</label>
						<div class="controls">
							<input type="password" name="password_confirm" id="password_confirm"  class="m-wrap" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Groups</label>
						<div class="controls"> 
							<select class="span12 m-wrap" multiple="multiple" data-placeholder="Choose a Category" tabindex="1" id="arr_groups" name="arr_groups[]">
								<?php $this->load->view('backend/object-templates/users/group-options'); ?>
							</select> 
						</div>
					</div>
					
				</form>
				
				<form enctype="multipart/form-data" class="form-horizontal form-bordered form-row-stripped" method="post" action="<?php echo site_url('media/image-uploader/profile'); ?>" id="profile-image-form">
					<div class="control-group">
                      <label class="control-label">Profile Image</label>
                      <div class="controls">
                      	<br />
							<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
							<input type="hidden" name="id" id="id" value="<?php echo $obj_user->get('id') ?>" />
								 <?php if( $obj_user->get('profile_image') != FALSE ): ?>
				                  	<img alt="<?php echo $obj_user->get('first_name').'\'s profile image.'; ?>" src="<?php echo $obj_user->get('profile_image') ?>" /> &nbsp;
				                  <?php else: ?>
				                  	<img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" />
				                  <?php endif; ?>
							</div>
							<div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
							<div>
								<span class="btn btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" name="upload" /></span>
								<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
							</div>
						</div>
                      </div>
                    </div>
				</form>
				
				<div class="form-actions">
					<button type="submit" onclick="$('#myaccount-update-form').submit()" class="btn blue"><i class="icon-ok"></i> Save</button>
				</div>
				
                 <!-- END FORM--> 
                 
              </div>
           </div>
        </div>
        
        <?php if( $this->security_lib->accessible(37) ) : ?>
        
        <div class="tab-pane" id="tab_2">
           <div class="portlet box blue">
              <div class="portlet-title">
                 <div class="caption"><i class="icon-reorder"></i>User Permissions for <?php echo $obj_user->get_full_name(); ?></div>
                 <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="#portlet-config" data-toggle="modal" class="config"></a>
                    <a href="javascript:;" class="reload"></a>
                    <a href="javascript:;" class="remove"></a>
                 </div>
              </div>
              <div class="portlet-body form">
                 <!-- BEGIN FORM-->
                 <form method="post" action="<?php echo site_url('permissions/update-user-permissions'); ?>" class="horizontal-form">
                    
                    <input type="hidden" value="moderator" name="referrer" />
                    <input type="hidden" value="<?php echo $obj_user->get('id'); ?>" name="user_id" />
                    
                    <?php $this->load->view('backend/object-templates/users/user-permissions'); ?>
                    
                    <div class="form-actions">
                       <button type="submit" class="btn blue"><i class="icon-ok"></i> Save</button>
                       <button type="button" class="btn">Cancel</button>
                    </div>
                 </form>
                 <!-- END FORM--> 
              </div>
           </div>
        </div>
        
        <?php endif; ?>
        
     </div>
	</div>
	
<?php $this->load->view('backend/includes/footer'); ?>