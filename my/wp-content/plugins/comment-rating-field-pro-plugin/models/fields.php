<?php
/**
* Rating Fields Model
* 
* @package WordPress
* @subpackage Comment Rating Field Pro
* @author n7 Studios
* @version 2.0
* @copyright n7 Studios 
*/   
class CRFPFields {

	/**
	* Primary SQL Table
	*/
	var $primaryTable = 'crfp_fields';
	
	/**
	* Primary SQL Table Primary Key
	*/
	var $primaryTableKey = 'fieldID';
	
    /**
    * Returns an array of records
    * 
    * @param string $orderBy Order By Column (default: name, optional)
    * @param string $order Order Direction (default: ASC, optional)
    * @param int $paged Pagination (default: 1, optional)
    * @param int $resultsPerPage Results per page (default: 5, optional)
    * @param string $search Search Keywords (optional)
    * @param bool $getAll Get all results (ignore pagination, optional)
    * @return array Records
    */
    function GetAll($orderBy = 'label', $order = 'ASC', $paged = 1, $resultsPerPage = 10, $search = '', $getAll = false) {
        global $wpdb;
        
        // Check in case empty parameters have been sent
        if (empty($orderBy)) $orderBy = 'label';
        if (empty($order)) $order = 'ASC';
        if (empty($paged)) $paged = 1;
        if (empty($resultsPerPage)) $resultsPerPage = 10;
        
        if (!empty($search)) {
	    	$query = $wpdb->prepare(" 	SELECT *
                                    	FROM ".$wpdb->prefix.$this->primaryTable."
                                    	WHERE label LIKE '%%%s%%'
                                    	ORDER BY %s %s
                                    	%s",
                                    	$search,
                                    	$orderBy,
                                    	$order,
                                    	(!$getAll ? " LIMIT ".(($paged - 1) * $resultsPerPage).",".$resultsPerPage : ""));    
        } else {
	        $query = $wpdb->prepare(" 	SELECT *
                                    	FROM ".$wpdb->prefix.$this->primaryTable."
                                    	ORDER BY %s %s
                                    	%s",
                                    	$orderBy,
                                    	$order,
                                    	(!$getAll ? " LIMIT ".(($paged - 1) * $resultsPerPage).",".$resultsPerPage : "")); 
        }
        
        
        $results = $wpdb->get_results($query);
        
        // Get taxonomy information                        	
		if ($results AND count($results) > 0) {
			foreach ($results as $key=>$result) {
				$placementOptions = unserialize($result->placementOptions);
				$results[$key]->placementOptions = $placementOptions;
				if (is_array($placementOptions)) {
					if (isset($placementOptions['type']) AND is_array($placementOptions['type'])) {
						foreach ($placementOptions['type'] as $type=>$enabled) {							
							$typeObj = get_post_type_object($type);
							if (!is_object($typeObj)) continue; // Fix for when existing post types are no longer available but still in settings
							$results[$key]->targeting->type[] = $typeObj->labels->name;
						}
					}
					if (isset($placementOptions['tax']) AND is_array($placementOptions['tax'])) {
						foreach ($placementOptions['tax'] as $tax=>$termIDs) {
							foreach ($termIDs as $termID=>$ignore) $results[$key]->targeting->tax[$tax][] = get_term($termID, $tax, ARRAY_A);
						}
					}	
				}
			}
		}
            
      	return $results;
    }
    
    /**
    * Checks if any records exist
    *
    * @return bool Exists
    */
    function hasRecords() {
    	global $wpdb;
    	
    	$count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix.$this->primaryTable);
    	return (($count > 0) ? true : false);
    } 
    
    /**
    * Returns an array of all fields that are active and set to be displayed for the given taxonomy
    * and terms array
    *
    * @param string $tax Taxonomy
    * @param array $terms Terms Object or Term IDs
    * @return mixed Records / WP_Error / false
    */
    function GetTaxonomyTermActive($tax, $terms) {
    	global $wpdb;
    	
    	// Build array of term IDs from $terms
    	foreach ($terms as $key=>$termObj) {
    		if (is_object($termObj)) {
    			$termIDs[] = $termObj->term_id;
    		} else {
    			$termIDs[] = $termObj;
    		}
    	}
    	
    	$results = $wpdb->get_results(" SELECT *
                                    	FROM ".$wpdb->prefix.$this->primaryTable."
                                    	ORDER BY name ASC");
		if ($results === FALSE) return new WP_Error('db_query_error', __('Fields could not be obtained from the database.', 'comment-rating-field-pro'), $wpdb->last_error);
		
		if (is_array($results)) {
			foreach ($results as $key=>$result) {
				// Unserialise placementOptions
				$placementOptions = unserialize($result->placementOptions);
				
				if (!is_array($placementOptions)) continue; // Skip results with no placement options
				if (!is_array($placementOptions['tax'][$tax])) continue; // Skip results with no placement options 
				
				$fieldTermIDs = '';
				foreach ($placementOptions['tax'][$tax] as $termID=>$enabled) {
					// Check if this term ID exists in the $termIDs
					if (in_array($termID, $termIDs)) {
						$fields[] = $result;
						break;
					}
				}
			}
		}
		
		return $fields;
    }
    
    /**
    * Returns an array of all fields that are active and set to be displayed for the given post type
    *
    * @param string $type Post Type
    * @return mixed Records / WP_Error / false
    */
    function GetPostTypeActive($type) {
    	global $wpdb;

    	$results = $wpdb->get_results(" SELECT *
                                    	FROM ".$wpdb->prefix.$this->primaryTable."
                                    	ORDER BY name ASC");
		if ($results === FALSE) return new WP_Error('db_query_error', __('Fields could not be obtained from the database.', 'comment-rating-field-pro'), $wpdb->last_error);
		
		if (is_array($results)) {
			foreach ($results as $key=>$result) {
				// Unserialise placementOptions
				$placementOptions = unserialize($result->placementOptions);
				if (!is_array($placementOptions)) continue; // Skip results with no placement options
				if ($placementOptions['type'][$type] == 1) $fields[] = $result; // Include results matching post type
			}
		}
		
		return $fields;
    }
   
    /**
    * Returns a count of the total number of records
    * 
    * @param string $search Search Keywords (optional)
    * @return int Record Count
    */
    function GetTotal($search = '') {
        global $wpdb;
        
        if (!empty($search)) {
        	$query = $wpdb->prepare(" 	SELECT *
										FROM ".$wpdb->prefix.$this->primaryTable."
        	                            WHERE label LIKE '%%%s%%'",
        	                            $search);
        	$results = $wpdb->get_results($query);
        } else {
        	$results = $wpdb->get_results(" SELECT *
        	                                FROM ".$wpdb->prefix.$this->primaryTable);
        }
        return count($results);    
    }
    
    /**
    * Transforms POSTed data before saving to the database.
    *
    * @param array $postData $_POST data
    * @return array Normalised field Array
    */
    function TransformPostData($postData) {
    	global $crfpCore;
    	
    	$field = $postData['comment-rating-field-pro-plugin']; // Main settings
    	if ($field['pKey'] != '') $field['fieldID'] = $field['pKey']; // Primary Key
        
       	return $field;
    }
    
    /**
    * Parses the given field data for frontend output, removing slashes
    *
    * @param array $field field
    * @return array Parsed field
    */
    function Parse($field) {
    	global $crfpCore;
    	
    	// Unserialize
    	if (isset($field['placementOptions'])) {
    		if (!is_array($field['placementOptions'])) $field['placementOptions'] = unserialize($field['placementOptions']); 
		}
		
        // Stripslashes and HTML entity encode characters that haven't already been encoded
    	if (isset($field['label'])) $field['label'] = htmlspecialchars(stripslashes($field['label']), ENT_QUOTES, 'UTF-8', false);
		
       	return $field;
    }

    /**
    * Returns a record with details for the given primary key ID
    * 
    * @param int $primaryKey Primary Key ID
    * @param bool $isFrontend Edits opt in code based on element settings and gets image sizes for elements (default: false)
    * @return array Record Details
    */
    function GetByID($primaryKey, $isFrontend = false) {
        global $wpdb;

        // Get record
        $query = $wpdb->prepare(" 	SELECT *
									FROM ".$wpdb->prefix.$this->primaryTable."
                                    WHERE ".$this->primaryTableKey." = '%s'
                                    LIMIT 1",
                                    $primaryKey);
        $results = $wpdb->get_results($query, ARRAY_A);
        if (count($results) == 0) return false;
        $result = $results[0];  

        return $this->Parse($result);
    }
    
    /**
    * Adds or edits a record, based on the given data array.
    * 
    * Must include pKey POST key if editing an existing record
    * 
    * @param array $data POST data
    * @return mixed object ID or WP_Error
    */
    function Save($data) {
        global $wpdb;
        
        // Validate form data
        if (empty($data['label'])) return new WP_Error('form_error', __('Please enter the rating field label.', 'comment-rating-field-pro'));
        if (!isset($data['placementOptions'])) return new WP_Error('form_error', __('Please choose at least one Placement Option.', 'comment-rating-field-pro'));
                
        // Check whether we are adding or editing a record
        $results = $this->GetByID($data['pKey']);
        if (!empty($results) AND count($results) > 1) {
            // Editing an existing record
            $query = $wpdb->prepare("UPDATE ".$wpdb->prefix.$this->primaryTable."
		                            SET label = '".htmlentities($data['label'], ENT_QUOTES, 'UTF-8')."',
		                            required = '".(isset($data['required']) ? 1 : 0)."',
                                    required_text = '".htmlentities($data['required_text'], ENT_QUOTES, 'UTF-8')."',
                                    cancel_text = '".htmlentities($data['cancel_text'], ENT_QUOTES, 'UTF-8')."',
                                    placementOptions = '".(trim($data['placementOptions'] != '') ? serialize($data['placementOptions']) : '')."'
                                    WHERE ".$this->primaryTableKey." = %s",
                                    $data['pKey']);
            $result = $wpdb->query($query);

            // Check query was successful
            if ($result === FALSE) return new WP_Error('db_query_error', __('Field could not be edited in the database. DB said: '.$wpdb->last_error), $wpdb->last_error); 

            // Success!
            return $data['pKey']; 
        } else {
            // Adding a new record       
            $result = $wpdb->query("INSERT INTO ".$wpdb->prefix.$this->primaryTable." (label, required, required_text, cancel_text, placementOptions)
            						VALUES ('".htmlentities($data['label'], ENT_QUOTES, 'UTF-8')."',
            						'".(isset($data['required']) ? 1 : 0)."',
		                            '".htmlentities($data['required_text'], ENT_QUOTES, 'UTF-8')."',
                                    '".htmlentities($data['cancel_text'], ENT_QUOTES, 'UTF-8')."',
                                    '".(trim($data['placementOptions'] != '') ? serialize($data['placementOptions']) : '')."')");
			

            // Check query was successful
            if ($result === FALSE) return new WP_Error('db_query_error', __('Field could not be saved to the database. DB said: '.$wpdb->last_error), $wpdb->last_error); 
            $fieldID = $wpdb->insert_id;

            // Success!
            return $fieldID;
        }    
    }
      
    /**
    * Deletes the record for the given primary key ID
    * 
    * @param int $primaryKeys Primary Key ID
    * @return bool Success
    */
    function DeleteByID($primaryKey) {
        global $wpdb;
        
        $query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix.$this->primaryTable."
                        		WHERE ".$this->primaryTableKey." = %s
                        		LIMIT 1",
                        		$primaryKey);
        $result = $wpdb->query($query);
                          
        // Check query was successful
        if ($result === FALSE) return new WP_Error('db_query_error', __('Field could not be deleted from the database.', 'comment-rating-field-pro'), $wpdb->last_error);

        return true;
    }
    
    /**
    * Deletes the records for the given primary key ID array
    * 
    * @param array $primaryKeys Primary Key ID array
    * @return bool Success
    */
    function DeleteByIDs($primaryKeys) {
        global $wpdb;
        
        $result = $wpdb->query("DELETE FROM ".$wpdb->prefix.$this->primaryTable."
                        		WHERE ".$this->primaryTableKey." IN (".implode(',', $primaryKeys).")");
                          
        // Check query was successful
        if ($result === FALSE) return new WP_Error('db_query_error', __('Fields could not be deleted from the database.', 'comment-rating-field-pro'), $wpdb->last_error); 

        return true;
    }
}
?>
