// Load weather on page load
$(document).ready(function() {
	
	// Get weather based on geolocation API, if required for page.
	if( location.pathname == '/' || location.pathname == '' ) {
	
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(getWeather);
		} else {
			getWeather(null);
		}
	
	}
	
});

// Get the weather
function getWeather(position) {
	// Set lat/lng
	var latitude = (position == null) ? null : position.coords.latitude;
	var longitude = (position == null) ? null : position.coords.longitude;

	// Get weather and display it
	$.post('/wp-admin/admin-ajax.php', { action : 'get_weather', is_ajax : 'true', 'data' : '{"lat":"'+latitude+'","lon":"'+longitude+'"}' }, function(data) {
		try {
			data = $.parseJSON(data);
			if (data.city && data.state && data.city != '' && data.state != '') {
				$('.current-location').html(data.city+', '+data.state);
				$('.current-weather').html(data.cur_temp+'<div class="fahrenheit">F</div>');
				$.each( data.data , function( key, val ) {
					$('.weather-days td:eq('+key+')').html(val.day);
					$('.weather-temps td:eq('+key+')').html(val.temp+'<div class="fahrenheit">F</div>');
				});
			}
		} catch(err) {
			// Do nothing, request failed
		}
	});
};