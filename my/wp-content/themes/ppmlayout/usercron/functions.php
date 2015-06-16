<?php

define( 'BLOCK_LOAD', true );
require_once('database.php');
require_once('/usr/share/nginx/html/saltsha.com/public_html/my/wp-config.php');

$wp_users = $table_prefix . "users";
$wp_usermeta = $table_prefix . "usermeta";
$wp_user_changes = $table_prefix . "user_changes";
$wp_groups_group = $table_prefix . "groups_group";
$wp_groups_user_group = $table_prefix . "groups_user_group";

$admin_email = get_settings('admin_email');

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}
function array_flatten(array $array) {
    $flat = array(); // initialize return array
    $stack = array_values($array); // initialize stack
    while($stack) {
        $value = array_shift($stack);
        if (is_array($value)) {
            $stack = array_merge(array_values($value), $stack);
        } else {
           $flat[] = $value;
        }
    }
    return $flat;
}	
?>