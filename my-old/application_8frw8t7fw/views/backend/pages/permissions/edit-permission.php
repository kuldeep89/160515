<?php
	
	/**
	* Create Module Page
	* Author: Thomas Melvin
	* Date: 8/9/13
	* Notes: 
	* This is a form to create a module in the permissions systems.
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
				<div class="caption"><i class="icon-reorder"></i>Edit Permission</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
			<div class="portlet-body form">
				<form action="<?php echo site_url('permissions/update-permission/'.$arr_permission['permission_id']); ?>" class="form-horizontal form-bordered" method="post">
					
					<input type="hidden" value="<?php echo $arr_permission['permission_id']; ?>" name="permission_id" />
					
				    <div class="control-group">
				       <label class="control-label">Permission Name</label>
				       <div class="controls">
				          <input type="text" name="permission_name" placeholder="Permission Name" class="m-wrap span12" value="<?php echo $arr_permission['permission_name']; ?>" />
				       </div>
				    </div>
				    <div class="control-group">
				       <label class="control-label">Permission Description</label>
				       <div class="controls">
				          <input type="text" name="permission_description" placeholder="Permission Description" class="m-wrap span12" value="<?php echo $arr_permission['permission_description']; ?>" />
				          <span class="help-block">Brief description about what this permission is about.</span>
				       </div>
				    </div>
				    <div class="form-actions">
				       <button type="submit" class="btn blue"><i class="icon-ok"></i> Update</button>
				    </div>
				 </form>
			</div>
		</div>
	</div>
</div>


<?php

	$this->load->view('backend/includes/footer');