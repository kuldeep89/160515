<div class="wrap">
    <div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2 class="wpcube">
    	<?php echo ((isset($_GET['cmd']) AND $_GET['cmd'] == 'edit') ? 'Edit Rating Field' : 'Add New Rating Field'); ?>
    </h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<form id="post" class="<?php echo $this->plugin->name; ?>" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>-rating-fields&cmd=<?php echo (isset($_GET['cmd']) ? $_GET['cmd'] : 'add'); ?>&pKey=<?php echo (isset($_GET['pKey']) ? $_GET['pKey'] : ''); ?>" enctype="multipart/form-data">		
	    		<!-- Content -->
	    		<div id="post-body-content">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
		                <!-- Rating Field -->
	                    <div class="postbox">
	                        <h3 class="hndle"><?php _e('Rating Field', $this->plugin->name); ?></h3>
	                        <input type="hidden" name="<?php echo $this->plugin->name; ?>[pKey]" id="pKey" value="<?php echo (isset($_GET['pKey']) ? $_GET['pKey'] : ''); ?>" />
	
	                        <div class="option">
								<p>
									<strong><?php _e('Label', $this->plugin->name); ?></strong>
									<input type="text" name="<?php echo $this->plugin->name; ?>[label]" value="<?php echo $this->field['label']; ?>" style="width: 30%;" />
								</p>
							</div>
							
							<div class="option">
								<p>
					    			<strong><?php _e('Required?', $this->plugin->name); ?></strong>
					    			<input type="checkbox" name="<?php echo $this->plugin->name; ?>[required]" value="1"<?php echo ((isset($this->field['required']) AND $this->field['required'] == '1') ? ' checked' : ''); ?> />
								</p>
							</div>
	
							<div class="option">
								<p>
									<strong><?php _e('Required Text', $this->plugin->name); ?></strong>
									<input type="text" name="<?php echo $this->plugin->name; ?>[required_text]" value="<?php echo $this->field['required_text']; ?>" style="width: 30%;" />
								</p>
							</div>
	
							<div class="option">
								<p>
									<strong><?php _e('Cancel Text', $this->plugin->name); ?></strong>
									<input type="text" name="<?php echo $this->plugin->name; ?>[cancel_text]" value="<?php echo $this->field['cancel_text']; ?>" style="width: 30%;" />
								</p>
							</div>
						</div>
		                
		                
		            	<!-- Save -->
		                <div class="submit">
		                	<input type="submit" name="submit" value="<?php echo ((isset($_GET['cmd']) AND $_GET['cmd'] == 'edit') ? __('Update', $this->plugin->name) : __('Save', $this->plugin->name)); ?>" class="button-primary" />
		                </div>
					</div>
					<!-- /normal-sortables -->
				    
	    			
	    		</div>
	    		<!-- /post-body-content -->
	    		
	    		<!-- Sidebar -->
	    		<div id="postbox-container-1" class="postbox-container">
	    		
					<!-- Save -->
	                <div class="postbox">
	                    <div class="handlediv" title="Click to toggle"><br /></div>
	                    <h3 class="hndle"><span><?php _e('Save'); ?></span></h3>
	                    <div class="inside col_box_visible">
	                    	<div id="major-publishing-actions">
	                    		<input type="submit" name="submit" value="<?php echo ((isset($_GET['cmd']) AND $_GET['cmd'] == 'edit') ? __('Update', $this->plugin->name) : __('Save', $this->plugin->name)); ?>" class="button-primary" />
	                    	</div>
	                    </div>  
	                </div>
	                
	                <!-- Targeted Placement Options -->
	                <div class="postbox targeted-placement-options">
	                    <h3 class="hndle"><?php _e('Targeted Placement Options', $this->plugin->name); ?></h3>
	                    <div class="inside">
	                		<?php
	                        // Go through all Post Types
	                    	$types = get_post_types('', 'names');
	                    	foreach ($types as $key=>$type) {
	                    		if (in_array($type, $this->ignoreTypes)) continue; // Skip ignored Post Types
	                    		$postType = get_post_type_object($type);
	                    		?>
	                    		<p>
	                    			<strong><?php _e('Enable on '.$postType->label); ?></strong>
	                    			<label class="screen-reader-text" for="label"><?php _e($postType->label.': Enable on All '.$postType->label); ?></label>
	                                <input type="checkbox" name="<?php echo $this->plugin->name; ?>[placementOptions][type][<?php echo $type; ?>]" value="1"<?php echo (isset($this->field['placementOptions']['type'][$type]) ? ' checked' : ''); ?> class="trigger-type-<?php echo $type; ?>" />   
	                            </p>
	                        	
	                    		<?php
	                    		// Go through all taxonomies for this Post Type
	                    		$taxonomies = get_object_taxonomies($type);
	                    		if ($taxonomies AND count($taxonomies) > 0) {
	                    			foreach ($taxonomies as $taxKey=>$taxonomyProgName) {
										if (in_array($taxonomyProgName, $this->ignoreTaxonomies)) continue; // Skip ignored taxonomies
										$taxonomy = get_taxonomy($taxonomyProgName);
										$terms = get_terms($taxonomyProgName, array('hide_empty' => 0));
	                					?>
	                					<p><strong><?php _e('Enable on '.$postType->label.' '.$taxonomy->label); ?></strong></p>
	                					<div class="tax-selection">
	                						<div class="tabs-panel trigger-tax-<?php echo $type; ?>" style="height: 70px;">
	                							<ul class="list:category categorychecklist form-no-clear" style="margin: 0; padding: 0;">				                    			
													<?php
													foreach ($terms as $termKey=>$term) {
					                                    ?>
					                                    <li>
															<label class="selectit">
																<input type="checkbox" name="<?php echo $this->plugin->name; ?>[placementOptions][tax][<?php echo $taxonomyProgName; ?>][<?php echo $term->term_id; ?>]" value="1"<?php echo (isset($this->field['placementOptions']['tax'][$taxonomyProgName][$term->term_id]) ? ' checked' : ''); ?> class="trigger-tax-<?php echo $type; ?>" />
																<?php echo $term->name; ?>      
															</label>
														</li>
					                                    <?php
													}	
													?>
												</ul>
											</div>
										</div>
										<?php
									}
								}
							}
	                    	?>
						</div>
	                </div>
			
	    		</div>
	    		<!-- /postbox-container -->
    		</form>
    	</div>
	</div>       
</div>