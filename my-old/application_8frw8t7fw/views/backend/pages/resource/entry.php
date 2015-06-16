<?php
	
	/**
	* Entry Page
	* Author: Thomas Melvin
	* Date: 27 June 2013
	* Notes:
	* This page displays a resource Entry item to the
	* viewer or the administrator.
	*
	*/	
	
	$arr_css[]	= 'plugins/ckeditor/skins/moono/editor.css';
	$arr_css[]	= 'css/entry.css';
	$arr_css[]	= 'plugins/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css';
	
	$arr_css[]	= 'plugins/bootstrap-datepicker/css/datepicker.css';
	$arr_css[]	= 'plugins/select2/select2_metro.css';
	
	$arr_js[]	= 'plugins/ckeditor/ckeditor.js';
	$arr_js[]	= 'scripts/save-resource-entry.js';
	$arr_js[]	= 'plugins/bootstrap-fileupload/bootstrap-fileupload.js';
	$arr_js[]	= 'plugins/bootstrap-datepicker/js/bootstrap-datepicker.js';
	$arr_js[]	= 'plugins/select2/select2.min.js';
	
	$arr_js[]	= 'plugins/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js';
	$arr_js[]	= 'plugins/ckeditor/skins/moono/skin.js';
	
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
                  <div class="row-fluid">
                     <div class="span8 blog-tag-data">
                        <h1 <?php if( $this->security_lib->accessible(2) ) : ?>contenteditable="true" <?php endif; ?> id="entry-title"><?php echo $obj_entry->get('title'); ?></h1>
                        <div class="row-fluid">
                           <div class="span6">
                              <ul class="unstyled inline blog-tags">
                                 <li>
                                    <i class="icon-tags"></i>
                                    
									<?php if( count($arr_tags) > 0 ) : ?>
									
										<?php foreach( $arr_tags as $arr_tag ) : ?>
											
											<a href="<?php echo site_url('resource/tag/'.$arr_tag['resource_entry_tag_id']); ?>"><?php echo $arr_tag['name']; ?></a>
											
											<?php endforeach; ?>
										
									<?php else: ?>
									
										Article Not Tagged
										
									<?php endif; ?>
									
                                 </li>
                              </ul>
                           </div>
                           <div class="span6 blog-tag-data-inner">
                           	  
                              <ul class="unstyled inline">
                                 <a href="#"><li><i class="icon-calendar"></i> <input type="text" style="font-size: 12px; padding: 0px; border: none !important; display: inline-block; width: auto; outline-width: 0px !important;" id="entry-publish-date" class="date-picker" value="<?php echo date('F d, Y', $obj_entry->get('schedule_post')); ?>" /></a></li>
                                 <li>Author: 
                                 	<select id="author_id">
                                 		<?php $this->load->view('backend/object-templates/users/user-options', array('obj_user_collection' => $obj_author_collection, 'selected_user' => $selected_author)); ?>
                                 	</select>
                                 	
                                 </li>
                              </ul>
                              
                           </div>
                        </div>
                        
                        <?php
							$image	= $obj_entry->get('featured_image');
						?>
                        
                        <div class="news-item-page">
                        
                        <?php if( !empty($image) && !is_null($image) ): ?>
							<img class="content-image"src="<?php echo $image; ?>" id="featured-image-preview" style="float: right;  "  />
					   <?php else: ?>
					   		<img src="http://www.placehold.it/1x1/EFEFEF/AAAAAA&text=no+image" id="featured-image-preview" width="0" />
						<?php endif; ?>
					   
                        	<div id="entry-content" <?php if( $this->security_lib->accessible(2) ) : ?>contenteditable="true" <?php endif; ?>>

								<?php 
								
									if( $this->security_lib->accessible(18) ) {
										echo $obj_entry->get('content'); 
									}
									else {
										echo neat_trim(strip_tags($obj_entry->get('content')), 300);
									}
									
								?>

                        	</div>
                        	
                        	<div class="space20"></div>
                        	<em>Categories: </em>
                        	
                        	<?php if( count($arr_categories) > 0 ) : ?>
                        	
                        		<?php $first = TRUE; ?>
								<?php foreach( $arr_categories as $arr_category ) : ?><?php if( !$first ) echo ', '; ?><a href="<?php echo site_url('resource/category/'.$arr_category['resource_entry_category_id']); ?>"><?php echo $arr_category['name']; ?></a><?php $first = FALSE; ?><?php endforeach; ?>
								
							<?php else: ?>
								No Categories
							<?php endif; ?>
							
                        </div>
                        <hr>
                        
                        <?php if( $this->security_lib->accessible(2) ) : ?>
                        
	                        <!-- Start of the Administrator Area -->
	                        <div class="row-fluid">
		                        <div class="span12 ">
		                        	<div class="portlet box grey">
		                        		
		                        		<div class="portlet-title">
		                        		
		                        			<div class="caption"><i class="icon-reorder"></i>Administrative Section</div>
		                        			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
		                        		
		                        		</div>
		                        		<div class="portlet-body form">
		                        		
			                        		<form action="<?php echo site_url('resource/update-entry/'.$obj_entry->get('id')); ?>" class="form-horizontal" id="resource-entry-form" method="post">
			                        		
												<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $obj_entry->get('id'); ?>" />
												
											    <div class="control-group">
											    
											        <label class="control-label">Browser Title</label>
											
											        <div class="controls">
											            <input id="browser-title" type="text" class="span6 m-wrap" name="browser_title" value="<?php echo $obj_entry->get('browser_title'); ?>"> <span class="help-inline">This title will appear at the top of your browser window.</span>
											        </div>
											        
											    </div>
											    
											    <div class="control-group">
											    
											        <label class="control-label">Article Description</label>
											
											        <div class="controls">
											            <input id="description" type="text" class="span6 m-wrap"  name="description" value="<?php echo $obj_entry->get('description'); ?>"> <span class="help-inline">Brief descriptions for search engines and viewers.</span>
											        </div>
											        
											    </div>
											    
											    <div class="control-group">
											    
											        <label class="control-label">Article Keywords</label>
											
											        <div class="controls">
											            <input id="keywords" type="text" class="span6 m-wrap"  name="keywords" value="<?php echo $obj_entry->get('keywords'); ?>"> <span class="help-inline">Keywords about the article for search engines.</span>
											        </div>
											        
											    </div>
											    
											    <div class="control-group">
											    
											        <label class="control-label">Article Tags</label>
											
											        <div class="controls">
											            <input type="text" class="span6 m-wrap" id="select-tags" name="tags" value="<?php echo implode(',', $obj_entry->get_tag_names()); ?>"> <span class="help-inline">Tags relate the article to specific ideas.</span>
											        </div>
											        
											    </div>
											    
											    <div class="control-group">
											    
											        <label class="control-label">Article Categories</label>
											
											        <div class="controls">
											            <input type="text" class="span6 m-wrap chosen chzn-done" id="select-categories" name="categories" value="<?php echo implode(',', $obj_entry->get_category_names()); ?>"> <span class="help-inline">Categories associate the article with a general topic.</span>
											        </div>
											        
											        
											    </div>
											    
											    <div class="control-group">
											    	
											    	<label class="control-label">Publish Article</label>
											    	
											    	<div class="controls">
											        	
											        	<div class="success-toggle-button">
															<input type="checkbox" name="published" id="published" class="toggle" <?php echo ($obj_entry->get('published') == 1)? 'checked="checked" value="1"':''; ?> />
														</div>
											        	
											        </div>
											    	
											    </div>
											    
											    <div class="control-group">
											    	
											    	<label class="control-label">Featured Image</label>
											    	<input type="hidden" id="featured-image" value="" />
											    	
											    	<div class="controls">
											    	
														<?php
															$image	= $obj_entry->get('featured_image');

															if( !empty($image) && $image != null ) {
																$file_info = pathinfo($image);
																$path_thumb = str_replace($file_info['basename'], $file_info['filename'].'_thumb.'.$file_info['extension'], $image);
															}
															
														?>
														<input type="hidden" id="featured-image-input" value="<?php echo $image; ?>" />
														
														<?php if( !empty($image) ): ?>
															<img src="<?php echo $path_thumb; ?>" class="featured-image-preview" width="30" />
														<?php else: ?>
															<img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" class="featured-image-preview" width="30" />
														<?php endif; ?>
														
											    		<a href="#myModal1" role="button" class="btn btn-primary" data-toggle="modal">Select Featured Image</a>
											    		<a id="remove-featured-image" class="btn red" href="javascript:return false;">X</a> 
										
	
											    	</div>
											    	
											    </div>
											    
											    <div class="form-actions">
											    
											        <?php if( $this->security_lib->accessible(2) ) : ?><button type="submit" class="btn blue">Update</button><?php endif; ?>
											        <?php if( $this->security_lib->accessible(1) ) : ?><a id="add-another-entry" href="<?php echo site_url('resource/create-entry'); ?>" type="submit" class="btn yellow">Add Another Entry</a><?php endif; ?>
											        <?php if( $this->security_lib->accessible(3) ) : ?><a href="<?php echo site_url('resource/remove-entry/'.$obj_entry->get('id')); ?>" class="btn red confirm-delete">Delete</a><?php endif; ?>
											        
											    </div>
	
			                        		</form>
			                        		
		                        		</div>
		                        		
		                        	</div><!-- end of portlet box grey -->
		                        </div>
	                        </div>
	                        	
	                        <!-- End of the Administrator Area -->
						<?php endif; ?>
                        
                     </div>
                     <div class="span4">
                     
                       	<h2>Related Articles</h2>
                       	
                        <?php
                        	$this->load->view('backend/object-templates/resource/article-teasers', array('obj_entry_collection' => $obj_related_collection));
                        ?>
                        
                        <div class="space20"></div>
                        
                        <h2>News Tags</h2>
                        
                        <ul class="unstyled inline sidebar-tags">
                        
                        	<?php if( count($arr_all_tags) > 0 ) : ?>
                        	
                        		<?php 
	                        
	                        		shuffle($arr_all_tags);											// randomizes tags
	                        		$arr_some_tags = array_slice($arr_all_tags, 0, 25);				// only displays 25 tags
                        	
                        		?>
                        	
                        		<?php foreach( $arr_some_tags as $arr_tag ) : ?>
                        			
                        			<li><a href="<?php echo site_url('resource/tag/'.$arr_tag['resource_entry_tag_id']); ?>"><i class="icon-tags"></i> <?php echo $arr_tag['name']; ?></a></li>
                        			
                        		<?php endforeach; ?>
                        	
                        	<?php endif; ?>
                        
                        </ul>
                        
                        <div class="space20"></div>
                        
                     </div>
                  </div>
               </div>
            </div>
            <!-- END PAGE CONTENT-->
         </div>
         <!-- END PAGE CONTAINER--> 
      </div>
	  
		<div id="myModal1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h3 id="myModalLabel1">Resource Images</h3>
			</div>
			<div class="modal-body">
				
				<!--BEGIN TABS-->
	              <div class="tabbable tabbable-custom">
	                 <ul class="nav nav-tabs">
	                    <li class="active"><a href="#tab_1_1" id="view-resource-images" data-toggle="tab">Select Featured Image</a></li>
	                    <li><a href="#tab_1_2" data-toggle="tab">Upload Image</a></li>
	                 </ul>
	                 <div class="tab-content">
	                    <div class="tab-pane active" id="tab_1_1">
	                       <div class="featured-images tiles">
	
						   </div>
	                    </div>
	                    <div class="tab-pane" id="tab_1_2">
							<iframe src="<?php echo site_url('media/upload/resource'); ?>" width="100%" height="200"></iframe>
	                    </div>
	                 </div>
	              </div>
	              <!--END TABS-->

			</div>
			<div class="modal-footer">
				<button class="btn" id="cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
				<button class="btn" id="save-and-close" data-dismiss="modal" aria-hidden="true">Save & Close</button>
			</div>
		</div>
	  
<?php
	
	$this->load->view('backend/includes/footer', array('arr_js' => $arr_js));