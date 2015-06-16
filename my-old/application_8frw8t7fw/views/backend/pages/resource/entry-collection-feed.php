<?php

/**
 * Entries
 * Author: Thomas Melvin
 * Date: 26th June 2013
 * Notes:
 * This template will display the passed blog_entry_collection.
 *
 */

?>

	<?php
		$this->load->view('backend/includes/header');
	?>

    	<?php
    		$this->load->view('backend/object-templates/resource/entry-collection-feed');
    	?>
        
    <?php
		$this->load->view('backend/includes/footer');
	?>