<?php
/**
 * Connection layer to CDN.
 */
class CFCDN_CDN {

	public $api_settings;
	public $uploads;
	public $cache_file;
	public $cache_folder;

	function __construct($custom_settings=NULL) {
		$this->api_settings = $this->settings($custom_settings);
		$this->uploads = wp_upload_dir();
		$this->cache_folder = $this->uploads['basedir'] . "/cdn/tmp/";
		$this->cache_file = $this->cache_folder . "cache.csv";
	}
  
	/**
	* CloudFiles CDN Settings.
	*/
	public static function settings($custom_settings=NULL) {
		$default_settings = array( 'username' => 'YOUR USERNAME',
			'apiKey' => 'YOUR API KEY',
			'container' => 'YOUR CONTAINER',
			'public_url' => 'http://YOUR LONG URL.rackcdn.com',
			'region' => 'DFW',
			'url' => 'https://identity.api.rackspacecloud.com/v2.0/',
			'serviceName' => 'cloudFiles',
			'urltype' => 'publicURL',
			'first_upload' => 'false',
			'delete_local_files' => 'true'
		);

		$cdn_settings = get_option( CFCDN_OPTIONS, $default_settings );
		
		if (!is_null($custom_settings)) {
			foreach ($custom_settings as $key => $value) {
				$cdn_settings[$key] = $value;
			}
		}
		
		return $cdn_settings;
	}
  
  
	/**
	 *  Openstack Connection Object.
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
		$cdn = $connection->ObjectStore( $api_settings['serviceName'], $api_settings['region'], $api_settings['urltype'] );
		return $cdn;
	}


	/**
	*  Openstack CDN Container Object.
	*/
	public function container_object(){
		$api_settings = $this->api_settings;
		$cdn = $this->connection_object();
		$container = $cdn->Container($api_settings['container']);
		return $container;
	}


	/**
	*  Openstack CDN File Object.
	*/
	public function file_object($container, $file_path){
		$file = $container->DataObject();
		$file->SetData( @file_get_contents( $file_path ) );
		$file->name = basename( $file_path );
		return $file;
	}


	/**
	* Puts given file attachment onto CDN.
	*/
	public function upload_file( $file_path ){
		// Get ready to upload file to CDN
		$container = $this->container_object();
		$file = $this->file_object($container, $file_path);
		if ($file->Create()) {
			return true;
		}
		return false;
	}

	/**
	* Removes given file attachment from CDN.
	*/
	public function delete_file( $file_path ){
		$container = $this->container_object();
		$file = $container->DataObject();
		$file->name = basename($file_path);
		try {
			$file->Delete();
		} catch (Exception $e) {
			return $e;
		}
		return true;
	}


	/**
	* List of files uploaded to CDN as recorded in cache file.
	*/
	public function get_uploaded_files(){
		if( !file_exists( $this->cache_file ) ){
			mkdir( $this->cache_folder, 0777, true );
			$fp = fopen( $this->cache_file, 'ab' ) or die('Cannot open file:  ' . $this->cache_file );
			fclose( $fp );
		}

		$fp = fopen( $this->cache_file, 'rb' ) or die('Cannot open file:  ' . $this->cache_file );
		$lines = array_map( "rtrim", file( $this->cache_file ) );
		$files = array_diff( $lines, array(".", "..", $this->cache_file) );
		fclose( $fp );

		return $files;
	}


	/**
	* Change setting via key value pair.
	*/
	public function update_setting( $setting, $value ){
		if( current_user_can('manage_options') && !empty($setting) ) {
			$api_settings = $this->api_settings;
			$api_settings[$setting] = $value;
			update_option( CFCDN_OPTIONS, $api_settings );
			$this->api_settings = $api_settings;
		}
	}
}
?>
