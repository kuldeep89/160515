<?php
	
	/**
	* Support Template
	* Author: Thomas Melvin
	* Date: 19 July 2013
	* Notes:
	* Displays supoprt form and information
	*
	*/	

	$this->load->view('backend/includes/header');
	
?>

<form action="#" class="horizontal-form">

    <div class="row-fluid">
    
        <div class="span6 ">
        	<h3 class="form-section">Support</h3>
           	<div class="control-group">
           		
           		<div class="row-fluid">
			        <div class="span6 ">
			            <div class="control-group">
			                <label class="control-label" for="firstName">First Name</label>
			
			                <div class="controls">
			                    <input type="text" id="firstName" class="m-wrap span12" placeholder="<?php echo $this->current_user->get('first_name'); ?>">
			                </div>
			            </div>
			        </div><!--/span-->
			
			        <div class="span6 ">
			            <div class="control-group">
			                <label class="control-label" for="lastName">Last Name</label>
			
			                <div class="controls">
			                    <input type="text" class="m-wrap span12" placeholder="<?php echo $this->current_user->get('last_name'); ?>">
			                </div>
			            </div>
			        </div><!--/span-->
			    </div><!--/row-->
			    
			    <div class="row-fluid">
			        <div class="span6 ">
			            <div class="control-group">
			                <label class="control-label" for="firstName">Phone</label>
			
			                <div class="controls">
			                    <input type="text" id="firstName" class="m-wrap span12" placeholder="<?php echo $this->current_user->get('phone'); ?>">
			                </div>
			            </div>
			        </div><!--/span-->
			
			        <div class="span6 ">
			            <div class="control-group">
			                <label class="control-label" for="lastName">Email Address</label>
			
			                <div class="controls">
			                    <input type="text"  class="m-wrap span12" placeholder="<?php echo $this->current_user->get('email'); ?>">
			                </div>
			            </div>
			        </div><!--/span-->
			    </div><!--/row-->
			    
			    <div class="row-fluid">
			    
			    	<div class="span12">
			    		
			    		<label class="control-label" for="lastName">Comments</label>
			    		<textarea class="span12"></textarea>
			    		
			    	</div>
			    	
			    </div>
           		
           		<div class="form-actions"> <button type="submit" class="btn blue">Submit</button> <button type="button" class="btn">Cancel</button> </div>
           		
            </div>
        </div>
       
		<div class="span6">
		
			<h3 class="form-section">Contact</h3>
			<?php echo $obj_page->get('content'); ?>
			
		</div>
           	
    </div><!--/row-->
</form>

<?php

	$this->load->view('backend/includes/footer');