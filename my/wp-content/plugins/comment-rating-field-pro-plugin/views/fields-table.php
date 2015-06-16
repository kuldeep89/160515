<div class="wrap">
	<div id="<?php echo $this->plugin->name; ?>-title" class="icon32"></div> 
    <h2 class="wpcube">
    	<?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Fields', $this->plugin->name); ?>
    	<a href="admin.php?page=<?php echo $this->plugin->name; ?>-rating-fields&cmd=add" class="add-new-h2"><?php _e('Add New', $this->plugin->name); ?></a>
    	<?php
	    // Search Subtitle
	    if (isset($_REQUEST['s']) AND !empty($_REQUEST['s'])) {
	    	?>
	    	<span class="subtitle"><?php _e('Search results for', $this->plugin->name); ?> &#8220;<?php echo urldecode($_REQUEST['s']); ?>&#8221;</span>
	    	<?php
	    }
	    ?>
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
    
	<form action="admin.php?page=<?php echo $this->plugin->name; ?>-rating-fields" method="post" id="bar">
		<p class="search-box">
	    	<label class="screen-reader-text" for="post-search-input"><?php _e('Search Rating Fields', $this->plugin->name); ?>:</label>
	    	<input type="text" id="field-search-input" name="s" value="<?php echo (isset($_REQUEST['s']) ? $_REQUEST['s'] : ''); ?>" />
	    	<input type="submit" name="search" class="button" value="<?php _e('Search Rating Fields', $this->plugin->name); ?>" />
	    </p>
	    
		<?php   
		$this->wpListTable->prepare_items();
		$this->wpListTable->display(); 
		?>	
	</form>
</div>