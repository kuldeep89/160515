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
	                
	                    <span>Update Category Name</span>
	                    
	                </div>
	            </div>
	            <div class="portlet-body form">
	            
	                <form action="<?php echo site_url('resource/update-category/'.$arr_category['resource_entry_category_id']); ?>" class="form-horizontal" method="post">
	                
	                    <div class="control-group">
	                        
	                        <label class="control-label">Category</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="category" placeholder="Enter Category Name" value="<?php echo $arr_category['name']; ?>" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Description</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="description" placeholder="Enter Description" value="<?php echo $arr_category['description']; ?>" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Color</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="color" placeholder="Enter Color" value="<?php echo $arr_category['color']; ?>" class="m-wrap small"> <span class="help-inline"></span>
	                        </div>
	                        
	                    </div>
	                    <div class="control-group">
	                        
	                        <label class="control-label">Icon</label>
	                        
	                        <div class="controls">
	                            <input type="text" name="icon" placeholder="Enter Icon" value="<?php echo $arr_category['icon']; ?>" class="m-wrap small"> <span class="help-inline"></span>
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
	