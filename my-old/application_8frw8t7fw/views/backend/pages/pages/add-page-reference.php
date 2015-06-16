<?php
	
	/**
	* Add Page Reference
	* Author: Thomas Melvin
	* Date: 15 July 2013
	* Notes:
	* This page presents a form to add a page reference.
	*
	*/	

	$this->load->view('backend/includes/header');
	
?>

<!-- Start of the Administrator Area -->
                        <div class="row-fluid">
	                        <div class="span12 ">
	                        	<div class="portlet box grey">
	                        		
	                        		<div class="portlet-title">
	                        		
	                        			<div class="caption"><i class="icon-reorder"></i>Page Attributes</div>
	                        			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	                        			
	                        		</div>
	                        		
	                        		<div class="portlet-body form">
	                        		
		                        		<form action="<?php echo site_url('pages/create-page-reference'); ?>" class="form-horizontal" id="page-form" method="post">
											
											<div class="control-group">
										    
										        <label class="control-label">Page Name</label>
										
										        <div class="controls">
										            <input id="page_name" type="text" class="span6 m-wrap" name="name" value=""> <span class="help-inline">This name will appear in the navigation links.</span>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Page URL</label>
										
										        <div class="controls">
										            <input id="url" type="text" class="span6 m-wrap" name="url" value=""> <span class="help-inline">SEO friendly URL.</span>
										        </div>
										        
										    </div>
										    
										    <div class="form-actions">
										        <button type="submit" class="btn blue">Create Page Reference</button>
										    </div>

		                        		</form>
		                        		
	                        		</div>
	                        		
	                        	</div><!-- end of portlet box grey -->
	                        </div>
                        </div>
                        	
                        <!-- End of the Administrator Area -->

<?php

	$this->load->view('backend/includes/footer');