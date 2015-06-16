<?php
	
	/**
	* Edit Category
	* Author: Thomas Melvin
	* Date: 29 July 2013
	* Notes:
	* This view presents a form to edit a page category.
	*
	*/	
	
	////////////////
	// Include CSS
	////////////////
	$arr_css[]	= 'css/pages/academy-edit-category.css';
	
	////////////////
	// Include JS
	////////////////
	$arr_js[]	= 'scripts/academy_edit_category.js';
	
	////////////////
	// Build Footer Array
	////////////////
	$arr_header 	= array('arr_css' => $arr_css);
	$arr_footer 	= array('arr_js' => $arr_js);
	
	$this->load->view('backend/includes/header', $arr_header);
	
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
	            
	                <form action="<?php echo site_url('academy/update-category/'.$arr_category['academy_entry_category_id']); ?>" class="form-horizontal" method="post">
	                
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
	                    	
	                    	<label class="control-label">Category Color</label>
	                    	
	                    	<div class="controls">
	                    		<a href="#myModal1" role="button" class="btn btn-primary" data-toggle="modal">Select Category Color</a>
	                    		<input type="text" id="color" name="color" placeholder="Enter Color" value="<?php echo $arr_category['color']; ?>" class="m-wrap small"> <span class="help-inline"></span>
	                    	</div>
	                    	
	                    </div>
	                    <div class="control-group">
	                    	
	                    	<label class="control-label">Category Icon</label>
	                    	
	                    	<div class="controls">
	                    		<a href="#myModal3" role="button" class="btn btn-primary" data-toggle="modal">Select Category Icon</a>
	                    		<input type="text" name="icon" id="icon" placeholder="Enter Icon" value="<?php echo $arr_category['icon']; ?>" class="m-wrap small"> <span class="help-inline"></span>
	                    	</div>
	                    	
	                    </div>
	                    <div class="form-actions">
			            	<button type="submit" class="btn blue">Save</button>
						</div>
	                    
	                </form>
	                
	            </div>
	            
	            <div id="myModal1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
				   <div class="modal-header">
				      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				      <h3 id="myModalLabel1">Modal Header</h3>
				   </div>
				   <div class="modal-body">
					   
					   <a data-dismiss="modal" class="purple color-box" alt="purple" href="#"></a>
					   <a data-dismiss="modal" class="blue color-box" alt="blue" href="#"></a>
					   <a data-dismiss="modal" class="red color-box" alt="red" href="#"></a>
					   <a data-dismiss="modal" class="green color-box" alt="green" href="#"></a>
					   <a data-dismiss="modal" class="grey color-box" alt="grey" href="#"></a>
					   <a data-dismiss="modal" class="yellow color-box" alt="yellow" href="#"></a>
					   <a data-dismiss="modal" class="light-grey color-box" alt="light-grey" href="#"></a>

				   </div>
				   <div class="modal-footer">
				      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				      <button class="btn yellow">Save</button>
				   </div>
				</div>
				
				<div id="myModal3" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
				   <div class="modal-header">
				      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				      <h3 id="myModalLabel1">Modal Header</h3>
				   </div>
				   <div class="modal-body">
					   
					   <ul class="icon-list">
        					<li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-glass </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-music </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-search </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-envelope-alt </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-heart </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-star </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-star-empty </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-user </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-film </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-th-large </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-th </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-th-list </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-ok </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-remove </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-zoom-in </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-zoom-out </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-off </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-signal </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-cog </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-trash </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-home </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-file-alt </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-time </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-road </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-download-alt </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-download </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-upload </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-inbox </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-play-circle </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-repeat </span>
					        </li>
					
					        <li data-dismiss="modal">
					            <i class="icon-fixed-width"></i> <span class="icon">icon-refresh </span>
					        </li>
	
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-list-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-lock </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-flag </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-headphones </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-volume-off </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-volume-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-volume-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-qrcode </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-barcode </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-tag </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-tags </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-book </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bookmark </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-print </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-camera </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-font </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bold </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-italic </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-text-height </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-text-width </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-align-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-align-center </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-align-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-align-justify </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-list </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-indent-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-indent-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-facetime-video </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-picture </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-pencil </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-map-marker </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-adjust </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-tint </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-edit </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-share </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-check </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-move </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-step-backward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-fast-backward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-backward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-play </span>
			        </li>
	
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-pause </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-stop </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-forward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-fast-forward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-step-forward </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-eject </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-chevron-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-chevron-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-plus-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-minus-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-remove-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-ok-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-question-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-info-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-screenshot </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-remove-circle </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-ok-circle </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-ban-circle </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-arrow-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-arrow-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-arrow-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-arrow-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-share-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-resize-full </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-resize-small </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-plus </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-minus </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-asterisk </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-exclamation-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-gift </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-leaf </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-fire </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-eye-open </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-eye-close </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-warning-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-plane </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-calendar </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-random </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-comment </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-magnet </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-chevron-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-chevron-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-retweet </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-shopping-cart </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-folder-close </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-folder-open </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-resize-vertical </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-resize-horizontal </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bar-chart </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-twitter-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-facebook-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-camera-retro </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-key </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-cogs </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-comments </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-thumbs-up-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-thumbs-down-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-star-half </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-heart-empty </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-signout </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-linkedin-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-pushpin </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-external-link </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-signin </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-trophy </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-github-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-upload-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-lemon </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-phone </span>
			        </li>
	
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-check-empty </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bookmark-empty </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-phone-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-twitter </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-facebook </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-github </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-unlock </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-credit-card </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-rss </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hdd </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bullhorn </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bell </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-certificate </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hand-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hand-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hand-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hand-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle-arrow-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle-arrow-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle-arrow-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle-arrow-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-globe </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-wrench </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-tasks </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-filter </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-briefcase </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-fullscreen </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-group </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-link </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-cloud </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-beaker </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-cut </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-copy </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-paper-clip </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-save </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-sign-blank </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-reorder </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-list-ul </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-list-ol </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-strikethrough </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-underline </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-table </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-magic </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-truck </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-pinterest </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-pinterest-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-google-plus-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-google-plus </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-money </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-caret-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-caret-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-caret-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-caret-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-columns </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-sort </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-sort-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-sort-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-envelope </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-linkedin </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-undo </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-legal </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-dashboard </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-comment-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-comments-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bolt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-sitemap </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-umbrella </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-paste </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-lightbulb </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-exchange </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-cloud-download </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-cloud-upload </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-user-md </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-stethoscope </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-suitcase </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-bell-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-coffee </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-food </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-file-text-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-building </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-hospital </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-ambulance </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-medkit </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-fighter-jet </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-beer </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-h-sign </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-plus-sign-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-double-angle-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-double-angle-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-double-angle-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-double-angle-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-angle-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-angle-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-angle-up </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-angle-down </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-desktop </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-laptop </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-tablet </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-mobile-phone </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle-blank </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-quote-left </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-quote-right </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-spinner </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-circle </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-reply </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-github-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-folder-close-alt </span>
			        </li>
			
			        <li data-dismiss="modal">
			            <i class="icon-fixed-width"></i> <span class="icon">icon-folder-open-alt </span>
			        </li>
        		</ul>

				   </div>
				   <div class="modal-footer">
				      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				      <button class="btn yellow">Save</button>
				   </div>
				</div>
				
	        </div>
	
	    </div>
	</div>

<?php

	$this->load->view('backend/includes/footer', $arr_footer);
	