<?php

/**
 * Tag Listing
 * Author: Thomas Melvin
 * Date: 3 July 2013
 * Notes:
 * This template display a entry collection for tag listings.
 *
 */

?>

	<?php
		$this->load->view('backend/includes/header');
	?>

    	<?php
    		$this->load->view('backend/object-templates/academy/entry-listing-teasers');
    	?>
        
    <?php
		$this->load->view('backend/includes/footer');
	?>