<?php

/**
 * Category Listing
 * Author: Thomas Melvin
 * Date: 3 July 2013
 * Notes:
 * This template display a entry collection for tag listings.
 *
 */

?>

	<?php
	
		////////////////
		// Build CSS Array
		////////////////
		$arr_css[]	= 'css/pages/news.css';
		$arr_css[]	= 'css/pages/blog.css';
		
		
		////////////////
		// Build Header Array
		////////////////
		$arr_header 	= array('arr_css' => $arr_css);
	
		$this->load->view('backend/includes/header', $arr_header);
	?>
	
	<h1><?php echo $obj_entry_collection->get('category'); ?></h1>
	
    	<?php
    		$this->load->view('backend/object-templates/academy/entry-listing-teasers');
    	?>
        
    <?php
		$this->load->view('backend/includes/footer');
	?>