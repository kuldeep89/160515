<?php
$dir = new DirectoryIterator(dirname(__FILE__));
mkdir('decrypted');
foreach ($dir as $fileinfo) {
	if (is_file($fileinfo)) {
	    if (!$fileinfo->isDot()) {
	    	$file_info = pathinfo($fileinfo->getFilename());
	    	if ($file_info['extension'] == 'enc') {
		        $file_name = str_replace('.txt', '', $file_info['filename']);
		        exec("openssl enc -aes-256-cbc -d -in ".$file_name.".txt.enc -out decrypted/".$file_name.".txt -k 'V|c!tgG0S.q3v44T2<1l&a8AB'");

    			// Convert to GZ file
    			$fp = gzopen ("decrypted/".$file_name.'.gz', 'w9');
    			gzwrite ($fp, file_get_contents("decrypted/".$file_name.'.txt'));
    			gzclose($fp);
		    }
	    }
	}
}
?>
