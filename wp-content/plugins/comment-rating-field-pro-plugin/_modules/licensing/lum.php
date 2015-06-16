<?php
/**
* Licensing and Update Manager Class
* 
* @package WP Cube
* @subpackage Licensing Wrapper
* @author Tim Carr
* @version 2.0.1
* @copyright WP Cube
*/
class LicensingUpdateManager {
    /**
    * Constructor.
    * 
    * @param object $plugin WordPress Plugin
    * @param string $endpoint Licensing Endpoint
    */
    function LicensingUpdateManager($plugin, $endpoint) {
        // Plugin Details
        $this->plugin = $plugin;
        $this->endpoint = $endpoint;
        
        // Admin Notice
        $this->notice = new stdClass;
        
        if (is_admin()) {
        	// Check if the licensing form has been submitted
        	// If so, save its results before we check the license key validitiy
			if (isset($_POST['submit'])) {
	        	if (isset($_POST[$this->plugin->name]['licenseKey'])) {
	        		update_option($this->plugin->name.'_licenseKey', $_POST[$this->plugin->name]['licenseKey']);
					
					// Force a license key check
					$this->checkLicenseKeyIsValid(true);
				}
			} else if (isset($_GET['page']) AND $_GET['page'] == $this->plugin->name) {
				// GET request on licensing screen
				// Force a license key check
				$this->checkLicenseKeyIsValid(true);
	        }
        	
        	// Hooks
        	add_action('admin_notices', array(&$this, 'adminNotices'));
        	add_filter('pre_set_site_transient_update_plugins', array(&$this, 'apiCheck'));
        	add_filter('plugins_api', array(&$this, 'getPluginInfo'), 10, 3);
        	add_filter('upgrader_post_install', array(&$this, 'updatePlugin' , 10, 3));
        	
        	// Import, Export + Support
			if (get_site_transient($this->plugin->name.'_valid') == '1') {
				add_action('admin_menu', array(&$this, 'adminPanels'), 99);
				add_action('plugins_loaded',array(&$this, 'exportSettings'));
        	}
        }
        
        // Always perform a license check, so if the transient expires, a fresh check takes place
        // to update the transient.
        $this->checkLicenseKeyIsValid(false);
    }
    
    /**
    * Outputs Administration Notices relating to license key validation
    */
    function adminNotices() {
		if (!isset($this->notice->message)) return false;
		echo ('<div class="'.((isset($this->notice->error) AND $this->notice->error == 1) ? 'error' : 'updated success').'"><p>'.$this->notice->message.'</p></div>');
    }
    
    /**
    * Checks whether a license key has been specified in the settings table.
    * 
    * @return bool License Key Exists
    */                   
    function checkLicenseKeyExists() {
    	$licenseKey = get_option($this->plugin->name.'_licenseKey');
		return ((isset($licenseKey) AND trim($licenseKey) != '') ? true : false);
    }    
    
    /**
    * Checks whether the license key stored in the settings table exists and is valid.
    *
    * If so, we store the latest remote version available.
    * 
    * @param bool $force Force License Key Check (used when saving the licensing screen form options)
    * @return bool License Key Valid
    */
    function checkLicenseKeyIsValid($force = false) { 
    	// Check last result from transient
    	// If it exists and is valid, assume the license key is still valid until
    	// this transient expires
    	if (!$force) {
    		if (get_site_transient($this->plugin->name.'_valid') == '1') {
    			// OK
    			return true;
    		}
    	}
    	
    	// If here, we're either forcing a check, the transient does not exist / has expired,
    	// or the license key wasn't valid last time around, so we need to keep checking.
    	if (!$this->checkLicenseKeyExists()) {
    		$this->notice->error = 1;
    		$this->notice->message = __($this->plugin->displayName.': Please specify a license key on the Licensing screen', $this->plugin->name);
    		delete_site_transient($this->plugin->name.'_valid');
    		delete_site_transient($this->plugin->name.'_version');
			delete_site_transient($this->plugin->name.'_package');
    		return false;
		}
		
		$isMultisite = (is_multisite() ? '1' : '0');
		$url = $this->endpoint."/index.php?request=CheckLicenseKeyIsValid&params[]=".get_option($this->plugin->name.'_licenseKey').'&params[]='.$this->plugin->name.'&params[]='.urlencode(str_replace('http://', '', get_bloginfo('url'))).'&params[]='.$isMultisite;
		
		// Set user agent to beat aggressive caching
		$response = wp_remote_get($url, array(
        	'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36',
        ));
		
		if (is_wp_error($response)) {
        	// Could not connect to licensing server
        	// Assume the license key is valid, so the plugin can run, but don't permit updates right now
        	set_site_transient($this->plugin->name.'_valid', 1, (HOUR_IN_SECONDS*12));
        	delete_site_transient($this->plugin->name.'_version');
			delete_site_transient($this->plugin->name.'_package');
        	return true;
        }
 
        $result = json_decode($response['body']);
        
		// Check license key is valid
		if ((int) $result->code != 1) {
			delete_site_transient($this->plugin->name.'_valid');
			delete_site_transient($this->plugin->name.'_version');
			delete_site_transient($this->plugin->name.'_package');
			
			$this->notice->error = 1;
    		$this->notice->message = __($this->plugin->displayName.': '.(string) $result->codeDescription, $this->plugin->name);
    		return false;	
		}

		// If here, license key is valid
		// Update in plugin settings, and store the remote version and packages available
		$this->notice->error = 0;
		$this->notice->message = __($this->plugin->displayName.': '.(string) $result->codeDescription, $this->plugin->name);
		set_site_transient($this->plugin->name.'_valid', 1, (HOUR_IN_SECONDS*12));
		set_site_transient($this->plugin->name.'_version', (string) $result->productVersion, (HOUR_IN_SECONDS*12));
		set_site_transient($this->plugin->name.'_package', (string) stripslashes($result->productPackage), (HOUR_IN_SECONDS*12));
		return true;
    }  
    
    /**
    * Hooks into the plugin update check, and checks our endpoint to see if an update is available
    */
    function apiCheck($transient) {
    	$thisPluginRemoteVersion = get_site_transient($this->plugin->name.'_version');
    	if ($thisPluginRemoteVersion > $this->plugin->version) {
			// New version available - add to transient
			$name = $this->plugin->name.'/'.$this->plugin->name.'.php';
			$response = new stdClass;
	        $response->new_version = $thisPluginRemoteVersion;
	        $response->slug = $this->plugin->name;
	        $response->url = 'http://www.wpcube.co.uk';
	        $response->package = get_site_transient($this->plugin->name.'_package');
	        $transient->response[$name] = $response;
        }
   
        return $transient;
    }
    
    /**
    * Content to output when 'View version X details' is clicked in Plugins screen
    *
    * Loads the plugin's readme.txt file 'changelog' section and outputs it here.
    */
    function getPluginInfo($false, $action, $response) {
    	// Check if this call API is for the right plugin
        if (!isset($response->slug) || $response->slug != $this->plugin->name) return false;
        
        // Get readme.txt file to get sections
        $filename = $this->plugin->folder.'/readme.txt'; // @TODO change to remote URL
		$handle = fopen($filename, 'r');
		$readmeContent = fread($handle, filesize($filename));
		fclose($handle);
		
		// Changelog
		$startPos = strpos($readmeContent, '== Changelog ==')+15;
		$endPos = strpos($readmeContent, '== Upgrade Notice ==', $startPos);
		$changeLog = $this->parseReadmeFile(substr($readmeContent, $startPos, ($endPos-$startPos)));
		
		// Return data
        $response->slug = $this->plugin->name;
        $response->plugin_name  = $this->plugin->displayName;
        $response->version = get_site_transient($this->plugin->name.'_version');
        $response->author = 'WP Cube';
        $response->homepage = 'http://www.wpcube.co.uk/plugins/'.$this->plugin->name;
        $response->requires = $this->plugin->requires;
        $response->tested = $this->plugin->tested;
        $response->downloaded = 0;
        $response->last_updated = $this->plugin->buildDate;
        $response->sections = array(
        	'changelog' => $changeLog,
        );
        $response->download_link = get_site_transient($this->plugin->name.'_package');

        return $response;	
    }
    
    /**
    * Parses the plugin's readme file into HTML compatible output for the plugin info window (getPluginInfo)
    */
    function parseReadmeFile($file) {
    	// Source: http://wordpress.org/plugins/readme-parser/
		// line end to \n
		$file = preg_replace("/(\n\r|\r\n|\r|\n)/", "\n", $file);
	
		// headlines
		$s = array('===','==','=' );
		$r = array('h2' ,'h3','h4');
		for ( $x = 0; $x < sizeof($s); $x++ )
			$file = preg_replace('/(.*?)'.$s[$x].'(?!\")(.*?)'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file);
	
		// inline
		$s = array('\*\*','\''  );
		$r = array('b'   ,'code');
		for ( $x = 0; $x < sizeof($s); $x++ )
			$file = preg_replace('/(.*?)'.$s[$x].'(?!\s)(.*?)(?!\s)'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file);
		
		// ' _italic_ '
		$file = preg_replace('/(\s)_(\S.*?\S)_(\s|$)/', ' <em>$2</em> ', $file);
		
		// ul lists	
		$s = array('\*','\+','\-');
		for ( $x = 0; $x < sizeof($s); $x++ )
			$file = preg_replace('/^['.$s[$x].'](\s)(.*?)(\n|$)/m', '<li>$2</li>', $file);
		$file = preg_replace('/\n<li>(.*?)/', '<ul><li>$1', $file);
		$file = preg_replace('/(<\/li>)(?!<li>)/', '$1</ul>', $file);
		
		// ol lists
		$file = preg_replace('/(\d{1,2}\.)\s(.*?)(\n|$)/', '<li>$2</li>', $file);
		$file = preg_replace('/\n<li>(.*?)/', '<ol><li>$1', $file);
		$file = preg_replace('/(<\/li>)(?!(\<li\>|\<\/ul\>))/', '$1</ol>', $file);
		
		// ol screenshots style
		$file = preg_replace('/(?=Screenshots)(.*?)<ol>/', '$1<ol class="readme-parser-screenshots">', $file);
		
		// line breaks
		$file = preg_replace('/(.*?)(\n)/', "$1<br/>\n", $file);
		$file = preg_replace('/(1|2|3|4)(><br\/>)/', '$1>', $file);
		$file = str_replace('</ul><br/>', '</ul>', $file);
		$file = str_replace('<br/><br/>', '<br/>', $file);
		
		// urls
		$file = str_replace('http://www.', 'www.', $file);
		$file = str_replace('www.', 'http://www.', $file);
		$file = preg_replace('#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#', '$1<a href="$2$3">$3</a>$4', $file);
			
		return $file;
    }
    
	/**
	* Move and activate the updated plugin
	*/
    function updatePlugin($true, $hook_extra, $result) {
    	global $wp_filesystem;

        // Move & Activate
        $wp_filesystem->move($result['destination'], $this->plugin->folder);
        $result['destination'] = $this->plugin->folder;
        $activate = activate_plugin($this->plugin->folder.'/'.$this->plugin->name.'.php');

        // Output the update message
        if (is_wp_error($activate)) {
        	// Failed
        	return __('Plugin updated, but could not be reactivated. Please reactivate manually.', $this->plugin->name);
        } else {
        	// Success
        	return __('Plugin updated and reactivated successfully.', $this->plugin->name);
        }
    }
    
    /**
    * Add Import, Export + Support Panels to the WordPress Administration interface
    */
    function adminPanels() {
    	add_submenu_page($this->plugin->name, __('Import & Export', $this->plugin->name), __('Import & Export', $this->plugin->name), 'manage_options', $this->plugin->name.'-import-export', array(&$this, 'importExportPanel')); 
    	add_submenu_page($this->plugin->name, __('Support', $this->plugin->name), __('Support', $this->plugin->name), 'manage_options', $this->plugin->name.'-support', array(&$this, 'supportPanel'));
    }
    
    /**
    * Import / Export Panel
    */
    function importExportPanel() {
        if (isset($_POST['submit'])) {
        	// Check nonce
        	if (!isset($_POST[$this->plugin->name.'_nonce'])) {
	        	// Missing nonce	
	        	$this->errorMessage = __('nonce field is missing. Settings NOT saved.', $this->plugin->name);
        	} elseif (!wp_verify_nonce($_POST[$this->plugin->name.'_nonce'], $this->plugin->name)) {
	        	// Invalid nonce
	        	$this->errorMessage = __('Invalid nonce specified. Settings NOT saved.', $this->plugin->name);
        	} else {       
        		// Read file
        		if (!is_array($_FILES)) {
	        		$this->errorMessage = __('No JSON file uploaded.', $this->plugin->name);
        		} elseif ($_FILES['import']['error'] != 0) {
	        		$this->errorMessage = __('Error when uploading JSON file.', $this->plugin->name);
        		} else {
	        		$handle = fopen($_FILES['import']['tmp_name'], "r");
					$json = fread($handle, $_FILES['import']['size']);
					fclose($handle);
					$settings = json_decode($json, true);
					
					if (!is_array($settings)) {
						$this->errorMessage = __('Supplied file is not a CRFP settings file, or has become corrupt.', $this->plugin->name);
					} else {
						// Save
						if (isset($this->plugin->settingsName)) {
							delete_option($this->plugin->settingsName);
							update_option($this->plugin->settingsName, $settings);	
						} else {
							delete_option($this->plugin->name);
							update_option($this->plugin->name, $settings);
						}
						
						$this->message = __('Settings Imported.', $this->plugin->name);
					}	
        		}
			}
        }
        
		// Output view
		include_once('views/import-export.php');;  
    }
    
    /**
    * Support Panel
    */
    function supportPanel() {    			
		// Get debug information
		global $wp_version;
		$debug = array();
		$debug['wordpress_version'] = $wp_version;
		$debug['php_version'] = phpversion();
		$debug['plugin'] = $this->plugin;
		$debug['domain'] = get_bloginfo('url');
		
		// Output view
		include_once('views/support.php');
    }
    
    /**
    * If we have requested the export JSON, force a file download
    */	
    function exportSettings() {
    	// Check we are on the right page
		if (!isset($_GET['page'])) {
			return;
		}
		if ($_GET['page'] != $this->plugin->name.'-import-export') {
			return;
		}
		if (!isset($_GET['export'])) {
			return;
		}
		if ($_GET['export'] != 1) {
			return;
		}
		
		// Get settings
		$settings = get_option($this->plugin->name);
		
		// If settings are false, we masy be using settingsName
		if (!$settings) {
			$settings = get_option($this->plugin->settingsName);
		}
		
		// Build JSON
		$json = json_encode($settings);
		
		header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=export.json");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $json;
        exit();
    }
}
?>