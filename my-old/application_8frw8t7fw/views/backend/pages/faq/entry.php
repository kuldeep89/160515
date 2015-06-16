<?php
	
	/**
	* Entry Page
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This page displays a Academy Entry item to the
	* viewer or the administrator.
	*
	*/	
	
	$arr_css[]	= 'plugins/ckeditor/skins/moono/editor.css';
	$arr_css[]	= 'plugins/select2/select2_metro.css';
	$arr_js[]	= 'plugins/select2/select2.min.js';
	$arr_js[]	= 'plugins/ckeditor/ckeditor.js';
	$arr_js[]	= 'scripts/save-faq-entry.js';
	$this->load->view('backend/includes/header', array('arr_css'=>$arr_css));
	
	////////////////
	// Load Tags/Categories
	////////////////
	$arr_tags		= $obj_entry->get_tags();
	$arr_categories	= $obj_entry->get_categories();

?>

            <!-- BEGIN PAGE CONTENT-->
            <div class="row-fluid">
               <div class="span12 news-page blog-page">

                        
                        <!-- Start of the Administrator Area -->
                        <div class="row-fluid">
	                        <div class="span12 ">
	                        	<div class="portlet box grey">
	                        		
	                        		<div class="portlet-title">
	                        			<div class="caption"><i class="icon-reorder"></i>Frequently Asked Question</div>
	                        			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	                        		</div>
	                        		<div class="portlet-body form">
	                        		
		                        		<form action="<?php echo site_url('faq/update-entry/'.$obj_entry->get('id')); ?>" class="form-horizontal" id="faq-entry-form" method="post">
		                        		
											<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $obj_entry->get('id'); ?>" />
											
										    <div class="control-group">
										    
										        <label class="control-label">FAQ</label>
										
										        <div class="controls">
													<div class="textarea" contenteditable="true" id="faq" name="faq"><?php echo $obj_entry->get('faq'); ?></div>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Response</label>
										
										        <div class="controls">
										            <div class="textarea" name="response" id="response" contenteditable="true" ><?php echo $obj_entry->get('response'); ?></div>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">FAQ Categories</label>
										
										        <div class="controls">
										            <input type="text" class="span6 m-wrap select2-choices" id="select-categories" name="categories" value="<?php echo implode(',', $obj_entry->get_category_names()); ?>"> <span class="help-inline"></span>
										        </div>
										        
										    </div>
										    
										    <div class="form-actions">
										    
										        <button type="submit" class="btn blue">Update</button>
										        <a href="<?php echo site_url('faq/create-entry'); ?>" type="submit" class="btn yellow">Add Another Entry</a>
										        <a href="<?php echo site_url('faq/remove-entry/'.$obj_entry->get('id')); ?>" class="btn red confirm-delete">Delete</a>
										        
										    </div>

		                        		</form>
		                        		
	                        		</div>
	                        		
	                        	</div><!-- end of portlet box grey -->
	                        </div>
                        </div>
                        	
                        <!-- End of the Administrator Area -->

               </div>
            </div>
            <!-- END PAGE CONTENT-->
         </div>
         <!-- END PAGE CONTAINER--> 
      </div>

<?php
	
	$this->load->view('backend/includes/footer', array('arr_js' => $arr_js));