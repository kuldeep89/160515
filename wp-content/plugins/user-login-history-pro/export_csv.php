<?php
	if( isset($_GET['file_name']) ){
			
		$filename	= $_GET['file_name'];
		$file		= $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/user-login-history-pro/export/".$filename;
		$first_part = strtok($filename, '-');
		if(isset($filename) && trim($filename) != '' && strlen($first_part) === 16) {
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: text/csv');
			    header("Content-Type: application/force-download");
				header("Content-Disposition:attachment;filename=".$filename); 
			    header("Content-Transfer-Encoding: binary ");	
			    header("Expires: 0");
			    header("Pragma: public");
			    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
				
			    ob_clean();
			    flush();
			    
				// Read file
				@readfile($file);
				
			    // Delete temp file
			    @unlink($file);
			    
			    // Kill script
			    die();
			}
		}
	}
?>