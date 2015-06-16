<?php
/*
Plugin Name:  User Accounts - NOT READY
Plugin URI: --
Description: Plugin to modify user accounts
Version: 1.0
Author: Bobbie Stump
Author URI: --
License: GPL
*/

// Show venue status (open, delayed, closed)
function user_accounts()
{
	$venue_status_string = "";

	// Check for pre-scheduled closings and delays
	$user_accounts_label = get_option('user_accounts_label');
	$user_accounts_schedule_delay_close = get_option('user_accounts_schedule_delay_close');
	$status_events = json_decode($user_accounts_schedule_delay_close, true);
	if (count($status_events["events"]) > 0) {
		foreach ($status_events["events"] as $cur_status_event) {
			if (date('m/d/Y') == $cur_status_event["eventDate"]) {
				$user_accounts = $cur_status_event["eventStatus"];
			} else {
				$user_accounts = get_option('user_accounts_status');
			}
		}
	} else {
		$user_accounts = get_option('user_accounts_status');
	}

	// Check for being closed on specific days of the week
	if (@unserialize(get_option('user_accounts_autoclose'))) {
		$user_accounts_autoclose = unserialize(get_option('user_accounts_autoclose'));
	} else {
		$user_accounts_autoclose = get_option('user_accounts_autoclose');
	}
	if (is_array($user_accounts_autoclose) && in_array(date('N'), $user_accounts_autoclose)) {
		$user_accounts = "CLOSED";
	}

	$user_accounts_zip = get_option('user_accounts_zip');
	$weatherXmlString = retrieveYahooWeather($user_accounts_zip);
	if ($weatherXmlString)
	{
		$weatherXmlObject = new SimpleXMLElement($weatherXmlString);
		if ($weatherXmlObject)
		{
			$currentCondition = $weatherXmlObject->xpath("//yweather:condition");
			$currentTemperature = $currentCondition[0]["temp"];
			$user_accounts_string .= '<link rel="stylesheet" href="'.plugins_url( 'css/style.css' , __FILE__ ).'" />';
			$user_accounts_string .= '<div id="venue-status">';
			$user_accounts_string .= '<div id="venue-status-label">'.$user_accounts_label.'</div>';
			$user_accounts_string .= '<div class="weather_'.strtolower($user_accounts).'">'.$user_accounts.'</div>';
			$user_accounts_string .= '<img src="http://l.yimg.com/a/i/us/we/52/'.$currentCondition[0]["code"].'.gif" class="weather_icon" />';
			$user_accounts_string .= '<p><span>'.$currentTemperature.'</span>&deg;F</p>';
			$user_accounts_string .= '</div>';
		}
		else
		{
			$user_accounts_string .= '-- ';
		}
	}
	else
	{
		$user_accounts_string .= '-- ';
	}
	return $user_accounts_string;
}

// Shortcode
add_shortcode('show_user_accounts', 'user_accounts' );

// Runs when plugin is activated
register_activation_hook(__FILE__,'user_accounts_install'); 

// Runs on plugin deactivation
register_deactivation_hook( __FILE__, 'user_accounts_remove' );

// Creates new database field(s)
function user_accounts_install() {
	add_option("user_accounts_label", 'Venue Status', '', 'yes');
	add_option("user_accounts_status", 'OPEN', '', 'yes');
	add_option("user_accounts_zip", '46590', '', 'yes');
	add_option("user_accounts_autoclose", 'a:1:{i:0;s:1:"7";}', '', 'yes');
	add_option("user_accounts_schedule_delay_close", '{"events":[]}', '', 'yes');
}

// Deletes database field(s) associated with plugin
function user_accounts_remove() {
	delete_option('user_accounts_label');
	delete_option('user_accounts_status');
	delete_option('user_accounts_zip');
	delete_option('user_accounts_autoclose');
	delete_option('user_accounts_schedule_delay_close');
}

// Get weather data from Yahoo!
function retrieveYahooWeather($zipCode="46590") {
	// Get weather data
	$yahooUrl = "http://weather.yahooapis.com/forecastrss";
	$yahooZip = "?p=$zipCode";
	$yahooFullUrl = $yahooUrl . $yahooZip; 
	$curlObject = curl_init();
	curl_setopt($curlObject,CURLOPT_URL,$yahooFullUrl);
	curl_setopt($curlObject,CURLOPT_HEADER,false);
	curl_setopt($curlObject,CURLOPT_RETURNTRANSFER,true);
	$returnYahooWeather = curl_exec($curlObject);
	curl_close($curlObject);
	return $returnYahooWeather;
}

// Admin page function
function user_accounts_html_page() {
?>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="<?php echo plugins_url( 'css/style.css' , __FILE__ ) ?>" />
<script>
var scheduledEvents = new Array();

function addEvent() {
	if (jQuery('#scheduled_events').children().find('tr').length <= 2 && jQuery('#scheduled_events tr:eq(1)').html().trim() == '<td colspan="3"><em>No Scheduled Events</em></td>') {
		jQuery('#scheduled_events tr:eq(1)').remove();
	}
	var curEventData = (jQuery('#scheduled_events tr:eq(1)').html() == null ? 'NULL_DATA' : jQuery('#scheduled_events tr:eq(1)').html());
	if (curEventData.indexOf('Set Event Date') < 0 && curEventData.indexOf('Set Event Status') < 0) {
		jQuery('#scheduled_events').append('<tr><td><em onclick="setEventDate(this)">Set Event Date</em></td><td><em onclick="setEventStatus(jQuery(this))">Set Event Status</em></td><td><img src="<?php echo plugins_url( 'images/delete.png' , __FILE__ ) ?>" onclick="removeEvent(jQuery(this).parent())" /></td></tr>');
	} else {
		if (curEventData != 'NULL_DATA') {
			alert('Please set event date and status on the current event before you add a new one.');
		} else {
			jQuery('#scheduled_events').append('<tr><td><em onclick="setEventDate(this)">Set Event Date</em></td><td><em onclick="setEventStatus(jQuery(this))">Set Event Status</em></td><td><img src="<?php echo plugins_url( 'images/delete.png' , __FILE__ ) ?>" onclick="removeEvent(jQuery(this).parent())" /></td></tr>');
		}
	}
}
function setEventDate(tdItem) {
	var parentNode = jQuery(tdItem).parent();
	jQuery(parentNode).html('<input type="text" onchange="setFinalDate(jQuery(this), jQuery(this).val())" />');
	jQuery(parentNode).find('input').datepicker();
	jQuery(parentNode).find('input').focus();
}
function setFinalDate(tdItem, finalValue) {
	var parentNode = jQuery(tdItem).parent();
	jQuery(parentNode).html('<em onclick="setEventDate(jQuery(this))">'+finalValue+'</em>');
}
function setEventStatus(tdItem) {
	var parentNode = jQuery(tdItem).parent();
	jQuery(parentNode).html('<select onchange="setFinalStatus(jQuery(this), jQuery(this).val())"><option value="">Choose Status…</option><option value="DELAY">DELAY</option><option value="CLOSED">CLOSED</option></select>');
	jQuery(parentNode).find('select').focus();
}
function setFinalStatus(tdItem, finalValue) {
	var parentNode = jQuery(tdItem).parent();
	jQuery(parentNode).html('<em onclick="setEventStatus(jQuery(this))">'+finalValue+'</em>');
}
function removeEvent(trId) {
	jQuery(trId).closest('tr').remove();
	if (jQuery('#scheduled_events').children().find('tr').length <= 1) {
		jQuery('#scheduled_events').append('<tr><td colspan="3"><em>No Scheduled Events</em></td></tr>');
	}
}

// Get scheduled events before submitting
jQuery(function() {
	jQuery('#update_status').submit(function() {
		jQuery.each(jQuery('#scheduled_events tr'), function() {
			if (jQuery(this).index() > 0) {
				if (jQuery(this).find('td:eq(0)').text() != "" && jQuery(this).find('td:eq(1)').text() != "") {
					var curEvent = new Array();
					curEvent[0] = jQuery(this).find('td:eq(0)').text();
					curEvent[1] = jQuery(this).find('td:eq(1)').text();
					scheduledEvents.push(curEvent);
				}
			}
		});
		// alert(scheduledEvents.length);
		var JSONData = '{"events":[';
		for (var i=0; i<scheduledEvents.length; i++) {
			JSONData += '{"eventDate":"'+scheduledEvents[i][0]+'","eventStatus":"'+scheduledEvents[i][1]+'"},';
		}
		if (scheduledEvents.length > 0) {
			JSONData = JSONData.substr(0, JSONData.length-1)+']}';
		} else {
			JSONData += ']}';
		}
		jQuery('#user_accounts_schedule_delay_close').val(JSONData);
	});
});
</script>
<div>
<h2>Venue Status Settings</h2>
<form id="update_status" method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<fieldset>
	<legend>Current Venue Status</legend>
	<select name="user_accounts_status" id="user_accounts_status">
		<option value="OPEN"<?php if (get_option('user_accounts_status') == "OPEN") {echo ' selected'; } ?>>OPEN</option>
		<option value="DELAY"<?php if (get_option('user_accounts_status') == "DELAY") {echo ' selected'; } ?>>DELAY</option>
		<option value="CLOSED"<?php if (get_option('user_accounts_status') == "CLOSED") {echo ' selected'; } ?>>CLOSED</option>
	</select>
</fieldset>
<br/>
<fieldset>
	<legend>Status Label</legend>
	<input type="text" name="user_accounts_label" id="user_accounts_label" value="<?php echo get_option('user_accounts_label') ?>" />
</fieldset>
<br/>
<fieldset>
	<legend>Weather Zip Code</legend>
	<input type="text" name="user_accounts_zip" id="user_accounts_zip" value="<?php echo get_option('user_accounts_zip') ?>" />
</fieldset>
<br/>
<fieldset>
	<legend>Automatically show venue CLOSED on these days of the week</legend>
	<?php
		if (@unserialize(get_option('user_accounts_autoclose'))) {
			$user_accounts_autoclose = unserialize(get_option('user_accounts_autoclose'));
		} else {
			$user_accounts_autoclose = get_option('user_accounts_autoclose');
		}
	?>
	<input type="checkbox" name="user_accounts_autoclose[]" value="1"<?php if (is_array($user_accounts_autoclose) && in_array("1", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Monday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="2"<?php if (is_array($user_accounts_autoclose) && in_array("2", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Tuesday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="3"<?php if (is_array($user_accounts_autoclose) && in_array("3", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Wednesday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="4"<?php if (is_array($user_accounts_autoclose) && in_array("4", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Thursday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="5"<?php if (is_array($user_accounts_autoclose) && in_array("5", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Friday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="6"<?php if (is_array($user_accounts_autoclose) && in_array("6", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Saturday<br/>
	<input type="checkbox" name="user_accounts_autoclose[]" value="7"<?php if (is_array($user_accounts_autoclose) && in_array("7", $user_accounts_autoclose)) {echo ' checked'; } ?> /> Sunday<br/>
</fieldset>
<br/>
<fieldset>
	<legend>Schedule Delays / Closings</legend>
	<span style="color: #ff0000; font-weight: bold; font-size: 10px;">Scheduled delays and closings override the "Current Venue Status" setting at the top of this page.</span><br/>
	<br/>
	<a href="javascript:addEvent()" title="Add Event" alt="Add Event" style="text-decoration: none;"><img src="<?php echo plugins_url( 'images/add.png' , __FILE__ ) ?>" style="vertical-align: top; height: 12px;" /> Add Event</a><br/>
	<br/>
	<table id="scheduled_events" style="width: 100%; max-width: 500px;">
		<tr>
			<td style="width: 45%"><strong>Date</strong></td>
			<td style="width: 45%"><strong>Status</strong></td>
			<td style="width: 10%"><strong>Actions</strong></td>
		</tr>
		<tr>
			<?php
				$user_accounts_schedule_delay_close = get_option('user_accounts_schedule_delay_close');
				$status_events = json_decode($user_accounts_schedule_delay_close, true);

				// Check if any events
				if (count($status_events["events"]) > 0) {
					foreach ($status_events["events"] as $cur_status_event) {
						echo '<tr><td><em onclick="setEventDate(this)">'.$cur_status_event["eventDate"].'</em></td><td><em onclick="setEventStatus(jQuery(this))">'.$cur_status_event["eventStatus"].'</em></td><td><img src="'.plugins_url( 'images/delete.png' , __FILE__ ).'" onclick="removeEvent(jQuery(this).parent())" /></td></tr>';
					}
				} else {
					echo '<td colspan="3"><em>No Scheduled Events</em></td>';
				}
			?>
		</tr>
	</table>
	<input type="hidden" id="user_accounts_schedule_delay_close" name="user_accounts_schedule_delay_close" value="" />
</fieldset>
<br/>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="user_accounts_label,user_accounts_status,user_accounts_zip,user_accounts_autoclose,user_accounts_schedule_delay_close" />
<input type="submit" value="<?php _e('Update Status / Settings') ?>" />
</form>
</div>
<?php
}

// Admin menu stuff
if (is_admin()){
	add_action('admin_menu', 'user_accounts_admin_menu');
	function user_accounts_admin_menu() {
		add_menu_page('Venue Status', 'Venue Status', 1, 'venue-status', 'user_accounts_html_page');
	}
}
?>