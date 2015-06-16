<?php
	$arr_js[]	= 'scripts/myaccount.js';
	$arr_js[]	= 'plugins/bootstrap-fileupload/bootstrap-fileupload.js';
	$arr_css[]	= 'plugins/bootstrap-fileupload/bootstrap-fileupload.css';
	$this->load->view('backend/includes/header', array('arr_js'=>$arr_js, 'arr_css'=>$arr_css));
?>

<div class="portlet box blue tabbable">
     <div class="portlet-title">
        <div class="caption">
           <i class="icon-reorder"></i>
           <span class="hidden-480">My Info</span>
        </div>
     </div>
     <div class="portlet-body form">
		<div class="tab-content" style="padding-top: 20px;">
			<div class="tab-pane active">
				<form id="myaccount-update-form" class="form-horizontal" method="post">
					<input type="hidden" name="id" id="id" value="<?php echo $obj_user->get('id') ?>" />
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
				</form>
				
				<form enctype="multipart/form-data" class="form-horizontal" method="post" action="<?php echo site_url('media/image-uploader/profile'); ?>" id="profile-image-form">
					<div class="control-group">
                      <label class="control-label">Image Upload</label>
                      <div class="controls">
							<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
								 <?php if( $this->users->current_user()->get('profile_image') != FALSE ): ?>
				                  	<img alt="<?php echo $this->users->current_user()->get('first_name').'\'s profile image.'; ?>" src="<?php echo $this->users->current_user()->get('profile_image') ?>" /> &nbsp;
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
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('backend/includes/footer'); ?>