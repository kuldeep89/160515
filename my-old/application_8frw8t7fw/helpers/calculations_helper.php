<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Get custom JS
if (!function_exists('time_since')) {

	function time_since( $timestamp_past, $timestamp_future = FALSE, $years = true, $months = true, $days = true, $hours = true, $mins = FALSE, $secs = FALSE, $display_output = true ) {
	
		if( $timestamp_future === FALSE ) {
			$timestamp_future = time();
		}
	
		$diff = $timestamp_future - $timestamp_past;
	    $calc_times = array();
	    $timeleft   = array();
	
	    // Prepare array, depending on the output we want to get.
	    if ($years)  $calc_times[] = array('Year',   'Years',   31104000);
	    if ($months) $calc_times[] = array('Month',  'Months',  2592000);
	    if ($days)   $calc_times[] = array('Day',    'Days',    86400);
	    if ($hours)  $calc_times[] = array('Hour',   'Hours',   3600);
	    if ($mins)   $calc_times[] = array('Minute', 'Minutes', 60);
	    if ($secs)   $calc_times[] = array('Second', 'Seconds', 1);
	
	    foreach ($calc_times AS $timedata)
	    {
	        list($time_sing, $time_plur, $offset) = $timedata;
	
	        if ($diff >= $offset)
	        {
	            $left = floor($diff / $offset);
	            $diff -= ($left * $offset);
	            if ($display_output === true) {
	                $timeleft[] = "{$left} " . ($left == 1 ? $time_sing : $time_plur);
	            } else {
	                if (!isset($timeleft[strtolower($time_sing)]))
	                    $timeleft[strtolower($time_sing)] = 0;
	                $timeleft[strtolower($time_sing)] += $left;
	            }
	        }
	    }
	    if ($display_output === false)
	        return $timeleft;
	        
	    return $timeleft ? ($timestamp_future > $timestamp_past ? null : '-') . implode(', ', $timeleft) : 0;

		
	}
	
}