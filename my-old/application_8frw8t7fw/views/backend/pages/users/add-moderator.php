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
                <form id="add-moderator" action="<?php echo site_url('users/insert-moderator'); ?>" class="form-horizontal form-bordered form-row-stripped" method="post">
               	 	<div class="control-group">
						<label class="control-label">Username</label>
						<div class="controls">
							<input type="text" name="username" id="username"  class="m-wrap" />
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
						<label class="control-label">Email</label>
						<div class="controls">
							<input type="text" name="email" id="email"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">First Name</label>
						<div class="controls">
							<input type="text" name="first_name" id="first_name"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Last Name</label>
						<div class="controls">
							<input type="text" name="last_name" id="last_name"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Company</label>
						<div class="controls">
							<input type="text" name="company" id="company"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Address</label>
						<div class="controls">
							<input type="text" name="address" id="address"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">City</label>
						<div class="controls">
							<input type="text" name="city" id="city"  class="m-wrap" />
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
										if ($state == 'IN') {
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
							<input type="text" name="zip" id="zip" class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Phone</label>
						<div class="controls">
							<input type="text" name="phone" id="phone"  class="m-wrap" />
						</div>
					</div>
						<div class="control-group">
						<label class="control-label">Google ID</label>
						<div class="controls">
							<input type="text" name="google_id" id="google_id"  class="m-wrap" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Groups</label>
						<div class="controls"> 
							<select class="span12 m-wrap" multiple="multiple" data-placeholder="Choose a Category" tabindex="1" name="arr_groups[]">
								<?php $this->load->view('backend/object-templates/users/group-options'); ?>
							</select> 
						</div>
					</div>
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