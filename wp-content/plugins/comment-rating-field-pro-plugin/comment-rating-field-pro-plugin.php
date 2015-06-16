<?php
/**
* Plugin Name: Comment Rating Field Pro Plugin
* Plugin URI: http://www.wpcube.co.uk/plugins/comment-rating-field-pro-plugin
* Version: 2.4.5
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Adds a 5 star rating field to the comments form in WordPress.
*/

ob_start();

/**
* Comment Rating Field Pro Plugin Class
* 
* @package WP Cube
* @subpackage Comment Rating Field Pro Plugin
* @author Tim Carr
* @version 2.4.5
* @copyright WP Cube
*/
class CommentRatingFieldProPlugin {
    /**
    * Constructor. Acts as a bootstrap to load the rest of the plugin
    */
    function CommentRatingFieldProPlugin() {
    	global $crfpFields;
    	
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name = 'comment-rating-field-pro-plugin'; // Plugin Folder
        $this->plugin->displayName = 'Comment Rating Field Pro'; // Plugin Name
        $this->plugin->freeName = 'comment-rating-field-pro';
        $this->plugin->version = '2.4.5'; // The version of this plugin
        $this->plugin->buildDate = '2014-05-23 18:00:00'; // Build date + time of this version
        $this->plugin->requires = 3.6; // Min. WP version required
        $this->plugin->tested = '3.9.1'; // WP version this plugin has been tested on
        $this->plugin->folder = WP_PLUGIN_DIR.'/'.$this->plugin->name; // Full Path to Plugin Folder
        $this->plugin->url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); // Ful URL to Plugin folder
        $this->plugin->subPanels = array(
        	__('Settings', $this->plugin->name), 
        	__('Rating Fields', $this->plugin->name),
        );
        $this->plugin->documentationURL = 'http://www.wpcube.co.uk/documentation/comment-rating-field-pro-plugin/';
        
        // Post Types and Taxonomies to ignore
		$this->ignoreTypes = array('attachment','revision','nav_menu_item');
       	$this->ignoreTaxonomies = array('post_tag', 'nav_menu', 'link_category', 'post_format');
       	
       	// Settings
        $this->settings = get_option($this->plugin->name);
        if (!is_array($this->settings)) {
        	// Check if there are any settings to import from the free version
        	$freeSettings = get_option($this->plugin->freeName);
        	update_option($this->plugin->name, $freeSettings);
        	$this->settings = get_option($this->plugin->name);
        }
        
        // Dashboard Submodule
        if (!class_exists('WPCubeDashboardWidget')) {
			require_once($this->plugin->folder.'/_modules/dashboard/dashboard.php');
		}
		$dashboard = new WPCubeDashboardWidget($this->plugin); 
		
		// Licensing Submodule
		if (!class_exists('LicensingUpdateManager')) {
			require_once($this->plugin->folder.'/_modules/licensing/lum.php');
		}
		$this->licensing = new LicensingUpdateManager($this->plugin, 'http://www.wpcube.co.uk/wp-content/plugins/lum', $this->plugin->name);

		// Models
		if(!class_exists('WP_List_Table')) require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
		require_once($this->plugin->folder.'/models/fields-table.php');
		require_once($this->plugin->folder.'/models/fields.php');
		$crfpFields = new CRFPFields();
		
		// Hooks
        add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndCSS'));
        add_action('wp_enqueue_scripts', array(&$this, 'adminScriptsAndCSS'));
        add_action('admin_menu', array(&$this, 'adminPanelsAndMetaBoxes'));
        add_action('plugins_loaded', array(&$this, 'loadLanguageFiles'));
        
        // Some hooks / filters might only need to be run if the plugin is licensed.
        // This is how you check:
        if (get_site_transient($this->plugin->name.'_valid') == '1') {
        	// Hooks and filters go here.
        	// Widgets
			add_action('widgets_init', create_function('', 'return register_widget("CRFPTopRatedPosts");'));
			
			// Actions and Filters
			add_action('comment_post', array(&$this, 'SaveRating')); // Save Rating Field on new comment
			add_action('edit_comment', array(&$this, 'SaveRating')); // Save Rating Field on editing existing comment
		    add_action('comment_text', array(&$this, 'DisplayCommentRating')); // Displays Rating on Comments 
		    add_filter('the_excerpt', array(&$this, 'DisplayAverageRatingExcerpt')); // Displays Average Rating for Excerpt
		    add_filter('the_content', array(&$this, 'DisplayAverageRatingContent')); // Displays Average Rating for Content
			
	        if (is_admin()) {
	        	add_action('admin_notices', array(&$this, 'adminNotices'));
	        	add_action('init', array(&$this, 'SetupTinyMCEPlugins'));
            	add_action('wp_set_comment_status', array(&$this, 'UpdatePostRatingByCommentID')); // Recalculate average rating on comment approval / hold / spam
		        add_action('trash_comment', array(&$this, 'UpdatePostRatingByCommentID')); // Recalculate average rating on comment -> trash
		        add_action('delete_comment', array(&$this, 'UpdatePostRatingByCommentID')); // Recalculate average rating on trash -> delete
		        add_action('untrashed_comment', array(&$this, 'UpdatePostRatingByCommentID')); // Recalculate average rating on trash -> restore
		        add_action('save_post', array(&$this, 'saveAverageAndTotalKeys')); // Define average + total rating meta keys if none exist, for post ordering
	        } else {
	        	add_action('wp_enqueue_scripts', array(&$this, 'FrontendScriptsAndCSS'));
	        	
	        	// Display the rating field before or after the comment fields
	        	switch ($this->settings['ratingFieldPosition']) {
	        		case 'above':
	        			// Before All Fields
	        			add_action('comment_form_logged_in_after', array(&$this, 'DisplayRatingField')); // Logged in
	        			add_action('comment_form_before_fields', array(&$this, 'DisplayRatingField')); // Guest
	        			break;
	        		case 'middle':
	        			// Before Comment Field
	        			add_action('comment_form_logged_in_after', array(&$this, 'DisplayRatingField')); // Logged in
	        			add_action('comment_form_after_fields', array(&$this, 'DisplayRatingField')); // Guest
	        			break;
	        		default:
	        			// After Comment Field
	        			add_filter('comment_form_field_comment', array(&$this, 'DisplayRatingField'));
	        			break;
	        	}
	        }
	        
	        // Shortcodes
	        add_shortcode('rating', array(&$this, 'DisplayAverageRatingWithShortcode'));
        	add_shortcode('crfp', array(&$this, 'DisplayAverageRatingWithShortcode'));
        }
    }
    
    /**
    * Activation routines
    */
    function activate() {
    	global $wpdb;

        // Create database tables
        $wpdb->query("	CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."crfp_fields (
							`fieldID` int(10) NOT NULL AUTO_INCREMENT,
							`label` varchar(200) NOT NULL,
							`required` tinyint(1) NOT NULL DEFAULT '0',
							`required_text` varchar(200) NOT NULL,
							`cancel_text` varchar(200) NOT NULL,
							`placementOptions` text NOT NULL,
							PRIMARY KEY (`fieldID`),
							KEY `required` (`required`)
						) ENGINE=MyISAM 
						DEFAULT CHARSET=".$wpdb->charset."
                        AUTO_INCREMENT=1");
    }
    
    /**
    * Deactivation routines
    * Note: these will also run on a plugin upgrade!
    */
    function deactivate() {
    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function adminScriptsAndCSS() {
    	// JS
    	wp_enqueue_script($this->plugin->name.'-jquery-rating-pack', $this->plugin->url.'js/jquery.rating.pack.js', array('jquery'), $this->plugin->version, true);
    	wp_enqueue_script($this->plugin->name.'-frontend', $this->plugin->url.'js/frontend.js', array('jquery'), $this->plugin->version, true);
    	wp_enqueue_script($this->plugin->name.'-admin', $this->plugin->url.'js/admin.js', array('jquery'), $this->plugin->version, true);
    	
    	// Localize JS
    	wp_localize_script($this->plugin->name.'-frontend', 'crfp', array(
    		'ratingDisableReplies' => $this->settings['ratingDisableReplies'],
    		'halfRatings' => ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false),
    	));
    	       
    	// CSS
        wp_enqueue_style($this->plugin->name.'-admin', $this->plugin->url.'css/admin.css', array(), $this->plugin->version);	
        wp_enqueue_style($this->plugin->name.'-frontend', $this->plugin->url.'css/rating.css', array(), $this->plugin->version); // Raings displayed in wp-admin are now styled	
    }
    
    /**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
        // Licensing
        add_menu_page($this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'), 'dashicons-testimonial');
        add_submenu_page($this->plugin->name, __('Licensing', $this->plugin->name), __('Licensing', $this->plugin->name), 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'));
        
        // Only hook sub menu elements if plugin is licensed
        if (get_site_transient($this->plugin->name.'_valid') == '1') {
	        foreach ($this->plugin->subPanels as $key=>$subPanel) {
	        	$url = str_replace(' ', '-', strtolower($subPanel));
	        	$url = str_replace('+', '', strtolower($url));
	        	
	            add_submenu_page($this->plugin->name, $subPanel, $subPanel, 'manage_options', $this->plugin->name.'-'.$url, array(&$this, 'adminPanel'));    
	        }
	        
	        // Comment Meta Box
	        add_meta_box($this->plugin->name.'-ratings', $this->plugin->displayName, array(&$this, 'adminDisplayRatingField'), 'comment', 'normal', 'low');
        }
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
    	global $crfpFields;
    	
    	// Check command to determine what to output
		switch (strtolower(str_replace($this->plugin->name.'-', '', $_GET['page']))) {
    		// Rating Fields
            case 'rating-fields':
            	$cmd = ((isset($_GET['cmd'])) ? $_GET['cmd'] : '');
            	
            	switch ($cmd) {
                    case 'edit':
                        // Check data
                        if (isset($_POST['submit'])) { 
                        	// Map post data back to bar, in case we need it in the form again due to form validation failure
                            $this->field = $crfpFields->TransformPostData($_POST);

                            // Save data and check result
                            $result = $crfpFields->Save($this->field);
                            if (is_wp_error($result)) {
                                $this->errorMessage = $result->get_error_message();
                                $this->field = $crfpFields->Parse($this->field); // Strips slashes
                            } else {
                                $this->message = 'Field '.($_GET['cmd'] == 'add' ? 'created' : 'updated').'.';
                                $this->field = $crfpFields->GetByID($_GET['pKey']); // Get updated bar from DB
                            }
                        } else {
                            if (isset($_GET['pKey'])) $this->field = $crfpFields->GetByID($_GET['pKey']);
                        }
                        
                        // If redirected from add to edit, show the user the bar was created
                        if (isset($_GET['msg'])) $this->message = __('Rating field created.', $this->plugin->name);
                        
                        // View
                        $view = 'views/fields-form.php';
                        
                        break;
                    case 'delete':
                        // Delete single
                        $result = $crfpFields->DeleteByID($_GET['pKey']);
                        if (is_wp_error($result)) {
                            $this->errorMessage = $result->get_error_message();
                        } else {
                            $this->message = __('Field deleted.', $this->plugin->name);
                        }
                        
                        // Include Bars_List_Table class files
                        require_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/models/fields-table.php');
                        $this->wpListTable = new CRFP_List_Table();

                        // View
                        $view = 'views/fields-table.php';
                        
                        break;
                    case 'add':
                    	if (isset($_POST['submit'])) {
                            // Map post data back to bar, in case we need it in the form again due to form validation failure
                            $this->field = $crfpFields->TransformPostData($_POST);
                            
                            // Save data and check result
                            $result = $crfpFields->Save($this->field);
                            if (is_wp_error($result)) {
                                $this->errorMessage = $result->get_error_message();
                                $this->field = $crfpFields->Parse($this->field); // Strips slashes
                            } else {
                            	// Redirect to edit
                            	header('Location: admin.php?page='.$this->plugin->name.'-rating-fields&cmd=edit&pKey='.$result.'&msg=1');
                                die();
                            }
                        } else {                    
	                        // Set some defaults
	                        $this->field = array();
	                        $this->field['label'] = 'Rating';
	                        $this->field['required'] = 0;
	                        $this->field['required_text'] = 'Please supply a rating';
	                        $this->field['cancel_text'] = 'Cancel rating';
	                        $this->field = $crfpFields->Parse($this->field);
                        }
                        
                        // View
                        $view = 'views/fields-form.php';
                        
                        break;
                    default:                        
                        // Bulk Actions
                        $action = '';
                        if (isset($_POST['action2']) AND $_POST['action2'] != '-1') {
                        	$action = $_POST['action2'];
                        } else if (isset($_POST['action']) AND $_POST['action'] != '-1') {
                        	$action = $_POST['action'];
                        }
                        switch ($action) {
                            case 'activate':
                                $result = $crfpFields->ActivateByIDs($_POST['fieldIDs']);
                                if (is_wp_error($result)) {
                                    $this->errorMessage = $result->get_error_message();
                                } else {
                                    $this->message = __('Fields activated.', $this->plugin->name);
                                }
                                break;
                            case 'delete':
                                $result = $crfpFields->DeleteByIDs($_POST['fieldIDs']);
                                if (is_wp_error($result)) {
                                    $this->errorMessage = $result->get_error_message();
                                } else {
                                    $this->message = __('Fields deleted.', $this->plugin->name);
                                }
                                break;
                            case 'deactivate':
                                $result = $crfpFields->DeactivateByIDs($_POST['fieldIDs']);
                                if (is_wp_error($result)) {
                                    $this->errorMessage = $result->get_error_message();
                                } else {
                                    $this->message = __('Fields deactivated.', $this->plugin->name);
                                }
                                break;  
                        }  
                        
                        // Include Bars_List_Table class files
                        require_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/models/fields-table.php');
                        $this->wpListTable = new CRFP_List_Table();
                        
                        // View
                        $view = 'views/fields-table.php';
                        
						break;    
                }
                break;
                
    		case 'settings':
    			// Settings
    			
    			// Save
		        if (isset($_POST['submit'])) {
		        	if (isset($_POST[$this->plugin->name])) {
		        		update_option($this->plugin->name, $_POST[$this->plugin->name]);
						$this->message = __('Settings Updated.', $this->plugin->name);
					}
		        }
		        
		        $view = 'views/settings.php';
		        
    			break;
    		
    		default:
    			// Licensing
    			// Save routine is handled in licensing submodule
    			
    			$view = '_modules/licensing/views/licensing.php';
    			
    			break;
    	}
    
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
        
		// Load Settings Form
        include_once($this->plugin->folder.'/'.$view);  
    }
    
    /**
    * Loads plugin textdomain
    */
    function loadLanguageFiles() {
    	load_plugin_textdomain($this->plugin->name, false, $this->plugin->name.'/languages/');
    }
    
    /**
	* Checks if the active theme's comments.php file has a call to comment_form(). If not, outputs an admin notice
	*/
    function adminNotices() {
    	global $crfpFields;
    	
    	// Check rating fields exist
    	if (!$crfpFields->hasRecords()) {
    		echo ('<div class="error"><p>'.__('Comment Rating Field Pro requires at least one Rating Field in order to function. <a href="admin.php?page='.$this->plugin->name.'-rating-fields&cmd=add" class="button">Configure Now</a>', $this->plugin->name).'</p></div>');
    	}
    }
    
	/**
    * Setup calls to add a button and plugin to the TinyMCE Rich Text Editors, except on the plugin's
    * own screens.
    */
    function SetupTinyMCEPlugins() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array(&$this, 'AddTinyMCEPlugin'));
        	add_filter('mce_buttons', array(&$this, 'AddTinyMCEButton'));
    	}
    }
    
    /**
    * Adds a button to the TinyMCE Editor for shortcode inserts
    */
	function AddTinyMCEButton($buttons) {
	    array_push($buttons, "|", 'crfp');
	    return $buttons;
	}
	
	/**
    * Adds a plugin to the TinyMCE Editor for shortcode inserts
    */
	function AddTinyMCEPlugin($plugin_array) {
	    $plugin_array['crfp'] = $this->plugin->url.'js/editor_plugin.js';
	    return $plugin_array;
	}
	
	/**
    * Adds the rating field, if required, to the comments form in the WordPress Admin
    *
    * @param object $comment Comment
    */
    function adminDisplayRatingField($comment) {
    	global $crfpFields;
    	
    	// Get comment meta
    	$ratings = get_comment_meta($comment->comment_ID, 'crfp', true);
    	if (!is_array($ratings) OR count($ratings) == 0) {
    		_e('No ratings were left by this user.', $this->plugin->name);
    		return;
    	}
    	
    	// Half ratings?
    	$halfRatings = ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false);
    	
    	foreach ($ratings as $fieldID=>$rating) {
    		// Get field
    		$field = $crfpFields->GetByID($fieldID);
    		?>
    		<div class="option">
    			<p class="crfp-field" data-required="<?php echo $field['required']; ?>" data-required-text="<?php echo $field['required_text']; ?>" data-cancel-text="<?php echo $field['cancel_text']; ?>">
    				<strong><?php echo $field['label']; ?></strong>
    				<?php
    				if ($halfRatings) {
	    				?>
	    				<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="0.5"<?php echo (($rating == 0.5) ? ' checked="checked"' : ''); ?> />
						<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="1"<?php echo (($rating == 1) ? ' checked="checked"' : ''); ?> />
						<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="1.5"<?php echo (($rating == 1.5) ? ' checked="checked"' : ''); ?> />
						<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="2"<?php echo (($rating == 2) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="2.5"<?php echo (($rating == 2.5) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="3"<?php echo (($rating == 3) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="3.5"<?php echo (($rating == 3.5) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="4"<?php echo (($rating == 4) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="4.5"<?php echo (($rating == 4.5) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="5"<?php echo (($rating == 5) ? ' checked="checked"' : ''); ?> />
						<?php	
    				} else {
	    				?>
	    				<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="1"<?php echo (($rating == 1) ? ' checked="checked"' : ''); ?> />
						<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="2"<?php echo (($rating == 2) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="3"<?php echo (($rating == 3) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="4"<?php echo (($rating == 4) ? ' checked="checked"' : ''); ?> />
			        	<input name="rating-star-<?php echo $fieldID; ?>" type="radio" class="star" value="5"<?php echo (($rating == 5) ? ' checked="checked"' : ''); ?> />
						<?php
    				}
    				?>
    				<input type="hidden" name="crfp-rating[<?php echo $fieldID; ?>]" value="<?php echo $rating; ?>" />
    			</p>
    		</div>
    		<?php
    	}
    } 
	
	/**
    * Saves the POSTed rating for the given comment ID to the comment meta table,
    * as well as storing the total ratings and average on the post itself.
    * 
    * @param int $commentID
    */
    function SaveRating($commentID) {
    	// Check a rating has been posted
    	if (!isset($_POST['crfp-rating'])) return;
    	
    	// Assign to array, and remove any zero entries
    	$ratingArr = $_POST['crfp-rating'];
    	if (is_array($ratingArr)) {
    		foreach ($ratingArr as $fieldID=>$value) {
    			if ($value == 0) unset($ratingArr[$fieldID]);
    		}
    		if (count($ratingArr) == 0) return; // No ratings were left, so nothing to add to comment meta
    	}
    	
    	// Update rating against comment
        update_comment_meta($commentID, 'crfp', $ratingArr);
        
        // Get post ID from comment and store total and average ratings against post
        // Run here in case comments are set to always be approved
        $this->UpdatePostRatingByCommentID($commentID); 
    }
    
    /**
    * Calculates the average rating and total number of ratings
    * for the given post ID, storing it in the post meta.
    *
    * @param int @postID Post ID
    * @return bool Rating Updated
    */
    function UpdatePostRatingByPostID($postID) {
    	global $wpdb, $crfpFields;	
    	
    	$totalRatings = array();
    	$countRatings = array();
    	$averageRatings = array();
    	$commentsWithARating = 0;

		// Get all comments and total the number of ratings and rating values for fields
		$fields = $crfpFields->GetAll('label', 'ASC', 1, 999, '', true);
		$comments = get_comments(array(
			'post_id' => $postID,
		));
		if (is_array($comments) AND count($comments) > 0) {
			foreach ($comments as $comment) {
				$ratings = get_comment_meta($comment->comment_ID, 'crfp', true);
				
				if (is_array($ratings)) {
					// Fix: 2.1.1 and earlier would allow a rating to be stored with zero values
					// This cannot happen from 2.1.2 onwards, but this code checks if the existing
					// rating array is zero. If so, it deletes it and ignores it.
					foreach ($ratings as $fieldID=>$rating) {
						if ($rating == 0) unset($ratings[$fieldID]);
					}
					if (count($ratings) == 0) {
						delete_comment_meta($comment->comment_ID, 'crfp');
					} else {				
						// If here, comment has valid rating(s)
						$commentsWithARating++;
						foreach ($ratings as $fieldID=>$rating) {
							if (!isset($totalRatings[$fieldID])) {
								$totalRatings[$fieldID] = $rating;
								$countRatings[$fieldID] = 1;
							} else {
								$totalRatings[$fieldID] += $rating;
								$countRatings[$fieldID]++;
							}
						}
					}
				}
			}
		}
		
		// Half Ratings?
		$halfRatings = ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false);
		
		// Calculate the average rating
		if (is_array($totalRatings) AND count($totalRatings) > 0) {
			foreach ($totalRatings as $fieldID=>$totalRating) {
				$averageRating = ($totalRating / $countRatings[$fieldID]);
				if ($halfRatings) {
					// Round to nearest .5
					$averageRatings[$fieldID] = (round(($averageRating*2))/2);	
				} else {
					// Round to nearest whole number
					$averageRatings[$fieldID] = round($averageRating, 0);
				}
				
			}
		}
		
		// Update post meta
		update_post_meta($postID, 'crfp-totals', $totalRatings);
        update_post_meta($postID, 'crfp-averages', $averageRatings);
        update_post_meta($postID, 'crfp-total-ratings', $commentsWithARating);
        if (count($averageRatings) > 0) {
        	$round = (array_sum($averageRatings) / count($averageRatings));
        	if ($halfRatings) {
        		// Round to nearest .5
	        	$round = (round($round*2) / 2);	
	        } else {
	        	// Round to nearest whole number
		        $round = round($round,0);
	        }
        	
        	update_post_meta($postID, 'crfp-average-rating', $round);
        
        }

        return true;
    }

    /**
    * Called by WP action, passes function call to UpdatePostRatingByPostID
    *
    * @param int $commentID Comment ID
    * @return int Comment ID
    */
    function UpdatePostRatingByCommentID($commentID) {
    	if (empty($commentID) OR !is_numeric($commentID)) {
	    	return true;
    	}
    	$comment = get_comment($commentID);
    	$this->UpdatePostRatingByPostID($comment->comment_post_ID);
    	return true;
    }
    
    /**
    * Whenever a Page, Post or CPT is saved, check if meta keys are set for:
    * - crfp-average-rating
    * - crfp-total-ratings
    *
    * If both are not set, define them as zero. This allows for WP_Query meta_value_num
    * sorting on custom queries, where some Posts might never have a rating.
    *
    * If either key IS set, do nothing.
    */
    function saveAverageAndTotalKeys($postID) {
		// Ignore revisions
		if (wp_is_post_revision($postID)) {
			return;
		}    
		
		// Get meta
		$averageRating = get_post_meta($postID, 'crfp-average-rating', true);
        $totalRatings = get_post_meta($postID, 'crfp-total-ratings', true);
        
        // Check meta
        if ($averageRating == '' AND $totalRatings == '') {
	        update_post_meta($postID, 'crfp-average-rating', 0);
	        update_post_meta($postID, 'crfp-total-ratings', 0);
        }
    }
    
    /**
    * Enqueue JS and CSS on frontend site
    */
    function FrontendScriptsAndCSS() {
    	// CSS
    	wp_enqueue_style($this->plugin->name.'-frontend', $this->plugin->url.'css/rating.css');
    	
    	// JS
    	wp_enqueue_script('jquery');
	    wp_enqueue_script('crfp-jquery-rating-pack', $this->plugin->url.'js/jquery.rating.pack.js', 'jquery', false, true);
    	wp_enqueue_script($this->plugin->name.'-frontend', $this->plugin->url.'js/frontend.js', array('jquery'), $this->plugin->version, true);
    	
    	// Localize JS
    	wp_localize_script($this->plugin->name.'-frontend', 'crfp', array(
    		'ratingDisableReplies' => $this->settings['ratingDisableReplies'],
    		'halfRatings' => ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false),
    	));
    }
    
	/**
    * Checks if the post can have a rating.
    *
    * If so, establishes which fields are available for this comment form.
    *
    * @param int $postID Post ID (optional - specified if shortcode is used on a different page)
    * @return bool Post can have rating
    */
    private function PostCanHaveRating($postID = false) {
    	global $post, $crfpFields;
		
    	if (!isset($this->allFields)) $this->allFields = $crfpFields->GetAll('label', 'ASC', 1, 999, '', true);
		if (count($this->allFields) == 0) return false; // No rating fields
		
		// If a $postID has been specified, use that instead of the current Post
        $postID = (($postID != false AND $postID > 0) ? $postID : $post->ID);
		
		// Check if enabled on comment replies
		if (isset($_GET['replytocom'])) {
			if (isset($this->settings['ratingDisableReplies']) AND $this->settings['ratingDisableReplies'] == '1') {
				return false; // Disabled on replies
			}
		}

		// Check if post type is enabled
		$this->fields = array();
    	$postType = get_post_type($postID);
    	foreach ($this->allFields as $field) {
    		// Post Type
    		if (isset($field->placementOptions['type']) AND is_array($field->placementOptions['type'])) {
				foreach ($field->placementOptions['type'] as $type=>$enabled) {	
    				if ($type == $postType) {
    					echo '<!-- Post Type Match for Field #'.$field->fieldID.' for '.$type.' -->';
    					$this->fields[$field->fieldID] = $field; // Add field
    				}
    			}
    		}
    		
    		// Taxonomies
    		if (isset($field->placementOptions['tax']) AND is_array($field->placementOptions['tax'])) {
				foreach ($field->placementOptions['tax'] as $tax=>$termIDs) {
					// Get Post Terms + build array of term IDs
					$postTermIDs = array();
					$terms = wp_get_post_terms($postID, $tax);
					foreach ($terms as $key=>$term) $postTermIDs[] = $term->term_id;	
					
					foreach ($termIDs as $termID=>$intVal) {
						if (in_array($termID, $postTermIDs)) {
							echo '<!-- Taxonomy Type Match for Field #'.$field->fieldID.' for '.$tax.', term ID '.$termID.' -->';
							$this->fields[$field->fieldID] = $field; // Add field
						}
					}
				}
			}
    	}
    	
    	if (count($this->fields) > 0) return true;
    	return false;
    } 
    
    /**
    * Displays the Average Rating on an Excerpt, if required
    *
    * @param string $content Post Excerpt
    * @return string Post Excerpt w/ Ratings HTML
    */
    function DisplayAverageRatingExcerpt($content) {
    	global $post;
    	
    	if (!$this->settings['enabled']['averageExcerpt']) return $content; // Don't display average
        if (is_singular()) return $content; // Only display on a non-single screen
        if (!$this->PostCanHaveRating()) return $content;
        
        // Get Ratings HTML
        $ratingHTML = $this->BuildAverageRatingHTML($this->settings['enabled']['averageExcerpt'],
        											$this->settings['displayStyleExcerpt'], 
        											$this->settings['displayAverageExcerpt'], 
        											$this->settings['averageRatingTextExcerpt'], 
        											$this->settings['displayTotalRatingsExcerpt'],
        											$this->settings['displayBreakdownExcerpt'],
        											$this->settings['displayLinkExcerpt']);
        											
		// Apply filters
        $ratingHTML = apply_filters('crfp_display_post_rating_excerpt', $ratingHTML);
        
        // Return rating widget with excerpt
        if ($this->settings['averageRatingPositionExcerpt'] == 'above') {
        	return $ratingHTML.$content;
        } else {
        	return $content.$ratingHTML;
        }   
    }

    /**
    * Displays the Average Rating on Content, if required
    *
    * @param string $content Post Content
    * @return string Post Content w/ Ratings HTML
    */
    function DisplayAverageRatingContent($content) {
    	global $post;
    	
    	if (!$this->settings['enabled']['average']) return $content; // Don't display average
    	if (!is_singular()) return $content; // Only display on a single screen
        if (!$this->PostCanHaveRating()) return $content;
        
        // Get Ratings HTML
        $ratingHTML = $this->BuildAverageRatingHTML($this->settings['enabled']['average'],
        											$this->settings['displayStyle'], 
        											$this->settings['displayAverage'], 
        											$this->settings['averageRatingText'], 
        											$this->settings['displayTotalRatings'],
        											$this->settings['displayBreakdown'],
        											$this->settings['displayLink']);
        											
		// Apply filters
        $ratingHTML = apply_filters('crfp_display_post_rating_content', $ratingHTML);
        
        // Return rating widget with excerpt
        if ($this->settings['averageRatingPosition'] == 'above') {
        	return $ratingHTML.$content;
        } else {
        	return $content.$ratingHTML;
        } 
    }
    
    /**
    * Called when the CRFP shortcode is used in content.
    *
    * @param array $instance Attributes
    * @return Outputs Rating
    */
    function DisplayAverageRatingWithShortcode($instance) {
      	global $post;
      	
      	// Set defaults if shortcode is missing attributes
      	if (!isset($instance['enabled'])) $instance['enabled'] = 0;
      	if (!isset($instance['displaystyle'])) $instance['displaystyle'] = 2;
      	if (!isset($instance['displayaverage'])) $instance['displayaverage'] = false;
      	if (!isset($instance['averageratingtext'])) $instance['averageratingtext'] = '';
      	if (!isset($instance['displaytotalratings'])) $instance['displaytotalratings'] = true;
      	if (!isset($instance['displaybreakdown'])) $instance['displaybreakdown'] = true;
      	if (!isset($instance['displaylink'])) $instance['displaylink'] = 0;
      	if (!isset($instance['id'])) $instance['id'] = 0;
      	
		// Output
    	$ratingHTML = $this->BuildAverageRatingHTML($instance['enabled'],
        										$instance['displaystyle'], 
        										$instance['displayaverage'],
        										$instance['averageratingtext'], 
        										$instance['displaytotalratings'],
        										$instance['displaybreakdown'],
        										$instance['displaylink'],
        										$instance['id']);
        				
        // Apply filters
        $ratingHTML = apply_filters('crfp_display_post_rating_shortcode', $ratingHTML);
        						
		return $ratingHTML;
    }
    
    /**
    * Returns the average rating HTML markup, which is used by:
    * - content
    * - excerpt
    * - shortcode
    *
    * @param int $displayType Display Type (0=never|1=when ratings exist|2=always)
    * @param string $displayStyle Display Style (''|grey)
    * @param bool $displayAverage Display Average
    * @param string $label Average Label
    * @param bool $displayTotal Show total ratings
    * @param bool $showBreakdown Show Breakdown
    * @param bool $displayLink Display Link
    * @return string Average Rating HTML
    */
    private function BuildAverageRatingHTML($displayType = 0, $displayStyle = '', $displayAverage = false, $label = '', $displayTotal = false, $showBreakdown = true, $displayLink = false, $postID = false) {
        global $post;
        
        // Check if rating needs to be displayed
        if ($displayType == 0) return;
        
        // If a $postID has been specified, show the average rating for that Post
        $postID = (($postID != false AND $postID > 0) ? $postID : $post->ID);

        $ratingHTML = '';
        
        // Get average rating, total ratings and breakdown
        $averageRating = get_post_meta($postID, 'crfp-average-rating', true);
        $totalRatings = get_post_meta($postID, 'crfp-total-ratings', true);
        $totals = get_post_meta($postID, 'crfp-totals', true);
        $averages = get_post_meta($postID, 'crfp-averages', true);
        
        // If above are blank, set to zero
        if (empty($averageRating)) $averageRating = 0;
        if (empty($totalRatings)) $totalRatings = 0;
        
        // Check ratings exist
        if ($displayType == 1 AND $totalRatings == 0) return;

		// Display Average        
        if ($displayAverage) {
        	$ratingHTML = '
	        	<div class="rating-container" itemscope itemtype="http://schema.org/AggregateRating">
		        	<span class="label" itemprop="itemreviewed">';
		        	
		        if ($displayLink) {
			        $ratingHTML .= '<a href="#comments">'.$label.'</a>';
		        } else {
			        $ratingHTML .= $label;
		        }
		        	
		        $ratingHTML .= '
		        	</span>
					<span'.((($displayType == 2 AND $totalRatings == 0) OR $displayStyle == 'grey') ? ' class="rating-always-on"' : '').'>
				    	<span class="crfp-rating crfp-rating-'.str_replace('.','-',$averageRating).'" itemprop="ratingValue">';
				
			// Link average rating to comments    	
			if ($displayLink) {
				$ratingHTML .= '<a href="#comments">'.$averageRating.'</a>';
			} else {
				$ratingHTML .= $averageRating;	
			}
				   	
			$ratingHTML .= '</span>
					</span>';
			
			if ($displayTotal) {
			   	$ratingHTML .= '
			   	<span class="total">
			   		'.__('', $this->plugin->name).'
			   		<span itemprop="reviewCount">'.$totalRatings.'</span>
			   		'.($totalRatings == 1 ? __('Review', $this->plugin->name) : __('Reviews', $this->plugin->name)).'
			   	</span>';
			}
			
			$ratingHTML .= '
				</div>';
		}
			
		// Display Breakdown
		if ($showBreakdown) {
			if (!isset($this->fields) OR count($this->fields) == 0) {
				// Get fields by calling PostCanHaveRating
				// This happens when using a shortcoe with a post ID specified
				$this->PostCanHaveRating($postID);
			}
			
			// Now $this->fields will be populated
			if (isset($this->fields) AND count($this->fields) > 0) {
		        foreach ($this->fields as $fieldID=>$field) {
		        	if (!isset($averages[$fieldID])) $averages[$fieldID] = 0;
		        	
		        	$ratingHTML .= '
					<div class="rating-container">
						<span class="label">'.$field->label.'</span>
						<span'.((($displayType == 2 AND $totalRatings == 0) OR $displayStyle == 'grey') ? ' class="rating-always-on"' : '').'>
					    	<span class="crfp-rating crfp-rating-'.str_replace('.','-',$averages[$fieldID]).'">'.$averages[$fieldID].'</span>
					   	</span>
					</div>';
				}
			}
		}
		
		// Apply filters
        $ratingHTML = apply_filters('crfp_display_post_rating', $ratingHTML);
		
		return $ratingHTML;
    }
    
    /**
    * Appends the rating to the end of the comment text for the given comment ID
    * 
    * @param text $comment
    */
    function DisplayCommentRating($comment) {
        global $post, $crfpFields;
        
        $commentID = get_comment_ID();
        
        // Check whether we need to display ratings
        if (!isset($this->display) OR !$this->display) { // Prevents checking for every comment in a single Post
        	$this->display = $this->PostCanHaveRating();
    	}

        // Display rating?
        $ratingHTML = '';
        if ($this->display) {
        	// Calculate Average Rating
            $ratings = get_comment_meta($commentID, 'crfp', true);
            $halfRatings = ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false);
    	    $averageRating = (is_array($ratings) ? (array_sum($ratings) / count($ratings)) : 0);
            if ($halfRatings) {
        		// Round to nearest .5
	        	$averageRating = (round($averageRating*2) / 2);	
	        } else {
	        	// Round to nearest whole number
		        $averageRating = round($averageRating,0);
	        }
	        
            // Show Average
            if ($this->settings['displayAverageComment'] == 2 OR ($this->settings['displayAverageComment'] && $averageRating > 0)) {
            	$ratingHTML .= '
	        		<div class="rating-container">
						<span class="label">'.$this->settings['commentRatingText'].'</span>
						<span'.(($this->settings['displayStyleComment'] == 'grey') ? ' class="rating-always-on"' : '').'>
					    	<span class="crfp-rating crfp-rating-'.str_replace('.','-',$averageRating).'">'.$averageRating.'</span>
					   	</span>
					 </div>';		
            }
            
            // Show Breakdown
            if ($this->settings['displayBreakdownComment'] AND is_array($ratings) AND count($ratings) > 0) {
            	foreach ($ratings as $fieldID=>$rating) {
            		// Find field to get its label
            		$label = '';
            		foreach ($this->fields as $key=>$field) {
            			if ($field->fieldID == $fieldID) $label = $field->label;
            		}
            	
            		$ratingHTML .= '
            		<div class="rating-container">
            			<span class="label">'.$label.':</span>
            			<span'.(($this->settings['displayStyleComment'] == 'grey') ? ' class="rating-always-on"' : '').'>
					    	<span class="crfp-rating crfp-rating-'.str_replace('.','-',$rating).'">'.$rating.'</span>
					   	</span>
					</div>';	
            	}
            } 
        }
        
        // Strip newlines from $ratingHTML, as WordPress will convert these to <br> in comments
        $ratingHTML = str_replace(array("\r", "\n"), '', $ratingHTML);
        
        // Apply filters
        $ratingHTML = apply_filters('crfp_display_comment_rating', $ratingHTML, $comment);
        
        // Return rating widget with comment
        if ($this->settings['commentRatingPosition'] == 'above') {
        	return $ratingHTML.$comment;
        } else {
        	return $comment.$ratingHTML;
        }
    } 
    
    /**
    * Adds the rating field, if required, to the comments form
    *
    * Called by:
    * - add_action. $commentFieldHTML will be an array of fields
    * - add_filter('comment_form_field_comment'), which sends us the comment form field HTML markup, so we must return this too.
    */
    function DisplayRatingField($commentFieldHTML = '') {
    	if (!$this->PostCanHaveRating()) return $commentFieldHTML;
    	
    	// Half ratings?
    	$halfRatings = ((isset($this->settings['enableHalfRatings']) AND $this->settings['enableHalfRatings'] == '1') ? true : false);
    	
    	$html = '';
    	foreach ($this->fields as $key=>$field) {
    		$html .= '<p class="crfp-field" data-required="'.$field->required.'" data-required-text="'.$field->required_text.'" data-cancel-text="'.$field->cancel_text.'">
		        <label for="rating-star-'.$field->fieldID.'">'.$field->label.'</label>';
		        
		    if ($halfRatings) {
			    $html .= '<input name="rating-star-'.$field->fieldID.'" type="radio" class="star'.($field->required ? ' required' : '').'" value="0.5" />
			    <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="1" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="1.5" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="2" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="2.5" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="3" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="3.5" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="4" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="4.5" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="5" />';
		    } else {
			    $html .= '<input name="rating-star-'.$field->fieldID.'" type="radio" class="star'.($field->required ? ' required' : '').'" value="1" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="2" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="3" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="4" />
		        <input name="rating-star-'.$field->fieldID.'" type="radio" class="star" value="5" />';
		    }
		        
		    $html .='	<input type="hidden" name="crfp-rating['.$field->fieldID.']" value="0" />
		    </p>';
    	}
    	
    	// Apply filters
        $html = apply_filters('crfp_display_rating_field', $html);
    	
    	// If $commentFieldHTML is a non-empty string, then this is called using add_filter, so we always want
    	// to return the comment field first, then the rating field.
    	// Otherwise, OUTPUT the rating field.
    	if (isset($commentFieldHTML) AND !is_array($commentFieldHTML) AND !empty($commentFieldHTML)) {
    		return $commentFieldHTML.$html;
    	} else {
    		echo $html;
    	}
    } 
}

/**
* Top Rated Posts Widget
*/
class CRFPTopRatedPosts extends WP_Widget {
    /**
    * Constructor.  Sets up Widget jobs, and adds it to the WP_Widget class
    */
    function CRFPTopRatedPosts() {
        $widget_ops = array('classname' => 'widget_crfp_top_rated', 'description' => __('Display the top rated Posts for any given taxonomy.', 'comment-rating-field-pro-plugin') );
        $this->WP_Widget('crfp-top-rated', __('CRFP Top Rated Posts', 'comment-rating-field-pro-plugin'), $widget_ops);
        $this->alt_option_name = 'widget_crfp_top_rated_widget';
    }

    /**
    * Displays the front end widget
    * 
    * @param string $args Native Wordpress Vars
    * @param string $instance Configurable jobs
    */
    function widget($args, $instance) { 
    	// Check if we have selected a post type or a term
    	if (strpos($instance['postTypeOrTerm'], '_') !== false) {
    		list($postType, $taxonomy, $term) = explode('_', $instance['postTypeOrTerm']);
    	} else {
    		$postType = $instance['postTypeOrTerm'];
    	}

    	if ($taxonomy != '' AND $term != '') {
    		// Query by taxonomy and term
    		$posts = new WP_Query(array(
	    		'post_type' => array($postType),
	    		'post_status' => 'publish',
	    		'meta_key' => 'crfp-average-rating',
	    		'orderby' => 'meta_value_num',
	    		'order' => 'DESC',
	    		'tax_query' => array(
	    			array(
	    				'taxonomy' => $taxonomy,
	    				'field' => 'id',
	    				'terms' => array($term)
	    			)
	    		),
	    		'posts_per_page' => (is_numeric($instance['limit']) ? $instance['limit'] : 5) 
	    	));
    	} else {
    		// Query by post type only
    		$posts = new WP_Query(array(
	    		'post_type' => array($postType),
	    		'post_status' => 'publish',
	    		'meta_key' => 'crfp-average-rating',
	    		'orderby' => 'meta_value_num',
	    		'order' => 'DESC',
	    		'posts_per_page' => (is_numeric($instance['limit']) ? $instance['limit'] : 5) 
	    	));
    	}
 		?>
 		<div class="widget widget_crfp_top_rated">
 			<?php
 			if (!empty($instance['title'])) { 
 				$title = apply_filters('widget_title', $instance['title']);
 				echo $args['before_title'].$title.$args['after_title'];
 			}
 			?>
	 		<ul>
				<?php
				if (is_array($posts->posts) AND count($posts->posts) > 0) {
					foreach ($posts->posts as $key=>$post) {
						// Get average rating
						$averageRating = get_post_meta($post->ID, 'crfp-average-rating', true);
						echo ('	<li>
									<a href="'.get_permalink($post->ID).'" title="'.$post->post_title.'">'.$post->post_title.'</a>
									<div class="crfp-rating crfp-rating-'.str_replace('.','-',$averageRating).'"></div>
								</li>');
					}
				}
				?>
			</ul>
		</div>
		<?php
    }

    /**
    * Process the new settings before they're sent off to be saved
    * 
    * @param array $new_instance Array of settings we're about to process, before saving
    * @param array $old_instance Old Settings
    * @return array New Settings to be saved
    */
    function update($new_instance, $old_instance) {                
        return $new_instance;
    }
    
    /**
    * Creates the edit form for the widget
    * 
    * @param array $instance Current Settings
    */
    function form($instance) {
    	// Post Types and Taxonomies to ignore
		$ignoreTypes = array('attachment','revision','nav_menu_item');
		$ignoreTaxonomies = array('post_tag', 'nav_menu', 'link_category', 'post_format');
		
		// Go through all Post Types
		$types = get_post_types('', 'names');
		foreach ($types as $key=>$type) {
    		if (in_array($type, $ignoreTypes)) continue; // Skip ignored Post Types
    		$postType = get_post_type_object($type);
    		$options[$type] = $postType->label; // Add post type to options list

    		// Go through all taxonomies for this Post Type
    		$taxonomies = get_object_taxonomies($type);
    		foreach ($taxonomies as $taxKey=>$taxonomyProgName) {
				if (in_array($taxonomyProgName, $ignoreTaxonomies)) continue; // Skip ignored taxonomies
				
				// Go through this taxonomies terms
				$taxonomy = get_taxonomy($taxonomyProgName);
				$terms = get_terms($taxonomyProgName, array('hide_empty' => 0));
				foreach ($terms as $termKey=>$term) {
                	$options[$type.'_'.$taxonomyProgName.'_'.$term->term_id] = $postType->label.': '.$taxonomy->label.': '.$term->name; // Add term to options list 	   
				}
			}	
    	}

        echo (' <p>
                    <label for="'.$this->get_field_id('title').'">
                        Title:
                        <input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.(isset($instance['title']) ? $instance['title'] : '').'" class="widefat" />
                    </label>
                </p>
                <p>
                   <label for="'.$this->get_field_id('postTypeOrTerm').'">
                        Post Type / Taxonomy / Term:
                        <select name="'.$this->get_field_name('postTypeOrTerm').'" id="'.$this->get_field_id('postTypeOrTerm').'" size="1">');
        foreach ($options as $key=>$option) {
			echo ('     	<option value="'.$key.'"'.((isset($instance['postTypeOrTerm']) AND $instance['postTypeOrTerm'] == $key) ? ' selected' : '').'>'.$option.'</option>'); 
        }                    
        echo ('         </select>
                    </label>
                </p>
                <p>
                    <label for="'.$this->get_field_id('limit').'">
                        Number of Posts:
                        <input type="text" name="'.$this->get_field_name('limit').'" id="'.$this->get_field_id('limit').'" value="'.(isset($instance['limit']) ? $instance['limit'] : '').'" class="widefat" />
                    </label>
                </p>');
    }
} // Close CRFPTopRatedPosts

$crfpCore = new CommentRatingFieldProPlugin();

// Activation + deactivation hooks - need to be outside of the class in order to function
register_activation_hook(__FILE__, array(&$crfpCore, 'activate'));
register_deactivation_hook(__FILE__, array(&$crfpCore, 'deactivate'));

/**
* Function wrapper to manually output rating fields within a custom comments form
*/
function display_rating_field() {
	global $crfpCore;
	$crfpCore->DisplayRatingField();
}

/**
* Function wrapper to manually output average rating, as some users don't like using do_shortcode, due to the regex overhead
*
* @param array $instance Display Arguments
*/
function display_average_rating($instance = array()) {
	global $crfpCore;
	echo $crfpCore->DisplayAverageRatingWithShortcode($instance);
}
?>