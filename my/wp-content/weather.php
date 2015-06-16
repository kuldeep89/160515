<?php
// Working on a new weather widget
$get_coords = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='.$_GET['address']);
echo '<pre>'.$get_coords.'</pre>'; /*
exit;
echo '<pre>'.print_r(json_decode($get_coords, true), true); exit;

$lat = 41.2381;
$lon = -85.8530469;

$xml=simplexml_load_file("http://graphical.weather.gov/xml/sample_products/browser_interface/ndfdXMLclient.php?lat=41.2381&lon=-85.8530469&product=time-series&begin=2014-03-26T00:00:00&end=2014-04-02T00:00:00&maxt=maxt&mint=mint");

$weather_data = $xml->data->parameters->temperature;
$weather_temps = $weather_data->value;
$weather_units = substr(strtoupper($weather_data['units']), 0, 1);

foreach ($weather_temps as $cur_temp) {
	echo '- '.$cur_temp.' '.$weather_units.'<br/>';
}
*/
?>