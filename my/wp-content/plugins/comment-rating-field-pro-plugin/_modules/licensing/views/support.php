<div class="wrap">
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Support', $this->plugin->name); ?></h2>
           
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
    	<div id="post-body" class="metabox-holder columns-1">
    		<!-- Content -->
    		<div id="post-body-content">
    		
    			<!-- Form Start -->
		        <div id="normal-sortables" class="meta-box-sortables ui-sortable">     
		        	<!-- Documentation -->
		        	<div class="postbox">
                        <h3 class="hndle"><?php _e('Documentation', $this->plugin->name); ?></h3>
                        
                        <div class="inside">
                        	<p>
								<?php _e('Firstly, please check and thoroughly review our Documentation.  It covers all available Settings and Frequently Asked Questions.', $this->plugin->name); ?>
                        	</p>
                        	<p>
								<a href="<?php echo $this->plugin->documentationURL; ?>" class="button button-primary" target="_blank">
									<?php _e('View Documentation', $this->plugin->name); ?>
								</a>
							</p>
                        </div>
                    </div>
                                       
	                <!-- Debug -->
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Email Support', $this->plugin->name); ?></h3>
                        
                        <div class="inside">
                        	<p>
                        		<?php _e('If you are still encountering an error, problem or have a feature request, please follow these steps:', $this->plugin->name); ?>
                        	</p>
                        	<p>
                        		<?php _e('1. Generate an Export of your Plugin\'s settings:', $this->plugin->name); ?><br />
                        		<a href="admin.php?page=<?php echo $this->plugin->name; ?>-import-export&export=1" class="button button-primary">
                        			<?php _e('Export', $this->plugin->name); ?>
                        		</a>
                        	</p>
                        	
                        	<p>
                        		<?php _e('2. Copy the below Debug Information:', $this->plugin->name); ?>
                        		<textarea name="wpcube-debug" class="widefat" style="height: 200px;"><?php echo print_r($debug,true); ?></textarea>
                        	</p>
                        	
                            <p>
                            	<?php _e('3. Email your Export file, Debug Information and precise issues you\'re experiencing:', $this->plugin->name); ?><br />
								<a href="mailto:support@wpcube.co.uk?subject=<?php echo $this->plugin->displayName; ?> <?php echo $this->plugin->version; ?>" class="button button-primary" target="_blank">
									<?php _e('Email support@wpcube.co.uk', $this->plugin->name); ?>
								</a>
							</p>
                        </div>
                    </div>
				</div>
				<!-- /normal-sortables -->		
    		</div>
    		<!-- /post-body-content -->
    	</div>
	</div>       
</div>