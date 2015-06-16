<?php
	
	/**
	* Search Results
	* Author: Thomas Melvin
	* Date: 17 July 2013
	* Notes:
	* Page displays search results of various modules.
	*
	*/	

	$this->load->view('backend/includes/header');
	
?>
	
	<?php if( isset($obj_academy_collection) && $obj_academy_collection->size() > 0 ) : ?>
		
		<h1>Academy Results</h1>
		
		<?php foreach( $obj_academy_collection->get('arr_collection') as $obj_entry ) : ?>
			<?php $this->load->view('backend/object-templates/academy/search-result', array('obj_entry'=>$obj_entry)); ?>
		<?php endforeach; ?>
		
	<?php endif; ?>
	
	
	<?php if( isset($obj_pages_collection) && $obj_pages_collection->size() > 0 ) : ?>
		
		<h1>Page Search Results</h1>
		
		<?php foreach( $obj_pages_collection->get('arr_collection') as $obj_entry ) : ?>
			<?php $this->load->view('backend/object-templates/pages/search-result', array('obj_entry'=>$obj_entry)); ?>
		<?php endforeach; ?>
		
	<?php endif; ?>
	
	<?php if( isset($obj_faq_collection) && $obj_faq_collection->size() > 0 ) : ?>
		
		<h1>FAQ Search Results</h1>
		
		<?php foreach( $obj_faq_collection->get('arr_collection') as $obj_entry ) : ?>
			<?php $this->load->view('backend/object-templates/faq/search-result', array('obj_entry'=>$obj_entry)); ?>
		<?php endforeach; ?>
		
	<?php endif; ?>

<?php

	$this->load->view('backend/includes/footer');