<div class="wrap">
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings', $this->plugin->name); ?></h2>
           
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
    		<!-- Content -->
    		<div id="post-body-content">
    		
    			<!-- Form Start -->
		        <form id="post" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>-settings">
		            <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
		                
		                <!-- Rating Input -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Rating Input', $this->plugin->name); ?></h3>
                            
                            <div class="inside">
                            	<p class="description">
                            		<?php _e('Controls where the rating field(s) are displayed on the Comment Form', $this->plugin->name); ?>.
                            	</p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Position', $this->plugin->name); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[ratingFieldPosition]" size="1">
                                    	<option value=""<?php echo (($this->settings['ratingFieldPosition'] == '') ? ' selected' : ''); ?>><?php _e('After Comment Field', $this->plugin->name); ?></option>
                                     	<option value="middle"<?php echo (($this->settings['ratingFieldPosition'] == 'middle') ? ' selected' : ''); ?>><?php _e('Before Comment Field', $this->plugin->name); ?></option>
                                   		<option value="above"<?php echo (($this->settings['ratingFieldPosition'] == 'above') ? ' selected' : ''); ?>><?php _e('Top of Comment Form / Before All Comment Fields', $this->plugin->name); ?></option>
                                    </select>
                                </p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Disable on Replies', $this->plugin->name); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[ratingDisableReplies]" size="1">
                                    	<option value="0"<?php echo ((!isset($this->settings['ratingDisableReplies']) OR $this->settings['ratingDisableReplies'] == '0') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
                                     	<option value="1"<?php echo ((isset($this->settings['ratingDisableReplies']) AND $this->settings['ratingDisableReplies'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
                                   	</select>
                                </p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Enable Half / .5 Ratings', $this->plugin->name); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[enableHalfRatings]" size="1">
                                    	<option value="0"<?php echo ((!isset($this->settings['enableHalfRatings']) OR $this->settings['enableHalfRatings'] == '0') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
                                     	<option value="1"<?php echo ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
                                   	</select>
                                </p>
                                <p class="description">
                                	<?php _e('Allow visitors leaving comments to leave a half star review e.g. 3.5, 4.5', $this->plugin->name); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Rating Output: Excerpt -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Rating Output: Excerpt', $this->plugin->name); ?></h3>
                            
                            <div class="inside">
                            	<p class="description">
                            		<?php _e('Allows you to display average ratings on archive pages, such as your Post lists', $this->plugin->name); ?>.
                            	</p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Display', $this->plugin->name); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[enabled][averageExcerpt]" size="1">
                                    	<option value=""<?php echo (($this->settings['enabled']['averageExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('Never Display', $this->plugin->name); ?></option>
                                    	<option value="1"<?php echo (($this->settings['enabled']['averageExcerpt'] == '1') ? ' selected' : ''); ?>><?php _e('Display when ratings exist', $this->plugin->name); ?></option>
                                    	<option value="2"<?php echo (($this->settings['enabled']['averageExcerpt'] == '2') ? ' selected' : ''); ?>><?php _e('Always Display', $this->plugin->name); ?></option>
                                    </select>  
                                </p>
                            </div>
                           
                           	<!-- Toggled by JS -->
                           	<div class="extra-options">
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Position', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[averageRatingPositionExcerpt]" size="1">
	                                    	<option value=""<?php echo (($this->settings['averageRatingPositionExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('Below Excerpt', $this->plugin->name); ?></option>
	                                    	<option value="above"<?php echo (($this->settings['averageRatingPositionExcerpt'] == 'above') ? ' selected' : ''); ?>><?php _e('Above Excerpt', $this->plugin->name); ?></option>
	                                    </select>
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Style', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayStyleExcerpt]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayStyleExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('Yellow Stars only', $this->plugin->name); ?></option>
	                                    	<option value="grey"<?php echo (($this->settings['displayStyleExcerpt'] == 'grey') ? ' selected' : ''); ?>><?php _e('Yellow Stars with Grey Stars', $this->plugin->name); ?></option>
	                                    </select>
	                                	<span class="description"><?php _e('If Yellow Stars with Grey Stars is chosen, ratings with less than 5 stars will show a mix denoting the rating in yellow plus remaining non-rating in grey.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Average', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayAverageExcerpt]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayAverageExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayAverageExcerpt'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Average Label', $this->plugin->name); ?></strong>
	                                	<input type="text" name="<?php echo $this->plugin->name; ?>[averageRatingTextExcerpt]" value="<?php echo ($this->settings['averageRatingTextExcerpt']); ?>" />   
	                                	<span class="description"><?php _e('If Display Average Rating above is selected, optionally define text to appear before the average rating stars are displayed.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Total Ratings', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayTotalRatingsExcerpt]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayTotalRatingsExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('Do not display the total number of ratings after the average rating', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayTotalRatingsExcerpt'] == '1') ? ' selected' : ''); ?>><?php _e('Display the total number of ratings after the average rating', $this->plugin->name); ?></option>
	                                    </select>
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Breakdown', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayBreakdownExcerpt]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayBreakdownExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayBreakdownExcerpt'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Link to Comments Section', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayLinkExcerpt]" size="1">
	                                    	<option value=""<?php echo ((!isset($this->settings['displayLinkExcerpt']) OR $this->settings['displayLinkExcerpt'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo ((isset($this->settings['displayLinkExcerpt']) AND $this->settings['displayLinkExcerpt'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                                <p class="description">
	                                	<?php _e('If enabled, the Average Rating will be linked to the comments section of your Page, Post or Custom Post Type', $this->plugin->name); ?>
	                                </p>
	                            </div>
                            </div>
                        </div> 
                         
                        <!-- Rating Output: Content -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Rating Output: Content', $this->plugin->name); ?></h3>
                            
                            <div class="inside">
                            	<p class="description">
                            		<?php _e('Allows you to display average ratings on single Pages, Posts and Custom Post Types', $this->plugin->name); ?>.
                            	</p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Display'); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[enabled][average]" size="1">
                                    	<option value=""<?php echo (($this->settings['enabled']['average'] == '') ? ' selected' : ''); ?>><?php _e('Never Display', $this->plugin->name); ?></option>
                                    	<option value="1"<?php echo (($this->settings['enabled']['average'] == '1') ? ' selected' : ''); ?>><?php _e('Display when ratings exist', $this->plugin->name); ?></option>
                                    	<option value="2"<?php echo (($this->settings['enabled']['average'] == '2') ? ' selected' : ''); ?>><?php _e('Always Display', $this->plugin->name); ?></option>
                                    </select>  
                                </p>
                            </div>
                            
                            <!-- Toggled by JS -->
                           	<div class="extra-options">                           
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Position', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[averageRatingPosition]" size="1">
	                                    	<option value=""<?php echo (($this->settings['averageRatingPosition'] == '') ? ' selected' : ''); ?>><?php _e('Below Content', $this->plugin->name); ?></option>
	                                    	<option value="above"<?php echo (($this->settings['averageRatingPosition'] == 'above') ? ' selected' : ''); ?>><?php _e('Above Content', $this->plugin->name); ?></option>
	                                    </select>
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Style', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayStyle]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayStyle'] == '') ? ' selected' : ''); ?>><?php _e('Yellow Stars only', $this->plugin->name); ?></option>
	                                    	<option value="grey"<?php echo (($this->settings['displayStyle'] == 'grey') ? ' selected' : ''); ?>><?php _e('Yellow Stars with Grey Stars', $this->plugin->name); ?></option>
	                                    </select>
	                                	<span class="description"><?php _e('If Yellow Stars with Grey Stars is chosen, ratings with less than 5 stars will show a mix denoting the rating in yellow plus remaining non-rating in grey.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                           	<div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Average', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayAverage]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayAverage'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayAverage'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Average Rating Text', $this->plugin->name); ?></strong>
	                                	<input type="text" name="<?php echo $this->plugin->name; ?>[averageRatingText]" value="<?php echo ($this->settings['averageRatingText']); ?>" />   
	                                	<span class="description"><?php _e('If Display Average Rating above is selected, optionally define text to appear before the average rating stars are displayed.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Total Ratings', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayTotalRatings]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayTotalRatings'] == '') ? ' selected' : ''); ?>><?php _e('Do not display the total number of ratings after the average rating', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayTotalRatings'] == '1') ? ' selected' : ''); ?>><?php _e('Display the total number of ratings after the average rating', $this->plugin->name); ?></option>
	                                    </select>
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Breakdown', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayBreakdown]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayBreakdown'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayBreakdown'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Link to Comments Section', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayLink]" size="1">
	                                    	<option value=""<?php echo ((!isset($this->settings['displayLink']) OR $this->settings['displayLink'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo ((isset($this->settings['displayLink']) AND $this->settings['displayLink'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                                <p class="description">
	                                	<?php _e('If enabled, the Average Rating will be linked to the comments section of your Page, Post or Custom Post Type', $this->plugin->name); ?>
	                                </p>
	                            </div>
                            </div>
                        </div> 
                        
                        <!-- Rating Output: Comments -->
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Rating Output: Comments', $this->plugin->name); ?></h3>
                            
                            <div class="inside">
                            	<p class="description">
                            		<?php _e('Defines how ratings are displayed on Comments', $this->plugin->name); ?>.
                            	</p>
                            </div>
                            
                            <div class="option">
                            	<p>
                            		<strong><?php _e('Display'); ?></strong>
                                	<select name="<?php echo $this->plugin->name; ?>[enabled][comment]" size="1">
                                    	<option value=""<?php echo (($this->settings['enabled']['comment'] == '') ? ' selected' : ''); ?>><?php _e('Never Display', $this->plugin->name); ?></option>
                                    	<option value="1"<?php echo (($this->settings['enabled']['comment'] == '1') ? ' selected' : ''); ?>><?php _e('Display when ratings exist', $this->plugin->name); ?></option>
                                    	<option value="2"<?php echo (($this->settings['enabled']['comment'] == '2') ? ' selected' : ''); ?>><?php _e('Always Display', $this->plugin->name); ?></option>
                                    </select>  
                                </p>
                            </div>
                            
                            <!-- Toggled by JS -->
                           	<div class="extra-options">                           
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Position', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[commentRatingPosition]" size="1">
	                                    	<option value=""<?php echo (($this->settings['commentRatingPosition'] == '') ? ' selected' : ''); ?>><?php _e('Below Comment Text', $this->plugin->name); ?></option>
	                                    	<option value="above"<?php echo (($this->settings['commentRatingPosition'] == 'above') ? ' selected' : ''); ?>><?php _e('Above Comment Text', $this->plugin->name); ?></option>
	                                    </select>
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Style', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayStyleComment]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayStyleComment'] == '') ? ' selected' : ''); ?>><?php _e('Yellow Stars only', $this->plugin->name); ?></option>
	                                    	<option value="grey"<?php echo (($this->settings['displayStyleComment'] == 'grey') ? ' selected' : ''); ?>><?php _e('Yellow Stars with Grey Stars', $this->plugin->name); ?></option>
	                                    </select>
	                                	<span class="description"><?php _e('If Yellow Stars with Grey Stars is chosen, ratings with less than 5 stars will show a mix denoting the rating in yellow plus remaining non-rating in grey.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Average', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayAverageComment]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayAverageComment'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayAverageComment'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
	                            
	                            <div class="option">    
	                                <p>
	                                	<strong><?php _e('Average Rating Text', $this->plugin->name); ?></strong>
	                                	<input type="text" name="<?php echo $this->plugin->name; ?>[commentRatingText]" value="<?php echo ($this->settings['commentRatingText']); ?>" />   
	                                	<span class="description"><?php _e('If Display Average Rating above is selected, optionally define text to appear before the average rating stars are displayed.', $this->plugin->name); ?></span>
	                                </p>
	                            </div>
	                            
	                            <div class="option">
	                            	<p>
	                            		<strong><?php _e('Show Breakdown', $this->plugin->name); ?></strong>
	                                	<select name="<?php echo $this->plugin->name; ?>[displayBreakdownComment]" size="1">
	                                    	<option value=""<?php echo (($this->settings['displayBreakdownComment'] == '') ? ' selected' : ''); ?>><?php _e('No', $this->plugin->name); ?></option>
	                                    	<option value="1"<?php echo (($this->settings['displayBreakdownComment'] == '1') ? ' selected' : ''); ?>><?php _e('Yes', $this->plugin->name); ?></option>
	                                    </select>  
	                                </p>
	                            </div>
                            </div>
                        </div>
		                
		               
		            	<!-- Save -->
		                <div class="submit">
		                    <input type="submit" name="submit" value="<?php _e('Save', $this->plugin->name); ?>" class="button button-primary" /> 
		                </div>
					</div>
					<!-- /normal-sortables -->
			    </form>
			    <!-- /form end -->
    			
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once($this->plugin->folder.'/_modules/dashboard/views/sidebar-pro.php'); ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div>       
</div>