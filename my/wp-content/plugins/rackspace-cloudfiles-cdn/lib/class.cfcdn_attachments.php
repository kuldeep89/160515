<?php 
/**
 * Abstraction layer over WordPress attachments for getting and 
 * pushing attachments to and from CDN.
 */
class CFCDN_Attachments {
	public $uploads;
	public $cache_file;
	public $local_files;
	public $uploaded_files;
	public $file_needing_upload;


	function __construct() {
		$this->uploads = wp_upload_dir();
	}


	/**
	* Finds local attachment files and uploads them to CDN. Will not upload all until
	* after first manual upload to CDN.
	* Requires PHP Directory Iterator installed on server.
	* Included in Standard PHP Library (SPL) - http://php.net/manual/en/book.spl.php
	*
	* @see http://de.php.net/manual/en/directoryiterator.construct.php
	*/
	public function upload_all() {
		$cdn = new CFCDN_CDN();

		if( $cdn->api_settings['first_upload'] == "true" ) {
			$this->load_files_needing_upload();

			foreach( $this->files_needing_upload as $file_path ) {
				$cdn->upload_file( $file_path );
			}
		}
	}


	/**
	* Calculate files that need to be uploaded. Not done on class init.
	* Sticks into array $this->files_needing_upload;
	*/
	public function load_files_needing_upload(){
		$this->files_needing_upload = array_diff( $this->local_files, $this->uploaded_files );
	}
}
?>
