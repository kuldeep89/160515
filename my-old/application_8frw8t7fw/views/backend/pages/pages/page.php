<?php
	
	/**
	* Add Page
	* Author: Thomas Melvin
	* Date: 5 July 2013
	* Notes:
	* This page displays a Page item to the
	* viewer or the administrator.
	*
	*/	
	////////////////
	// Define JS and CSS
	////////////////
	$arr_css[]	= 'plugins/ckeditor/skins/moono/editor.css';
	$arr_css[]	= 'plugins/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css';

	$arr_css[]	= 'plugins/select2/select2_metro.css';

	$arr_js[]	= 'plugins/ckeditor/ckeditor.js';
	$arr_js[]	= 'plugins/select2/select2.min.js';
	$arr_js[]	= 'plugins/ckeditor/skins/moono/skin.js';
	$arr_js[]	= 'scripts/pages_page.js';

	$arr_footer['arr_js']	= $arr_js;

	$this->load->view('backend/includes/header');

?>

            <!-- BEGIN PAGE CONTENT-->
            <div class="row-fluid">
               <div class="span12 news-page blog-page">
                  <div class="row-fluid">
                     <div class="span8 blog-tag-data">
                        <h1 contenteditable="true" id="title"><?php echo (isset($obj_page)) ? $obj_page->get('title') : 'Enter Page Title'; ?></h1>
                        <div class="news-item-page">
                        	
                        	<div id="page-content" style="min-height: 200px;" contenteditable="true">
								<?php echo (isset($obj_page)) ? $obj_page->get('content') : '<em>Enter your page content here</em>'; ?>
                        	</div>
                        	
                        	<div class="space20"></div>
							
                        </div>
                        <hr>
                        
                        <!-- Start of the Administrator Area -->
                        <div class="row-fluid">
	                        <div class="span12 ">
	                        	<div class="portlet box grey">
	                        		
	                        		<div class="portlet-title">
	                        			<div class="caption"><i class="icon-reorder"></i>Administrative Section</div>
	                        			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	                        		</div>
	                        		
	                        		<div class="portlet-body form">
	                        		
		                        		<form class="form-horizontal" id="page-form" method="post">
											
											<input type="hidden" name="content" id="content-input" />
											<input type="hidden" name="title" id="title-input" />
											<input type="hidden" name="template" id="template-input" />
											<input type="hidden" name="page_id" id="page_id" value="<?php echo (isset($obj_page)) ? $obj_page->get('id') : ''; ?>" />
											
											<div class="control-group">
										    
										        <label class="control-label">Page Name</label>
										
										        <div class="controls">
										            <input id="page_name" type="text" class="span6 m-wrap" name="name" placeholder="Page Name" value="<?php echo (isset($obj_page)) ? $obj_page->get('name') : ''; ?>"> <span class="help-inline">This name will appear in the navigation links.</span>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Page URL</label>
										
										        <div class="controls">
										            <input id="page_url" type="text" class="span6 m-wrap" name="url" placeholder="Page URL" value="<?php echo (isset($obj_page)) ? $obj_page->get('url') : ''; ?>"> <span class="help-inline">SEO friendly URL.</span>
										        </div>
								        
										    </div>
											
										    <div class="control-group">
										    
										        <label class="control-label">Browser Title</label>
										
										        <div class="controls">
										            <input id="browser-title" type="text" class="span6 m-wrap" name="browser_title" placeholder="Browser Title" value="<?php echo (isset($obj_page)) ? $obj_page->get('browser_title') : ''; ?>"> <span class="help-inline">This title will appear at the top of your browser window.</span>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Page Description</label>
										
										        <div class="controls">
										            <input id="description" type="text" class="span6 m-wrap"  name="description" placeholder="META Description" value="<?php echo (isset($obj_page)) ? $obj_page->get('description') : ''; ?>"> <span class="help-inline">Brief descriptions for search engines and viewers.</span>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Page Keywords</label>
										
										        <div class="controls">
										            <input id="keywords" type="text" class="span6 m-wrap"  name="keywords" placeholder="META Keywords" value="<?php echo (isset($obj_page)) ? $obj_page->get('keywords') : ''; ?>"> <span class="help-inline">Keywords about the page for search engines.</span>
										        </div>
										        
										    </div>
										    
										    <div class="control-group">
										    
										        <label class="control-label">Template</label>
										
										        <div class="controls">
										            <select name="template" id="template">
				                                 		<?php if( isset($arr_templates) && count($arr_templates) > 0 ) : ?>
				                                 			<?php foreach( $arr_templates as $template ) : ?>
				                                 				
				                                 				<option <?php echo (isset($obj_page) && $obj_page->get('template') == $template)? 'selected="selected"':''; ?> value="<?php echo $template; ?>"><?php echo $template; ?></option>
				                                 			
				                                 			<?php endforeach; ?>
				                                 		<?php endif; ?>
				                                 	</select>
										        </div>
										        
										    </div>
										    
										    <div class="form-actions">
										        <?php echo ($this->router->method == 'add_page') ? '<button type="submit" class="btn blue">Add Page</button>' : '<button type="submit" class="btn blue">Update Page</button>' ?>
										    </div>

		                        		</form>
		                        		
	                        		</div>
	                        		
	                        	</div><!-- end of portlet box grey -->
	                        </div>
                        </div>
                        	
                        <!-- End of the Administrator Area -->
                        
                     </div>
                    
                  </div>
               </div>
            </div>
            <!-- END PAGE CONTENT-->
         </div>
         <!-- END PAGE CONTAINER--> 
      </div>

<?php

	$this->load->view('backend/includes/footer', $arr_footer);