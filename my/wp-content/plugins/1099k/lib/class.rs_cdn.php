<?php
/**
 * Functions used to connect to the CDN
 */
 

/**
 * CDN class
 */
class RS_CDN {


	public $oc_client;
	public $oc_service;
	public $oc_conn_object;
	public $oc_container;
	public $cdn_url;
	public $opencloud_version;
	public $api_settings;
	public $uploads;
	public $object_cache;


	/**
	 *  Create new Openstack Object
	 */
	function __construct($custom_settings = null, $oc_version = null) {
		// Set opencloud version to use
		// $this->opencloud_version = (version_compare(phpversion(), '5.3.3') >= 0) ? '1.10.0' : '1.5.10';
		// $this->opencloud_version = (!is_null($oc_version)) ? $oc_version : $this->opencloud_version;
		$this->opencloud_version = '1.5.10';

		// Get settings, if they exist
		(object) $custom_settings = (!is_null($custom_settings)) ? $custom_settings : null;

        // Set container
        $current_hostname = gethostname();
        $the_container = (strpos($current_hostname, 'local.') !== FALSE || strpos($current_hostname, 'sbcglobal.') !== FALSE) ? 'saltsha_1099k' : 'saltsha_1099k';

		// Set settings
		$settings = new stdClass();
		$settings->username = (isset($custom_settings->username)) ? $custom_settings->username : 'saltsha';
		$settings->apiKey = (isset($custom_settings->apiKey)) ? $custom_settings->apiKey : '9d2fe4da01564dfb8886e9d3a3353440';
		$settings->use_ssl = (isset($custom_settings->use_ssl)) ? $custom_settings->use_ssl : false;
		$settings->container = (isset($custom_settings->container)) ? $custom_settings->container : $the_container;
		$settings->cache_cdn_objects = (isset($custom_settings->cache_cdn_objects)) ? $custom_settings->cache_cdn_objects : 15;
		$settings->last_cache_time = (isset($custom_settings->last_cache_time)) ? $custom_settings->last_cache_time : null;
		$settings->cdn_url = (isset($custom_settings->cdn_url)) ? $custom_settings->cdn_url : null;
		$settings->files_to_ignore = (isset($custom_settings->files_to_ignore)) ? $custom_settings->files_to_ignore : null;
		$settings->remove_local_files = (isset($custom_settings->remove_local_files)) ? $custom_settings->remove_local_files : false;
		$settings->custom_cname = (isset($custom_settings->custom_cname)) ? $custom_settings->custom_cname : null;
		$settings->region = (isset($custom_settings->region)) ? $custom_settings->region : 'ORD';
		$settings->url = (isset($custom_settings->url)) ? $custom_settings->url : 'https://identity.api.rackspacecloud.com/v2.0/';

		// Set API settings
		$this->api_settings = (object) $settings;

		// Return client OR set settings
			if ($this->opencloud_version == '1.10.0') {
			// Set Rackspace CDN settings
			$this->oc_client = $this->opencloud_client();
			$this->oc_service = $this->oc_client->objectStoreService('cloudFiles', $settings->region);
		}

		// Set container object
		$this->oc_container = $this->container_object();
	}


	/**
	 * Cloud files client
	 */
	public function opencloud_client() {
		// Set new Cloud Files client
		$cloud_files_client = new \OpenCloud\Rackspace(
			$this->api_settings->url,
			(array) $this->api_settings
		);

		// Set Rackspace CDN settings
		$this->oc_client = $cloud_files_client;
		
		return $cloud_files_client;
	}


	/**
	 *  Openstack Connection Object
	 */
	function connection_object(){
		if ($this->opencloud_version == '1.10.0') {
			// Return service
			return $this->oc_service;
		} else {
			// If connection object is already set, return it
			if (isset($this->oc_service)) {
				return $this->oc_service;
			}

			// Get settings and connection object
			$api_settings = $this->api_settings;
			$connection = new \OpenCloud\Rackspace(
				$api_settings->url,
				array(
					'username' => $api_settings->username,
					'apiKey' => $api_settings->apiKey
				)
			);

			// Return connection object
			$cdn = $connection->ObjectStore( 'cloudFiles', $api_settings->region, 'publicURL' );
			$this->oc_service = $cdn;
			return $cdn;
		}
	}


	/**
	*  Retrieve Openstack CDN Container Object
	*/
	public function container_object() {
		if ($this->opencloud_version == '1.10.0') {
			$api_settings = $this->api_settings;
			if (!isset($this->oc_container)) {
				try {
					$this->oc_container = $this->oc_service->getContainer($api_settings->container);
				} catch (Exception $exc) {
					$this->oc_container = null;
				}
			}
			return $this->oc_container;
		} else {
			$api_settings = $this->api_settings;
			if (isset($this->oc_container)) {
				try {
					$this->connection_object()->Container($api_settings->container);
					return $this->oc_container;
				} catch (Exception $exc) {
					$new_cdn_instance = new RS_CDN();
					return $new_cdn_instance->oc_container;
				}
			}
			$cdn = $this->connection_object();
			$container = $cdn->Container($api_settings->container);
			$this->oc_container = $container;
			return $this->oc_container;
		}
	}


	/**
	*  Create Openstack CDN File Object
	*/
	public function file_object($container, $file_path, $file_name = null){
		// Get file content
		$file_contents = @file_get_contents( $file_path );
		$file_name = (isset($file_name) && !is_null($file_name)) ? $file_name : basename( $file_path );

		// Create file object
		if ($this->opencloud_version == '1.10.0') {
			return array('file_name' => $file_name, 'file_content' => $file_contents);
		} else {
			$file = $container->DataObject();
			$file->SetData( $file_contents );
			$file->name = $file_name;
			return $file;
		}
	}


	/**
	* Uploads given file attachment onto CDN if it doesn't already exist
	*/
	public function upload_file( $file_path , $file_name = null, $existing_container = null, $post_id = null){
		// Check if file exists
		$check_file_name = (isset($file_name)) ? $file_name : basename($file_path);

		// Get ready to upload file to CDN
		$container = $this->container_object();
		$file = $this->file_object($container, $file_path, $file_name);

		// Upload object
		if ($this->opencloud_version == '1.10.0') {
			if ($container->uploadObject($file['file_name'], $file['file_content'])) {
				return true;
			}
		} else {
			$content_type = get_content_type( $file_path );
			if ($content_type !== false) {
				if ($file->Create(array('content_type' => $content_type))) {
					return true;
				}
			} else {
				if ($file->Create()) {
					return true;
				}
			}
		}

		// Upload failed, remove local images
		if (stripos($file_path, 'http') == 0) {
			$upload_dir = wp_upload_dir();
			$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_path);
			unlink($file_path);
		} else {
			unlink($file_path);
		}

		return false;
	}


	/**
	*  Get list of CDN objects
	*/
	public function get_cdn_objects( $force_cache = false ) {
		// Get time difference
		$this->api_settings->last_cache_time = (isset($this->api_settings->last_cache_time)) ? $this->api_settings->last_cache_time : time();
		$time_diff = (time()-$this->api_settings->last_cache_time)/60;

		// If caching is set, return CDN cache
		if (isset($this->api_settings->cache_cdn_objects) && $this->api_settings->cache_cdn_objects > 0) {
			if (isset($this->object_cache) && $time_diff < $this->api_settings->cache_cdn_objects && $force_cache === false) {
				return $this->object_cache;
			}
			
		}

		// Ensure CDN instance exists
		if (check_cdn() === false) {
			return array('response' => 'fail', 'message' => 'Error instantiating CDN session.');
		}

		// Set array to store CDN objects
		$cdn_objects = array();

		// Get objects
		if ($this->opencloud_version == '1.10.0') {
			$oc_service = $this->opencloud_client()->objectStoreService('cloudFiles', $this->api_settings->region);
			$objects = $oc_service->getContainer($this->api_settings->container)->objectList();
			foreach ($objects as $object) {
				$cdn_objects[] = array('file_name' => $object->getName(), 'file_size' => $object->getContentLength());
			}
		} else {
			$files = $this->container_object()->objectList();
			while ($file = $files->next()) {
				$cdn_objects[] = array('file_name' => $file->name, 'file_size' => $file->bytes);
			}
		}

		// Set object cache
		if ((isset($this->api_settings->cache_cdn_objects) && $this->api_settings->cache_cdn_objects > 0) || $force_cache == true) {
			$this->api_settings->last_cache_time = time();
			$this->object_cache = $cdn_objects;
		}

		// Return CDN objects
		return $cdn_objects;
	}


	/**
	 * Force CDN object cache
	 */
	public function force_object_cache() {
		$this->get_cdn_objects( true );
	}


	/**
	* Removes given file attachment(s) from CDN
	*/
	public function delete_files( $files ) {
		// Get container object
		$container = $this->container_object();

		// Delete object(s)
		if ($this->opencloud_version == '1.10.0') {
			foreach ($files as $cur_file) {
				if (trim($cur_file) == '') {
					continue;
				}
				try {
					$file = $container->getObject($cur_file);
					try {
						$file->Delete();
					} catch (Exception $exc) {
						// Do nothing
					}
				} catch (Exception $exc) {
					// Do nothing
				}
			}
		} else {
			if (count($files) > 0) {
    			foreach ($files as $cur_file) {
    				if (trim($cur_file) == '') {
    					continue;
    				}
    				try {
    					$file = $container->DataObject();
    					$file->name = $cur_file;
    					try {
    						@$file->Delete();
    					} catch (Exception $exc) {
    						// Do nothing
    					}
    				} catch (Exception $exc) {
    					// Do nothing
    				}
    			}
			}
			return true;
		}
	}


    /**
     * Get CDN URL
     */
    public function get_url($type = null) {
    	$type = (is_null($type)) ? 'http' : strtolower($type);
    	if ($this->opencloud_version == '1.10.0') {
    		if ($type == 'ssl' || $type == 'https') {
    			// Return SSL URI
    			return $this->container_object()->getCdn()->getCdnSslUri();
    		} elseif ($type == 'streaming') {
    			// Return Streaming URI
    			return $this->container_object()->getCdn()->getCdnStreamingUri();
    		} elseif ($type == 'ios-streaming') {
    			// Return Streaming URI
    			return $this->container_object()->getCdn()->getIosStreamingUri();
    		} else {
    			// Return HTTP URI
    			return $this->container_object()->getCdn()->getCdnUri();
    		}			
    	} else {
    		if ($type == 'ssl' || $type == 'https') {
    			// Return SSL URI
    			return $this->container_object()->SSLURI();
    		} elseif ($type == 'streaming') {
    			// Return Streaming URI
    			return $this->container_object()->CDNURI();
    		} elseif ($type == 'ios-streaming') {
    			// Return Streaming URI
    			return $this->container_object()->CDNURI();
    		} else {
    			// Return HTTP URI
    			return $this->container_object()->CDNURI();
    		}
    	}
    }
}
?>