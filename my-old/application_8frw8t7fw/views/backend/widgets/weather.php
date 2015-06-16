<?php
	// Get location data from Google
	$city_or_zip = (isset($widget_items['city_or_zip'])) ? $widget_items['city_or_zip'] : "Troy, MI";
	$location_data = json_decode(@file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($city_or_zip)."&sensor=false"));

	// Get city information
	foreach ($location_data->results[0]->address_components as $address_part) :
		if (in_array('locality', $address_part->types)) :
			// Get city name
			$address_city = $address_part->long_name;
			endif;
		if (in_array('administrative_area_level_1', $address_part->types)) :
			// Get state abbreviation
			$address_state = $address_part->short_name;
			endif;
	endforeach;
?>
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_type="1" db_id="<?php echo $db_id ?>">
	<div class="portlet-title">
		<div class="top-news">
			<a href="" class="btn green" style="margin-bottom: -10px;">
				<span>Weather</span>
				<em class="stock-time"><?php echo date('M j, h:iA T') ?></em>
				<i class="icon-cloud top-news-icon"></i>
			</a>
		</div>
	
		<div class="tools" style="margin-top: -2.0em;">
			<a href="javascript:;" class="collapse btn-group"></a>
		</div>
	</div>
	<!-- End of Category Title -->
	
	<div class="portlet-body">
		<div id="widget-forecast">
			<iframe id="forecast_embed" type="text/html" frameborder="0" height="220" width="100%" src="http://forecast.io/embed/#lat=<?php echo $location_data->results[0]->geometry->location->lat ?>&lon=<?php echo $location_data->results[0]->geometry->location->lng ?>&name=<?php echo $address_city.', '.$address_state ?>"> </iframe>
		</div>
	</div>
</div>