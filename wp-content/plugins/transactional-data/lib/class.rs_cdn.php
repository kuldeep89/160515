<?php
/**
 * Functions used to connect to the CDN
 */

class RS_CDN {

	public $api_settings;
	public $uploads;

	function __construct($custom_settings = null) {
		$this->api_settings = $this->settings($custom_settings);
	}

	/**
	* Setup Cloud Files CDN Settings
	*/
	public static function settings($custom_settings = null){
		$settings = array( 'username' => 'saltsha',
			'apiKey' => '9d2fe4da01564dfb8886e9d3a3353440',
			'use_ssl' => true,
			'container' => 'saltsha_transaction_backups',
			'public_url' => null,
			'verified' => false,
			'region' => 'ORD',
			'url' => 'https://identity.api.rackspacecloud.com/v2.0/');

		// Return settings
		return $settings;
	}

	/**
	 *  Openstack Connection Object
	 */
	function connection_object(){
		$api_settings = $this->api_settings;
		$connection = new \OpenCloud\Rackspace(
			$api_settings['url'],
			array(
				'username' => $api_settings['username'],
				'apiKey' => $api_settings['apiKey']
			)
		);
		$cdn = $connection->ObjectStore( 'cloudFiles', $api_settings['region'], 'publicURL' );
		return $cdn;
	}

	/**
	*  Openstack CDN Container Object
	*/
	public function container_object(){
		$api_settings = $this->api_settings;
		$cdn = $this->connection_object();
		$container = $cdn->Container($api_settings['container']);
		return $container;
	}


	/**
	*  Openstack CDN File Object
	*/
	public function file_object($container, $file_path, $file_name = null){
		$file = $container->DataObject();
		$file->SetData( @file_get_contents( $file_path ) );
		$file->name = (isset($file_name) && !is_null($file_name)) ? $file_name : basename( $file_path );
		return $file;
	}

	/**
	* Uploads given file attachment onto CDN if it doesn't already exist
	*/
	public function upload_file( $file_path , $file_name = null){

		// Check if file exists
		$check_file_name = (isset($file_name)) ? $file_name : basename($file_path);
		if (@fopen($check_file_name, 'r') !== false) {
			return true;
		} else {
			// Get ready to upload file to CDN
			$container = $this->container_object();
			$file = $this->file_object($container, $file_path, $file_name);
			if ($file->Create()) {
				return true;
			}
		}
		return false;
	}

	/**
	* Removes given file attachment(s) from CDN
	*/
	public function delete_files( $files ) {
		$container = $this->container_object();

		// Loop through files and delete
		foreach ($files as $cur_file) {
			$file = $container->DataObject();
			$file->name = $cur_file;
			try {
				$file->Delete();
			} catch (Exception $e) {
				return $e;
			}
		}
		return true;
	}
}
?>
