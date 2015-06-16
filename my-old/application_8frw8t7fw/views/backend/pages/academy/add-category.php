<?php
	
	/**
	* Edit Category
	* Author: Thomas Melvin
	* Date: 29 July 2013
	* Notes:
	* This view presents a form to edit a page category.
	*
	*/	

	$this->load->view('backend/includes/header');
	
?>

	<div class="row-fluid">
	    <div class="span4">
	        <!-- BEGIN SAMPLE FORM PORTLET-->
	
	        <div class="portlet box blue">
	            
	            <div class="portlet-title">
	                <div class="caption">
	                
	                    <span>Add Category</span>
	                    
	                </div>
	            </div>
	            
	            <div class="portlet-body form">
	            
	                <form action="<?php echo site_url('academy/insert-category/'); ?>" class="form-horizontal" method="post">
	                
	                    <div class="control-group">
	                        
	                        <label class="control-label">Category</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="category" placeholder="Enter Category Name" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Description</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="description" placeholder="Enter Description" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Color</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="color" placeholder="Enter Color" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Icon</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="icon" placeholder="Enter Icon" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="form-actions">
			            	<button type="submit" class="btn blue">Save</button>
						</div>
	                    
	                </form>
	                
	            </div>
	            
				
	        </div>
	
	    </div>
	</div>

<?php

	$this->load->view('backend/includes/footer');
	