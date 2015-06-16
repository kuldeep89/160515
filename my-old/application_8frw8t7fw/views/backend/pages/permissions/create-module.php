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
				<div class="caption"><i class="icon-reorder"></i>Add Module</div>
				<div class="tools">
					<a href="javascript:;" class="collapse"></a>
					<a href="#portlet-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
			<div class="portlet-body form">
				<form method="post" action="<?php echo site_url('permissions/add-module'); ?>" class="form-horizontal form-bordered">
				    <div class="control-group">
				       <label class="control-label">Module Name</label>
				       <div class="controls">
				          <input type="text" name="module_name" placeholder="Module Name" class="m-wrap span12" />
				          <span class="help-block">This name will likely be the name of the Controller class.</span>
				       </div>
				    </div>
				    <div class="control-group">
				       <label class="control-label">Module Description</label>
				       <div class="controls">
				          <input type="text" name="module_description" placeholder="Module Description" class="m-wrap span12" />
				          <span class="help-block">Brief description about what this module does.</span>
				       </div>
				    </div>
				    <div class="form-actions">
				       <button type="submit" class="btn blue"><i class="icon-ok"></i> Save</button>
				    </div>
				 </form>
			</div>
		</div>
	</div>
</div>


<?php

	$this->load->view('backend/includes/footer');