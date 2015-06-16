<?php
	
	/**
	* Default Page Template
	* Author: Thomas Melvin
	* Date: 5 July 2013
	* Notes:
	* 
	*
	*/
	
	
	$arr_js[]	= 'scripts/page-editor.js';
	$arr_js[]	= 'scripts/pages_page.js';
	
	$this->load->view('backend/includes/header');
	
?>

	<div class="row-fluid">
		<div class="span12 news-page blog-page">
		
			<?php
				$this->load->view('backend/object-templates/pages/page');
			?>
	
		</div><!-- END PAGE CONTENT-->
	</div><!-- END PAGE CONTAINER-->

<?php

	$this->load->view('backend/includes/footer', array('arr_js'=>$arr_js));