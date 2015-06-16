<?php
/*
Plugin Name: Weather Data
Plugin URI: http://www.paypromedia.com/
Description: This plugin shows weather data.
Version: 0.0.1
Contributors: bstump
Author URI: http://www.paypromedia.com/individuals/bobbie-stump/
License: GPLv2
*/

// Start session
session_start();

// Get weather
function get_weather($location_data = null, $is_ajax = true) {
    // Get location data and figure out if this is an ajax request
    $location_data = isset($_POST['data']) ? $_POST['data'] : $location_data;
    $is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : $is_ajax;

    $user_meta = get_user_meta(get_current_user_id(), 'last_weather_update');
    if (count($user_meta) == 0 || time()-$user_meta[0] > 3600) {
            // Get lat/lng from request
            $location = (!is_null($location_data) && isset($location_data['lat']) && isset($location_data['lon'])) ? array('lat' => $location_data['lat'], 'lng' => $location_data['lon']) : get_ip_location();

            // Load weather data
            $weather_data = simplexml_load_file('http://graphical.weather.gov/xml/sample_products/browser_interface/ndfdXMLclient.php?lat='.$location['lat'].'&lon='.$location['lng'].'&product=time-series&begin='.date('Y-m-d').'T00:00:00&end='.date('Y-m-d', strtotime('+7 days')).'T00:00:00&maxt=maxt&mint=mint');

            // Turn location data into array
            $weather_data = json_decode(json_encode($weather_data), true);

            // Get city and state from Google Maps, if not already set
            if (!isset($location['city']) && !isset($location['state'])) {
                    // Get location data from Google Maps
                    $location_data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$location['lat'].','.$location['lng'].'&sensor=true'), true);

                    // Assign location data to array
                    foreach ($location_data['results'][0]['address_components'] as $cur_data)  {
                            $loc_data[$cur_data['types'][0]] = $cur_data['long_name'];
                    }

                    // Set city and state
                    $location['city'] = $loc_data['locality'];
                    $location['state'] = $loc_data['administrative_area_level_1'];
            }

            // Get current temperature
            $cur_temp = json_decode(file_get_contents('http://api.worldweatheronline.com/free/v1/weather.ashx?q=Warsaw,%20IN&format=json&extra=isDayTime&num_of_days=1&key=d3227ampqc759w4e2zmwggjk'), true);

            // Set weather dates
            $dates = $weather_data['data']['time-layout'][0]['start-valid-time'];

            // Add city and state to forecast
            $forecast['city'] = ucwords($location['city']);
            $forecast['state'] = ucwords($location['state']);
            $forecast['cur_temp'] = (isset($cur_temp['data']['current_condition'][0]['temp_F'])) ? $cur_temp['data']['current_condition'][0]['temp_F'] : '--';

            // Add weather data to array
            if (isset($weather_data['data']['parameters']['temperature'][0]['value'])) {
                foreach ($weather_data['data']['parameters']['temperature'][0]['value'] as $key => $cur_temp) {
                    $the_day = date('D', strtotime($dates[$key]));
                    $forecast['data'][] = array('day' => $the_day[0], 'temp' => $cur_temp);
                }
            } else {
                $forecast['data'][] = array();
            }

            // Encode forecast as JSON
            $forecast = json_encode($forecast);

            // Cache info in user's meta data
            if (is_user_logged_in()) {
            	update_user_meta(get_current_user_id(), 'last_weather_update', time());
				update_user_meta(get_current_user_id(), 'latest_weather_information', $forecast);
			} else {
            	$_SESSION['last_weather_update'] = time();
				$_SESSION['latest_weather_information'] = $forecast;
			}
    } else {
        // Retrieve cached weather info
		if (is_user_logged_in()) {
			$forecast = get_user_meta(get_current_user_id(), 'latest_weather_information');
			$forecast = $forecast[0];
		} else {
			$forecast = $_SESSION['latest_weather_information'];
			$forecast = $forecast[0];
		}
    }

    // Return weather data
    if ($is_ajax == true) {
            echo $forecast;
            die();
    } else {
            return $forecast;
    }
}
add_action('wp_ajax_nopriv_get_weather', 'get_weather');
add_action('wp_ajax_get_weather', 'get_weather');

// Get user's location via IP address
function get_ip_location() {
	// Get user IP address
	$user_ip_address = get_user_ip();

	// Get location by IP
	if ($user_ip_address != '127.0.0.1') {
		$location = json_decode(file_get_contents('http://api.ipinfodb.com/v3/ip-city/?key=a82ef0ebe0b74910a71f997cd4580cb6bb63140f9635ef5f8584f18cf5fed6b3&ip='.$user_ip_address.'&format=json'), true);
	}

	// Return lat/lng data
	return (isset($location['latitude']) && isset($location['longitude']) && $user_ip_address != '127.0.0.1') ? array('lat' => $location['latitude'], 'lng' => $location['longitude'], 'city' => strtolower(ucwords($location['cityName'])), 'state' => strtolower(ucwords($location['regionName']))) : array('lat' => '41.2381', 'lng' => '-85.8530469', 'city' => 'Warsaw', 'state' => 'Indiana');
}

// Get user's IP address
function get_user_ip() {
	$ipaddress = '';

	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if(isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';

	return $ipaddress;
}
?>
